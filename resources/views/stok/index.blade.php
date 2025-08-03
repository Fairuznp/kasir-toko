@extends('layouts.main', ['title' => 'Stok'])

@section('title-content')
    <i class="fas fa-pallet mr-2"></i>
    Stok
@endsection

@section('content')
    @if (session('store') == 'success')
        <x-alert type="success">
            <strong>Berhasil dibuat!</strong> Stok berhasil dibuat.
        </x-alert>
    @endif

    @if (session('destroy') == 'success')
        <x-alert type="success">
            <strong>Berhasil dihapus!</strong> Stok berhasil dihapus.
        </x-alert>
    @endif

    <div class="card card-orange card-outline shadow">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <a href="{{ route('stok.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus mr-2"></i> 
                        <span class="d-none d-sm-inline">Tambah Stok</span>
                        <span class="d-sm-none">Tambah</span>
                    </a>
                </div>

                <div>
                    <form action="?" method="get" class="d-flex">
                        <div class="input-group">
                            <input type="date" class="form-control" name="search" value="<?= request()->search ?>"
                                placeholder="Tanggal">
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
            <!-- Desktop Table -->
            <div class="d-none d-lg-block">
                <table class="table table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="8%">#</th>
                            <th width="35%">Nama Produk</th>
                            <th width="15%">Jumlah</th>
                            <th width="25%">Nama Suplier</th>
                            <th width="12%">Tanggal</th>
                            <th width="5%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($stoks as $key => $stok)
                            <tr>
                                <td>{{ $stoks->firstItem() + $key }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-box text-primary mr-2"></i>
                                        {{ $stok->nama_produk }}
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-success badge-lg">
                                        {{ number_format($stok->jumlah) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-truck text-warning mr-2"></i>
                                        {{ $stok->nama_suplier }}
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ \Carbon\Carbon::parse($stok->tanggal)->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <button type="button" data-toggle="modal" data-target="#modalDelete"
                                        data-url="{{ route('stok.destroy', ['stok' => $stok->id]) }}"
                                        class="btn btn-sm btn-outline-danger btn-delete"
                                        title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3"></i>
                                        <h5>Belum Ada Data Stok</h5>
                                        <p>Silakan tambah stok barang terlebih dahulu</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile/Tablet Cards -->
            <div class="d-lg-none">
                @forelse ($stoks as $key => $stok)
                    <div class="card border-0 border-bottom rounded-0">
                        <div class="card-body py-3">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h6 class="mb-1 font-weight-semibold">
                                            <i class="fas fa-box text-primary mr-2"></i>
                                            {{ $stok->nama_produk }}
                                        </h6>
                                        <button type="button" data-toggle="modal" data-target="#modalDelete"
                                            data-url="{{ route('stok.destroy', ['stok' => $stok->id]) }}"
                                            class="btn btn-sm btn-outline-danger btn-delete ml-2">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="row text-sm">
                                        <div class="col-6">
                                            <div class="mb-1">
                                                <i class="fas fa-sort-numeric-up text-success mr-1"></i>
                                                <strong>Jumlah:</strong>
                                                <span class="badge badge-success ml-1">{{ number_format($stok->jumlah) }}</span>
                                            </div>
                                            <div class="mb-1">
                                                <i class="fas fa-calendar text-info mr-1"></i>
                                                <strong>Tanggal:</strong><br>
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($stok->tanggal)->format('d/m/Y') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-1">
                                                <i class="fas fa-truck text-warning mr-1"></i>
                                                <strong>Suplier:</strong><br>
                                                <small>{{ $stok->nama_suplier }}</small>
                                            </div>
                                            <div class="mb-1">
                                                <i class="fas fa-hashtag text-secondary mr-1"></i>
                                                <strong>No:</strong> {{ $stoks->firstItem() + $key }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-box-open fa-3x mb-3"></i>
                            <h5>Belum Ada Data Stok</h5>
                            <p>Silakan tambah stok barang terlebih dahulu</p>
                            <a href="{{ route('stok.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Stok Pertama
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        @if($stoks->hasPages())
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small d-none d-md-block">
                    Menampilkan {{ $stoks->firstItem() }} sampai {{ $stoks->lastItem() }} 
                    dari {{ $stoks->total() }} data
                </div>
                <div class="ml-auto">
                    {{ $stoks->links('vendor.pagination.bootstrap-4') }}
                </div>
            </div>
        </div>
        @endif
    </div>

<style>
    .card {
        border-radius: 1rem;
        border: none;
    }
    
    .card-header {
        border-radius: 1rem 1rem 0 0 !important;
    }
    
    .shadow {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }
    
    .badge-lg {
        font-size: 0.875rem;
        padding: 0.375rem 0.75rem;
    }
    
    .text-sm {
        font-size: 0.875rem;
    }
    
    @media (max-width: 767.98px) {
        .card-body {
            padding: 1rem !important;
        }
        
        .btn-lg {
            padding: 0.5rem 1rem;
            font-size: 1rem;
        }
    }
    
    .btn:hover {
        transform: translateY(-1px);
        transition: all 0.3s ease;
    }
    
    .table th {
        border-top: none;
        font-weight: 600;
        font-size: 0.875rem;
        color: #495057;
    }
    
    .card.border-bottom:last-child {
        border-bottom: none !important;
    }
</style>
@endsection

@push('modals')
    <x-modal-delete />
@endpush