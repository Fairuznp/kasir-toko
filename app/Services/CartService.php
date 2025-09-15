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
        
        // Inisialisasi diskon_applied dan harga_setelah_diskon untuk semua item
        if (isset($response['items']) && is_array($response['items'])) {
            foreach ($response['items'] as $i => $item) {
                $response['items'][$i]['diskon_applied'] = false;
                $response['items'][$i]['harga_setelah_diskon'] = $item['price'];
                $response['items'][$i]['original_subtotal'] = $item['subtotal'];
            }
        }

        if (isset($extraInfo['diskons']) && is_array($extraInfo['diskons'])) {
            // Kelompokkan diskon berdasarkan tipe
            $diskonProduk = [];
            $diskonKategori = [];
            $diskonGlobal = [];

            foreach ($extraInfo['diskons'] as $diskonData) {
                $diskon = \App\Models\Diskon::find($diskonData['id']);
                if (!$diskon) continue;

                $validation = $diskon->isValid($cartDetails->get('subtotal'), $cartDetails->get('items'));
                if (!$validation['valid']) continue;

                if ($diskon->produk_id) {
                    $diskonProduk[$diskon->produk_id][] = $diskon;
                } elseif ($diskon->kategori_id) {
                    $diskonKategori[$diskon->kategori_id][] = $diskon;
                } else {
                    $diskonGlobal[] = $diskon;
                }
            }

            // Hitung diskon per item
            $totalDiskonAmount = 0;
            if (isset($response['items']) && is_array($response['items'])) {
                foreach ($response['items'] as $i => $item) {
                    $produk = \App\Models\Produk::find($item['id']);
                    if (!$produk) continue;

                    $itemSubtotal = $item['quantity'] * $item['price'];
                    $itemDiskon = 0;

                    // 1. Cek diskon produk (ambil yang terbesar)
                    if (isset($diskonProduk[$produk->id])) {
                        $maxDiskon = 0;
                        foreach ($diskonProduk[$produk->id] as $diskon) {
                            $nilaiDiskon = $diskon->hitungNilaiDiskonItem($itemSubtotal);
                            if ($nilaiDiskon > $maxDiskon) {
                                $maxDiskon = $nilaiDiskon;
                            }
                        }
                        $itemDiskon += $maxDiskon;
                    }

                    // 2. Cek diskon kategori (ambil yang terbesar)
                    if (isset($diskonKategori[$produk->kategori_id])) {
                        $maxDiskon = 0;
                        foreach ($diskonKategori[$produk->kategori_id] as $diskon) {
                            $nilaiDiskon = $diskon->hitungNilaiDiskonItem($itemSubtotal);
                            if ($nilaiDiskon > $maxDiskon) {
                                $maxDiskon = $nilaiDiskon;
                            }
                        }
                        $itemDiskon += $maxDiskon;
                    }

                    // 3. Cek diskon global (ambil yang terbesar)
                    if (!empty($diskonGlobal)) {
                        $maxDiskon = 0;
                        foreach ($diskonGlobal as $diskon) {
                            $nilaiDiskon = $diskon->hitungNilaiDiskonItem($itemSubtotal);
                            if ($nilaiDiskon > $maxDiskon) {
                                $maxDiskon = $nilaiDiskon;
                            }
                        }
                        $itemDiskon += $maxDiskon;
                    }

                    // Update item dengan diskon
                    if ($itemDiskon > 0) {
                        $response['items'][$i]['subtotal'] = $itemSubtotal - $itemDiskon;
                        $response['items'][$i]['diskon_applied'] = true;
                        $response['items'][$i]['harga_setelah_diskon'] = $item['price'] - ($itemDiskon / $item['quantity']);
                        $totalDiskonAmount += $itemDiskon;
                    }
                }
            }

            $response['discount_amount'] = $totalDiskonAmount;
            
            // Hitung ulang subtotal dan total
            if ($totalDiskonAmount > 0) {
                $newSubtotal = array_sum(array_column($response['items'], 'subtotal'));
                $response['subtotal'] = $newSubtotal;
                $response['total'] = $newSubtotal + ($response['tax_amount'] ?? 0);
            }
        } else {
            $response['discount_amount'] = 0;
        }

        // Tambahkan informasi stok dan kategori untuk setiap item
        if (isset($response['items']) && is_array($response['items'])) {
            foreach ($response['items'] as $i => $item) {
                $produk = $this->produkRepository->getProdukById($item['id']);
                $response['items'][$i]['stok'] = $produk ? $produk->stok : 0;
                $response['items'][$i]['nama_kategori'] = $produk && $produk->kategori ? $produk->kategori->nama_kategori : '-';
            }
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
        
        // Cek quantity yang sudah ada di cart untuk produk ini
        $existingQty = 0;
        $cartItems = $cart->getDetails()->get('items');
        
        if ($cartItems) {
            foreach ($cartItems as $item) {
                if ($item['id'] == $produk->id) {
                    $existingQty = $item['quantity'];
                    break;
                }
            }
        }
        
        // Total quantity yang akan ada setelah penambahan
        $totalQty = $existingQty + $qty;
        
        // Validasi stok
        if ($totalQty > $produk->stok) {
            $sisaStok = $produk->stok - $existingQty;
            if ($sisaStok <= 0) {
                throw new \Exception('Stok produk sudah habis di keranjang.');
            } else {
                throw new \Exception("Stok tidak mencukupi. Sisa stok yang bisa ditambahkan: {$sisaStok}");
            }
        }

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

        // Ambil data produk untuk validasi stok
        $produk = $this->produkRepository->getProdukById($item->getId());
        
        if (!$produk) {
            throw new \Exception('Produk tidak ditemukan');
        }

        // Hitung quantity baru
        $newQuantity = $item->getQuantity() + $qty;

        // Validasi quantity tidak boleh kurang dari 0
        if ($newQuantity <= 0) {
            throw new \Exception('Quantity tidak boleh kurang dari 1');
        }

        // Validasi stok
        if ($newQuantity > $produk->stok) {
            throw new \Exception("Stok tidak mencukupi. Stok tersedia: {$produk->stok}");
        }

        $cart->updateItem($item->getHash(), [
            'quantity' => $newQuantity
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
