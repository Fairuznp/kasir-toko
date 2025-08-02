<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Diskon;
use App\Models\Kategori;
use App\Models\Produk;
use Jackiedo\Cart\Facades\Cart;

class DiskonController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $diskons = Diskon::with(['kategori', 'produk'])
            ->when($search, function ($q, $search) {
                return $q->where('kode_diskon', 'like', "%{$search}%");
            })
            ->orderBy('id', 'desc')
            ->paginate();

        if ($search) $diskons->appends(['search' => $search]);

        return view('diskon.index', compact('diskons'));
    }

    public function create()
    {
        $kategoris = Kategori::select('id', 'nama_kategori')->get();
        $produks = Produk::select('id', 'nama_produk')->get();

        return view('diskon.create', compact('kategoris', 'produks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_diskon' => 'required|unique:diskons,kode_diskon',
            'jenis_diskon' => 'required|in:persen,nominal',
            'jumlah_diskon' => 'required|integer|min:1',
            'minimal_pembelian' => 'nullable|integer|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'produk_id' => 'nullable|exists:produks,id',
        ]);

        Diskon::create($request->all());

        return redirect()->route('diskon.index')->with('store', 'success');
    }

    public function edit(Diskon $diskon)
    {
        $kategoris = Kategori::select('id', 'nama_kategori')->get();
        $produks = Produk::select('id', 'nama_produk')->get();

        return view('diskon.edit', compact('diskon', 'kategoris', 'produks'));
    }

    public function update(Request $request, Diskon $diskon)
    {
        $request->validate([
            'kode_diskon' => 'required|unique:diskons,kode_diskon,' . $diskon->id,
            'jenis_diskon' => 'required|in:persen,nominal',
            'jumlah_diskon' => 'required|integer|min:1',
            'minimal_pembelian' => 'nullable|integer|min:0',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'status' => 'boolean',
            'kategori_id' => 'nullable|exists:kategoris,id',
            'produk_id' => 'nullable|exists:produks,id',
        ]);

        $diskon->update($request->all());

        return redirect()->route('diskon.index')->with('update', 'success');
    }

    public function destroy(Diskon $diskon)
    {
        $diskon->delete();
        return back()->with('destroy', 'success');
    }

    public function terapkanDiskon(Request $request)
    {
        $request->validate([
            'kode_diskon' => 'required|exists:diskons,kode_diskon'
        ]);

        $cart = Cart::name($request->user()->id);
        $cartDetails = $cart->getDetails();
        $subtotal = $cartDetails->get('subtotal');
        $items = $cartDetails->get('items');

        $diskon = Diskon::where('kode_diskon', $request->kode_diskon)->first();

        $validation = $diskon->isValid($subtotal, $items);

        if (!$validation['valid']) {
            return response()->json(['success' => false, 'message' => $validation['message']], 400);
        }

        $nilaiDiskon = $diskon->hitungNilaiDiskon($subtotal);

        // Simpan diskon ke cart extra info
        $extraInfo = $cart->getExtraInfo();
        $extraInfo['diskon'] = [
            'id' => $diskon->id,
            'kode_diskon' => $diskon->kode_diskon,
            'nilai_diskon' => $nilaiDiskon
        ];

        $cart->setExtraInfo($extraInfo);

        return response()->json([
            'success' => true,
            'message' => $validation['message'],
            'nilai_diskon' => $nilaiDiskon
        ]);
    }
}
