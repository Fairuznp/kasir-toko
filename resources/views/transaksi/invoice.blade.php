@extends('layouts.main', ['title' => 'Invoice'])

@section('title-content')
    <i class="fas fa-file-invoice mr-2"></i>
    Invoice
@endsection

@section('content')
    @if (session('destroy') == 'success')
        <x-alert type="success">
            <strong>Berhasil dibatalkan!</strong> Transaksi berhasil dibatalkan.
        </x-alert>
    @endif

    <div class="card card-orange card-outline">
        <div class="card-header">
            <div class="row">
                <div class="col-12 col-lg-6 mb-3 mb-lg-0">
                    <p class="mb-1"><strong>No. Transaksi:</strong> {{ $penjualan->nomor_transaksi }}</p>
                    <p class="mb-1"><strong>Nama Pelanggan:</strong> {{ $pelanggan->nama }}</p>
                    <p class="mb-1 d-none d-sm-block"><strong>No. Telepon:</strong> {{ $pelanggan->nomor_tlp }}</p>
                    <p class="mb-0 d-none d-md-block"><strong>Alamat:</strong> {{ $pelanggan->alamat }}</p>
                </div>
                <div class="col-12 col-lg-6">
                    <p class="mb-1"><strong>Tgl. Transaksi:</strong> {{ date('d/m/Y H:i:s', strtotime($penjualan->tanggal)) }}</p>
                    <p class="mb-1"><strong>Kasir:</strong> {{ $user->nama }}</p>
                    <p class="mb-0">
                        <strong>Status:</strong>
                        @if ($penjualan->status == 'selesai')
                            <span class="badge badge-success">Selesai</span>
                        @endif
                        @if ($penjualan->status == 'batal')
                            <span class="badge badge-danger">Dibatalkan</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th class="d-none d-md-table-cell">#</th>
                            <th>Nama Produk</th>
                            <th>Qty</th>
                            <th class="d-none d-sm-table-cell">Harga</th>
                            <th>Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($detilPenjualan as $key => $item)
                            <tr>
                                <td class="d-none d-md-table-cell">{{ $key + 1 }}</td>
                                <td>
                                    <div>{{ $item->nama_produk }}</div>
                                    <small class="text-muted d-sm-none">
                                        {{ number_format($item->harga_produk, 0, ',', '.') }} x {{ $item->jumlah }}
                                    </small>
                                </td>
                                <td>{{ $item->jumlah }}</td>
                                <td class="d-none d-sm-table-cell">{{ number_format($item->harga_produk, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->subtotal, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-12 col-md-6 offset-md-6">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <tr>
                                <td>Sub Total:</td>
                                <td class="text-right">{{ number_format($penjualan->subtotal, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>Pajak 10%:</td>
                                <td class="text-right">{{ number_format($penjualan->pajak, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>Diskon:</td>
                                <td class="text-right">- {{ number_format($penjualan->nilai_diskon, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="font-weight-bold">
                                <td>Total:</td>
                                <td class="text-right">{{ number_format($penjualan->total, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>Cash:</td>
                                <td class="text-right">{{ number_format($penjualan->tunai, 0, ',', '.') }}</td>
                            </tr>
                            <tr>
                                <td>Kembalian:</td>
                                <td class="text-right">{{ number_format($penjualan->kembalian, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="row">
                <div class="col-12 col-md-auto mb-2 mb-md-0">
                    <a href="{{ route('transaksi.index') }}" class="btn btn-secondary btn-block btn-md-auto">
                        <i class="fas fa-arrow-left mr-2"></i>Ke Transaksi
                    </a>
                </div>
                <div class="col-12 col-md-auto ml-md-auto">
                    <div class="btn-group w-100" role="group">
                        @if ($penjualan->status == 'selesai')
                            <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#modalBatal">
                                <i class="fas fa-times mr-2 d-none d-sm-inline"></i>Dibatalkan
                            </button>
                        @endif
                        <a target="_blank" href="{{ route('transaksi.cetak', ['transaksi' => $penjualan->id]) }}"
                            class="btn btn-primary">
                            <i class="fas fa-print mr-2"></i>Cetak
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="modalBatal" tabindex="-1">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Dibatalkan</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body text-center">
                    <p>Apakah yakin akan dibatalkan?</p>
                    <form action="{{ route('transaksi.destroy', ['transaksi' => $penjualan->id]) }}"
                        method="post" style="display: none;" id="formBatal">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="yesBatal">Ya, Batal!</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        $(function(){
            $('#yesBatal').click(function(){
                $('#formBatal').submit();
            });
        })
    </script>
@endpush

<style>
@media (max-width: 576px) {
    .card-header p {
        font-size: 0.875rem;
        margin-bottom: 0.5rem;
    }
    
    .table td, .table th {
        padding: 0.5rem 0.25rem;
        font-size: 0.875rem;
    }
    
    .btn-group {
        display: flex;
        flex-direction: column;
    }
    
    .btn-group .btn {
        border-radius: 0.25rem !important;
        margin-bottom: 0.5rem;
    }
    
    .btn-group .btn:last-child {
        margin-bottom: 0;
    }
}

@media (max-width: 768px) {
    .btn-block {
        display: block;
        width: 100%;
    }
    
    .btn-md-auto {
        display: inline-block;
        width: auto;
    }
}

@media (min-width: 768px) {
    .btn-block {
        display: inline-block;
        width: auto;
    }
    
    .btn-group {
        display: inline-flex;
        flex-direction: row;
    }
    
    .btn-group .btn {
        margin-bottom: 0;
    }
}

@media (min-width: 992px) {
    .table-sm td, .table-sm th {
        padding: 0.3rem;
    }
}
</style>