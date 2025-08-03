<?php

namespace App\Services;

use App\Repositories\KategoriRepository;

class KategoriService
{
    protected $kategoriRepository;

    public function __construct(KategoriRepository $kategoriRepository)
    {
        $this->kategoriRepository = $kategoriRepository;
    }

    public function getAllKategori($search = null)
    {
        $kategoris = $this->kategoriRepository->getAllKategori($search);

        if ($search) {
            $kategoris->appends(['search' => $search]);
        }

        return $kategoris;
    }

    public function createKategori(array $data)
    {
        return $this->kategoriRepository->createKategori($data);
    }

    public function updateKategori($kategori, array $data)
    {
        return $this->kategoriRepository->updateKategori($kategori, $data);
    }

    public function deleteKategori($kategori)
    {
        return $this->kategoriRepository->deleteKategori($kategori);
    }

    public function getKategoriForSelect()
    {
        return $this->kategoriRepository->getKategoriForSelect();
    }
}
