@extends('layouts.main', ['title' => 'Stok'])

@section('title-content')
    <i class="fas fa-pallet mr-2"></i>
    Stok
@endsection

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                <i class="fas fa-plus-circle mr-2"></i>
                Tambah Stok Barang
            </h4>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('stok.store') }}">
                @csrf
                <div id="produkList">
                    <div class="produk-item border rounded p-3 mb-3">
                        <div class="form-row align-items-center">
                            <div class="col-md-6 mb-3">
                                <label for="namaProduk" class="font-weight-bold">Nama Produk</label>
                                <div class="input-group">
                                    <input type="text" class="form-control nama-produk" placeholder="Pilih produk..." disabled>
                                    <input type="hidden" name="produk_id[]" class="produk-id">
                                    <div class="input-group-append">
                                        <button type="button" class="btn btn-primary btn-cari-produk" data-toggle="modal" data-target="#modalCari">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="jumlah" class="font-weight-bold">Jumlah</label>
                                <input type="number" name="jumlah[]" class="form-control" placeholder="Masukkan jumlah stok">
                            </div>
                            <div class="col-md-2 text-right">
                                <button type="button" class="btn btn-danger btn-remove-produk mt-4">Hapus</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="addProduk" class="btn btn-success mb-3">
                    <i class="fas fa-plus mr-1"></i> Tambah Produk
                </button>

                <div class="form-group">
                    <label for="namaSuplier" class="font-weight-bold">Nama Suplier</label>
                    <input type="text" name="nama_suplier" class="form-control" placeholder="Masukkan nama suplier">
                </div>

                <div class="text-right">
                    <a href="{{ route('stok.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left mr-1"></i> Batal
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save mr-1"></i> Simpan Stok
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .card {
        border-radius: 1rem;
    }

    .btn {
        border-radius: 0.5rem;
    }

    .form-control {
        border-radius: 0.5rem;
    }

    .produk-item {
        background-color: #f8f9fa;
    }

    .btn-remove-produk {
        background-color: #dc3545;
        color: white;
    }

    .btn-remove-produk:hover {
        background-color: #c82333;
    }
</style>
@endsection

@push('modals')
<div class="modal fade" id="modalCari" data-backdrop="static" data-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-search mr-2"></i>
                    Cari Produk
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body p-4">
                <form id="formSearch" action="" method="get" class="mb-3">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-lg" id="search" 
                               placeholder="Ketik minimal 3 karakter untuk mencari...">
                        <div class="input-group-append">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-sm table-striped table-hover">
                        <thead class="bg-light">
                            <tr>
                                <th width="10%">#</th>
                                <th width="70%">Nama Produk</th>
                                <th width="20%" class="text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="resultProduk">
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    <i class="fas fa-search fa-2x mb-2"></i><br>
                                    Ketik minimal 3 karakter untuk mencari produk
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    let produkTarget = null;

    $(function () {
        $('#formSearch').submit(function (e) {
            e.preventDefault();
            let search = $(this).find('#search').val();
            if (search.length >= 3) {
                fetchProduk(search);
            } else {
                $('#resultProduk').html(`
                    <tr>
                        <td colspan="3" class="text-center text-warning py-4">
                            <i class="fas fa-exclamation-triangle fa-2x mb-2"></i><br>
                            Minimal 3 karakter untuk mencari
                        </td>
                    </tr>
                `);
            }
        });

        // Real-time search
        $('#search').on('keyup', function() {
            let search = $(this).val();
            if (search.length >= 3) {
                fetchProduk(search);
            } else if (search.length === 0) {
                $('#resultProduk').html(`
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            <i class="fas fa-search fa-2x mb-2"></i><br>
                            Ketik minimal 3 karakter untuk mencari produk
                        </td>
                    </tr>
                `);
            }
        });

        $(document).on('click', '#addProduk', function() {
            let newProduk = $('.produk-item:first').clone();
            newProduk.find('input').val('');
            $('#produkList').append(newProduk);
        });

        $(document).on('click', '.btn-remove-produk', function() {
            if ($('.produk-item').length > 1) {
                $(this).closest('.produk-item').remove();
            } else {
                alert('Minimal harus ada satu produk.');
            }
        });

        // Simpan baris aktif saat klik cari produk
        $(document).on('click', '.btn-cari-produk', function() {
            produkTarget = $(this).closest('.produk-item');
        });
    });

    function fetchProduk(search) {
        $('#resultProduk').html(`
            <tr>
                <td colspan="3" class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x mb-2"></i><br>
                    Mencari produk...
                </td>
            </tr>
        `);

        let url = "{{ route('stok.produk') }}?search=" + search;
        $.getJSON(url, function(result) {
            $('#resultProduk').html('');
            if (result.length > 0) {
                result.forEach((produk, index) => {
                    let row = '<tr>';
                    row += `<td>${ index + 1 }</td>`;
                    row += `<td>${produk.nama_produk}</td>`;
                    row += `<td class="text-center">`;
                    row += `<button type="button" class="btn btn-sm btn-success" onclick="addProduk(${produk.id},'${produk.nama_produk}')">`;
                    row += `<i class="fas fa-plus mr-1"></i>Pilih`;
                    row += `</button>`;
                    row += '</td>';
                    row += '</tr>';
                    $('#resultProduk').append(row);
                });
            } else {
                $('#resultProduk').html(`
                    <tr>
                        <td colspan="3" class="text-center text-muted py-4">
                            <i class="fas fa-box-open fa-2x mb-2"></i><br>
                            Tidak ada produk ditemukan
                        </td>
                    </tr>
                `);
            }
        }).fail(function() {
            $('#resultProduk').html(`
                <tr>
                    <td colspan="3" class="text-center text-danger py-4">
                        <i class="fas fa-exclamation-circle fa-2x mb-2"></i><br>
                        Gagal memuat data produk
                    </td>
                </tr>
            `);
        });
    }

    function addProduk(id, nama_produk) {
        if (produkTarget) {
            produkTarget.find('.nama-produk').val(nama_produk);
            produkTarget.find('.produk-id').val(id);
        }
        $('#modalCari').modal('hide');
    }
</script>
@endpush