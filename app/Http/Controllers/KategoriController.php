<?php

namespace App\Http\Controllers;

use App\Models\Kategori;
use App\Services\KategoriService;
use Illuminate\Http\Request;

class KategoriController extends Controller
{
    protected $kategoriService;

    public function __construct(KategoriService $kategoriService)
    {
        $this->kategoriService = $kategoriService;
    }

    public function index(Request $request)
    {
        $kategoris = $this->kategoriService->getAllKategori($request->search);

        return view('kategori.index', [
            'kategoris' => $kategoris
        ]);
    }

    public function create()
    {
        return view('kategori.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => ['required', 'max:100'],
        ]);

        $this->kategoriService->createKategori($request->all());

        return redirect()->route('kategori.index')->with('store', 'success');
    }

    public function show(Kategori $kategori)
    {
        abort(404);
    }

    public function edit(Kategori $kategori)
    {
        return view('kategori.edit', [
            'kategori' => $kategori
        ]);
    }

    public function update(Request $request, Kategori $kategori)
    {
        $request->validate([
            'nama_kategori' => ['required', 'max:100'],
        ]);

        $this->kategoriService->updateKategori($kategori, $request->all());

        return redirect()->route('kategori.index')->with('update', 'success');
    }

    public function destroy(Kategori $kategori)
    {
        $this->kategoriService->deleteKategori($kategori);

        return back()->with('destroy', 'success');
    }
}
