<?php

namespace App\Repositories;

use App\Models\Penjualan;
use Illuminate\Support\Facades\DB;

class LaporanRepository
{
    public function getLaporanHarian($tanggal)
    {
        return Penjualan::join('users', 'users.id', 'penjualans.user_id')
            ->leftJoin('pelanggans', 'pelanggans.id', 'penjualans.pelanggan_id')
            ->whereDate('tanggal', $tanggal)
            ->where('penjualans.status', '!=', 'batal')
            ->select('penjualans.*', DB::raw("COALESCE(pelanggans.nama, 'Anonymous') as nama_pelanggan"), 'users.nama as nama_kasir')
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
    // Laporan produk terjual per bulan
    public function getLaporanProdukBulanan($bulan, $tahun)
    {
        return DB::table('penjualans')
            ->select(
                DB::raw('"Total Penjualan" as nama_produk'),
                DB::raw('SUM(penjualans.total) as total_terjual')
            )
            ->whereMonth('penjualans.tanggal', $bulan)
            ->whereYear('penjualans.tanggal', $tahun)
            ->where('penjualans.status', '!=', 'batal')
            ->groupBy('penjualans.tanggal')
            ->orderByDesc('total_terjual')
            ->get();
    }

    // Laporan produk terjual per hari
    public function getLaporanProdukHarian($tanggal)
    {
        return DB::table('detil_penjualans')
            ->join('produks', 'detil_penjualans.produk_id', '=', 'produks.id')
            ->join('kategoris', 'produks.kategori_id', '=', 'kategoris.id')
            ->join('penjualans', 'detil_penjualans.penjualan_id', '=', 'penjualans.id')
            ->select(
                'produks.nama_produk',
                'kategoris.nama_kategori',
                'produks.harga_jual',
                DB::raw('SUM(detil_penjualans.jumlah) as total_terjual'),
                DB::raw('SUM(detil_penjualans.subtotal) as total_pendapatan')
            )
            ->whereDate('penjualans.tanggal', $tanggal)
            ->where('penjualans.status', '!=', 'batal')
            ->groupBy('produks.id', 'produks.nama_produk', 'kategoris.nama_kategori', 'produks.harga_jual')
            ->orderByDesc('total_terjual')
            ->get();
    }
}
