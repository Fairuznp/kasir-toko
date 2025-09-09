<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LaporanService;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    protected $laporanService;

    public function __construct(LaporanService $laporanService)
    {
        $this->laporanService = $laporanService;
    }

    public function index()
    {
        return view('laporan.form');
    }

    public function harian(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $penjualan = $this->laporanService->getLaporanHarian($tanggal);

        // Tambahkan perhitungan keuntungan/kerugian harian
        $keuntunganKerugianHarian = DB::select("
            SELECT 
                COALESCE(SUM(dp.jumlah * p.harga_jual), 0) as total_sales_revenue,
                COALESCE(SUM(dp.jumlah * p.harga_modal), 0) as total_cost_of_goods_sold,
                COALESCE((
                    SELECT SUM(pe.jumlah * p2.harga_modal)
                    FROM produk_expireds pe 
                    JOIN produks p2 ON pe.produk_id = p2.id 
                    WHERE DATE(pe.tanggal_expired) = ?
                ), 0) as total_expired_cost
            FROM detil_penjualans dp
            JOIN penjualans pj ON dp.penjualan_id = pj.id
            JOIN produks p ON dp.produk_id = p.id
            WHERE DATE(pj.tanggal) = ? AND pj.status != 'batal'
        ", [$tanggal, $tanggal]);

        $profitLoss = $keuntunganKerugianHarian[0] ?? null;
        $keuntunganKerugian = 0;
        
        if ($profitLoss) {
            $keuntunganKerugian = $profitLoss->total_sales_revenue - $profitLoss->total_cost_of_goods_sold - $profitLoss->total_expired_cost;
        }

        return view('laporan.harian', [
            'penjualan' => $penjualan,
            'tanggal' => $tanggal,
            'totalSalesRevenue' => $profitLoss->total_sales_revenue ?? 0,
            'totalCostOfGoodsSold' => $profitLoss->total_cost_of_goods_sold ?? 0,
            'totalExpiredCost' => $profitLoss->total_expired_cost ?? 0,
            'keuntunganKerugian' => $keuntunganKerugian
        ]);
    }

    public function bulanan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $data = $this->laporanService->getLaporanBulanan($bulan, $tahun);

        // Logika baru untuk keuntungan/kerugian
        
        // 1. Total Sales Revenue: Sum of total transaction amounts from penjualans
        $totalSalesRevenue = DB::table('penjualans')
            ->where('penjualans.status', '!=', 'batal')
            ->whereMonth('penjualans.tanggal', $bulan)
            ->whereYear('penjualans.tanggal', $tahun)
            ->sum('penjualans.total');

        // 2. Total Cost of Goods Sold: Sum of (quantity sold × cost price) from detil_penjualan
        $totalCostOfGoodsSold = DB::table('detil_penjualans')
            ->join('penjualans', 'detil_penjualans.penjualan_id', '=', 'penjualans.id')
            ->join('produks', 'detil_penjualans.produk_id', '=', 'produks.id')
            ->where('penjualans.status', '!=', 'batal')
            ->whereMonth('penjualans.tanggal', $bulan)
            ->whereYear('penjualans.tanggal', $tahun)
            ->sum(DB::raw('detil_penjualans.jumlah * produks.harga_modal'));

        // 3. Total Expired Product Cost: Sum of (expired quantity × cost price) from produk_expired
        $totalExpiredCost = DB::table('produk_expireds')
            ->join('produks', 'produk_expireds.produk_id', '=', 'produks.id')
            ->whereMonth('produk_expireds.tanggal_expired', $bulan)
            ->whereYear('produk_expireds.tanggal_expired', $tahun)
            ->sum(DB::raw('produk_expireds.jumlah * produks.harga_modal'));

        // 4. Profit/Loss = Total Sales Revenue - Total Cost of Goods Sold - Total Expired Product Cost
        $keuntunganKerugian = $totalSalesRevenue - $totalCostOfGoodsSold - $totalExpiredCost;

        // Total pendapatan dari penjualan (untuk kompatibilitas dengan view lama)
        $totalPendapatan = collect($data['penjualan'])->sum('jumlah_total');

        return view('laporan.bulanan', array_merge($data, [
            'totalSalesRevenue' => $totalSalesRevenue ?? 0,
            'totalCostOfGoodsSold' => $totalCostOfGoodsSold ?? 0,
            'totalExpiredCost' => $totalExpiredCost ?? 0,
            'keuntunganKerugian' => $keuntunganKerugian,
            'totalPendapatan' => $totalPendapatan,
            'tahun' => $tahun
        ]));
    }

    public function produkBulanan(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $laporanProdukBulanan = $this->laporanService->getLaporanProdukBulanan($bulan, $tahun);
        return view('laporan.produk_bulanan', [
            'laporanProdukBulanan' => $laporanProdukBulanan,
            'bulan' => $bulan,
            'tahun' => $tahun
        ]);
    }

    public function cetakProdukBulanan(Request $request)
    {
        $bulan = $request->bulan ?? date('m');
        $tahun = $request->tahun ?? date('Y');
        $laporanProdukBulanan = $this->laporanService->getLaporanProdukBulanan($bulan, $tahun);
        return view('laporan.cetak_produk_bulanan', [
            'laporanProdukBulanan' => $laporanProdukBulanan,
            'bulan' => $bulan,
            'tahun' => $tahun
        ]);
    }

    public function produkHarian(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $laporanProdukHarian = $this->laporanService->getLaporanProdukHarian($tanggal);
        return view('laporan.produk_harian', [
            'laporanProdukHarian' => $laporanProdukHarian,
            'tanggal' => $tanggal
        ]);
    }

    public function cetakProdukHarian(Request $request)
    {
        $tanggal = $request->tanggal ?? date('Y-m-d');
        $laporanProdukHarian = $this->laporanService->getLaporanProdukHarian($tanggal);
        return view('laporan.cetak_produk_harian', [
            'laporanProdukHarian' => $laporanProdukHarian,
            'tanggal' => $tanggal
        ]);
    }
}
