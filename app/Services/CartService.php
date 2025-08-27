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
        $response = $cartDetails->toArray();
        if (isset($extraInfo['diskon'])) {
            $diskon = \App\Models\Diskon::find($extraInfo['diskon']['id']);
            if ($diskon) {
                $validation = $diskon->isValid($cartDetails->get('subtotal'), $cartDetails->get('items'));
                if ($validation['valid']) {
                    // Jika diskon khusus produk/kategori
                    if ($diskon->produk_id || $diskon->kategori_id) {
                        // Kurangi subtotal produk yang didiskon
                        $items = $response['items'];
                        foreach ($items as $i => $item) {
                            $produk = \App\Models\Produk::find($item['id']);
                            $isEligible = false;
                            if ($diskon->produk_id && $produk && $produk->id == $diskon->produk_id) {
                                $isEligible = true;
                            } elseif ($diskon->kategori_id && $produk && $produk->kategori_id == $diskon->kategori_id) {
                                $isEligible = true;
                            }
                            if ($isEligible && $item['quantity'] * $item['price'] >= $diskon->minimal_pembelian) {
                                $nilaiDiskon = ($item['quantity'] * $item['price']) * $diskon->jumlah_diskon / 100;
                                $response['items'][$i]['subtotal'] -= $nilaiDiskon;
                                $response['items'][$i]['diskon_applied'] = true;
                                $response['items'][$i]['harga_setelah_diskon'] = $item['price'] - ($item['price'] * $diskon->jumlah_diskon / 100);
                            } else {
                                $response['items'][$i]['diskon_applied'] = false;
                                $response['items'][$i]['harga_setelah_diskon'] = $item['price'];
                            }
                        }
                        $response['discount_amount'] = 0; // Diskon tidak ditampilkan di bawah pajak
                        // Hitung ulang subtotal
                        $response['subtotal'] = array_sum(array_column($response['items'], 'subtotal'));
                        // Pajak tetap dari subtotal baru
                        $response['total'] = $response['subtotal'] + ($response['tax_amount'] ?? 0);
                    } else {
                        // Diskon untuk semua produk
                        $discountAmount = $diskon->hitungNilaiDiskon($cartDetails->get('subtotal'), $cartDetails->get('items'));
                        $response['discount_amount'] = $discountAmount;
                        if ($discountAmount > 0) {
                            $response['total'] = $response['total'] - $discountAmount;
                        }
                    }
                } else {
                    // Diskon tidak valid lagi, hapus dari extra_info
                    unset($extraInfo['diskon']);
                    $cart->setExtraInfo($extraInfo);
                }
            }
        } else {
            $response['discount_amount'] = 0;
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
            'price' => $produk->harga_jual
        ]);

        return [
            'kode_produk' => $kodeProduk,
            'nama_produk' => $produk->nama_produk,
            'quantity' => $qty,
            'harga' => $produk->harga_jual
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
