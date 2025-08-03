<?php

namespace App\Services;

use App\Repositories\DashboardRepository;

class DashboardService
{
    protected $dashboardRepository;

    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    public function getDashboardData()
    {
        $user = $this->dashboardRepository->getUserCount();
        $pelanggan = $this->dashboardRepository->getPelangganCount();
        $diskon = $this->dashboardRepository->getDiscountCount();
        $kategori = $this->dashboardRepository->getKategoriCount();
        $produk = $this->dashboardRepository->getProdukCount();
        $penjualan = $this->dashboardRepository->getPenjualanThisMonth();

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

        $label = 'Transaksi ' . $nama_bulan[date('m') - 1] . ' ' . date('Y');
        $labels = [];
        $data = [];

        foreach ($penjualan as $row) {
            $labels[] = substr($row->tgl, 0, 2);
            $data[] = (int) $row->jumlah_total;
        }

        return [
            'user' => $user,
            'pelanggan' => $pelanggan,
            'kategori' => $kategori,
            'produk' => $produk,
            'diskon' => $diskon,
            'cart' => [
                'label' => $label,
                'labels' => json_encode($labels),
                'data' => json_encode($data)
            ]
        ];
    }
}
