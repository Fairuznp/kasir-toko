<?php

namespace App\Services;

use App\Repositories\PelangganRepository;

class PelangganService
{
    protected $pelangganRepository;

    public function __construct(PelangganRepository $pelangganRepository)
    {
        $this->pelangganRepository = $pelangganRepository;
    }

    public function getAllPelanggan($search = null)
    {
        $pelanggans = $this->pelangganRepository->getAllPelanggan($search);

        if ($search) {
            $pelanggans->appends(['search' => $search]);
        }

        return $pelanggans;
    }

    public function createPelanggan(array $data)
    {
        return $this->pelangganRepository->createPelanggan($data);
    }

    public function updatePelanggan($pelanggan, array $data)
    {
        return $this->pelangganRepository->updatePelanggan($pelanggan, $data);
    }

    public function deletePelanggan($pelanggan)
    {
        return $this->pelangganRepository->deletePelanggan($pelanggan);
    }
}
