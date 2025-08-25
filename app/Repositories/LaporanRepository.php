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
            DB::raw("DATE_FORMAT(tanggal, '%d/%m/%Y') as tgl"),
            DB::raw('COUNT(id) as jumlah_transaksi'),
            DB::raw("SUM(CASE WHEN status = 'selesai' THEN 1 ELSE 0 END) as transaksi_berhasil"),
            DB::raw("SUM(CASE WHEN status = 'batal' THEN 1 ELSE 0 END) as transaksi_batal"),
            DB::raw("SUM(CASE WHEN status != 'batal' THEN total ELSE 0 END) as jumlah_total")
        )
            ->whereMonth('tanggal', $bulan)
            ->whereYear('tanggal', $tahun)
            ->groupBy('tgl')
            ->orderBy('tgl')
            ->get();
    }
}
