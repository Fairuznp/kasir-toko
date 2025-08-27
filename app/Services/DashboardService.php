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
        $produkTerjual = $this->dashboardRepository->getProdukTerjualBulanIni();
        $user = $this->dashboardRepository->getUserCount();
        $pelanggan = $this->dashboardRepository->getPelangganCount();
        $diskon = $this->dashboardRepository->getDiscountCount();
        $kategori = $this->dashboardRepository->getKategoriCount();
        $produk = $this->dashboardRepository->getProdukCount();
        $penjualan = $this->dashboardRepository->getPenjualanThisMonth();
        $pengeluaranStok = $this->dashboardRepository->getPengeluaranStokPerBulan();

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

        // Siapkan data chart pengeluaran stok
        $labelsPengeluaran = [];
        $dataPengeluaran = [];
        foreach ($pengeluaranStok as $row) {
            $labelsPengeluaran[] = $nama_bulan[$row->bulan - 1] . ' ' . $row->tahun;
            $dataPengeluaran[] = (int) $row->total_pengeluaran;
        }

        // Siapkan data chart produk terjual
        $labelsProdukTerjual = [];
        $dataProdukTerjual = [];
        foreach ($produkTerjual as $row) {
            $labelsProdukTerjual[] = $row->nama_produk;
            $dataProdukTerjual[] = (int) $row->total_terjual;
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
            ],
            'pengeluaran_stok' => [
                'label' => 'Pengeluaran per Bulan',
                'labels' => json_encode($labelsPengeluaran),
                'data' => json_encode($dataPengeluaran)
            ],
            'produk_terjual' => [
                'label' => 'Produk Terjual Bulan Ini',
                'labels' => json_encode($labelsProdukTerjual),
                'data' => json_encode($dataProdukTerjual)
            ]
        ];
    }
}
