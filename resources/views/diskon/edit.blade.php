@extends('layouts.main', ['title' => 'Edit Diskon'])

@section('title-content')
    <i class="fas fa-tags mr-2"></i>
    Edit Diskon
@endsection

@section('content')
    <form action="{{ route('diskon.update', $diskon) }}" method="POST" class="card card-orange card-outline">
        @csrf
        @method('PUT')
        <div class="card-body p-3 p-md-4">
            <div class="row">
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <div class="form-group">
                        <label>Kode Diskon</label>
                        <input type="text" name="kode_diskon" 
                               class="form-control @error('kode_diskon') is-invalid @enderror"
                               value="{{ old('kode_diskon', $diskon->kode_diskon) }}">
                        @error('kode_diskon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label>Jenis Diskon</label>
                        <select name="jenis_diskon" class="form-control @error('jenis_diskon') is-invalid @enderror">
                            <option value="persen" {{ old('jenis_diskon', $diskon->jenis_diskon) == 'persen' ? 'selected' : '' }}>Persentase (%)</option>
                            <option value="nominal" {{ old('jenis_diskon', $diskon->jenis_diskon) == 'nominal' ? 'selected' : '' }}>Nominal (Rp)</option>
                        </select>
                        @error('jenis_diskon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <div class="form-group">
                        <label>Jumlah Diskon</label>
                        <input type="number" name="jumlah_diskon" 
                               class="form-control @error('jumlah_diskon') is-invalid @enderror"
                               value="{{ old('jumlah_diskon', $diskon->jumlah_diskon) }}">
                        @error('jumlah_diskon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label>Minimal Pembelian</label>
                        <input type="number" name="minimal_pembelian" 
                               class="form-control @error('minimal_pembelian') is-invalid @enderror"
                               value="{{ old('minimal_pembelian', $diskon->minimal_pembelian) }}">
                        @error('minimal_pembelian')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-6 mb-3 mb-md-0">
                    <div class="form-group">
                        <label>Tanggal Mulai</label>
                        <input type="datetime-local" name="tanggal_mulai" 
                               class="form-control @error('tanggal_mulai') is-invalid @enderror"
                               value="{{ old('tanggal_mulai', $diskon->tanggal_mulai->format('Y-m-d\TH:i')) }}">
                        @error('tanggal_mulai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <div class="form-group">
                        <label>Tanggal Selesai</label>
                        <input type="datetime-local" name="tanggal_selesai" 
                               class="form-control @error('tanggal_selesai') is-invalid @enderror"
                               value="{{ old('tanggal_selesai', $diskon->tanggal_selesai->format('Y-m-d\TH:i')) }}">
                        @error('tanggal_selesai')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12 col-md-4 mb-3 mb-md-0">
                    <div class="form-group">
                        <label>Status</label>
                        <select name="status" class="form-control">
                            <option value="1" {{ old('status', $diskon->status) ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ !old('status', $diskon->status) ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-3 mb-md-0">
                    <div class="form-group">
                        <label>Berlaku Untuk</label>
                        <select id="berlaku_untuk" class="form-control">
                            <option value="semua" {{ !$diskon->kategori_id && !$diskon->produk_id ? 'selected' : '' }}>Semua Produk</option>
                            <option value="kategori" {{ $diskon->kategori_id ? 'selected' : '' }}>Kategori Tertentu</option>
                            <option value="produk" {{ $diskon->produk_id ? 'selected' : '' }}>Produk Tertentu</option>
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-4" id="kategori_field" style="{{ $diskon->kategori_id ? '' : 'display: none;' }}">
                    <div class="form-group">
                        <label>Pilih Kategori</label>
                        <select name="kategori_id" class="form-control">
                            <option value="">Pilih Kategori</option>
                            @foreach($kategoris as $kategori)
                                <option value="{{ $kategori->id }}" {{ old('kategori_id', $diskon->kategori_id) == $kategori->id ? 'selected' : '' }}>
                                    {{ $kategori->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-12 col-md-4" id="produk_field" style="{{ $diskon->produk_id ? '' : 'display: none;' }}">
                    <div class="form-group">
                        <label>Pilih Produk</label>
                        <select name="produk_id" class="form-control">
                            <option value="">Pilih Produk</option>
                            @foreach($produks as $produk)
                                <option value="{{ $produk->id }}" {{ old('produk_id', $diskon->produk_id) == $produk->id ? 'selected' : '' }}>
                                    {{ $produk->nama_produk }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-footer">
            <div class="d-flex flex-column flex-md-row justify-content-between">
                <div class="mb-2 mb-md-0">
                    <a href="{{ route('diskon.index') }}" class="btn btn-secondary btn-block btn-md-inline">Kembali</a>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary btn-block btn-md-inline">Update</button>
                </div>
            </div>
        </div>
    </form>

    <style>
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
    $('#berlaku_untuk').change(function() {
        const value = $(this).val();
        
        $('#kategori_field, #produk_field').hide();
        $('select[name="kategori_id"], select[name="produk_id"]').val('');
        
        if (value === 'kategori') {
            $('#kategori_field').show();
        } else if (value === 'produk') {
            $('#produk_field').show();
        }
    });
</script>
@endpush