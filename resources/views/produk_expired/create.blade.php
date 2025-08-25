@extends('layouts.main')

@section('title', 'Tambah Produk Expired')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Tambah Produk Expired</h3>
                    </div>
                    <form action="{{ route('produk-expired.store') }}" method="POST">
                        @csrf
                        <div class="card-body">
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="form-group">
                                <label for="produk_id">Produk</label>
                                <select name="produk_id" id="produk_id" class="form-control @error('produk_id') is-invalid @enderror" required>
                                    <option value="">Pilih Produk</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" {{ old('produk_id') == $product->id ? 'selected' : '' }}>
                                            {{ $product->nama_produk }} (Stok: {{ $product->stok }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('produk_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="jumlah">Jumlah</label>
                                <input type="number" name="jumlah" id="jumlah" class="form-control @error('jumlah') is-invalid @enderror" 
                                    value="{{ old('jumlah') }}" required min="1">
                                @error('jumlah')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="tanggal_expired">Tanggal Expired</label>
                                <input type="date" name="tanggal_expired" id="tanggal_expired" 
                                    class="form-control @error('tanggal_expired') is-invalid @enderror" 
                                    value="{{ old('tanggal_expired') }}" required>
                                @error('tanggal_expired')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label for="keterangan">Keterangan</label>
                                <textarea name="keterangan" id="keterangan" rows="3" 
                                    class="form-control @error('keterangan') is-invalid @enderror">{{ old('keterangan') }}</textarea>
                                @error('keterangan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <a href="{{ route('produk-expired.index') }}" class="btn btn-secondary">Kembali</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Add client-side validation for jumlah based on available stock
    document.getElementById('produk_id').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const stockText = selectedOption.text.match(/Stok: (\d+)/);
        if (stockText) {
            const maxStock = parseInt(stockText[1]);
            const jumlahInput = document.getElementById('jumlah');
            jumlahInput.max = maxStock;
            jumlahInput.setAttribute('max', maxStock);
        }
    });
</script>
@endpush
