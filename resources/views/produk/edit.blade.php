@extends('layouts.main', ['title' => 'Produk'])

@section('title-content')
    <i class="fas fa-box-open mr-2"></i>
    Produk
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6 col-xl-4">
        <form method="POST" class="card card-orange card-outline shadow"
              action="{{ route('produk.update', ['produk' => $produk->id]) }}">
            <div class="card-header bg-white border-bottom">
                <h3 class="card-title mb-0">
                    <i class="fas fa-edit mr-2 text-orange"></i>
                    Ubah Produk
                </h3>
                <div class="card-tools">
                    <span class="badge badge-warning">Edit Mode</span>
                </div>
            </div>

            <div class="card-body p-3 p-md-4">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="font-weight-semibold text-dark">
                        <i class="fas fa-barcode text-info mr-1"></i>
                        Kode Produk
                        <span class="text-danger">*</span>
                    </label>
                    <x-input name="kode_produk" type="text" :value="$produk->kode_produk" class="form-control-lg" />
                </div>

                <div class="form-group">
                    <label class="font-weight-semibold text-dark">
                        <i class="fas fa-tag text-success mr-1"></i>
                        Nama Produk
                        <span class="text-danger">*</span>
                    </label>
                    <x-input name="nama_produk" type="text" :value="$produk->nama_produk" class="form-control-lg" />
                </div>

                <div class="form-group">
                    <label class="font-weight-semibold text-dark">
                        <i class="fas fa-money-bill-wave text-warning mr-1"></i>
                        Harga Produk
                        <span class="text-danger">*</span>
                    </label>
                    <x-input name="harga" type="text" :value="$produk->harga" class="form-control-lg" />
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Masukkan harga dalam format Rupiah
                    </small>
                </div>

                <div class="form-group">
                    <label class="font-weight-semibold text-dark">
                        <i class="fas fa-layer-group text-secondary mr-1"></i>
                        Kategori
                        <span class="text-danger">*</span>
                    </label>
                    <x-select name="kategori_id" :options="$kategoris" :value="$produk->kategori_id" class="form-control-lg" />
                </div>
            </div>

            <div class="card-footer bg-light border-top">
                <div class="d-flex flex-column flex-md-row justify-content-between">
                    <div class="mb-2 mb-md-0">
                        <a href="{{ route('produk.index') }}" class="btn btn-secondary btn-block btn-md-inline">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Batal
                        </a>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-primary btn-block btn-md-inline px-4">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Update Produk
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
    
    .badge-warning {
        background-color: #ffc107;
        color: #212529;
    }
</style>
@endsection