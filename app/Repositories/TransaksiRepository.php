<?php

namespace App\Repositories;

use App\Models\DetilPenjualan;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\User;
use App\Models\Penjualan;

class TransaksiRepository
{
    public function getAllPenjualan($search = null)
    {
        return Penjualan::join('users', 'users.id', 'penjualans.user_id')
            ->join('pelanggans', 'pelanggans.id', 'penjualans.pelanggan_id')
            ->select('penjualans.*', 'users.nama as nama_kasir', 'pelanggans.nama as nama_pelanggan')
            ->orderBy('id', 'desc')
            ->when($search, function ($q, $search) {
                return $q->where('nomor_transaksi', 'like', "%{$search}%");
            })
            ->paginate();
    }

    public function getLastPenjualan()
    {
        return Penjualan::orderBy('id', 'desc')->first();
    }

    public function createPenjualan(array $data)
    {
        return Penjualan::create($data);
    }

    public function createDetilPenjualan(array $data)
    {
        return DetilPenjualan::create($data);
    }

    public function getPelangganById($id)
    {
        return Pelanggan::find($id);
    }

    public function getUserById($id)
    {
        return User::find($id);
    }

    public function getDetilPenjualanByPenjualanId($penjualanId)
    {
        return DetilPenjualan::join('produks', 'produks.id', 'detil_penjualans.produk_id')
            ->select('detil_penjualans.*', 'nama_produk')
            ->where('penjualan_id', $penjualanId)
            ->get();
    }

    public function getDetilPenjualanForCancel($penjualanId)
    {
        return DetilPenjualan::where('penjualan_id', $penjualanId)->get();
    }

    public function updatePenjualanStatus($penjualan, $status)
    {
        return $penjualan->update(['status' => $status]);
    }

    public function searchProduk($search = null)
    {
        return Produk::select('id', 'kode_produk', 'nama_produk')
            ->when($search, function ($q, $search) {
                return $q->where('nama_produk', 'like', "%{$search}%");
            })
            ->orderBy('nama_produk')
            ->take(15)
            ->get();
    }

    public function searchPelanggan($search = null)
    {
        return Pelanggan::select('id', 'nama')
            ->when($search, function ($q, $search) {
                return $q->where('nama', 'like', "%{$search}%");
            })
            ->orderBy('nama')
            ->take(15)
            ->get();
    }
}
