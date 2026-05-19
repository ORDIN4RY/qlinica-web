<?php

namespace App\Http\Controllers;

use App\Models\RawatInap;
use App\Models\Kamar;
use App\Models\Pasien;
use App\Models\Pegawai;
use App\Models\Billing;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RawatInapController extends Controller
{
    public function index(Request $request)
    {
        $query = RawatInap::with(['pasien', 'kamar', 'dokter', 'billing']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        } else {
            // Default show active admissions
            $query->where('status', 'Aktif');
        }

        $rawat_inaps = $query->orderBy('tgl_masuk', 'desc')->paginate(15);
        $kamarsTersedia = Kamar::where('status', 'Tersedia')->orderBy('kelas')->get();
        $dokters = Pegawai::where('jabatan_id', 1)->get(); // DPJP
        $pasiens = Pasien::orderBy('nama')->get();
        $rekomendasiIds = \App\Models\RekamMedis::where('is_rekomendasi_rawat_inap', true)
            ->whereDate('tanggal_periksa', '>=', Carbon::now()->subDays(7)) // Berlaku 7 hari
            ->pluck('pasien_id')->toArray();

        return view('admin.rawat_inap', compact('rawat_inaps', 'kamarsTersedia', 'dokters', 'pasiens', 'rekomendasiIds'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pasien_id' => 'required|exists:pasien,id',
            'kamar_id' => 'required|exists:kamar,id',
            'dokter_id' => 'required|exists:pegawai,id',
            'jenis_penjamin' => 'required|in:Umum,BPJS KESEHATAN,Asuransi Lain',
            'no_sep' => 'nullable|string|max:50',
            'tgl_masuk' => 'required|date',
        ]);

        \DB::transaction(function() use ($validated) {
            // Create Rawat Inap record
            $rawatInap = RawatInap::create([
                'pasien_id' => $validated['pasien_id'],
                'kamar_id' => $validated['kamar_id'],
                'dokter_id' => $validated['dokter_id'],
                'jenis_penjamin' => $validated['jenis_penjamin'],
                'no_sep' => $validated['no_sep'],
                'tgl_masuk' => Carbon::parse($validated['tgl_masuk']),
                'status' => 'Aktif'
            ]);

            // Update bed status to Terisi
            $kamar = Kamar::find($validated['kamar_id']);
            $kamar->update(['status' => 'Terisi']);

            // Hapus/konsumsi flag rekomendasi rawat inap agar tidak muncul lagi di dropdown
            \App\Models\RekamMedis::where('pasien_id', $validated['pasien_id'])
                ->where('is_rekomendasi_rawat_inap', true)
                ->update(['is_rekomendasi_rawat_inap' => false]);

            // Cari tagihan poli (Rawat Jalan) terakhir yang masih 'Belum Bayar' dan belum diikat rawat_inap_id
            $billing = Billing::where('pasien_id', $rawatInap->pasien_id)
                ->where('status', 'Belum Bayar')
                ->whereNull('rawat_inap_id')
                ->latest()
                ->first();

            if ($billing) {
                // Ikat billing poli ini ke rawat inap, jadi tagihannya menyatu
                $billing->update([
                    'rawat_inap_id' => $rawatInap->id,
                    // Perbarui info BPJS jika rawat inap pakai BPJS
                    'no_bpjs' => $rawatInap->jenis_penjamin === 'BPJS KESEHATAN' ? ($rawatInap->pasien->no_bpjs ?? $rawatInap->no_sep) : $billing->no_bpjs,
                ]);
                $billing->recalculateTotals();
                $billing->save();
            } else {
                // Jika tidak ada tagihan poli yang menggantung, buat tagihan rawat inap baru
                $billing = new Billing();
                $billing->rawat_inap_id = $rawatInap->id;
                $billing->pasien_id = $rawatInap->pasien_id;
                $billing->no_invoice = 'INV-RI-' . date('Ymd') . '-' . str_pad(Billing::count() + 1, 4, '0', STR_PAD_LEFT);
                $billing->no_bpjs = $rawatInap->jenis_penjamin === 'BPJS KESEHATAN' ? ($rawatInap->pasien->no_bpjs ?? $rawatInap->no_sep) : null;
                $billing->status = 'Belum Lunas'; // Akan dirender sebagai Belum Bayar di UI, atau ubah jadi 'Belum Bayar' jika strictly ENUM
                // Tunggu, ENUM status di database Billing adalah 'Belum Bayar', 'Lunas', 'Batal'
                $billing->status = 'Belum Bayar'; 
                $billing->save();
            }
        });

        return redirect()->back()->with('success', 'Pasien berhasil di-Check-In ke kamar.');
    }

    public function checkout(Request $request, $id)
    {
        $rawatInap = RawatInap::findOrFail($id);
        
        if ($rawatInap->status === 'Selesai') {
            return redirect()->back()->with('error', 'Pasien ini sudah selesai dirawat (Check-Out).');
        }

        $validated = $request->validate([
            'tgl_keluar' => 'required|date|after_or_equal:' . $rawatInap->tgl_masuk->format('Y-m-d\TH:i'),
            'catatan_keluar' => 'nullable|string',
        ]);

        \DB::transaction(function() use ($rawatInap, $validated) {
            $tglKeluar = Carbon::parse($validated['tgl_keluar']);
            $tglMasuk = Carbon::parse($rawatInap->tgl_masuk);
            
            // Hitung durasi hari
            $durasiHari = $tglMasuk->startOfDay()->diffInDays($tglKeluar->startOfDay());
            if ($durasiHari == 0) {
                $durasiHari = 1; // Minimal 1 hari
            }

            // Hitung biaya kamar
            $biayaKamar = $durasiHari * $rawatInap->kamar->tarif_per_malam;

            // Update status kamar jadi Tersedia
            $rawatInap->kamar->update(['status' => 'Tersedia']);

            // Update Rawat Inap
            $rawatInap->update([
                'tgl_keluar' => $tglKeluar,
                'status' => 'Selesai',
                'catatan_keluar' => $validated['catatan_keluar'],
            ]);

            // Injeksi Biaya Kamar ke Billing dan kalkulasi
            if ($rawatInap->billing) {
                // Kalkulasi kamar sekarang sudah sepenuhnya dinamis di dalam model Billing.php!
                // Kita cukup memanggil recalculateTotals() dan save()
                $rawatInap->billing->recalculateTotals();
                $rawatInap->billing->save();
            }
        });

        return redirect()->back()->with('success', 'Pasien berhasil di-Check-Out. Tagihan kamar telah masuk ke sistem Kasir.');
    }
}
