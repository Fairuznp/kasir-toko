@extends('layouts.main', ['title' => 'Kategori'])

@section('title-content')
    <i class="fas fa-list mr-2"></i>
    Kategori
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-md-8 col-lg-6 col-xl-4">
        <form method="POST" class="card card-orange card-outline shadow"
            action="{{ route('kategori.update', ['kategori' => $kategori->id]) }}">
            <div class="card-header bg-white border-bottom">
                <h3 class="card-title mb-0">
                    <i class="fas fa-edit mr-2 text-orange"></i>
                    Ubah Kategori
                </h3>
            </div>
            
            <div class="card-body p-3 p-md-4">
                @csrf
                @method('PUT')
                
                <div class="form-group">
                    <label class="font-weight-semibold text-dark">
                        <i class="fas fa-tag text-info mr-1"></i>
                        Nama Kategori
                        <span class="text-danger">*</span>
                    </label>
                    <x-input name="nama_kategori" type="text" :value="$kategori->nama_kategori" 
                             class="form-control-lg" placeholder="Masukkan nama kategori" />
                </div>
            </div>
            
            <div class="card-footer bg-light border-top">
                <div class="d-flex flex-column flex-md-row justify-content-between">
                    <div class="mb-2 mb-md-0">
                        <a href="{{ route('kategori.index') }}" class="btn btn-secondary btn-block btn-md-inline">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Batal
                        </a>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-warning btn-block btn-md-inline px-4">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Update Kategori
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