<?php

namespace App\Services;

use App\Repositories\TransaksiRepository;
use App\Repositories\ProdukRepository;
use App\Models\Diskon;
use App\Models\Produk;
use Jackiedo\Cart\Facades\Cart;

class TransaksiService
{
    protected $transaksiRepository;
    protected $produkRepository;

    public function __construct(
        TransaksiRepository $transaksiRepository,
        ProdukRepository $produkRepository
    ) {
        $this->transaksiRepository = $transaksiRepository;
        $this->produkRepository = $produkRepository;
    }

    public function getAllTransaksi($search = null)
    {
        $penjualans = $this->transaksiRepository->getAllPenjualan($search);

        if ($search) {
            $penjualans->appends(['search' => $search]);
        }

        return $penjualans;
    }

    public function validateStock($allItems)
    {
        foreach ($allItems as $item) {
            $produk = Produk::find($item->id);
            if (!$produk || $produk->stok < $item->quantity) {
                throw new \Exception('Stok produk "' . ($produk->nama_produk ?? 'Unknown') . '" tidak mencukupi.');
            }
        }
    }

    public function calculateDiscount($extraInfo, $subtotal, $allItems)
    {
        $diskonId = null;
        $nilaiDiskon = 0;

        if (isset($extraInfo['diskon'])) {
            $diskon = Diskon::find($extraInfo['diskon']['id']);
            if ($diskon) {
                // PERBAIKAN: Validasi berdasarkan subtotal yang memenuhi syarat
                $eligibleSubtotal = $diskon->getEligibleSubtotal($allItems);

                // Untuk validasi minimal pembelian, gunakan total subtotal
                $validation = $diskon->isValid($subtotal, $allItems);

                if ($validation['valid']) {
                    $diskonId = $diskon->id;
                    // PERBAIKAN: Hitung diskon berdasarkan items yang memenuhi syarat
                    $nilaiDiskon = $diskon->hitungNilaiDiskon($subtotal, $allItems);
                }
            }
        }

        return ['diskon_id' => $diskonId, 'nilai_diskon' => $nilaiDiskon];
    }

    public function generateTransactionNumber()
    {
        $lastPenjualan = $this->transaksiRepository->getLastPenjualan();
        $no = $lastPenjualan ? $lastPenjualan->id + 1 : 1;
        $no = sprintf("%04d", $no);
        return date('Ymd') . $no;
    }

    public function createTransaction($user, $cart, $request, $discount)
    {
        $cartDetails = $cart->getDetails();
        $extraInfo = $cart->getExtraInfo();
        $total = $cartDetails->get('total');
        $allItems = $cartDetails->get('items');

        $totalFinal = $total - $discount['nilai_diskon'];
        $kembalian = $request->cash - $totalFinal;

        if ($kembalian < 0) {
            throw new \Exception('Cash tidak mencukupi');
        }

        $nomorTransaksi = $this->generateTransactionNumber();

        // PERBAIKAN: Pastikan pelanggan_id tidak null
        $pelangganId = null;
        if (isset($extraInfo['pelanggan']['id'])) {
            $pelangganId = $extraInfo['pelanggan']['id'];
        } elseif (is_array($cart->getExtraInfo('pelanggan'))) {
            $pelangganId = $cart->getExtraInfo('pelanggan')['id'] ?? null;
        }

        if (!$pelangganId) {
            throw new \Exception('Pelanggan belum dipilih');
        }

        $penjualan = $this->transaksiRepository->createPenjualan([
            'user_id' => $user->id,
            'pelanggan_id' => $pelangganId,
            'nomor_transaksi' => $nomorTransaksi,
            'tanggal' => now(),
            'total' => (int) $totalFinal,
            'tunai' => (int) $request->cash,
            'kembalian' => (int) $kembalian,
            'pajak' => (int) $cartDetails->get('tax_amount'),
            'subtotal' => (int) $cartDetails->get('subtotal'),
            'diskon_id' => $discount['diskon_id'],
            'nilai_diskon' => (int) $discount['nilai_diskon'],
        ]);

        foreach ($allItems as $item) {
            $this->transaksiRepository->createDetilPenjualan([
                'penjualan_id' => $penjualan->id,
                'produk_id' => $item->id,
                'jumlah' => $item->quantity,
                'harga_produk' => $item->price,
                'subtotal' => $item->subtotal,
            ]);

            $this->produkRepository->updateStock($item->id, -$item->quantity);
        }

        $cart->destroy();

        return $penjualan;
    }

    public function getTransactionDetails($transaksi)
    {
        return [
            'penjualan' => $transaksi,
            'pelanggan' => $this->transaksiRepository->getPelangganById($transaksi->pelanggan_id),
            'user' => $this->transaksiRepository->getUserById($transaksi->user_id),
            'detilPenjualan' => $this->transaksiRepository->getDetilPenjualanByPenjualanId($transaksi->id)
        ];
    }

    public function cancelTransaction($transaksi)
    {
        if ($transaksi->status == "batal") {
            return;
        }

        $detail = $this->transaksiRepository->getDetilPenjualanForCancel($transaksi->id);

        foreach ($detail as $item) {
            $this->produkRepository->updateStock($item->produk_id, $item->jumlah);
        }

        $this->transaksiRepository->updatePenjualanStatus($transaksi, 'batal');
    }

    public function searchProduk($search = null)
    {
        return $this->transaksiRepository->searchProduk($search);
    }

    public function searchPelanggan($search = null)
    {
        return $this->transaksiRepository->searchPelanggan($search);
    }

    public function addPelangganToCart($pelangganId, $userId)
    {
        $pelanggan = $this->transaksiRepository->getPelangganById($pelangganId);

        if (!$pelanggan) {
            throw new \Exception('Pelanggan tidak ditemukan');
        }

        $cart = Cart::name($userId);

        $cart->setExtraInfo([
            'pelanggan' => [
                'id' => $pelanggan->id,
                'nama' => $pelanggan->nama,
            ]
        ]);

        return $pelanggan;
    }

    /**
     * TAMBAHAN: Method untuk menerapkan diskon ke cart
     */
    public function applyDiscountToCart($diskonId, $userId)
    {
        $diskon = Diskon::find($diskonId);
        if (!$diskon) {
            throw new \Exception('Diskon tidak ditemukan');
        }

        $cart = Cart::name($userId);
        $cartDetails = $cart->getDetails();
        $allItems = $cartDetails->get('items');
        $subtotal = $cartDetails->get('subtotal');

        $validation = $diskon->isValid($subtotal, $allItems);
        if (!$validation['valid']) {
            throw new \Exception($validation['message']);
        }

        $extraInfo = $cart->getExtraInfo();
        $extraInfo['diskon'] = [
            'id' => $diskon->id,
            'kode_diskon' => $diskon->kode_diskon,
            'jenis_diskon' => $diskon->jenis_diskon,
            'jumlah_diskon' => $diskon->jumlah_diskon,
        ];

        $cart->setExtraInfo($extraInfo);

        return $diskon;
    }

    /**
     * TAMBAHAN: Method untuk menghapus diskon dari cart
     */
    public function removeDiscountFromCart($userId)
    {
        $cart = Cart::name($userId);
        $extraInfo = $cart->getExtraInfo();

        if (isset($extraInfo['diskon'])) {
            unset($extraInfo['diskon']);
            $cart->setExtraInfo($extraInfo);
        }
    }
}
