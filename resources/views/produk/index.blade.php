@extends('layouts.main', ['title' => 'Produk'])

@section('title-content')
    <i class="fas fa-box-open mr-2"></i>
    Produk
@endsection

@section('content')
    @if (session('store') == 'success')
        <x-alert type="success">
            <strong>Berhasil dibuat!</strong> Produk berhasil dibuat.
        </x-alert>
    @endif

    @if (session('update') == 'success')
        <x-alert type="success">
            <strong>Berhasil diupdate!</strong> Produk berhasil diupdate.
        </x-alert>
    @endif

    @if (session('destroy') == 'success')
        <x-alert type="success">
            <strong>Berhasil dihapus!</strong> Produk berhasil dihapus.
        </x-alert>
    @endif

    <div class="card card-orange card-outline">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-2 mb-md-0">
                    <a href="{{ route('produk.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-2"></i> Tambah
                    </a>
                </div>
                <form action="?" method="get" class="w-100 w-md-auto">
                    <div class="input-group" style="max-width: 300px;">
                        <input type="text" class="form-control form-control-sm" name="search" value="{{ request()->search }}" placeholder="Kode, Nama Produk">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card-body p-0">
            <!-- Mobile view -->
            <div class="d-block d-lg-none">
                @foreach ($produks as $key => $produk)
                    <div class="card m-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="card-title mb-1 text-primary">{{ $produk->nama_produk }}</h6>
                                    <small class="text-muted">{{ $produk->kode_produk }}</small>
                                </div>
                                <div class="text-right">
                                    <a href="{{ route('produk.edit', ['produk' => $produk->id]) }}" class="btn btn-xs text-success p-1 mr-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" data-toggle="modal" data-target="#modalDelete"
                                            data-url="{{ route('produk.destroy', ['produk' => $produk->id]) }}"
                                            class="btn btn-xs text-danger p-1 btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-6">
                                    <small class="text-muted">Kategori:</small>
                                    <div class="font-weight-bold">{{ $produk->nama_kategori }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Harga:</small>
                                    <div class="font-weight-bold text-success">{{ $produk->harga }}</div>
                                </div>
                            </div>
                            
                            <div class="row mb-2">
                                <div class="col-6">
                                    <small class="text-muted">Stok:</small>
                                    <div class="font-weight-bold">{{ $produk->stok }}</div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">QR Code:</small>
                                    <div class="mt-1">
                                        <a href="{{ route('produk.qr.download', $produk->kode_produk) }}" class="btn btn-xs btn-outline-primary">
                                            <i class="fas fa-download mr-1"></i>Download
                                        </a>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- QR Code Preview for Mobile -->
                            <div class="text-center mt-2">
                                <div class="d-inline-block p-2 bg-white border rounded">
                                    {!! QrCode::format('svg')->size(60)->generate($produk->kode_produk) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop view -->
            <div class="d-none d-lg-block">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kode</th>
                                <th>QR CODE</th>
                                <th>Nama Produk</th>
                                <th>Kategori</th>
                                <th>Harga</th>
                                <th>Stok</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($produks as $key => $produk)
                                <tr>
                                    <td>{{ $produks->firstItem() + $key }}</td>
                                    <td>{{ $produk->kode_produk }}</td>
                                    <td class="text-center">
                                        {!! QrCode::format('svg')->size(70)->generate($produk->kode_produk) !!}
                                        <br>
                                        <a href="{{ route('produk.qr.download', $produk->kode_produk) }}" class="btn btn-sm btn-outline-primary mt-1">
                                            Download
                                        </a>
                                    </td>
                                    <td>{{ $produk->nama_produk }}</td>
                                    <td>{{ $produk->nama_kategori }}</td>
                                    <td>{{ $produk->harga }}</td>
                                    <td>{{ $produk->stok }}</td>
                                    <td class="text-right">
                                        <a href="{{ route('produk.edit', ['produk' => $produk->id]) }}"
                                           class="btn btn-xs text-success p-0 mr-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" data-toggle="modal" data-target="#modalDelete"
                                                data-url="{{ route('produk.destroy', ['produk' => $produk->id]) }}"
                                                class="btn btn-xs text-danger p-0 btn-delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="card-footer">
            {{ $produks->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>

    <style>
        @media (max-width: 991.98px) {
            .w-100 {
                width: 100% !important;
            }
            
            .input-group {
                max-width: 100% !important;
            }
            
            .card-body .card {
                border-left: 4px solid #fd7e14;
            }
            
            .btn-xs {
                padding: 0.25rem 0.5rem;
                font-size: 0.75rem;
            }
        }

        @media (min-width: 992px) {
            .w-md-auto {
                width: auto !important;
            }
        }

        /* Table responsive improvements */
        .table-responsive {
            border: none;
        }

        .table td, .table th {
            vertical-align: middle;
        }

        .table td:nth-child(3) {
            white-space: nowrap;
        }

        /* Mobile card styling */
        .card .card {
            border: 1px solid #dee2e6;
            border-radius: 0.5rem;
        }

        .card .card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            transition: box-shadow 0.3s ease;
        }

        /* QR Code styling */
        .card .card svg {
            max-width: 100%;
            height: auto;
        }

        /* Responsive text */
        @media (max-width: 575.98px) {
            .card-title {
                font-size: 1rem;
            }
            
            .font-weight-bold {
                font-size: 0.9rem;
            }
        }
    </style>
@endsection

@push('modals')
    <x-modal-delete />
@endpush