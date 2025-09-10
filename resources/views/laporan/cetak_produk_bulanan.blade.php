@extends('layouts.cetak', ['title' => 'Cetak Laporan Produk Bulanan'])

@section('content')
<div class="container">
    <div class="text-center mb-4">
        <h2>LAPORAN PENJUALAN PRODUK BULANAN</h2>
        <h4>Bulan: {{ $bulan }} | Tahun: {{ $tahun }}</h4>
        <hr>
    </div>

    @if(isset($laporanProdukBulanan) && count($laporanProdukBulanan) > 0)
    <div class="mb-3">
        <strong>Total Produk Terjual: {{ count($laporanProdukBulanan) }} jenis produk</strong>
    </div>

    <table class="table table-bordered table-sm">
        <thead>
            <tr style="background-color: #f8f9fa;">
                <th width="5%" class="text-center">No</th>
                <th width="35%">Nama Produk</th>
                <th width="15%">Kategori</th>
                <th width="15%" class="text-right">Harga Satuan</th>
                <th width="10%" class="text-center">Qty</th>
                <th width="20%" class="text-right">Total Pendapatan Kotor</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalQty = 0;
                $totalPendapatan = 0;
            @endphp
            @foreach($laporanProdukBulanan as $index => $produk)
            @php
                $totalQty += $produk->total_terjual;
                $totalPendapatan += $produk->total_pendapatan;
            @endphp
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $produk->nama_produk }}</td>
                <td>{{ $produk->nama_kategori ?? '-' }}</td>
                <td class="text-right">Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                <td class="text-center">{{ $produk->total_terjual }}</td>
                <td class="text-right">Rp {{ number_format($produk->total_pendapatan, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #f8f9fa; font-weight: bold;">
                <td colspan="4" class="text-right">TOTAL:</td>
                <td class="text-center">{{ $totalQty }}</td>
                <td class="text-right">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="row mt-4">
        <div class="col-6">
            <div class="border p-3">
                <strong>Ringkasan:</strong><br>
                • Jumlah Jenis Produk: {{ count($laporanProdukBulanan) }}<br>
                • Total Qty Terjual: {{ $totalQty }}<br>
                • Total Pendapatan Kotor: Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
            </div>
        </div>
    </div>
    @else
        <div class="alert alert-info">Tidak ada data untuk bulan dan tahun yang dipilih.</div>
    @endif
</div>
@endsection