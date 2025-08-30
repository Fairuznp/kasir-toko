@extends('layouts.cetak', ['title' => 'Cetak Laporan Produk Bulanan'])

@section('content')
<div class="container">
    <h2 class="mb-3">Laporan Produk Bulanan</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Jumlah Terjual</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporanProdukBulanan as $produk)
            <tr>
                <td>{{ $produk->nama_produk }}</td>
                <td>{{ $produk->total_terjual }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="mt-4">Bulan: {{ $bulan }} | Tahun: {{ $tahun }}</div>
</div>
@endsection