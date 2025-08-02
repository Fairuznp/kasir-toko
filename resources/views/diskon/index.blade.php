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
        <div class="card-header form-inline">
            <a href="{{ route('diskon.create') }}" class="btn btn-primary">
                <i class="fas fa-plus mr-2"></i> Tambah
            </a>
            <form action="?" method="get" class="ml-auto">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" value="{{ request()->search }}" placeholder="Kode Diskon">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-body p-0">
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

        <div class="card-footer">
            {{ $diskons->links('vendor.pagination.bootstrap-4') }}
        </div>
    </div>
@endsection

@push('modals')
    <x-modal-delete />
@endpush