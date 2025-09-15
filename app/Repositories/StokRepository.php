<?php

namespace App\Repositories;

use App\Models\Stok;
use App\Models\Produk;

class StokRepository
{
    public function getAllStok($search = null)
    {
        return Stok::join('produks', 'produks.id', 'stoks.produk_id')
            ->select('stoks.*', 'nama_produk')
            ->orderBy('stoks.id', 'desc')
            ->when($search, function ($q, $search) {
                return $q->where('tanggal', 'like', "%{$search}%");
            })
            ->paginate();
    }

    public function searchProduk($search)
    {
        return Produk::join('kategoris', 'kategoris.id', '=', 'produks.kategori_id')
            ->select('produks.id', 'produks.nama_produk', 'produks.kode_produk', 'produks.stok', 'kategoris.nama_kategori')
            ->where('produks.nama_produk', 'like', "%{$search}%")
            ->take(15)
            ->orderBy('produks.nama_produk')
            ->get();
    }

    public function createStok(array $data)
    {
        return Stok::create($data);
    }

    public function deleteStok($stok)
    {
        return $stok->delete();
    }

    public function getProdukById($id)
    {
        return Produk::find($id);
    }

    public function updateProdukStok($produk, $stok)
    {
        return $produk->update(['stok' => $stok]);
    }
}
