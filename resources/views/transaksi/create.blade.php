@extends('layouts.main', ['title' => 'Transaksi'])

@section('title-content')
    <i class="fas fa-cash-register mr-2"></i>
    Transaksi
@endsection

@section('content')
 @if ($errors->has('stok'))
    <x-alert type="danger">
        <strong>Stok tidak mencukupi:</strong> {{ $errors->first('stok') }}
    </x-alert>
@endif

<div class="row">
    <div class="col-12 col-lg-4 mb-3 mb-lg-0">
        @include('transaksi.form-code')
        
        <div class="card card-orange card-outline card-tabs">
            <div class="card-header p-0 pt-1 border-bottom-0">
                <ul class="nav nav-tabs nav-fill" id="cari-tab" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active small" id="cari-produk-tab" data-toggle="pill"
                            href="#cari-produk" role="tab">
                            <i class="fas fa-box d-lg-none"></i>
                            <span class="d-none d-lg-inline">Cari Produk</span>
                            <span class="d-lg-none">Produk</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link small" id="cari-pelanggan-tab" data-toggle="pill"
                            href="#cari-pelanggan" role="tab">
                            <i class="fas fa-user d-lg-none"></i>
                            <span class="d-none d-lg-inline">Cari Pelanggan</span>
                            <span class="d-lg-none">Customer</span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="cari-tabContent">
                    <div class="tab-pane fade active show" id="cari-produk" role="tabpanel">
                        @include('transaksi.cari-produk')
                    </div>
                    <div class="tab-pane fade" id="cari-pelanggan" role="tabpanel">
                        @include('transaksi.cari-pelanggan')
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-lg-8">
        @include('transaksi.form-cart')
    </div>
</div>
@endsection

<style>
@media (max-width: 576px) {
    .nav-tabs .nav-link {
        padding: 0.5rem 0.25rem;
        font-size: 0.875rem;
    }
    
    .card-body {
        padding: 0.75rem;
    }
    
    .row {
        margin-left: -0.5rem;
        margin-right: -0.5rem;
    }
    
    .row > [class*="col-"] {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
}

@media (max-width: 768px) {
    .nav-tabs .nav-link {
        padding: 0.75rem 0.5rem;
    }
    
    .tab-content {
        padding-top: 0.5rem;
    }
}

@media (min-width: 992px) {
    .nav-tabs .nav-link {
        padding: 0.75rem 1rem;
    }
}

/* Mobile optimization for better touch targets */
@media (max-width: 768px) {
    .btn {
        min-height: 38px;
        padding: 0.5rem 0.75rem;
    }
    
    .btn-xs {
        min-height: 32px;
        padding: 0.25rem 0.5rem;
    }
    
    .form-control {
        min-height: 38px;
    }
    
    .input-group-append .btn {
        border-left: 1px solid #ced4da;
    }
}
</style>