<?php

namespace App\Repositories;

use App\Models\Diskon;
use App\Models\Kategori;
use App\Models\Produk;

class DiskonRepository
{
    public function getAllDiskon($search = null)
    {
        return Diskon::with(['kategori', 'produk'])
            ->when($search, function ($q, $search) {
                return $q->where('kode_diskon', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate();
    }

    public function getKategoriForSelect()
    {
        return Kategori::select('id', 'nama_kategori')->get();
    }

    public function getProdukForSelect()
    {
        return Produk::select('id', 'nama_produk')->get();
    }

    public function createDiskon(array $data)
    {
        return Diskon::create($data);
    }

    public function updateDiskon($diskon, array $data)
    {
        return $diskon->update($data);
    }

    public function deleteDiskon($diskon)
    {
        return $diskon->delete();
    }

    public function getDiskonByKode($kodeDiskon)
    {
        return Diskon::where('kode_diskon', $kodeDiskon)->first();
    }
}
