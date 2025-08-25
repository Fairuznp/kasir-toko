<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Models\ProdukExpired;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdukExpiredController extends Controller
{
    public function index()
    {
        $expired_products = ProdukExpired::with('produk')->get();
        return view('produk_expired.index', compact('expired_products'));
    }

    public function create()
    {
        $products = Produk::all();
        return view('produk_expired.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|integer|min:1',
            'tanggal_expired' => 'required|date',
            'keterangan' => 'nullable|string'
        ]);

        DB::transaction(function () use ($request) {
            // Create expired product record
            ProdukExpired::create($request->all());

            // Update product stock
            $product = Produk::findOrFail($request->produk_id);
            $product->decrement('stok', $request->jumlah);
        });

        return redirect()->route('produk-expired.index')
            ->with('success', 'Produk expired berhasil ditambahkan');
    }

    public function destroy($id)
    {
        $expired_product = ProdukExpired::findOrFail($id);

        DB::transaction(function () use ($expired_product) {
            // Return the stock to the product
            $expired_product->produk->increment('stok', $expired_product->jumlah);

            // Delete the expired product record
            $expired_product->delete();
        });

        return redirect()->route('produk-expired.index')
            ->with('success', 'Produk expired berhasil dihapus');
    }
}
