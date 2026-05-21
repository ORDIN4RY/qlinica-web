<?php

namespace App\Http\Controllers;

use App\Models\RawatInap;
use App\Models\Kamar;
use App\Models\Pasien;
use App\Models\Pegawai;
use App\Models\Billing;
use App\Models\RawatInapKamarHistory;
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
        $rekomendasiData = \App\Models\RekamMedis::with(['pasien', 'dokter'])
            ->where('is_rekomendasi_rawat_inap', true)
            ->whereDate('tanggal_periksa', '>=', Carbon::now()->subDays(7)) // Berlaku 7 hari
            ->get()
            ->keyBy('pasien_id');

        return view('admin.rawat_inap', compact('rawat_inaps', 'kamarsTersedia', 'dokters', 'pasiens', 'rekomendasiData'));
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

            // Update bed status
            $kamar = Kamar::find($validated['kamar_id']);
            $kamar->increment('terisi');
            if ($kamar->isFull()) {
                $kamar->update(['status' => 'Terisi']);
            }

            // Create initial room history record
            RawatInapKamarHistory::create([
                'rawat_inap_id' => $rawatInap->id,
                'kamar_id' => $rawatInap->kamar_id,
                'tarif_per_malam' => $kamar->tarif_per_malam,
                'tgl_mulai' => $rawatInap->tgl_masuk,
                'tgl_selesai' => null,
            ]);

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
            
            // Update status kamar jadi Tersedia & kurangi kapasitas
            $rawatInap->kamar->decrement('terisi');
            $rawatInap->kamar->update(['status' => 'Tersedia']);

            // Close the active room history record
            $activeHistory = $rawatInap->kamarHistories()->whereNull('tgl_selesai')->first();
            if ($activeHistory) {
                $activeHistory->update([
                    'tgl_selesai' => $tglKeluar,
                ]);
            }

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

    public function pindahKamar(Request $request, $id)
    {
        $rawatInap = RawatInap::findOrFail($id);

        if ($rawatInap->status !== 'Aktif') {
            return redirect()->back()->with('error', 'Pasien ini sudah tidak aktif dirawat.');
        }

        $activeHistory = $rawatInap->kamarHistories()->whereNull('tgl_selesai')->first();
        $minDate = $activeHistory ? Carbon::parse($activeHistory->tgl_mulai) : $rawatInap->tgl_masuk;

        $validated = $request->validate([
            'kamar_id' => 'required|exists:kamar,id',
            'tgl_pindah' => 'required|date|after_or_equal:' . $minDate->format('Y-m-d\TH:i'),
            'sertakan_biaya_lama' => 'nullable|boolean',
        ]);

        $newKamarId = $validated['kamar_id'];
        $tglPindah = Carbon::parse($validated['tgl_pindah']);
        $sertakanBiayaLama = $request->boolean('sertakan_biaya_lama');

        // Check if the new room is available
        $newKamar = Kamar::findOrFail($newKamarId);
        if ($newKamar->id === $rawatInap->kamar_id) {
            return redirect()->back()->with('error', 'Kamar baru tidak boleh sama dengan kamar saat ini.');
        }
        if ($newKamar->isFull()) {
            return redirect()->back()->with('error', 'Kamar baru sudah terisi penuh.');
        }

        \DB::transaction(function() use ($rawatInap, $newKamar, $tglPindah, $activeHistory, $sertakanBiayaLama) {
            if ($activeHistory) {
                if ($sertakanBiayaLama) {
                    // Close the old segment, and create a new segment
                    $activeHistory->update([
                        'tgl_selesai' => $tglPindah,
                    ]);

                    // Release old room
                    $oldKamar = Kamar::find($activeHistory->kamar_id);
                    if ($oldKamar) {
                        $oldKamar->decrement('terisi');
                        if ($oldKamar->status === 'Terisi') {
                            $oldKamar->update(['status' => 'Tersedia']);
                        }
                    }

                    // Assign new room to rawat_inap
                    $rawatInap->update([
                        'kamar_id' => $newKamar->id,
                    ]);

                    // Occupy new room
                    $newKamar->increment('terisi');
                    if ($newKamar->isFull()) {
                        $newKamar->update(['status' => 'Terisi']);
                    }

                    // Create new active history record
                    RawatInapKamarHistory::create([
                        'rawat_inap_id' => $rawatInap->id,
                        'kamar_id' => $newKamar->id,
                        'tarif_per_malam' => $newKamar->tarif_per_malam,
                        'tgl_mulai' => $tglPindah,
                        'tgl_selesai' => null,
                    ]);
                } else {
                    // Release old room
                    $oldKamar = Kamar::find($activeHistory->kamar_id);
                    if ($oldKamar) {
                        $oldKamar->decrement('terisi');
                        if ($oldKamar->status === 'Terisi') {
                            $oldKamar->update(['status' => 'Tersedia']);
                        }
                    }

                    // Occupy new room
                    $newKamar->increment('terisi');
                    if ($newKamar->isFull()) {
                        $newKamar->update(['status' => 'Terisi']);
                    }

                    // Update existing history record directly (retaining tgl_mulai)
                    $activeHistory->update([
                        'kamar_id' => $newKamar->id,
                        'tarif_per_malam' => $newKamar->tarif_per_malam,
                        // tgl_mulai remains unchanged!
                    ]);

                    // Assign new room to rawat_inap
                    $rawatInap->update([
                        'kamar_id' => $newKamar->id,
                    ]);
                }
            } else {
                // Fallback: If no history was found, just assign and create one
                // Assign new room to rawat_inap
                $rawatInap->update([
                    'kamar_id' => $newKamar->id,
                ]);

                // Occupy new room
                $newKamar->increment('terisi');
                if ($newKamar->isFull()) {
                    $newKamar->update(['status' => 'Terisi']);
                }

                RawatInapKamarHistory::create([
                    'rawat_inap_id' => $rawatInap->id,
                    'kamar_id' => $newKamar->id,
                    'tarif_per_malam' => $newKamar->tarif_per_malam,
                    'tgl_mulai' => $tglPindah,
                    'tgl_selesai' => null,
                ]);
            }

            // Recalculate totals in real-time if billing exists
            if ($rawatInap->billing) {
                $rawatInap->billing->recalculateTotals();
                $rawatInap->billing->save();
            }
        });

        return redirect()->back()->with('success', 'Kamar pasien berhasil dipindahkan.');
    }
}
