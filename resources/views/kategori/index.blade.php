@extends('layouts.main', ['title' => 'Kategori'])

@section('title-content')
    <i class="fas fa-list mr-2"></i>
    Kategori
@endsection

@section('content')
    @if (session('store') == 'success')
        <x-alert type="success">
            <strong>Berhasil dibuat!</strong> Kategori berhasil dibuat.
        </x-alert>
    @endif
    @if (session('update') == 'success')
        <x-alert type="success">
            <strong>Berhasil diupdate!</strong> Kategori berhasil diupdate.
        </x-alert>
    @endif
    @if (session('destroy') == 'success')
        <x-alert type="success">
            <strong>Berhasil dihapus!</strong> Kategori berhasil dihapus dan semua produk dipindahkan ke kategori "Tidak Berkategori".
        </x-alert>
    @endif
    @if (session('error'))
        <x-alert type="danger">
            <strong>Error!</strong> {{ session('error') }}
        </x-alert>
    @endif

    <div class="card card-orange card-outline shadow">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
                <div class="mb-3 mb-md-0">
                    <a href="{{ route('kategori.create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus mr-2"></i> 
                        <span class="d-none d-sm-inline">Tambah Kategori</span>
                        <span class="d-sm-none">Tambah</span>
                    </a>
                </div>

                <div>
                    <form action="?" method="get" class="d-flex">
                        <div class="input-group">
                            <input type="text" class="form-control" name="search" value="<?= request()->search ?>"
                                placeholder="Cari kategori...">
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
            <div class="d-none d-md-block">
                <table class="table table-striped table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="10%">#</th>
                            <th width="50%">Nama Kategori</th>
                            <th width="20%" class="text-center">Jumlah Produk</th>
                            <th width="20%" class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($kategoris as $key => $kategori)
                            <tr>
                                <td>{{ $kategoris->firstItem() + $key }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-tag text-primary mr-2"></i>
                                        {{ $kategori->nama_kategori }}
                                        @if($kategori->nama_kategori === 'Tidak Berkategori')
                                            <span class="badge badge-secondary ml-2">Default</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-info">{{ $kategori->produks_count }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('kategori.edit', ['kategori' => $kategori->id]) }}"
                                            class="btn btn-sm btn-outline-success"
                                            title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if($kategori->nama_kategori !== 'Tidak Berkategori')
                                            <button type="button" data-toggle="modal" data-target="#modalDelete"
                                                data-url="{{ route('kategori.destroy', ['kategori' => $kategori->id]) }}"
                                                data-produk-count="{{ $kategori->produks_count }}"
                                                data-kategori-name="{{ $kategori->nama_kategori }}"
                                                class="btn btn-sm btn-outline-danger btn-delete"
                                                title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        @else
                                            <button type="button" 
                                                class="btn btn-sm btn-outline-secondary" 
                                                disabled
                                                title="Kategori default tidak dapat dihapus">
                                                <i class="fas fa-lock"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-list fa-3x mb-3"></i>
                                        <h5>Belum Ada Kategori</h5>
                                        <p>Silakan tambah kategori terlebih dahulu</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="d-md-none">
                @forelse ($kategoris as $key => $kategori)
                    <div class="card border-0 border-bottom rounded-0">
                        <div class="card-body py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="flex-grow-1">
                                    <div class="d-flex align-items-center mb-1">
                                        <span class="badge badge-secondary mr-2">{{ $kategoris->firstItem() + $key }}</span>
                                        <i class="fas fa-tag text-primary mr-2"></i>
                                        <h6 class="mb-0 font-weight-semibold">{{ $kategori->nama_kategori }}</h6>
                                    </div>
                                </div>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('kategori.edit', ['kategori' => $kategori->id]) }}"
                                        class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" data-toggle="modal" data-target="#modalDelete"
                                        data-url="{{ route('kategori.destroy', ['kategori' => $kategori->id]) }}"
                                        class="btn btn-sm btn-outline-danger btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <div class="text-muted">
                            <i class="fas fa-list fa-3x mb-3"></i>
                            <h5>Belum Ada Kategori</h5>
                            <p>Silakan tambah kategori terlebih dahulu</p>
                            <a href="{{ route('kategori.create') }}" class="btn btn-primary mt-3">
                                <i class="fas fa-plus mr-2"></i>
                                Tambah Kategori Pertama
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        @if($kategoris->hasPages())
        <div class="card-footer bg-light">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small d-none d-md-block">
                    Menampilkan {{ $kategoris->firstItem() }} sampai {{ $kategoris->lastItem() }} 
                    dari {{ $kategoris->total() }} data
                </div>
                <div class="ml-auto">
                    {{ $kategoris->links('vendor.pagination.bootstrap-4') }}
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
</style>
@endsection

@push('modals')
    <x-modal-delete />
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    $('.btn-delete').on('click', function() {
        const url = $(this).data('url');
        const produkCount = $(this).data('produk-count');
        const kategoriName = $(this).data('kategori-name');
        
        $('#formDelete').attr('action', url);
        
        // Update modal message based on product count
        let message = `Yakin ingin menghapus kategori "${kategoriName}"?`;
        
        if (produkCount > 0) {
            message += `<br><br><div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i>
                <strong>Perhatian:</strong> Terdapat <strong>${produkCount} produk</strong> dalam kategori ini.
                Semua produk akan dipindahkan ke kategori <strong>"Tidak Berkategori"</strong>.
            </div>`;
        }
        
        $('#modalDelete .modal-body p').html(message);
    });
});
</script>
@endpush