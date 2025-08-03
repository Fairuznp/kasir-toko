<form action="" method="get" id="formCariPelanggan">
    <div class="input-group">
        <input type="text" class="form-control" placeholder="Nama Pelanggan" id="searchPelanggan">
        <div class="input-group-append">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search d-sm-none"></i>
                <span class="d-none d-sm-inline">Cari</span>
            </button>
        </div>
    </div>
</form>

<div class="mt-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <small class="text-muted">Hasil Pencarian:</small>
    </div>
    
    <div class="table-responsive">
        <table class="table table-sm table-hover">
            <tbody id="resultPelanggan">
                <tr>
                    <td class="text-center text-muted">Ketik minimal 3 karakter untuk mencari</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    $(function() {
        $('#formCariPelanggan').submit(function(e) {
            e.preventDefault();
            const search = $('#searchPelanggan').val();
            if (search.length >= 3) {
                fetchCariPelanggan(search);
            } else {
                $('#resultPelanggan').html('<tr><td class="text-center text-muted">Ketik minimal 3 karakter untuk mencari</td></tr>');
            }
        });
        
        // Real-time search
        $('#searchPelanggan').on('input', function() {
            const search = $(this).val();
            if (search.length >= 3) {
                fetchCariPelanggan(search);
            } else if (search.length === 0) {
                $('#resultPelanggan').html('<tr><td class="text-center text-muted">Ketik minimal 3 karakter untuk mencari</td></tr>');
            }
        });
    });

    function fetchCariPelanggan(search) {
        $('#resultPelanggan').html('<tr><td class="text-center text-muted"><i class="fas fa-spinner fa-spin mr-2"></i>Mencari...</td></tr>');
        
        $.getJSON("/transaksi/pelanggan", {
            search: search
        }, function(response) {
            $('#resultPelanggan').html('');
            if (response.length === 0) {
                $('#resultPelanggan').html('<tr><td class="text-center text-muted">Tidak ada hasil ditemukan</td></tr>');
            } else {
                response.forEach(item => {
                    addResultPelanggan(item);
                });
            }
        }).fail(function() {
            $('#resultPelanggan').html('<tr><td class="text-center text-danger">Terjadi kesalahan saat mencari</td></tr>');
        });
    }

    function addResultPelanggan(item) {
        const { id, nama } = item;

        const btn = `<button type="button" 
                        class="btn btn-xs btn-success btn-block" 
                        onclick="addPelanggan(${id})"
                        title="Pilih pelanggan ${nama}">
                        <i class="fas fa-check mr-1 d-none d-sm-inline"></i>
                        <span class="d-none d-sm-inline">Pilih</span>
                        <i class="fas fa-check d-sm-none"></i>
                    </button>`;

        const row = `<tr class="cursor-pointer" onclick="addPelanggan(${id})">
                        <td>
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>${nama}</strong>
                                    <div class="d-none d-md-block">
                                        <small class="text-muted">ID: ${id}</small>
                                    </div>
                                </div>
                                <div class="ml-2">
                                    ${btn}
                                </div>
                            </div>
                        </td>
                    </tr>`;
        $('#resultPelanggan').append(row);
    }

    function addPelanggan(id) {
        // Show loading state
        const loadingBtn = `<button type="button" class="btn btn-xs btn-secondary" disabled>
                               <i class="fas fa-spinner fa-spin"></i>
                           </button>`;
        
        $(`button[onclick="addPelanggan(${id})"]`).html(loadingBtn);
        
        $.post("/transaksi/pelanggan", {
            id: id
        }, function(response) {
            fetchCart();
            // Show success feedback
            $(`button[onclick="addPelanggan(${id})"]`).closest('tr').addClass('table-success');
            setTimeout(() => {
                $(`button[onclick="addPelanggan(${id})"]`).closest('tr').removeClass('table-success');
            }, 1000);
        }, "json").fail(function() {
            // Show error feedback
            $(`button[onclick="addPelanggan(${id})"]`).closest('tr').addClass('table-danger');
            setTimeout(() => {
                $(`button[onclick="addPelanggan(${id})"]`).closest('tr').removeClass('table-danger');
            }, 1000);
        });
    }
</script>
@endpush

<style>
@media (max-width: 576px) {
    .table-sm td {
        padding: 0.5rem 0.25rem;
        font-size: 0.875rem;
    }
    
    .btn-xs {
        padding: 0.25rem 0.375rem;
        font-size: 0.75rem;
        min-height: 28px;
    }
    
    .input-group .btn {
        min-height: 38px;
        padding: 0.375rem 0.5rem;
    }
    
    .form-control {
        min-height: 38px;
    }
}

@media (max-width: 768px) {
    .table-responsive {
        border: none;
        margin-bottom: 0;
    }
    
    .d-flex {
        flex-wrap: wrap;
    }
    
    .cursor-pointer {
        cursor: pointer;
    }
    
    .cursor-pointer:hover {
        background-color: rgba(0, 123, 255, 0.1);
    }
}

/* Touch-friendly improvements */
@media (max-width: 768px) {
    .table td {
        padding: 0.75rem 0.5rem;
    }
    
    .btn {
        touch-action: manipulation;
        min-height: 44px; /* Better touch target */
    }
    
    .btn-xs {
        min-height: 36px;
    }
}

/* Loading and feedback states */
.table-success {
    animation: successFade 1s ease-in-out;
}

.table-danger {
    animation: errorFade 1s ease-in-out;
}

@keyframes successFade {
    0% { background-color: transparent; }
    50% { background-color: #d4edda; }
    100% { background-color: transparent; }
}

@keyframes errorFade {
    0% { background-color: transparent; }
    50% { background-color: #f8d7da; }
    100% { background-color: transparent; }
}

/* Improved spacing */
@media (max-width: 576px) {
    .mt-3 {
        margin-top: 1rem !important;
    }
    
    .mb-2 {
        margin-bottom: 0.5rem !important;
    }
}
</style>