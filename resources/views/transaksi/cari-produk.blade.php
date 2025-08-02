<!-- Form Cari Produk -->
<form action="" method="get" id="formCariProduk">
    <div class="input-group">
        <input type="text" class="form-control" placeholder="Nama Produk" id="searchProduk">
        <div class="input-group-append">
            <button type="submit" class="btn btn-primary">Cari</button>
        </div>
    </div>
</form>

<table class="table table-sm mt-3">
    <thead>
        <tr>
            <th colspan="2" class="border-0">Hasil Pencarian :</th>
        </tr>
    </thead>
    <tbody id="resultProduk"></tbody>
</table>

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
<script>
    let selectedKodeProduk = null;
    $(function () {
        $('#formCariProduk').submit(function (e) {
            e.preventDefault();
            const search = $('#searchProduk').val().trim();
            if (search.length >= 3) {
                fetchCariProduk(search);
            }
        });
    });

    function fetchCariProduk(search) {
        $.getJSON("/transaksi/produk", { search: search }, function (response) {
            $('#resultProduk').html('');
            response.forEach(item => {
                addResultProduk(item);
            });
        });
    }

    function addResultProduk(item) {
        const { nama_produk, kode_produk } = item;

        const btn = `<button type="button"
                        class="btn btn-xs btn-success"
                        onclick="addItem('${kode_produk}')">
                        Add
                    </button>`;

        const row = `<tr>
                        <td>${nama_produk}</td>
                        <td class="text-right">${btn}</td>
                    </tr>`;
        $('#resultProduk').append(row);
    }

   function addItem(kode_produk) {
    if (!kode_produk) return;

    selectedKodeProduk = kode_produk; // simpan ke global
    $('#qtyInput').val(1);
    $('#qtyModal').modal('show');
}

     $('#qtyForm').submit(function(e) {
        e.preventDefault();
        const qty = parseInt($('#qtyInput').val()) || 1;
        if (!selectedKodeProduk || qty < 1) return;

        $('#qtyModal').modal('hide');
        $.post("/cart", {
            kode_produk:selectedKodeProduk,
            quantity: qty
        }, function(response) {
            fetchCart(); // Reload cart
        }, "json").fail(function(error) {
            if (error.status === 422) {
                $('#msgErrorBarcode').addClass('d-block')
                    .html(error.responseJSON.errors.kode_produk[0]);
                $('#barcode').addClass('is-invalid');
            }
        })
    });
</script>
@endpush
