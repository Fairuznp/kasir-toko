<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use App\Services\ProdukService;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ProdukController extends Controller
{
    protected $produkService;

    public function __construct(ProdukService $produkService)
    {
        $this->produkService = $produkService;
    }

    public function index(Request $request)
    {
        $produks = $this->produkService->getAllProduk($request->search);

        return view('produk.index', [
            'produks' => $produks
        ]);
    }

    public function create()
    {
        $kategoris = $this->produkService->getKategoriForCreate();

        return view('produk.create', [
            'kategoris' => $kategoris
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_produk' => ['required', 'max:250', 'unique:produks'],
            'nama_produk' => ['required', 'max:150'],
            'harga'       => ['required', 'numeric'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
        ]);

        $this->produkService->createProduk($request->all());

        return redirect()->route('produk.index')->with('store', 'success');
    }

    public function show(Produk $produk)
    {
        abort(404);
    }

    public function edit(Produk $produk)
    {
        $kategoris = $this->produkService->getKategoriForCreate();

        return view('produk.edit', [
            'produk' => $produk,
            'kategoris' => $kategoris,
        ]);
    }

    public function update(Request $request, Produk $produk)
    {
        $request->validate([
            'kode_produk' => ['required', 'max:250', 'unique:produks,kode_produk,' . $produk->id],
            'nama_produk' => ['required', 'max:150'],
            'harga'       => ['required', 'numeric'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
        ]);

        $this->produkService->updateProduk($produk, $request->all());

        return redirect()->route('produk.index')->with('update', 'success');
    }

    public function destroy(Produk $produk)
    {
        $this->produkService->deleteProduk($produk);

        return back()->with('destroy', 'success');
    }

    public function downloadQr($kode)
    {
        // Generate QR Code dalam format SVG
        $qr = QrCode::format('svg')->size(300)->generate($kode);

        // Nama file yang akan diunduh
        $filename = 'qr-' . $kode . '.svg';

        // Kirim response dengan header download
        return response($qr)
            ->header('Content-Type', 'image/svg+xml') // Lebih eksplisit MIME type-nya
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
