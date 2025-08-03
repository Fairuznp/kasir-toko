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
        $produks = $this->produkRepository->getAllProdukWithKategori($search);

        if ($search) {
            $produks->appends(['search' => $search]);
        }

        return $produks;
    }

    public function getKategoriForCreate()
    {
        $dataKategori = $this->kategoriRepository->getKategoriForSelect();

        $kategoris = [
            '' => 'Pilih Kategori:'
        ];

        foreach ($dataKategori as $kategori) {
            $kategoris[] = [$kategori->id, $kategori->nama_kategori];
        }

        return $kategoris;
    }

    public function createProduk(array $data)
    {
        return $this->produkRepository->createProduk($data);
    }

    public function updateProduk($produk, array $data)
    {
        return $this->produkRepository->updateProduk($produk, $data);
    }

    public function deleteProduk($produk)
    {
        return $this->produkRepository->deleteProduk($produk);
    }
}
