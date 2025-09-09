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
                        <i class="fas fa-money-bill-wave text-primary mr-1"></i>
                        Harga Modal
                        <span class="text-danger">*</span>
                    </label>
                    <x-input name="harga_modal" type="text" :value="$produk->harga_modal" class="form-control-lg" id="harga_modal" />
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Masukkan harga modal dalam format Rupiah
                    </small>
                </div>

                <div class="form-group">
                    <label class="font-weight-semibold text-dark">
                        <i class="fas fa-cogs text-warning mr-1"></i>
                        Metode Penetapan Harga
                        <span class="text-danger">*</span>
                    </label>
                    <div class="row">
                        <div class="col-6">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="pricing_manual" name="pricing_type" value="manual" class="custom-control-input" 
                                       {{ ($produk->pricing_type ?? 'manual') === 'manual' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="pricing_manual">
                                    <i class="fas fa-edit mr-1"></i>Manual
                                </label>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="custom-control custom-radio">
                                <input type="radio" id="pricing_margin" name="pricing_type" value="margin" class="custom-control-input"
                                       {{ ($produk->pricing_type ?? 'manual') === 'margin' ? 'checked' : '' }}>
                                <label class="custom-control-label" for="pricing_margin">
                                    <i class="fas fa-percentage mr-1"></i>Margin
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group" id="margin_input" style="display: {{ ($produk->pricing_type ?? 'manual') === 'margin' ? 'block' : 'none' }};">
                    <label class="font-weight-semibold text-dark">
                        <i class="fas fa-percentage text-info mr-1"></i>
                        Margin Keuntungan (%)
                        <span class="text-danger">*</span>
                    </label>
                    <x-input name="margin_percentage" type="number" :value="$produk->margin_percentage" class="form-control-lg" id="margin_percentage" min="0" step="0.01" />
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        Masukkan persentase keuntungan (minimal 0%)
                    </small>
                    <!-- Preview Perhitungan -->
                    <div id="margin_preview" class="alert alert-info mt-2" style="display: none;">
                        <div class="d-flex justify-content-between">
                            <small>
                                <strong>Harga Modal:</strong> <span id="preview_modal">Rp 0</span><br>
                                <strong>Margin:</strong> <span id="preview_margin">0%</span><br>
                                <strong>Keuntungan:</strong> <span id="preview_profit">Rp 0</span><br>
                                <strong>Harga Jual:</strong> <span id="preview_jual">Rp 0</span>
                            </small>
                            <div class="text-center">
                                <i class="fas fa-calculator fa-2x text-info"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="font-weight-semibold text-dark">
                        <i class="fas fa-money-bill-wave text-success mr-1"></i>
                        Harga Jual
                        <span class="text-danger">*</span>
                    </label>
                    <x-input name="harga_jual" type="text" :value="$produk->harga_jual" class="form-control-lg" id="harga_jual" />
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle mr-1"></i>
                        <span id="harga_jual_note">Masukkan harga jual dalam format Rupiah</span>
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
    
    .form-control:disabled, 
    .form-control[readonly] {
        background-color: #f8f9fa !important;
        color: #6c757d !important;
        cursor: not-allowed !important;
        opacity: 0.8 !important;
        border-color: #dee2e6 !important;
    }
    
    .form-control:disabled:focus, 
    .form-control[readonly]:focus {
        background-color: #f8f9fa !important;
        border-color: #dee2e6 !important;
        box-shadow: none !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const pricingManual = document.getElementById('pricing_manual');
    const pricingMargin = document.getElementById('pricing_margin');
    const marginInput = document.getElementById('margin_input');
    const hargaJualInput = document.getElementById('harga_jual');
    const hargaModalInput = document.getElementById('harga_modal');
    const marginPercentageInput = document.getElementById('margin_percentage');
    const hargaJualNote = document.getElementById('harga_jual_note');

    // Function to toggle pricing mode
    function togglePricingMode() {
        console.log('togglePricingMode called, margin checked:', pricingMargin.checked);
        
        if (pricingMargin.checked) {
            // Margin mode
            marginInput.style.display = 'block';
            hargaJualInput.disabled = true;
            hargaJualInput.readOnly = true;
            hargaJualInput.style.backgroundColor = '#f8f9fa';
            hargaJualInput.style.cursor = 'not-allowed';
            hargaJualInput.style.opacity = '0.8';
            hargaJualNote.innerHTML = '<i class="fas fa-calculator mr-1"></i>Harga jual akan dihitung otomatis berdasarkan margin';
            calculateMarginPrice();
        } else {
            // Manual mode
            marginInput.style.display = 'none';
            hargaJualInput.disabled = false;
            hargaJualInput.readOnly = false;
            hargaJualInput.style.backgroundColor = '';
            hargaJualInput.style.cursor = '';
            hargaJualInput.style.opacity = '';
            hargaJualNote.innerHTML = '<i class="fas fa-info-circle mr-1"></i>Masukkan harga jual dalam format Rupiah';
            hideMarginPreview();
        }
    }

    // Function to calculate price based on margin
    function calculateMarginPrice() {
        if (pricingMargin.checked) {
            // Hapus semua karakter non-digit untuk parsing
            const hargaModalStr = hargaModalInput.value.replace(/[^\d]/g, '');
            const hargaModal = parseFloat(hargaModalStr) || 0;
            const margin = parseFloat(marginPercentageInput.value) || 0;
            
            if (hargaModal > 0 && margin >= 0) {
                // Rumus: Harga Modal + (Harga Modal × Margin%)
                const keuntungan = hargaModal * (margin / 100);
                const hargaJual = hargaModal + keuntungan;
                
                // Set value ke input harga jual
                hargaJualInput.value = formatRupiah(hargaJual);
                
                // Update preview
                updateMarginPreview(hargaModal, margin, keuntungan, hargaJual);
            } else if (hargaModal > 0) {
                // Jika hanya ada harga modal tapi margin 0
                hargaJualInput.value = formatRupiah(hargaModal);
                updateMarginPreview(hargaModal, 0, 0, hargaModal);
            } else {
                hargaJualInput.value = '';
                hideMarginPreview();
            }
        }
    }

    // Function to update margin preview
    function updateMarginPreview(hargaModal, margin, keuntungan, hargaJual) {
        const marginPreview = document.getElementById('margin_preview');
        const previewModal = document.getElementById('preview_modal');
        const previewMargin = document.getElementById('preview_margin');
        const previewProfit = document.getElementById('preview_profit');
        const previewJual = document.getElementById('preview_jual');
        
        if (hargaModal > 0) {
            previewModal.textContent = 'Rp ' + formatRupiah(hargaModal);
            previewMargin.textContent = margin + '%';
            previewProfit.textContent = 'Rp ' + formatRupiah(keuntungan);
            previewJual.textContent = 'Rp ' + formatRupiah(hargaJual);
            marginPreview.style.display = 'block';
        } else {
            hideMarginPreview();
        }
    }

    // Function to hide margin preview
    function hideMarginPreview() {
        const marginPreview = document.getElementById('margin_preview');
        marginPreview.style.display = 'none';
    }

    // Function to format number as Rupiah
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(Math.round(number));
    }

    // Function to parse Rupiah format to number
    function parseRupiah(rupiahString) {
        return parseFloat(rupiahString.replace(/[^\d]/g, '')) || 0;
    }

    // Event listeners
    pricingManual.addEventListener('change', togglePricingMode);
    pricingMargin.addEventListener('change', togglePricingMode);
    
    // Calculate price when modal price changes
    hargaModalInput.addEventListener('input', function() {
        // Format input sebagai angka
        let value = this.value.replace(/[^\d]/g, '');
        if (value) {
            this.value = formatRupiah(parseFloat(value));
        }
        
        if (pricingMargin.checked) {
            calculateMarginPrice();
        }
    });
    
    // Calculate price when margin changes
    marginPercentageInput.addEventListener('input', function() {
        // Validate margin percentage
        let value = parseFloat(this.value);
        if (isNaN(value) || value < 0) {
            this.value = 0;
            value = 0;
        }
        
        if (pricingMargin.checked) {
            calculateMarginPrice();
        }
    });

    // Format harga jual input untuk mode manual
    hargaJualInput.addEventListener('input', function() {
        if (pricingManual.checked) {
            let value = this.value.replace(/[^\d]/g, '');
            if (value) {
                this.value = formatRupiah(parseFloat(value));
            }
        }
    });

    // Initialize form state
    togglePricingMode();
});
</script>
@endsection