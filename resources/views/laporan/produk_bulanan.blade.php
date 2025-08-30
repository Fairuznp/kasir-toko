@extends('layouts.main', ['title' => 'Laporan Produk Bulanan'])

@section('content')
<div class="container">
    <h2>Laporan Produk Bulanan</h2>
    <form method="GET" action="{{ route('laporan.produkBulanan') }}" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <label for="bulan">Bulan</label>
                <select name="bulan" id="bulan" class="form-control">
                    @for ($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('bulan') == $i ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label for="tahun">Tahun</label>
                <input type="number" name="tahun" id="tahun" class="form-control" value="{{ request('tahun', date('Y')) }}">
            </div>
            <div class="col-md-3 align-self-end">
                <button type="submit" class="btn btn-primary">Tampilkan</button>
            </div>
        </div>
    </form>

    @if(isset($laporanProdukBulanan) && count($laporanProdukBulanan) > 0)
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Produk</th>
                <th>Jumlah Terjual</th>
                <th>Total Penjualan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($laporanProdukBulanan as $produk)
            <tr>
                <td>{{ $produk->nama_produk }}</td>
                <td>{{ $produk->total_terjual }}</td>
                <td>-</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <a href="#" onclick="window.print()" class="btn btn-success">Cetak</a>
    @else
        <div class="alert alert-info">Tidak ada data untuk bulan dan tahun yang dipilih.</div>
    @endif
</div>
@endsection
