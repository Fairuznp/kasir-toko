@extends('layouts.main', ['title' => 'Pelanggan'])

@section('title-content')
    <i class="fas fa-users mr-2"></i>
    Pelanggan
@endsection

@section('content')
    @if (session('store') == 'success')
        <x-alert type="success">
            <strong>Berhasil dibuat!</strong> Pelanggan berhasil dibuat.
        </x-alert>
    @endif
    @if (session('update') == 'success')
        <x-alert type="success">
            <strong>Berhasil diupdate!</strong> Pelanggan berhasil diupdate.
        </x-alert>
    @endif
    @if (session('destroy') == 'success')
        <x-alert type="success">
            <strong>Berhasil dihapus!</strong> Pelanggan berhasil dihapus.
        </x-alert>
    @endif

    <div class="card card-orange card-outline shadow">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <a href="{{ route('pelanggan.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus mr-2"></i> 
                        <span class="d-none d-sm-inline">Tambah Pelanggan</span>
                        <span class="d-sm-none">Tambah</span>
                    </a>
                </div>
                
                <div>
                    <form action="?" method="get" class="d-flex">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search"
                                value="<?= request()->search ?>" placeholder="Cari nama pelanggan...">
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
                            <th width="25%">Nama</th>
                            <th width="20%">Nomor Tlp</th>
                            <th width="35%">Alamat</th>
                            <th width="12%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pelanggans as $key => $pelanggan)
                            <tr>
                                <td>{{ $pelanggans->firstItem() + $key }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle text-primary mr-2"></i>
                                        <span class="font-weight-semibold">{{ $pelanggan->nama }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-phone text-success mr-2"></i>
                                        <span>{{ $pelanggan->nomor_tlp }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-map-marker-alt text-warning mr-2"></i>
                                        <span class="text-truncate" style="max-width: 200px;" title="{{ $pelanggan->alamat }}">
                                            {{ $pelanggan->alamat }}
                                        </span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('pelanggan.edit', ['pelanggan' => $pelanggan->id]) }}"
                                            class="btn btn-sm btn-outline-success"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" data-toggle="modal" data-target="#modalDelete"
                                            data-url="{{ route('pelanggan.destroy', ['pelanggan' => $pelanggan->id]) }}"
                                            class="btn btn-sm btn-outline-danger btn-delete"
                                            title="Hapus">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-users fa-3x mb-3"></i>
                                        <h5>Belum Ada Pelanggan</h5>
                                        <p>Silakan tambah pelanggan terlebih dahulu</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile/Tablet Cards -->
            <div class="d-lg-none">
                @forelse ($pelanggans as $key => $pelanggan)
                    <div class="card border-0 border-bottom rounded-0">
                        <div class="card-body py-3">
                            <div class="row align-items-center">
                                <div class="col-12">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="badge badge-secondary mr-2">{{ $pelanggans->firstItem() + $key }}</span>
                                                <i class="fas fa-user-circle text-primary mr-2"></i>
                                                <h6 class="mb-0 font-weight-semibold">{{ $pelanggan->nama }}</h6>
                                            </div>
                                        </div>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('pelanggan.edit', ['pelanggan' => $pelanggan->id]) }}"
                                                class="btn btn-sm btn-outline-success">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" data-toggle="modal" data-target="#modalDelete"
                                                data-url="{{ route('pelanggan.destroy', ['pelanggan' => $pelanggan->id]) }}"
                                                class="btn btn-sm btn-outline-danger btn-delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="text-sm">
                                        <div class="mb-1">
                                            <i class="fas fa-phone text-success mr-1"></i>
                                            <strong>Telepon:</strong> {{ $pelanggan->nomor_tlp }}
                                        </div>
                                        <div class="mb-1">
                                            <i class="fas fa-map-marker-alt text-warning mr-1"></i>
                                            <strong>Alamat:</strong><br>
                                            <small class="text-muted">{{ $pelanggan->alamat }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-users fa-3x mb-3"></i>
                            <h5>Belum Ada Pelanggan</h5>
                            <p>Silakan tambah pelanggan terlebih dahulu</p>
                            <a href="{{ route('pelanggan.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Pelanggan Pertama
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        @if($pelanggans->hasPages())
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small d-none d-md-block">
                    Menampilkan {{ $pelanggans->firstItem() }} sampai {{ $pelanggans->lastItem() }} 
                    dari {{ $pelanggans->total() }} data
                </div>
                <div class="ml-auto">
                    {{ $pelanggans->links('vendor.pagination.bootstrap-4') }}
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
    
    .btn-group .btn {
        border-radius: 0.25rem !important;
        margin-left: 2px;
    }
    
    .btn-group .btn:first-child {
        margin-left: 0;
    }
    
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection

@push('modals')
    <x-modal-delete />
@endpush