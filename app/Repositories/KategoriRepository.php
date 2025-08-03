<?php

namespace App\Repositories;

use App\Models\Kategori;

class KategoriRepository
{
    public function getAllKategori($search = null)
    {
        return Kategori::orderBy('id')
            ->when($search, function ($q, $search) {
                return $q->where('nama_kategori', 'like', "%{$search}%");
            })
            ->paginate();
    }

    public function createKategori(array $data)
    {
        return Kategori::create($data);
    }

    public function updateKategori($kategori, array $data)
    {
        return $kategori->update($data);
    }

    public function deleteKategori($kategori)
    {
        return $kategori->delete();
    }

    public function getKategoriForSelect()
    {
        return Kategori::orderBy('nama_kategori')->get();
    }
}
