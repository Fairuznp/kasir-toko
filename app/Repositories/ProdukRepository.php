<?php

namespace App\Repositories;

use App\Models\Produk;

class ProdukRepository
{
    public function updateStock($produkId, $quantity)
    {
        $produk = Produk::find($produkId);
        if ($produk) {
            $produk->stok += $quantity;
            $produk->save();
        }
        return $produk;
    }

    public function getProdukById($id)
    {
        return Produk::find($id);
    }

    public function getAllProduk($search = null)
    {
        return Produk::join('kategoris', 'kategoris.id', 'produks.kategori_id')
            ->orderBy('produks.id')
            ->select('produks.*', 'nama_kategori')
            ->when($search, function ($q, $search) {
                return $q->where('kode_produk', 'like', "%{$search}%")
                    ->orWhere('nama_produk', 'like', "%{$search}%");
            })
            ->paginate();
    }

    public function createProduk(array $data)
    {
        return Produk::create($data);
    }

    public function updateProduk($produk, array $data)
    {
        return $produk->update($data);
    }

    public function deleteProduk($produk)
    {
        return $produk->delete();
    }

    public function getProdukByKode($kodeProduk)
    {
        return Produk::where('kode_produk', $kodeProduk)->first();
    }

    public function getAllProdukWithKategori($search = null)
    {
        return Produk::join('kategoris', 'kategoris.id', 'produks.kategori_id')
            ->orderBy('produks.id')
            ->select('produks.*', 'nama_kategori')
            ->when($search, function ($q, $search) {
                return $q->where('kode_produk', 'like', "%{$search}%")
                    ->orWhere('nama_produk', 'like', "%{$search}%");
            })
            ->paginate();
    }
}
