<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LaporanService;

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
        $penjualan = $this->laporanService->getLaporanHarian($request->tanggal);

        return view('laporan.harian', [
            'penjualan' => $penjualan
        ]);
    }

    public function bulanan(Request $request)
    {
        $bulan = $request->bulan;
        $tahun = $request->tahun;
        $data = $this->laporanService->getLaporanBulanan($bulan, $tahun);
        $keuntunganKerugian = $this->laporanService->getKeuntunganKerugianBulanan($bulan, $tahun);
        return view('laporan.bulanan', array_merge($data, [
            'keuntunganKerugian' => $keuntunganKerugian,
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
}
