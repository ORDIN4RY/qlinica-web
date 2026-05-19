@extends('layouts.app')

@section('title', 'Billing & Kasir')
@section('page-title', 'Billing & Pembayaran Kasir')
@section('page-subtitle', 'Kelola tagihan, invoice, dan pembayaran pasien')

@section('content')
<div class="bg-white rounded-xl border border-gray-200 p-4 md:p-6 shadow-sm">
  <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-4 mb-6">
    <div>
      <h2 class="font-bold text-lg text-gray-800">Antrean Tagihan Pasien</h2>
      <p class="text-sm text-gray-500">Pilih pasien untuk melunasi pembayaran tindakan dokter dan resep obat.</p>
    </div>
    
    <form method="GET" action="{{ route('admin.billing') }}" class="flex flex-col sm:flex-row gap-3 w-full sm:w-auto">
      <input name="search" value="{{ $search }}" type="text" placeholder="Nama pasien, NIK atau Invoice..." class="flex-grow sm:w-64 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
      
      <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 text-sm">
        @foreach(['Belum Bayar' => 'Belum Lunas', 'Lunas' => 'Sudah Lunas', 'Batal' => 'Dibatalkan', 'Semua' => 'Semua Status'] as $val => $label)
          <option value="{{ $val }}" @selected($status === $val)>{{ $label }}</option>
        @endforeach
      </select>
      
      <button type="submit" class="px-4 py-2 bg-blue-900 text-white rounded-lg hover:bg-blue-800 transition text-sm font-medium">Filter</button>
    </form>
  </div>

  @if($billings->isEmpty())
    <div class="p-8 rounded-xl bg-blue-50 border border-blue-100 text-blue-900 flex items-center gap-3">
      <i class="fas fa-info-circle text-blue-500 text-xl"></i>
      <div>
        <p class="font-semibold">Tidak Ada Data Tagihan</p>
        <p class="text-sm text-blue-700">Tidak ada rekam medis pasien yang terdaftar atau memerlukan penagihan untuk filter saat ini.</p>
      </div>
    </div>
  @else
    <div class="overflow-x-auto rounded-xl border border-gray-200">
      <table class="min-w-full divide-y divide-gray-200 text-left text-sm text-gray-700">
        <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-600">
          <tr>
            <th class="px-6 py-4">No. Invoice</th>
            <th class="px-6 py-4">Nama Pasien</th>
            <th class="px-6 py-4">Tanggal Kunjungan</th>
            <th class="px-6 py-4">Dokter Pemeriksa</th>
            <th class="px-6 py-4 text-right">Total Tagihan</th>
            <th class="px-6 py-4 text-center">Status</th>
            <th class="px-6 py-4 text-center">Aksi</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-200 bg-white">
          @foreach($billings as $b)
            @php
              $statusClasses = [
                'Belum Bayar' => 'bg-amber-100 text-amber-800 border-amber-200',
                'Lunas' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                'Batal' => 'bg-red-100 text-red-800 border-red-200',
              ];
            @endphp
            <tr class="hover:bg-gray-50 transition">
              <td class="px-6 py-4 font-mono font-semibold text-gray-900">
                {{ $b->no_invoice }}
              </td>
              <td class="px-6 py-4">
                <div class="font-semibold text-gray-800">{{ $b->pasien?->nama ?? 'Pasien tidak ditemukan' }}</div>
                <div class="text-xs text-gray-500">NIK: {{ $b->pasien?->nik ?: '–' }}</div>
              </td>
              <td class="px-6 py-4">
                {{ $b->created_at->isoFormat('D MMM YYYY, HH:mm') }}
              </td>
              <td class="px-6 py-4">
                {{ $b->rekamMedis?->dokter?->nama ?? '–' }}
              </td>
              <td class="px-6 py-4 text-right font-bold text-gray-900">
                Rp {{ number_format($b->grand_total, 2, ',', '.') }}
              </td>
              <td class="px-6 py-4 text-center">
                <span class="inline-block px-2.5 py-1 rounded-full text-xs font-semibold border {{ $statusClasses[$b->status] ?? 'bg-gray-100 text-gray-800' }}">
                  {{ $b->status }}
                </span>
              </td>
              <td class="px-6 py-4 text-center">
                <a href="{{ route('admin.billing.show', $b) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold transition {{ $b->status === 'Belum Bayar' ? 'bg-blue-900 text-white hover:bg-blue-800' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                  <i class="fas {{ $b->status === 'Belum Bayar' ? 'fa-wallet' : 'fa-receipt' }}"></i>
                  {{ $b->status === 'Belum Bayar' ? 'Bayar Kasir' : 'Detail Invoice' }}
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    
    <div class="mt-5">
      {{ $billings->links() }}
    </div>
  @endif
</div>
@endsection
