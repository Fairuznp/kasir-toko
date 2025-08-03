@extends('layouts.main', ['title' => 'Stok'])

@section('title-content')
    <i class="fas fa-pallet mr-2"></i>
    Stok
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6 col-xl-4">
        <form method="POST" action="{{ route('stok.store') }}" class="card card-orange card-outline shadow">
            <div class="card-header bg-white border-bottom">
                <h3 class="card-title mb-0">
                    <i class="fas fa-plus-circle mr-2 text-orange"></i>
                    Tambah Stok Barang
                </h3>
            </div>

            <div class="card-body p-3 p-md-4">
                @csrf
                
                <div class="form-group">
                    <label class="font-weight-semibold text-dark">
                        <i class="fas fa-box text-info mr-1"></i>
                        Nama Produk
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input type="text" id="namaProduk" name="nama_produk"
                            class="form-control form-control-lg @error('produk_id') is-invalid @enderror" 
                            placeholder="Pilih produk..." disabled>

                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#modalCari">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    @error('produk_id')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                    @enderror
                    <input type="hidden" name="produk_id" id="produkId">
                </div>

                <div class="form-group">
                    <label class="font-weight-semibold text-dark">
                        <i class="fas fa-sort-numeric-up text-success mr-1"></i>
                        Jumlah
                        <span class="text-danger">*</span>
                    </label>
                    <x-input name="jumlah" type="text" class="form-control-lg" placeholder="Masukkan jumlah stok" />
                </div>

                <div class="form-group">
                    <label class="font-weight-semibold text-dark">
                        <i class="fas fa-truck text-warning mr-1"></i>
                        Nama Suplier
                        <span class="text-danger">*</span>
                    </label>
                    <x-input name="nama_suplier" type="text" class="form-control-lg" placeholder="Masukkan nama suplier" />
                </div>
            </div>

            <div class="card-footer bg-light border-top">
                <div class="d-flex flex-column flex-md-row justify-content-between">
                    <div class="mb-2 mb-md-0">
                        <a href="{{ route('stok.index') }}" class="btn btn-secondary btn-block btn-md-inline">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Batal
                        </a>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary btn-block btn-md-inline px-4">
                            <i class="fas fa-save mr-2"></i>
                            Simpan Stok
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .form-control-lg {
        border-radius: 0.5rem;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    
    .form-control-lg:focus {
        border-color: #fd7e14;
        box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.25);
    }
    
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
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .card-body {
            padding: 1.5rem !important;
        }
        
        .form-control-lg {
            font-size: 16px; /* Prevent zoom on iOS */
        }
    }

    @media (min-width: 768px) {
        .btn-md-inline {
            display: inline-block;
            width: auto;
        }
    }
    
    .btn:hover {
        transform: translateY(-1px);
        transition: all 0.3s ease;
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
        $('#namaProduk').val(nama_produk);
        $('#produkId').val(id);
        $('#modalCari').modal('hide');
        
        // Show success feedback
        $('#namaProduk').addClass('border-success');
        setTimeout(() => {
            $('#namaProduk').removeClass('border-success');
        }, 2000);
    }
</script>
@endpush