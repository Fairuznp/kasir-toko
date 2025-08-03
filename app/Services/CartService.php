<?php

namespace App\Services;

use App\Repositories\ProdukRepository;
use Jackiedo\Cart\Facades\Cart;

class CartService
{
    protected $produkRepository;

    public function __construct(ProdukRepository $produkRepository)
    {
        $this->produkRepository = $produkRepository;
    }

    public function getCartDetails($userId)
    {
        $cart = Cart::name($userId);

        $cart->applyTax([
            'id' => 1,
            'rate' => 10,
            'title' => 'Pajak PPN 10%'
        ]);

        $cartDetails = $cart->getDetails();
        $extraInfo = $cart->getExtraInfo();

        // Hitung diskon jika ada dan valid
        $discountAmount = 0;
        if (isset($extraInfo['diskon'])) {
            $diskon = \App\Models\Diskon::find($extraInfo['diskon']['id']);

            if ($diskon) {
                $validation = $diskon->isValid($cartDetails->get('subtotal'), $cartDetails->get('items'));

                if ($validation['valid']) {
                    $discountAmount = $diskon->hitungNilaiDiskon($cartDetails->get('subtotal'), $cartDetails->get('items'));
                } else {
                    // Diskon tidak valid lagi, hapus dari extra_info
                    unset($extraInfo['diskon']);
                    $cart->setExtraInfo($extraInfo);
                }
            }
        }

        // Buat response dengan discount_amount
        $response = $cartDetails->toArray();
        $response['discount_amount'] = $discountAmount;

        // Hitung ulang total setelah diskon
        if ($discountAmount > 0) {
            $response['total'] = $response['total'] - $discountAmount;
        }

        return $response;
    }

    public function addItemToCart($kodeProduk, $quantity, $userId)
    {
        // Ambil produk dari database
        $produk = $this->produkRepository->getProdukByKode($kodeProduk);

        if (!$produk) {
            throw new \Exception('Produk tidak ditemukan.');
        }

        // Ambil quantity dari input atau default ke 1
        $qty = (int) $quantity ?: 1;
        $qty = max(1, $qty); // Untuk berjaga-jaga

        // Ambil cart berdasarkan user ID
        $cart = Cart::name($userId);

        // Tambahkan ke cart
        $cart->addItem([
            'id' => $produk->id,
            'title' => $produk->nama_produk,
            'quantity' => $qty,
            'price' => $produk->harga
        ]);

        return [
            'kode_produk' => $kodeProduk,
            'nama_produk' => $produk->nama_produk,
            'quantity' => $qty,
            'harga' => $produk->harga
        ];
    }

    public function updateCartItem($hash, $qty, $userId)
    {
        $cart = Cart::name($userId);
        $item = $cart->getItem($hash);

        if (!$item) {
            throw new \Exception('Item tidak ditemukan');
        }

        $cart->updateItem($item->getHash(), [
            'quantity' => $item->getQuantity() + $qty
        ]);

        return $item;
    }

    public function removeCartItem($hash, $userId)
    {
        $cart = Cart::name($userId);
        $cart->removeItem($hash);
    }

    public function clearCart($userId)
    {
        $cart = Cart::name($userId);
        $cart->destroy();
    }
}
