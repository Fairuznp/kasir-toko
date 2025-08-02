<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pelanggan;
use App\Models\Produk;
use Jackiedo\Cart\Facades\Cart;

class CartController extends Controller
{
    public function index(Request $request)
    {
        $cart = Cart::name($request->user()->id);

        $cart->applyTax([
            'id' => 1,
            'rate' => 10,
            'title' => 'Pajak PPN 10%'
        ]);

        $cartDetails = $cart->getDetails();
        $extraInfo = $cart->getExtraInfo();

        // Hitung diskon jika ada
        $discountAmount = 0;
        if (isset($extraInfo['diskon'])) {
            $discountAmount = $extraInfo['diskon']['nilai_diskon'];
        }

        // Buat response dengan discount_amount
        $response = $cartDetails->toArray();
        $response['discount_amount'] = $discountAmount;

        // Hitung ulang total setelah diskon
        if ($discountAmount > 0) {
            $response['total'] = $response['total'] - $discountAmount;
        }

        return response()->json($response);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'kode_produk' => ['required', 'exists:produks,kode_produk'],
            'quantity' => ['nullable', 'integer', 'min:1']
        ]);

        // Ambil produk dari database
        $produk = Produk::where('kode_produk', $request->kode_produk)->first();

        if (!$produk) {
            return response()->json(['message' => 'Produk tidak ditemukan.'], 404);
        }

        // Ambil quantity dari input atau default ke 1
        $qty = (int) $request->input('quantity', 1);
        $qty = max(1, $qty); // Untuk berjaga-jaga

        // Ambil cart berdasarkan user ID
        $cart = Cart::name($request->user()->id);

        // Tambahkan ke cart
        $cart->addItem([
            'id' => $produk->id,
            'title' => $produk->nama_produk,
            'quantity' => $qty,
            'price' => $produk->harga
        ]);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan ke keranjang.',
            'item' => [
                'kode_produk' => $request->kode_produk,
                'nama_produk' => $produk->nama_produk,
                'quantity' => $qty,
                'harga' => $produk->harga
            ]
        ]);
    }


    public function update(Request $request, $hash)
    {
        $request->validate([
            'qty' => ['required', 'in:-1,1']
        ]);

        $cart = Cart::name($request->user()->id);
        $item = $cart->getItem($hash);

        if (!$item) {
            return abort(404);
        }

        $cart->updateItem($item->getHash(), [
            'quantity' => $item->getQuantity() + $request->qty
        ]);

        return response()->json(['message' => 'Berhasil diupdate.']);
    }

    public function destroy(Request $request, $hash)
    {
        $cart = Cart::name($request->user()->id);
        $cart->removeItem($hash);
        return response()->json(['message' => 'Berhasil dihapus.']);
    }

    public function clear(Request $request)
    {
        $cart = Cart::name($request->user()->id);
        $cart->destroy();

        return back();
    }
}
