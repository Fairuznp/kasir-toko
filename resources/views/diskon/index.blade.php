@extends('layouts.main', ['title' => 'Diskon'])

@section('title-content')
    <i class="fas fa-tags mr-2"></i>
    Diskon
@endsection

@section('content')
    @if (session('store') == 'success')
        <x-alert type="success">
            <strong>Berhasil dibuat!</strong> Diskon berhasil dibuat.
        </x-alert>
    @endif

    @if (session('update') == 'success')
        <x-alert type="success">
            <strong>Berhasil diupdate!</strong> Diskon berhasil diupdate.
        </x-alert>
    @endif

    @if (session('destroy') == 'success')
        <x-alert type="success">
            <strong>Berhasil dihapus!</strong> Diskon berhasil dihapus.
        </x-alert>
    @endif

    <div class="card card-orange card-outline">
        <div class="card-header">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                <div class="mb-2 mb-md-0">
                    <a href="{{ route('diskon.create') }}" class="btn btn-primary btn-sm">
                        <i class="fas fa-plus mr-2"></i> Tambah
                    </a>
                </div>
                <form action="?" method="get" class="w-100 w-md-auto">
                    <div class="input-group" style="max-width: 300px;">
                        <input type="text" class="form-control form-control-sm" name="search" value="{{ request()->search }}" placeholder="Kode Diskon">
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
            <div class="d-block d-md-none">
                @foreach ($diskons as $key => $diskon)
                    <div class="card m-3 shadow-sm">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="card-title mb-0 text-primary">{{ $diskon->kode_diskon }}</h6>
                                <div class="text-right">
                                    <a href="{{ route('diskon.edit', $diskon) }}" class="btn btn-xs text-success p-1 mr-1">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" data-toggle="modal" data-target="#modalDelete"
                                            data-url="{{ route('diskon.destroy', $diskon) }}"
                                            class="btn btn-xs text-danger p-1 btn-delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-6">
                                    <small class="text-muted">Jenis & Nilai:</small>
                                    <div class="font-weight-bold">
                                        @if($diskon->jenis_diskon == 'persen')
                                            {{ $diskon->jumlah_diskon }}%
                                        @else
                                            Rp {{ number_format($diskon->jumlah_diskon) }}
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <small class="text-muted">Status:</small>
                                    <div>
                                        @if($diskon->status)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Tidak Aktif</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-2">
                                <small class="text-muted">Berlaku Untuk:</small>
                                <div class="font-weight-bold">
                                    @if($diskon->kategori_id)
                                        Kategori: {{ $diskon->kategori->nama_kategori }}
                                    @elseif($diskon->produk_id)
                                        Produk: {{ $diskon->produk->nama_produk }}
                                    @else
                                        Semua Produk
                                    @endif
                                </div>
                            </div>
                            
                            <div class="mt-2">
                                <small class="text-muted">Min. Pembelian:</small>
                                <div class="font-weight-bold">Rp {{ number_format($diskon->minimal_pembelian) }}</div>
                            </div>
                            
                            <div class="mt-2">
                                <small class="text-muted">Periode:</small>
                                <div class="font-weight-bold">
                                    {{ $diskon->tanggal_mulai->format('d/m/Y') }} - 
                                    {{ $diskon->tanggal_selesai->format('d/m/Y') }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Desktop view -->
            <div class="d-none d-md-block">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Kode Diskon</th>
                                <th>Jenis & Nilai</th>
                                <th>Berlaku Untuk</th>
                                <th>Min. Pembelian</th>
                                <th>Periode</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($diskons as $key => $diskon)
                                <tr>
                                    <td>{{ $diskons->firstItem() + $key }}</td>
                                    <td>{{ $diskon->kode_diskon }}</td>
                                    <td>
                                        @if($diskon->jenis_diskon == 'persen')
                                            {{ $diskon->jumlah_diskon }}%
                                        @else
                                            Rp {{ number_format($diskon->jumlah_diskon) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if($diskon->kategori_id)
                                            Kategori: {{ $diskon->kategori->nama_kategori }}
                                        @elseif($diskon->produk_id)
                                            Produk: {{ $diskon->produk->nama_produk }}
                                        @else
                                            Semua Produk
                                        @endif
                                    </td>
                                    <td>Rp {{ number_format($diskon->minimal_pembelian) }}</td>
                                    <td>
                                        {{ $diskon->tanggal_mulai->format('d/m/Y') }} - 
                                        {{ $diskon->tanggal_selesai->format('d/m/Y') }}
                                    </td>
                                    <td>
                                        @if($diskon->status)
                                            <span class="badge badge-success">Aktif</span>
                                        @else
                                            <span class="badge badge-danger">Tidak Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('diskon.edit', $diskon) }}"
                                           class="btn btn-xs text-success p-0 mr-1">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" data-toggle="modal" data-target="#modalDelete"
                                                data-url="{{ route('diskon.destroy', $diskon) }}"
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
            {{ $diskons->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>

    <style>
        @media (max-width: 767.98px) {
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

        @media (min-width: 768px) {
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
    </style>
@endsection

@push('modals')
    <x-modal-delete />
@endpush