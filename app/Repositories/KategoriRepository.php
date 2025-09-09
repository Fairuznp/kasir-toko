<?php

namespace App\Repositories;

use App\Models\Kategori;

class KategoriRepository
{
    public function getAllKategori($search = null)
    {
        return Kategori::withCount('produks')
            ->orderBy('id')
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
        // Pastikan kategori default "Tidak Berkategori" tersedia
        $defaultKategori = $this->getOrCreateDefaultKategori();
        
        // Update semua produk yang menggunakan kategori ini ke kategori default
        \App\Models\Produk::where('kategori_id', $kategori->id)
            ->update(['kategori_id' => $defaultKategori->id]);
        
        // Hapus kategori
        return $kategori->delete();
    }

    private function getOrCreateDefaultKategori()
    {
        // Cari kategori default, jika tidak ada maka buat
        $defaultKategori = Kategori::where('nama_kategori', 'Tidak Berkategori')->first();
        
        if (!$defaultKategori) {
            $defaultKategori = Kategori::create([
                'nama_kategori' => 'Tidak Berkategori'
            ]);
        }
        
        return $defaultKategori;
    }

    public function getKategoriForSelect()
    {
        return Kategori::orderBy('nama_kategori')->get();
    }
}
