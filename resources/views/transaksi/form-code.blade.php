<!-- Barcode Form -->
<form action="#" class="card card-orange card-outline" id="formBarcode">
    <div class="card-body">
        <div class="input-group">
            <input type="text" class="form-control" id="barcode" placeholder="Kode / Barcode">
            <div class="input-group-append">
                <button type="button" class="btn btn-primary" id="scanQR">
                    <i class="fas fa-qrcode"></i> Scan
                </button>
                <button type="reset" class="btn btn-danger">Clear</button>
            </div>
        </div>
        <div class="invalid-feedback" id="msgErrorBarcode"></div>
    </div>
</form>

<!-- QR Modal -->
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Scan QR Code</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body text-center">
                <div id="scanner" class="scanner"></div>
                <div class="mt-3">
                    <button class="btn btn-success" id="startBtn"><i class="fas fa-play"></i> Mulai</button>
                    <button class="btn btn-danger d-none" id="stopBtn"><i class="fas fa-stop"></i> Stop</button>
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
                    <input type="number" class="form-control" id="qtyInput" value="1" min="1" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Tambah ke Keranjang</button>
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

    // Submit quantity
    $('#qtyForm').submit(function(e) {
        e.preventDefault();
        const qty = parseInt($('#qtyInput').val()) || 1;
        if (!currentKodeProduk || qty < 1) return;

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
        });
    });
});
</script>

<style>
.scanner {
    width: 280px;
    height: 280px;
    margin: 0 auto;
    background: #000;
    border-radius: 8px;
    position: relative;
    overflow: hidden;
}

.scanner::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 180px;
    height: 180px;
    margin: -90px 0 0 -90px;
    border: 2px solid #28a745;
    border-radius: 6px;
    z-index: 10;
}

.scanner.active::before {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { border-color: #28a745; }
    50% { border-color: #20c997; }
}

@media (max-width: 576px) {
    .scanner { width: 100%; height: 240px; }
    .scanner::before { width: 140px; height: 140px; margin: -70px 0 0 -70px; }
}
</style>
@endpush
