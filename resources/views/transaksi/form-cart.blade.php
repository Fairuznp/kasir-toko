<div class="card card-orange card-outline">
    <div class="card-body">
        <h3 class="m-0 text-right text-success">Rp <span id="totalJumlah">0</span> ,-</h3>
    </div>
</div>

<form action="{{ route('transaksi.store') }}" method="POST" class="card card-orange card-outline" id="formTransaksi">
    @csrf
    <div id="form-submit-overlay" style="display:none"></div>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        let isSubmitting = false;
        
        document.getElementById('formTransaksi').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (isSubmitting) return;
            
            const pelangganId = document.getElementById('pelangganId').value;
            
            if (!pelangganId) {
                try {
                    const result = await Swal.fire({
                        title: 'Konfirmasi',
                        text: 'Apakah anda ingin melanjutkan transaksi tanpa nama pelanggan?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Ya, Lanjutkan',
                        cancelButtonText: 'Tidak',
                        allowOutsideClick: false
                    });
                    
                    if (result.isConfirmed) {
                        isSubmitting = true;
                        this.removeEventListener('submit', arguments.callee);
                        this.submit();
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            } else {
                isSubmitting = true;
                this.removeEventListener('submit', arguments.callee);
                this.submit();
            }
        });
    </script>
    <div class="card-body">
        <p class="text-right small text-muted">Tanggal : {{ $tanggal }}</p>

        <div class="row">
            <div class="col-12 col-md-6 mb-3 mb-md-0">
                <label class="form-label">Nama Pelanggan</label>
                <input type="text" id="namaPelanggan"
                       class="form-control @error('pelanggan_id') is-invalid @enderror"
                       disabled>
                @error('pelanggan_id')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
                @enderror
                <input type="hidden" name="pelanggan_id" id="pelangganId">
            </div>
            <div class="col-12 col-md-6">
                <label class="form-label">Nama Kasir</label>
                <input type="text" class="form-control" value="{{ $nama_kasir }}" disabled>
            </div>
        </div>

        <div class="table-responsive mt-3">
            <table class="table table-striped table-hover table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Nama Produk</th>
                        <th class="text-center">Qty</th>
                        <th class="text-right d-none d-sm-table-cell">Harga</th>
                        <th class="text-right">Sub Total</th>
                        <th class="text-center" width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody id="resultCart">
                    <tr>
                        <td colspan="5" class="text-center text-muted">Tidak ada data.</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Form Diskon -->
        <div class="row mt-3">
            <div class="col-12 col-lg-6">
                <div class="form-group">
                    <label class="form-label">Kode Diskon</label>
                    <div class="input-group">
                        <input type="text" id="kodeDiskon" class="form-control" placeholder="Masukkan kode diskon">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-info" onclick="terapkanDiskon()">
                                <i class="fas fa-check mr-1 d-none d-sm-inline"></i> 
                                <span class="d-none d-sm-inline">Terapkan</span>
                                <i class="fas fa-check d-sm-none"></i>
                            </button>
                        </div>
                    </div>
                    <div id="diskonError" class="text-danger mt-2 small" style="display: none;"></div>
                    <div id="diskonSuccess" class="text-success mt-2 small" style="display: none;"></div>
                </div>
            </div>
        </div>

        <!-- Summary Section -->
        <div class="row mt-3">
            <div class="col-12 col-md-6 offset-md-6">
                <div class="card bg-light">
                    <div class="card-body p-3">
                        <div class="row mb-2">
                            <div class="col-6"><strong>Subtotal</strong></div>
                            <div class="col-6 text-right" id="subtotal">0</div>
                        </div>
                        <div class="row mb-2" id="diskonRow" style="display: none;">
                            <div class="col-6"><strong>Diskon</strong></div>
                            <div class="col-6 text-right text-success" id="diskonAmount">0</div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-6"><strong>Pajak 10%</strong></div>
                            <div class="col-6 text-right" id="taxAmount">0</div>
                        </div>
                        <hr class="my-2">
                        <div class="row mb-3">
                            <div class="col-6"><h5>Total Bayar</h5></div>
                            <div class="col-6 text-right"><h5 id="total">0</h5></div>
                        </div>
                        
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">Cash</span>
                            </div>
                            <input type="text" name="cash"
                                   class="form-control @error('cash') is-invalid @enderror"
                                   placeholder="Jumlah Cash" value="{{ old('cash') }}">
                        </div>
                        <input type="hidden" name="total_bayar" id="totalBayar">
                        @error('cash')
                        <div class="invalid-feedback d-block">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-stretch align-items-md-center">
                    <div class="mb-2 mb-md-0">
                        <a href="{{ route('transaksi.index') }}" class="btn btn-secondary mr-2">
                            <i class="fas fa-arrow-left mr-2"></i>Ke Transaksi
                        </a>
                        <a href="{{ route('cart.clear') }}" class="btn btn-danger">
                            <i class="fas fa-trash mr-2"></i>Kosongkan
                        </a>
                    </div>
                    <div>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-money-bill-wave mr-2"></i> Bayar Transaksi
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Quantity Modal -->
<div class="modal fade" id="qtyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <form id="qtyForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="qtyModalTitle">Masukkan Jumlah</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="qtyInput" class="form-label">Quantity:</label>
                        <input type="number" class="form-control form-control-lg text-center" 
                               id="qtyInput" value="1" min="1" required>
                    </div>
                    <div class="card bg-light">
                        <div class="card-body p-3">
                            <div id="currentQtyInfo" class="small"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary" id="qtySubmitBtn">
                        <i class="fas fa-check mr-2"></i><span id="qtySubmitText">Update</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        fetchCart();
    });
    
    function fetchCart() {
        $.getJSON("/cart", function(response) {
            $('#resultCart').empty();

            const {
                items,
                subtotal,
                tax_amount,
                total,
                extra_info,
                discount_amount
            } = response;

            $('#subtotal').html(rupiah(subtotal));
            $('#taxAmount').html(rupiah(tax_amount));
            $('#total, #totalJumlah').html(rupiah(total));
            $('#totalBayar').val(total);

            // Tampilkan diskon jika ada
            if (discount_amount > 0) {
                $('#diskonRow').show();
                $('#diskonAmount').html('- ' + rupiah(discount_amount));
            } else {
                $('#diskonRow').hide();
            }

            if (Array.isArray(items)) {
                $('#resultCart').html(`<tr><td colspan="5" class="text-center text-muted">Tidak ada data.</td></tr>`);
            }

            if (extra_info && extra_info.pelanggan) {
                const { id, nama } = extra_info.pelanggan;
                $('#namaPelanggan').val(nama);
                $('#pelangganId').val(id);
            }

            for (const property in items) {
                addRow(items[property]);
            }
        });
    }
    
    function addRow(item) {
        const {
            hash,
            title,
            quantity,
            price,
            subtotal,
            diskon_applied,
            harga_setelah_diskon
        } = item;

        let btn = `<div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-success" onclick="openQtyModal('${hash}', ${quantity}, 'add', ${item.stok || 0})" title="Tambah">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-primary" onclick="openQtyModal('${hash}', ${quantity}, 'subtract', ${item.stok || 0})" title="Kurang">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button type="button" class="btn btn-danger" onclick="eDel('${hash}')" title="Hapus">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>`;

        let subtotalTampil = `${rupiah(subtotal)}`;
        if (diskon_applied && harga_setelah_diskon < price) {
            const subtotalSebelumDiskon = price * quantity;
            subtotalTampil = `<span style='text-decoration:line-through;color:#dc3545'>${rupiah(subtotalSebelumDiskon)}</span> <span class='text-success font-weight-bold'>${rupiah(subtotal)}</span>`;
        }

        const row = `<tr>
                        <td>
                            <div>${title}</div>
                            <small class="text-muted d-sm-none">${rupiah(price)} x ${quantity}</small>
                        </td>
                        <td class="text-center">${quantity}x</td>
                        <td class="text-right d-none d-sm-table-cell">${rupiah(price)}</td>
                        <td class="text-right font-weight-bold">${subtotalTampil}</td>
                        <td class="text-center">${btn}</td>
                    </tr>`;

        $('#resultCart').append(row);
    }

    function rupiah(number) {
        return new Intl.NumberFormat("id-ID").format(number);
    }

    function ePut(hash, qty) {
        $.ajax({
            type: "PUT",
            url: "/cart/" + hash,
            data: { qty: qty },
            dataType: "json",
            success: function(response) {
                fetchCart();
            }
        });
    }

    function eDel(hash) {
        $.ajax({
            type: "DELETE",
            url: "/cart/" + hash,
            dataType: "json",
            success: function(response) {
                fetchCart();
            }
        });
    }
    
    // Global variables for modal
    let currentHash = null;
    let currentQty = 0;
    let currentStok = 0;
    let actionType = null;
    
    function openQtyModal(hash, currentQuantity, action, stok = 0) {
        currentHash = hash;
        currentQty = currentQuantity;
        currentStok = stok;
        actionType = action;
        
        // Set modal title and info based on action
        if (action === 'add') {
            $('#qtyModalTitle').text('Tambah Quantity');
            $('#qtySubmitText').text('Tambah');
            $('#qtySubmitBtn').removeClass('btn-primary').addClass('btn-success');
            $('#qtySubmitBtn i').removeClass('fa-check').addClass('fa-plus');
            // Set max quantity untuk penambahan
            const maxAdd = Math.max(0, stok - currentQuantity);
            $('#qtyInput').attr('max', maxAdd);
        } else {
            $('#qtyModalTitle').text('Kurangi Quantity');
            $('#qtySubmitText').text('Kurangi');
            $('#qtySubmitBtn').removeClass('btn-success').addClass('btn-primary');
            $('#qtySubmitBtn i').removeClass('fa-plus').addClass('fa-minus');
            $('#qtyInput').attr('max', currentQuantity);
        }
        
        // Reset input value
        $('#qtyInput').val(1);
        
        // Update info display
        updateQtyInfo();
        
        // Show modal
        $('#qtyModal').modal('show');
    }
    
    function updateQtyInfo() {
        const inputQty = parseInt($('#qtyInput').val()) || 0;
        let infoText = '';
        let isValid = true;
        
        if (actionType === 'add') {
            const newQty = currentQty + inputQty;
            const maxAdd = Math.max(0, currentStok - currentQty);
            isValid = inputQty <= maxAdd && inputQty > 0;
            
            infoText = `<div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span>Quantity saat ini:</span>
                                    <strong>${currentQty}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span>Stok tersedia:</span>
                                    <strong class="text-info">${currentStok}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span>Akan ditambah:</span>
                                    <strong class="text-primary">+${inputQty}</strong>
                                </div>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span><strong>Total quantity:</strong></span>
                                    <strong class="${isValid ? 'text-success' : 'text-danger'}">${newQty}</strong>
                                </div>
                            </div>
                        </div>`;
            
            if (!isValid) {
                infoText += `<div class="row mt-2">
                                <div class="col-12">
                                    <div class="alert alert-danger py-2 mb-0">
                                        <small><i class="fas fa-exclamation-triangle"></i> 
                                        ${inputQty > maxAdd ? `Maksimal yang bisa ditambahkan: ${maxAdd}` : 'Quantity harus lebih dari 0'}</small>
                                    </div>
                                </div>
                            </div>`;
            }
        } else {
            const newQty = Math.max(0, currentQty - inputQty);
            isValid = inputQty <= currentQty && inputQty > 0;
            
            infoText = `<div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span>Quantity saat ini:</span>
                                    <strong>${currentQty}</strong>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-1">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span>Akan dikurangi:</span>
                                    <strong class="text-warning">-${inputQty}</strong>
                                </div>
                            </div>
                        </div>
                        <hr class="my-2">
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <span><strong>Sisa quantity:</strong></span>
                                    <strong class="${isValid ? 'text-success' : 'text-danger'}">${newQty}</strong>
                                </div>
                            </div>
                        </div>`;
            
            if (!isValid) {
                infoText += `<div class="row mt-2">
                                <div class="col-12">
                                    <div class="alert alert-danger py-2 mb-0">
                                        <small><i class="fas fa-exclamation-triangle"></i> 
                                        ${inputQty > currentQty ? `Maksimal yang bisa dikurangi: ${currentQty}` : 'Quantity harus lebih dari 0'}</small>
                                    </div>
                                </div>
                            </div>`;
            }
        }
        
        $('#currentQtyInfo').html(infoText);
        
        // Enable/disable submit button based on validation
        $('#qtySubmitBtn').prop('disabled', !isValid);
    }
    
    // Handle form submission
    $('#qtyForm').on('submit', function(e) {
        e.preventDefault();
        
        const inputQty = parseInt($('#qtyInput').val());
        let finalQty;
        
        if (actionType === 'add') {
            finalQty = inputQty; // Positive number for addition
            // Double check validation
            const maxAdd = Math.max(0, currentStok - currentQty);
            if (inputQty > maxAdd) {
                alert(`Maksimal yang bisa ditambahkan: ${maxAdd}`);
                return;
            }
        } else {
            finalQty = -inputQty; // Negative number for subtraction
            // Double check validation
            if (inputQty > currentQty) {
                alert(`Maksimal yang bisa dikurangi: ${currentQty}`);
                return;
            }
        }
        
        // Call the update function
        ePut(currentHash, finalQty);
        
        // Hide modal
        $('#qtyModal').modal('hide');
    });
    
    // Update info when input changes
    $('#qtyInput').on('input', function() {
        updateQtyInfo();
    });
    
    // Reset modal when hidden
    $('#qtyModal').on('hidden.bs.modal', function() {
        $('#qtyInput').removeAttr('max');
        currentHash = null;
        currentQty = 0;
        currentStok = 0;
        actionType = null;
    });
    
    function terapkanDiskon() {
        const kodeDiskon = $('#kodeDiskon').val();
        
        if (!kodeDiskon) {
            showDiskonError('Masukkan kode diskon');
            return;
        }

        $.ajax({
            type: "POST",
            url: "/terapkan-diskon",
            data: { 
                kode_diskon: kodeDiskon,
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    showDiskonSuccess(response.message);
                    fetchCart(); // Refresh cart
                } else {
                    showDiskonError(response.message);
                }
            },
            error: function(xhr) {
                const response = xhr.responseJSON;
                showDiskonError(response.message || 'Terjadi kesalahan');
            }
        });
    }

    function showDiskonError(message) {
        $('#diskonError').text(message).show();
        $('#diskonSuccess').hide();
    }

    function showDiskonSuccess(message) {
        $('#diskonSuccess').text(message).show();
        $('#diskonError').hide();
    }
</script>
@endpush

<style>
@media (max-width: 576px) {
    .table td, .table th {
        padding: 0.5rem 0.25rem;
        font-size: 0.875rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.375rem;
        font-size: 0.75rem;
    }
    
    .card-body {
        padding: 0.75rem;
    }
    
    .form-label {
        font-size: 0.875rem;
        margin-bottom: 0.25rem;
    }
    
    .input-group {
        margin-bottom: 0.5rem;
    }
    
    .d-flex.flex-column .btn {
        margin-bottom: 0.5rem;
        width: 100%;
    }
    
    .d-flex.flex-column .btn:last-child {
        margin-bottom: 0;
    }
    
    h3 {
        font-size: 1.5rem;
    }
    
    h5 {
        font-size: 1.1rem;
    }
}

@media (max-width: 768px) {
    .table-responsive {
        border: none;
        font-size: 0.875rem;
    }
    
    .btn-group {
        display: flex;
        flex-direction: row;
    }
    
    .card.bg-light {
        margin-top: 1rem;
    }
}

@media (min-width: 768px) {
    .d-flex.flex-md-row .btn {
        width: auto;
        margin-bottom: 0;
    }
}

@media (min-width: 992px) {
    .table td, .table th {
        padding: 0.75rem;
    }
}

/* Better touch targets for mobile */
@media (max-width: 768px) {
    .btn {
        min-height: 38px;
        touch-action: manipulation;
    }
    
    .btn-sm {
        min-height: 32px;
    }
    
    .form-control {
        min-height: 38px;
    }
}

/* Improved table layout for mobile */
@media (max-width: 576px) {
    .table-responsive table {
        margin-bottom: 0;
    }
    
    .table-responsive {
        border-radius: 0.25rem;
    }
}
</style>