@extends('layouts.main', ['title' => 'Tambah Diskon'])

@section('title-content')
    <i class="fas fa-tags mr-2"></i> Tambah Diskon
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <form action="{{ route('diskon.store') }}" method="POST" class="card card-orange card-outline shadow">
                @csrf
                
                <!-- Card Header -->
                <div class="card-header bg-white border-bottom">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                        <h3 class="card-title mb-2 mb-md-0">
                            <i class="fas fa-plus-circle mr-2"></i>Form Tambah Diskon Baru
                        </h3>
                        <div class="card-tools">
                            <span class="badge">Required Fields</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-3 p-md-4">
                    {{-- Baris 1: Kode & Jenis Diskon --}}
                    <div class="row mb-3 mb-md-4">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <div class="form-group">
                                <label class="font-weight-semibold text-dark">
                                    <i class="fas fa-code text-orange mr-1"></i>Kode Diskon 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="kode_diskon"
                                       class="form-control border-0 shadow-sm @error('kode_diskon') is-invalid @enderror"
                                       value="{{ old('kode_diskon') }}" 
                                       placeholder="Masukkan kode diskon, contoh: DISKON10"
                                       style="background-color: #f8f9fa;">
                                @error('kode_diskon')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="font-weight-semibold text-dark">
                                    <i class="fas fa-percentage text-success mr-1"></i>Jenis Diskon 
                                    <span class="text-danger">*</span>
                                </label>
                                <select name="jenis_diskon" class="form-control border-0 shadow-sm @error('jenis_diskon') is-invalid @enderror" style="background-color: #f8f9fa;">
                                    <option value="">-- Pilih Jenis Diskon --</option>
                                    <option value="persen" {{ old('jenis_diskon') == 'persen' ? 'selected' : '' }}>
                                        <i class="fas fa-percent"></i> Persentase (%)
                                    </option>
                                    <option value="nominal" {{ old('jenis_diskon') == 'nominal' ? 'selected' : '' }}>
                                        <i class="fas fa-money-bill"></i> Nominal (Rp)
                                    </option>
                                </select>
                                @error('jenis_diskon')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Baris 2: Jumlah & Minimal Pembelian --}}
                    <div class="row mb-3 mb-md-4">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <div class="form-group">
                                <label class="font-weight-semibold text-dark">
                                    <i class="fas fa-calculator text-warning mr-1"></i>Jumlah Diskon 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="number" name="jumlah_diskon"
                                       class="form-control border-0 shadow-sm @error('jumlah_diskon') is-invalid @enderror"
                                       value="{{ old('jumlah_diskon') }}" 
                                       placeholder="Contoh: 10 (untuk %) atau 50000 (untuk Rp)"
                                       style="background-color: #f8f9fa;">
                                @error('jumlah_diskon')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="font-weight-semibold text-dark">
                                    <i class="fas fa-coins text-info mr-1"></i>Minimal Pembelian
                                </label>
                                <input type="number" name="minimal_pembelian"
                                       class="form-control border-0 shadow-sm @error('minimal_pembelian') is-invalid @enderror"
                                       value="{{ old('minimal_pembelian', 0) }}" 
                                       placeholder="0 = Tanpa minimal pembelian"
                                       style="background-color: #f8f9fa;">
                                @error('minimal_pembelian')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                    </div>
                                @enderror
                                <small class="form-text text-muted">
                                    <i class="fas fa-info-circle mr-1"></i>Kosongkan atau isi 0 jika tidak ada minimal pembelian
                                </small>
                            </div>
                        </div>
                    </div>

                    {{-- Baris 3: Tanggal Mulai & Selesai --}}
                    <div class="row mb-3 mb-md-4">
                        <div class="col-12 col-md-6 mb-3 mb-md-0">
                            <div class="form-group">
                                <label class="font-weight-semibold text-dark">
                                    <i class="fas fa-calendar-plus text-success mr-1"></i>Tanggal Mulai 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" name="tanggal_mulai"
                                       class="form-control border-0 shadow-sm @error('tanggal_mulai') is-invalid @enderror"
                                       value="{{ old('tanggal_mulai') }}"
                                       style="background-color: #f8f9fa;">
                                @error('tanggal_mulai')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-12 col-md-6">
                            <div class="form-group">
                                <label class="font-weight-semibold text-dark">
                                    <i class="fas fa-calendar-times text-danger mr-1"></i>Tanggal Selesai 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="datetime-local" name="tanggal_selesai"
                                       class="form-control border-0 shadow-sm @error('tanggal_selesai') is-invalid @enderror"
                                       value="{{ old('tanggal_selesai') }}"
                                       style="background-color: #f8f9fa;">
                                @error('tanggal_selesai')
                                    <div class="invalid-feedback">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>{{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <hr class="my-3 my-md-4">

                    {{-- Baris 4: Berlaku Untuk --}}
                    <div class="card card-secondary card-outline">
                        <div class="card-header bg-light">
                            <h4 class="card-title mb-0 text-center text-md-left">
                                <i class="fas fa-bullseye text-secondary mr-2"></i>Pengaturan Target Diskon
                            </h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-md-4 mb-3 mb-md-0">
                                    <div class="form-group">
                                        <label class="font-weight-semibold text-dark">Berlaku Untuk</label>
                                        <select id="berlaku_untuk" class="form-control border-0 shadow-sm" style="background-color: #f8f9fa;">
                                            <option value="semua">
                                                <i class="fas fa-globe"></i> Semua Produk
                                            </option>
                                            <option value="kategori">
                                                <i class="fas fa-tags"></i> Kategori Tertentu
                                            </option>
                                            <option value="produk">
                                                <i class="fas fa-box"></i> Produk Tertentu
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 mb-3 mb-md-0" id="kategori_field" style="display: none;">
                                    <div class="form-group">
                                        <label class="font-weight-semibold text-dark">
                                            <i class="fas fa-tags text-info mr-1"></i>Pilih Kategori
                                        </label>
                                        <select name="kategori_id" class="form-control border-0 shadow-sm" style="background-color: #f8f9fa;">
                                            <option value="">-- Pilih Kategori --</option>
                                            @foreach($kategoris as $kategori)
                                                <option value="{{ $kategori->id }}">{{ $kategori->nama_kategori }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4" id="produk_field" style="display: none;">
                                    <div class="form-group">
                                        <label class="font-weight-semibold text-dark">
                                            <i class="fas fa-box text-orange mr-1"></i>Pilih Produk
                                        </label>
                                        <select name="produk_id" class="form-control border-0 shadow-sm" style="background-color: #f8f9fa;">
                                            <option value="">-- Pilih Produk --</option>
                                            @foreach($produks as $produk)
                                                <option value="{{ $produk->id }}">{{ $produk->nama_produk }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Footer --}}
                <div class="card-footer bg-light border-top">
                    <div class="d-flex flex-column flex-md-row justify-content-between">
                        <div class="mb-2 mb-md-0">
                            <a href="{{ route('diskon.index') }}" class="btn btn-secondary btn-block btn-md-inline">
                                <i class="fas fa-arrow-left mr-2"></i>Kembali
                            </a>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-success px-4 btn-block btn-md-inline">
                                <i class="fas fa-save mr-2"></i>Simpan Diskon
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Custom CSS untuk AdminLTE --}}
    <style>
        .form-control:focus {
            border-color: #fd7e14;
            box-shadow: 0 0 0 0.2rem rgba(253, 126, 20, 0.25);
        }
        
        .form-control-lg {
            border-radius: 0.375rem;
        }
        
        .card {
            border-radius: 1rem;
        }
        
        .card-header {
            border-radius: 1rem 1rem 0 0 !important;
        }
        
        .shadow {
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        }
        
        .shadow-sm {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075) !important;
        }
        
        .form-control:hover {
            background-color: #ffffff !important;
            transition: all 0.3s ease;
        }

        #kategori_field, #produk_field {
            transition: all 0.4s ease-in-out;
        }

        .btn:hover {
            transform: translateY(-1px);
            transition: all 0.3s ease;
        }

        /* Custom responsive classes */
        @media (max-width: 767.98px) {
            .btn-block {
                display: block;
                width: 100%;
            }
            
            .card-body {
                padding: 1rem !important;
            }
            
            .form-control {
                font-size: 16px; /* Prevent zoom on iOS */
            }
        }

        @media (min-width: 768px) {
            .btn-md-inline {
                display: inline-block;
                width: auto;
            }
        }
    </style>
@endsection

@push('scripts')
<script>
    $('#berlaku_untuk').change(function () {
        const value = $(this).val();

        $('#kategori_field, #produk_field').hide();
        $('select[name="kategori_id"], select[name="produk_id"]').val('');

        if (value === 'kategori') {
            $('#kategori_field').fadeIn(400);
        } else if (value === 'produk') {
            $('#produk_field').fadeIn(400);
        }
    });
</script>
@endpush