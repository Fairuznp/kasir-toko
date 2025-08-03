<!-- Barcode Form -->
<form action="#" class="card card-orange card-outline" id="formBarcode">
    <div class="card-body">
        <div class="input-group">
            <input type="text" class="form-control" id="barcode" placeholder="Kode / Barcode">
            <div class="input-group-append">
                <button type="button" class="btn btn-primary" id="scanQR">
                    <i class="fas fa-qrcode"></i> 
                    <span class="d-none d-sm-inline ml-1">Scan</span>
                </button>
                <button type="reset" class="btn btn-danger">
                    <i class="fas fa-times"></i>
                    <span class="d-none d-sm-inline ml-1">Clear</span>
                </button>
            </div>
        </div>
        <div class="invalid-feedback" id="msgErrorBarcode"></div>
    </div>
</form>

<!-- QR Modal -->
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan QR Code</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <div id="scanner" class="scanner mx-auto"></div>
                <div class="mt-3">
                    <button class="btn btn-success" id="startBtn">
                        <i class="fas fa-play"></i> 
                        <span class="ml-1">Mulai</span>
                    </button>
                    <button class="btn btn-danger d-none ml-2" id="stopBtn">
                        <i class="fas fa-stop"></i> 
                        <span class="ml-1">Stop</span>
                    </button>
                </div>
                <small id="status" class="d-block mt-2 text-muted">Tekan tombol untuk mulai scan</small>
            </div>
        </div>
    </div>
</div>

<!-- Modal Input Quantity -->
<div class="modal fade" id="qtyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form id="qtyForm">
                <div class="modal-header">
                    <h5 class="modal-title">Masukkan Jumlah</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="qtyInput" class="form-label">Quantity:</label>
                        <input type="number" class="form-control form-control-lg text-center" 
                               id="qtyInput" value="1" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-cart-plus mr-1"></i>Tambah ke Keranjang
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html5-qrcode/2.3.8/html5-qrcode.min.js"></script>
<script>
$(function() {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }});

    let scanner = null,
        isScanning = false,
        currentKodeProduk = null;

    $('#barcode').focus();

    // Tangani input manual barcode
    $('#formBarcode').submit(e => {
        e.preventDefault();
        const kode = $('#barcode').val().trim();
        if (kode) addItem(kode);
    });

    $('button[type="reset"]').click(() => $('#barcode').val('').focus());

    // Tombol buka QR modal
    $('#scanQR').click(() => $('#qrModal').modal('show'));

    // QR Modal open & close
    $('#qrModal').on('shown.bs.modal', () => {
        scanner = new Html5Qrcode("scanner");
        status("Siap untuk scan");
    }).on('hidden.bs.modal', () => stopScan());

    $('#startBtn').click(async () => {
        if (isScanning) return;
        try {
            status("Memulai...");
            $('#startBtn').prop('disabled', true);
            await scanner.start(
                { facingMode: "environment" },
                { fps: 10, qrbox: 200 },
                text => {
                    status("✓ Berhasil!");
                    stopScan();
                    setTimeout(() => {
                        $('#qrModal').modal('hide');
                        addItem(text);
                    }, 800);
                }
            );
            isScanning = true;
            $('.scanner').addClass('active');
            $('#startBtn').addClass('d-none');
            $('#stopBtn').removeClass('d-none');
            status("Arahkan ke QR code");
        } catch (e) {
            status("Error: " + e.message);
            $('#startBtn').prop('disabled', false);
        }
    });

    $('#stopBtn').click(stopScan);

    async function stopScan() {
        if (!isScanning || !scanner) return;
        try {
            await scanner.stop();
            isScanning = false;
            $('.scanner').removeClass('active');
            $('#stopBtn').addClass('d-none');
            $('#startBtn').removeClass('d-none').prop('disabled', false);
            status("Scanner dihentikan");
        } catch (e) {}
    }

    function status(msg) {
        $('#status').html(msg);
    }

    // Handler tambah produk
    function addItem(kode_produk) {
        if (!kode_produk) return;
        currentKodeProduk = kode_produk;
        $('#qtyInput').val(1);
        $('#qtyModal').modal('show');
    }

    // Auto focus on quantity input when modal opens
    $('#qtyModal').on('shown.bs.modal', function() {
        $('#qtyInput').focus().select();
    });

    // Submit quantity
    $('#qtyForm').submit(function(e) {
        e.preventDefault();
        const qty = parseInt($('#qtyInput').val()) || 1;
        if (!currentKodeProduk || qty < 1) return;

        // Show loading state
        const submitBtn = $('#qtyForm button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i>Menambahkan...').prop('disabled', true);

        $('#qtyModal').modal('hide');
        $('#msgErrorBarcode').removeClass('d-block').html('');
        $('#barcode').removeClass('is-invalid').prop('disabled', true);

        $.post("/cart", {
            kode_produk: currentKodeProduk,
            quantity: qty
        }, function(response) {
            fetchCart(); // Reload cart
        }, "json").fail(function(error) {
            if (error.status === 422) {
                $('#msgErrorBarcode').addClass('d-block')
                    .html(error.responseJSON.errors.kode_produk[0]);
                $('#barcode').addClass('is-invalid');
            }
        }).always(function() {
            $('#barcode').val('').prop('disabled', false).focus();
            currentKodeProduk = null;
            // Reset button state
            submitBtn.html(originalText).prop('disabled', false);
        });
    });
});
</script>

<style>
.scanner {
    width: 100%;
    max-width: 300px;
    height: 300px;
    background: #000;
    border-radius: 12px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

.scanner::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 200px;
    height: 200px;
    margin: -100px 0 0 -100px;
    border: 3px solid #28a745;
    border-radius: 8px;
    z-index: 10;
    opacity: 0.8;
}

.scanner.active::before {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { 
        border-color: #28a745; 
        transform: scale(1);
        opacity: 0.8;
    }
    50% { 
        border-color: #20c997; 
        transform: scale(1.02);
        opacity: 1;
    }
}

/* Mobile responsiveness */
@media (max-width: 576px) {
    .scanner { 
        max-width: 280px;
        height: 280px; 
    }
    
    .scanner::before { 
        width: 180px; 
        height: 180px; 
        margin: -90px 0 0 -90px; 
        border-width: 2px;
    }
    
    .input-group-append .btn {
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
    }
    
    .input-group-append .btn .ml-1 {
        margin-left: 0.25rem !important;
    }
    
    .card-body {
        padding: 1rem 0.75rem;
    }
    
    .modal-dialog {
        margin: 0.5rem;
    }
    
    .modal-body {
        padding: 1rem;
    }
    
    .modal-footer {
        padding: 0.75rem 1rem;
        flex-direction: column;
    }
    
    .modal-footer .btn {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .modal-footer .btn:last-child {
        margin-bottom: 0;
    }
}

@media (max-width: 768px) {
    .scanner {
        max-width: 260px;
        height: 260px;
    }
    
    .scanner::before {
        width: 160px;
        height: 160px;
        margin: -80px 0 0 -80px;
    }
    
    .modal-title {
        font-size: 1.1rem;
    }
    
    #startBtn, #stopBtn {
        padding: 0.5rem 1rem;
        font-size: 0.9rem;
    }
    
    #status {
        font-size: 0.85rem;
    }
}

/* Better touch targets */
@media (max-width: 768px) {
    .btn {
        min-height: 44px;
        touch-action: manipulation;
    }
    
    .form-control {
        min-height: 44px;
        font-size: 16px; /* Prevents zoom on iOS */
    }
    
    .form-control-lg {
        min-height: 52px;
        font-size: 1.125rem;
    }
}

/* Input group improvements */
.input-group {
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    border-radius: 8px;
    overflow: hidden;
}

.input-group .form-control {
    border-right: none;
    background-color: #fff;
}

.input-group .form-control:focus {
    border-color: #80bdff;
    box-shadow: none;
}

.input-group-append .btn {
    border-left: 1px solid #ced4da;
    z-index: 2;
}

.input-group-append .btn:first-child {
    border-left: none;
}

/* Modal improvements */
.modal-content {
    border-radius: 12px;
    border: none;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
}

.modal-header {
    border-bottom: 1px solid #e9ecef;
    background-color: #f8f9fa;
    border-radius: 12px 12px 0 0;
}

.modal-footer {
    border-top: 1px solid #e9ecef;
    background-color: #f8f9fa;
    border-radius: 0 0 12px 12px;
}

/* Error feedback */
.invalid-feedback {
    display: none;
    font-size: 0.875rem;
    margin-top: 0.5rem;
}

.invalid-feedback.d-block {
    display: block !important;
}

.form-control.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

/* Loading button state */
.btn:disabled {
    cursor: not-allowed;
    opacity: 0.65;
}

/* Responsive modal for large screens */
@media (min-width: 992px) {
    .modal-dialog {
        max-width: 600px;
    }
    
    .scanner {
        max-width: 350px;
        height: 350px;
    }
    
    .scanner::before {
        width: 220px;
        height: 220px;
        margin: -110px 0 0 -110px;
    }
}
</style>
@endpush