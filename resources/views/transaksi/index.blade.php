@extends('layouts.main', ['title' => 'Transaksi'])

@section('title-content')
    <i class="fas fa-cash-register mr-2"></i>
    Transaksi
@endsection

@section('content')
    @if (session('store') == 'success')
        <x-alert type="success">
            <strong>Berhasil dibuat!</strong> Transaksi berhasil dibuat.
        </x-alert>
    @endif

    <div class="card card-orange card-outline">
        <div class="card-header">
            <div class="row">
                <div class="col-12 col-md-6 mb-2 mb-md-0">
                    <a href="{{ route('transaksi.create') }}" class="btn btn-primary btn-md-inline">
                        <i class="fas fa-plus mr-2"></i> <span class="d-none d-sm-inline">Buat Transaksi Baru</span><span class="d-sm-none">Buat Baru</span>
                    </a>
                </div>
                <div class="col-12 col-md-6">
                    <form action="?" method="get">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" value="<?= request()->search ?>" placeholder="Nomor Transaksi">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">#</th>
                            <th>Nomor Transaksi</th>
                            <th class="d-none d-lg-table-cell">Pelanggan</th>
                            <th class="d-none d-lg-table-cell">Kasir</th>
                            <th>Total</th>
                            <th class="d-none d-sm-table-cell">Status</th>
                            <th class="d-none d-md-table-cell">Tanggal</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($penjualans as $key => $penjualan)
                            <tr>
                                <td class="d-none d-md-table-cell">{{ $penjualans->firstItem() + $key }}</td>
                                <td>
                                    <div>{{ $penjualan->nomor_transaksi }}</div>
                                    <small class="text-muted d-md-none">
                                        {{ date('d/m/Y H:i', strtotime($penjualan->tanggal)) }}
                                    </small>
                                </td>
                                <td class="d-none d-lg-table-cell">{{ $penjualan->nama_pelanggan }}</td>
                                <td class="d-none d-lg-table-cell">{{ $penjualan->nama_kasir }}</td>
                                <td>
                                    <div>{{ $penjualan->total }}</div>
                                    <small class="text-muted d-lg-none">
                                        {{ $penjualan->nama_pelanggan }}
                                    </small>
                                </td>
                                <td class="d-none d-sm-table-cell">
                                    @php
                                        $iconStatus = $penjualan->status == 'selesai' 
                                            ? 'fa-check text-success' 
                                            : 'fa-times text-danger';
                                    @endphp
                                    <i class="fas {{ $iconStatus }}"></i>
                                </td>
                                <td class="d-none d-md-table-cell">{{ date('d/m/Y H:i:s', strtotime($penjualan->tanggal)) }}</td>
                                <td class="text-right">
                                    <a href="{{ route('transaksi.show', ['transaksi' => $penjualan->id]) }}"
                                       class="btn btn-xs btn-success">
                                        <i class="fas fa-file-invoice mr-1 d-none d-sm-inline"></i> 
                                        <span class="d-none d-sm-inline">Invoice</span>
                                        <i class="fas fa-file-invoice d-sm-none"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-footer">
            {{ $penjualans->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>
@endsection

<style>
@media (max-width: 576px) {
    .btn-xs {
        padding: 0.25rem 0.4rem;
        font-size: 0.75rem;
    }
    
    .card-header .row {
        margin: 0;
    }
    
    .table td, .table th {
        padding: 0.5rem 0.25rem;
        font-size: 0.875rem;
    }
}

@media (max-width: 768px) {
    .table-responsive {
        border: none;
    }
    
    .btn-block {
        display: block;
        width: 100%;
    }
    
    .btn-md-inline {
        display: inline-block;
        width: auto;
    }
}

@media (min-width: 768px) {
    .btn-block {
        display: inline-block;
        width: auto;
    }
}
</style>