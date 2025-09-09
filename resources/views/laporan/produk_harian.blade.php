@extends('layouts.main', ['title' => 'Laporan Produk Harian'])

@section('title-content')
<i class="fas fa-chart-line mr-2"></i>
Laporan Produk Harian
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Laporan Penjualan Produk Harian</h3>
        <div class="card-tools">
            <a href="{{ route('laporan.produkHarian.cetak', ['tanggal' => $tanggal]) }}" 
               target="_blank" class="btn btn-success btn-sm">
                <i class="fas fa-print mr-1"></i> Cetak
            </a>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('laporan.produkHarian') }}" class="mb-4">
            <div class="row">
                <div class="col-md-4">
                    <label for="tanggal">Tanggal</label>
                    <input type="date" name="tanggal" id="tanggal" class="form-control" 
                           value="{{ $tanggal }}">
                </div>
                <div class="col-md-2 align-self-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search mr-1"></i> Tampilkan
                    </button>
                </div>
            </div>
        </form>

        @if(isset($laporanProdukHarian) && count($laporanProdukHarian) > 0)
        <div class="alert alert-info">
            <i class="fas fa-info-circle mr-2"></i>
            <strong>Tanggal:</strong> {{ date('d/m/Y', strtotime($tanggal)) }} |
            <strong>Total Produk:</strong> {{ count($laporanProdukHarian) }}
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
                        <th width="20%">Total Pendapatan</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $totalQty = 0;
                        $totalPendapatan = 0;
                    @endphp
                    @foreach($laporanProdukHarian as $index => $produk)
                    @php
                        $totalQty += $produk->total_terjual;
                        $totalPendapatan += $produk->total_pendapatan;
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $produk->nama_produk }}</td>
                        <td>
                            <span class="badge badge-primary">{{ $produk->nama_kategori }}</span>
                        </td>
                        <td>Rp {{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge badge-success">{{ $produk->total_terjual }}</span>
                        </td>
                        <td>
                            <strong>Rp {{ number_format($produk->total_pendapatan, 0, ',', '.') }}</strong>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-light">
                    <tr>
                        <th colspan="4" class="text-right">TOTAL:</th>
                        <th class="text-center">
                            <span class="badge badge-warning">{{ $totalQty }}</span>
                        </th>
                        <th>
                            <strong class="text-success">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</strong>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                Tidak ada data penjualan produk untuk tanggal {{ date('d/m/Y', strtotime($tanggal)) }}
            </div>
        @endif
    </div>
</div>
@endsection
