<?php

namespace App\Repositories;

use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;

class LaporanRepository
{
    public function getLaporanHarian($tanggal)
    {
        return Penjualan::join('users', 'users.id', 'penjualans.user_id')
            ->join('pelanggans', 'pelanggans.id', 'penjualans.pelanggan_id')
            ->whereDate('tanggal', $tanggal)
            ->select('penjualans.*', 'pelanggans.nama as nama_pelanggan', 'users.nama as nama_kasir')
            ->orderBy('id')
            ->get();
    }

    public function getLaporanBulanan($bulan, $tahun)
    {
        return Penjualan::select(
            DB::raw('COUNT(id) as jumlah_transaksi'),
            DB::raw('SUM(total) as jumlah_total'),
            DB::raw("DATE_FORMAT(tanggal, '%d/%m/%Y') tgl")
        )
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->groupBy('tgl')
            ->get();
    }
}
