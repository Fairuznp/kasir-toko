<?php

namespace App\Repositories;

use App\Models\Kategori;
use App\Models\Pelanggan;
use App\Models\Penjualan;
use App\Models\Produk;
use App\Models\Diskon;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function getUserCount()
    {
        return User::selectRaw('count(*) as jumlah')->first();
    }

    public function getPelangganCount()
    {
        return Pelanggan::selectRaw('count(*) as jumlah')->first();
    }

    public function getKategoriCount()
    {
        return Kategori::selectRaw('count(*) as jumlah')->first();
    }

    public function getProdukCount()
    {
        return Produk::selectRaw('count(*) as jumlah')->first();
    }

    public function getDiscountCount()
    {
        return Diskon::selectRaw('count(*) as jumlah')->first();
    }

    public function getPenjualanThisMonth()
    {
        return Penjualan::select(
            DB::raw('SUM(total) as jumlah_total'),
            DB::raw("DATE_FORMAT(tanggal, '%d/%m/%Y') tgl")
        )
            ->where('status', '!=', 'batal') // Filter transaksi batal tidak dihitung
            ->whereMonth('tanggal', date('m'))
            ->whereYear('tanggal', date('Y'))
            ->groupBy('tgl')
            ->get();
    }
}
