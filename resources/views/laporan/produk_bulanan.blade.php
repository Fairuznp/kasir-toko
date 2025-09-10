@extends('layouts.main', ['title' => 'Laporan Produk Bulanan'])

@section('title-content')
<i class="fas fa-chart-line mr-2"></i>
Laporan Produk Bulanan
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan Penjualan Produk Bulanan</h3>
        <div class="card-tools">
            <a href="{{ route('laporan.produkBulanan.cetak', ['bulan' => $bulan, 'tahun' => $tahun]) }}" 
               target="_blank" class="btn btn-success btn-sm">
                <i class="fas fa-print mr-1"></i> Cetak
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('laporan.produkBulanan') }}" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="bulan">Bulan</label>
                    <select name="bulan" id="bulan" class="form-control">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $bulan == $i ? 'selected' : '' }}>{{ DateTime::createFromFormat('!m', $i)->format('F') }}</option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tahun">Tahun</label>
                    <input type="number" name="tahun" id="tahun" class="form-control" value="{{ $tahun }}">
                </div>
                <div class="col-md-2 align-self-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>

        @if(isset($laporanProdukBulanan) && count($laporanProdukBulanan) > 0)
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Bulan:</strong> {{ $bulan }} / <strong>Tahun:</strong> {{ $tahun }} |
            <strong>Total Produk:</strong> {{ count($laporanProdukBulanan) }}
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead class="bg-light">
                    <tr>
                        <th width="5%">No</th>
                        <th width="30%">Nama Produk</th>
                        <th width="15%">Kategori</th>
                        <th width="15%">Harga Satuan</th>
                        <th width="10%">Qty Terjual</th>
                        <th width="20%">Total Pendapatan Kotor</th>
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
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $produk->nama_produk }}</td>
                        <td>{{ $produk->nama_kategori ?? '-' }}</td>
                        <td>Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                        <td>{{ $produk->total_terjual }}</td>
                        <td>Rp {{ number_format($produk->total_pendapatan, 0, ',', '.') }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-light font-weight-bold">
                        <td colspan="4" class="text-right">TOTAL:</td>
                        <td>{{ $totalQty }}</td>
                        <td>Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
            <div class="alert alert-info">Tidak ada data untuk bulan dan tahun yang dipilih.</div>
        @endif
    </div>
</div>
@endsection
