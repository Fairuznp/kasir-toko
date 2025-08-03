<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CartService;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index(Request $request)
    {
        $response = $this->cartService->getCartDetails($request->user()->id);

        return response()->json($response);
    }

    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'kode_produk' => ['required', 'exists:produks,kode_produk'],
            'quantity' => ['nullable', 'integer', 'min:1']
        ]);

        try {
            $item = $this->cartService->addItemToCart(
                $request->kode_produk,
                $request->quantity,
                $request->user()->id
            );

            return response()->json([
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
                'item' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function update(Request $request, $hash)
    {
        $request->validate([
            'qty' => ['required', 'in:-1,1']
        ]);

        try {
            $this->cartService->updateCartItem($hash, $request->qty, $request->user()->id);

            return response()->json(['message' => 'Berhasil diupdate.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }
    }

    public function destroy(Request $request, $hash)
    {
        $this->cartService->removeCartItem($hash, $request->user()->id);

        return response()->json(['message' => 'Berhasil dihapus.']);
    }

    public function clear(Request $request)
    {
        $this->cartService->clearCart($request->user()->id);

        return back();
    }
}
