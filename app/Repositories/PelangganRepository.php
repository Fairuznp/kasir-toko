<?php

namespace App\Repositories;

use App\Models\Pelanggan;

class PelangganRepository
{
    public function getAllPelanggan($search = null)
    {
        return Pelanggan::orderBy('id')
            ->when($search, function ($q, $search) {
                return $q->where('nama', 'like', "%{$search}%");
            })
            ->paginate();
    }

    public function createPelanggan(array $data)
    {
        return Pelanggan::create($data);
    }

    public function updatePelanggan($pelanggan, array $data)
    {
        return $pelanggan->update($data);
    }

    public function deletePelanggan($pelanggan)
    {
        return $pelanggan->delete();
    }

    public function getPelangganById($id)
    {
        return Pelanggan::find($id);
    }
}
