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
                @foreach ($produks as $produk)
                    <div class="card mb-2">
                        <div class="card-body">
                            <h5 class="card-title">{{ $produk->nama_produk }}</h5>
                            <p class="card-text">
                                <strong>Harga Modal:</strong> {{ number_format($produk->harga_modal, 0, ',', '.') }}<br>
                                <strong>Harga Jual:</strong> {{ number_format($produk->harga_jual, 0, ',', '.') }}<br>
                                <strong>Stok:</strong> {{ $produk->stok }}
                            </p>
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
                                <th>Harga Modal</th>
                                <th>Harga Jual</th>
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
                                    <td>{{ number_format($produk->harga_modal, 0, ',', '.') }}</td>
                                    <td>{{ number_format($produk->harga_jual, 0, ',', '.') }}</td>
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