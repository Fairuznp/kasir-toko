@extends('layouts.laporan', ['title' => 'Laporan Bulanan'])
@section('content')

<h1 class="text-center">Laporan Bulanan</h1>

<p>Bulan : {{ $bulan }} {{ request()->tahun }}</p>

<table class="table table-bordered table-sm">
    <thead>
    <tr>
        <th>No</th>
        <th>Tanggal</th>
        <th>Jumlah Transaksi</th>
        <th>Transaksi Berhasil</th>
        <th>Transaksi Batal</th>
        <th>Total</th>
    </tr>
</thead>
<tbody>
    @foreach ($penjualan as $key => $row)
        <tr>
            <td>{{ $key + 1 }}</td>
            <td>{{ $row->tgl }}</td>
            <td>{{ $row->jumlah_transaksi }}</td>
            <td>{{ $row->transaksi_berhasil }}</td>
            <td>{{ $row->transaksi_batal }}</td>
            <td>{{ number_format($row->jumlah_total, 0, ',', '.') }}</td>
        </tr>
    @endforeach
</tbody>
<tfoot>
    <tr>
        <th colspan="5">Jumlah Total</th>
        <th>{{ number_format($penjualan->sum('jumlah_total'), 0, ',', '.') }}</th>
    </tr>
    <tr>
        <th colspan="5">Total Pengeluaran</th>
        <th>{{ number_format($keuntunganKerugian['total_pengeluaran'] ?? 0, 0, ',', '.') }}</th>
    </tr>
    <tr>
        <th colspan="5">Keuntungan/Kerugian</th>
        <th>{{ number_format($keuntunganKerugian['keuntungan_kerugian'] ?? 0, 0, ',', '.') }}</th>
    </tr>
</tfoot>

</table>
@endsection