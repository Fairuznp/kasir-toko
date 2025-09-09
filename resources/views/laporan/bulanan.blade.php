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
        <th colspan="5">Jumlah Total Transaksi</th>
        <th>{{ 'Rp ' . number_format($penjualan->sum('jumlah_total'), 0, ',', '.') }}</th>
    </tr>
    <tr style="background-color: #e8f5e8;">
        <th colspan="5">Total Pendapatan Penjualan (Qty × Harga Jual)</th>
        <th style="color: #28a745; font-weight: bold;">
            {{ 'Rp ' . number_format($totalSalesRevenue, 0, ',', '.') }}
        </th>
    </tr>
    <tr style="background-color: #fff3cd;">
        <th colspan="5">Total Harga Pokok Penjualan (Qty × Harga Modal)</th>
        <th style="color: #856404; font-weight: bold;">
            {{ 'Rp ' . number_format($totalCostOfGoodsSold, 0, ',', '.') }}
        </th>
    </tr>
    <tr style="background-color: #f8d7da;">
        <th colspan="5">Total Kerugian Produk Expired (Qty × Harga Modal)</th>
        <th style="color: #721c24; font-weight: bold;">
            {{ 'Rp ' . number_format($totalExpiredCost, 0, ',', '.') }}
        </th>
    </tr>
    <tr style="background-color: #d1ecf1; border-top: 2px solid #bee5eb;">
        <th colspan="5" style="font-size: 16px;">
            <strong>KEUNTUNGAN/KERUGIAN BERSIH</strong><br>
            <small style="font-weight: normal; font-style: italic;">
                (Pendapatan - Harga Pokok - Expired)
            </small>
        </th>
        <th style="font-size: 16px;">
            <span style="color: {{ $keuntunganKerugian < 0 ? '#dc3545' : '#28a745' }}; font-weight: bold;">
                {{ 'Rp ' . number_format($keuntunganKerugian, 0, ',', '.') }}
            </span>
        </th>
    </tr>
</tfoot>

</table>
@endsection