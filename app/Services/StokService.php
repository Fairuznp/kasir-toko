<?php

namespace App\Services;

use App\Repositories\StokRepository;

class StokService
{
    protected $stokRepository;

    public function __construct(StokRepository $stokRepository)
    {
        $this->stokRepository = $stokRepository;
    }

    public function getAllStok($search = null)
    {
        $stoks = $this->stokRepository->getAllStok($search);

        if ($search) {
            $stoks->appends(['search' => $search]);
        }

        return $stoks;
    }

    public function searchProduk($search)
    {
        return $this->stokRepository->searchProduk($search);
    }

    public function createStok(array $data)
    {
        // Merge tanggal hari ini
        $data['tanggal'] = date('Y-m-d');

        // Buat stok baru
        $stok = $this->stokRepository->createStok($data);

        // Update stok produk
        $produk = $this->stokRepository->getProdukById($data['produk_id']);
        $this->stokRepository->updateProdukStok($produk, $produk->stok + $data['jumlah']);

        return $stok;
    }

    public function deleteStok($stok)
    {
        // Update stok produk sebelum hapus
        $produk = $this->stokRepository->getProdukById($stok->produk_id);
        $this->stokRepository->updateProdukStok($produk, $produk->stok - $stok->jumlah);

        // Hapus stok
        return $this->stokRepository->deleteStok($stok);
    }
}
