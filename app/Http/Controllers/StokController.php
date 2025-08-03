<?php

namespace App\Http\Controllers;

use App\Models\Stok;
use App\Services\StokService;
use Illuminate\Http\Request;

class StokController extends Controller
{
    protected $stokService;

    public function __construct(StokService $stokService)
    {
        $this->stokService = $stokService;
    }

    public function index(Request $request)
    {
        $stoks = $this->stokService->getAllStok($request->search);

        return view('stok.index', [
            'stoks' => $stoks
        ]);
    }

    public function create()
    {
        return view('stok.create');
    }

    public function produk(Request $request)
    {
        $produks = $this->stokService->searchProduk($request->search);

        return response()->json($produks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => ['required', 'exists:produks,id'],
            'jumlah' => ['required', 'numeric'],
            'nama_suplier' => ['required', 'max:150']
        ], [], [
            'produk_id' => 'Nama produk'
        ]);

        $this->stokService->createStok($request->all());

        return redirect()->route('stok.index')->with('store', 'success');
    }

    public function destroy(Stok $stok)
    {
        $this->stokService->deleteStok($stok);

        return back()->with('destroy', 'success');
    }
}
