<?php

namespace App\Services;

use App\Repositories\ProdukRepository;
use App\Repositories\KategoriRepository;

class ProdukService
{
    protected $produkRepository;
    protected $kategoriRepository;

    public function __construct(
        ProdukRepository $produkRepository,
        KategoriRepository $kategoriRepository
    ) {
        $this->produkRepository = $produkRepository;
        $this->kategoriRepository = $kategoriRepository;
    }

    public function getAllProduk($search = null)
    {
        $produks = $this->produkRepository->getAllProdukWithKategori($search, func_num_args() > 1 ? func_get_arg(1) : null);

        if ($search) {
            $produks->appends(['search' => $search]);
        }
        if (func_num_args() > 1 && func_get_arg(1)) {
            $produks->appends(['search_exact' => func_get_arg(1)]);
        }
        return $produks;
    }

    public function getKategoriForCreate()
    {
        $dataKategori = $this->kategoriRepository->getKategoriForSelect();

        $kategoris = [];
        // Opsi default
        $kategoris[] = ['', '-- Pilih Kategori --'];

        foreach ($dataKategori as $kategori) {
            $kategoris[] = [$kategori->id, $kategori->nama_kategori];
        }

        return $kategoris;
    }

    public function createProduk(array $data)
    {
        // Ensure harga_modal and harga_jual are set
        $data['harga_modal'] = $data['harga_modal'] ?? 0;
        $data['harga_jual'] = $data['harga_jual'] ?? 0;

        return $this->produkRepository->createProduk($data);
    }

    public function updateProduk($produk, array $data)
    {
        // Ensure harga_modal and harga_jual are set
        $data['harga_modal'] = $data['harga_modal'] ?? $produk->harga_modal;
        $data['harga_jual'] = $data['harga_jual'] ?? $produk->harga_jual;

        return $this->produkRepository->updateProduk($produk, $data);
    }

    public function deleteProduk($produk)
    {
        return $this->produkRepository->deleteProduk($produk);
    }
}
