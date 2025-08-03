<?php

namespace App\Services;

use App\Repositories\LaporanRepository;

class LaporanService
{
    protected $laporanRepository;

    public function __construct(LaporanRepository $laporanRepository)
    {
        $this->laporanRepository = $laporanRepository;
    }

    public function getLaporanHarian($tanggal)
    {
        return $this->laporanRepository->getLaporanHarian($tanggal);
    }

    public function getLaporanBulanan($bulan, $tahun)
    {
        $penjualan = $this->laporanRepository->getLaporanBulanan($bulan, $tahun);

        $nama_bulan = [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember'
        ];

        $bulanNama = isset($nama_bulan[$bulan - 1]) ? $nama_bulan[$bulan - 1] : null;

        return [
            'penjualan' => $penjualan,
            'bulan' => $bulanNama
        ];
    }
}
