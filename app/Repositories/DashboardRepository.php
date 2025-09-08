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
    // Pengeluaran stok per bulan
    public function getPengeluaranStokPerBulan()
    {
        $tahun = date('Y');
        $result = DB::table('stoks')
            ->join('produks', 'stoks.produk_id', '=', 'produks.id')
            ->select(
                DB::raw('MONTH(stoks.tanggal) as bulan'),
                DB::raw('YEAR(stoks.tanggal) as tahun'),
                DB::raw('SUM(stoks.jumlah * produks.harga_modal) as total_pengeluaran')
            )
            ->whereYear('stoks.tanggal', $tahun)
            ->groupBy('tahun', 'bulan')
            ->orderBy('bulan', 'asc')
            ->get();

        // Buat array semua bulan
        $dataBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataBulan[$i] = 0;
        }
        foreach ($result as $row) {
            $dataBulan[(int)$row->bulan] = (int)$row->total_pengeluaran;
        }

        // Return array bulan dan total_pengeluaran
        $output = [];
        foreach ($dataBulan as $bulan => $total) {
            $output[] = (object) [
                'bulan' => $bulan,
                'tahun' => (int)$tahun,
                'total_pengeluaran' => $total
            ];
        }
        return $output;
    }

    // Produk terjual bulan ini
    public function getProdukTerjualBulanIni()
    {
        $bulan = date('m');
        $tahun = date('Y');
        return DB::table('detil_penjualans')
            ->join('produks', 'detil_penjualans.produk_id', '=', 'produks.id')
            ->join('penjualans', 'detil_penjualans.penjualan_id', '=', 'penjualans.id')
            ->select('produks.nama_produk', DB::raw('SUM(detil_penjualans.jumlah) as total_terjual'))
            ->whereMonth('penjualans.tanggal', $bulan)
            ->whereYear('penjualans.tanggal', $tahun)
            ->groupBy('produks.nama_produk')
            ->orderByDesc('total_terjual')
            ->get();
    }

    // Keuntungan/Kerugian per bulan
    public function getKeuntunganKerugianPerBulan()
    {
        $tahun = date('Y');
        // Jumlah total transaksi (omzet) per bulan
        $result = DB::table('penjualans')
            ->select(
                DB::raw('MONTH(tanggal) as bulan'),
                DB::raw('YEAR(tanggal) as tahun'),
                DB::raw('SUM(total) as jumlah_total')
            )
            ->where('status', '!=', 'batal')
            ->whereYear('tanggal', $tahun)
            ->groupBy('tahun', 'bulan')
            ->orderBy('bulan', 'asc')
            ->get();

        $pengeluaran = $this->getPengeluaranStokPerBulan();
        $pengeluaran = array_values($pengeluaran);
        
        $dataBulan = [];
        for ($i = 1; $i <= 12; $i++) {
            $dataBulan[$i] = 0;
        }
        
        foreach ($result as $row) {
            $dataBulan[(int)$row->bulan] = (int)$row->jumlah_total;
        }
        
        $output = [];
        foreach ($dataBulan as $idx => $total) {
            $bulan = $idx;
            // Fix: pengeluaran array dimulai dari index 0, jadi kita perlu -1
            $total_pengeluaran = isset($pengeluaran[$idx - 1]) ? $pengeluaran[$idx - 1]->total_pengeluaran : 0;
            $selisih = $total - $total_pengeluaran;
            
            $output[] = (object) [
                'bulan' => $bulan,
                'tahun' => (int)$tahun,
                'total_transaksi' => $total,
                'total_pengeluaran' => $total_pengeluaran,
                'keuntungan_kerugian' => $selisih,
                'status' => $selisih >= 0 ? 'keuntungan' : 'kerugian'
            ];
        }
        return $output;
    }

    // Target harian berdasarkan pengeluaran stok dibagi jumlah hari dalam bulan
    public function getTargetHarian()
    {
        $bulanIni = date('m');
        $tahunIni = date('Y');
        $jumlahHariDalamBulan = date('t'); // Total hari dalam bulan ini
        
        // Ambil pengeluaran stok bulan ini
        $pengeluaranBulanIni = DB::table('stoks')
            ->join('produks', 'stoks.produk_id', '=', 'produks.id')
            ->select(DB::raw('SUM(stoks.jumlah * produks.harga_modal) as total_pengeluaran'))
            ->whereMonth('stoks.tanggal', $bulanIni)
            ->whereYear('stoks.tanggal', $tahunIni)
            ->first();
        
        $targetHarian = $pengeluaranBulanIni->total_pengeluaran / $jumlahHariDalamBulan;
        
        // Ambil total transaksi hari ini
        $transaksiHariIni = Penjualan::select(DB::raw('SUM(total) as total_hari_ini'))
            ->where('status', '!=', 'batal')
            ->whereDate('tanggal', date('Y-m-d'))
            ->first();
        
        $totalHariIni = $transaksiHariIni->total_hari_ini ?? 0;
        
        return [
            'target' => round($targetHarian),
            'current' => $totalHariIni,
            'percentage' => $targetHarian > 0 ? min(100, round(($totalHariIni / $targetHarian) * 100)) : 0
        ];
    }

    // Target bulanan berdasarkan pengeluaran stok
    public function getTargetBulanan()
    {
        $bulanIni = date('m');
        $tahunIni = date('Y');
        
        // Ambil pengeluaran stok bulan ini
        $pengeluaranBulanIni = DB::table('stoks')
            ->join('produks', 'stoks.produk_id', '=', 'produks.id')
            ->select(DB::raw('SUM(stoks.jumlah * produks.harga_modal) as total_pengeluaran'))
            ->whereMonth('stoks.tanggal', $bulanIni)
            ->whereYear('stoks.tanggal', $tahunIni)
            ->first();
        
        $targetBulanan = $pengeluaranBulanIni->total_pengeluaran ?? 0;
        
        // Ambil total transaksi bulan ini
        $transaksiBulanIni = Penjualan::select(DB::raw('SUM(total) as total_bulan_ini'))
            ->where('status', '!=', 'batal')
            ->whereMonth('tanggal', $bulanIni)
            ->whereYear('tanggal', $tahunIni)
            ->first();
        
        $totalBulanIni = $transaksiBulanIni->total_bulan_ini ?? 0;
        
        return [
            'target' => $targetBulanan,
            'current' => $totalBulanIni,
            'percentage' => $targetBulanan > 0 ? min(100, round(($totalBulanIni / $targetBulanan) * 100)) : 0
        ];
    }
}
