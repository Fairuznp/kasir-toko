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
        $rules = [
            'kode_produk' => ['required', 'max:250', 'unique:produks'],
            'nama_produk' => ['required', 'max:150'],
            'harga_modal' => ['required'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'pricing_type' => ['required', 'in:manual,margin'],
        ];

        // Validasi conditional berdasarkan tipe pricing
        if ($request->pricing_type === 'manual') {
            $rules['harga_jual'] = ['required'];
        } else if ($request->pricing_type === 'margin') {
            $rules['margin_percentage'] = ['required', 'numeric', 'min:0'];
        }

        $request->validate($rules);

        // Konversi format Rupiah ke angka
        $data = $request->all();
        $data['harga_modal'] = (float) preg_replace('/[^\d]/', '', $data['harga_modal']);
        
        // Jika menggunakan margin, hitung harga jual otomatis
        if ($data['pricing_type'] === 'margin' && isset($data['margin_percentage'])) {
            $hargaModal = $data['harga_modal'];
            $margin = (float) $data['margin_percentage'];
            $data['harga_jual'] = $hargaModal + ($hargaModal * $margin / 100);
        } else {
            // Mode manual
            $data['harga_jual'] = (float) preg_replace('/[^\d]/', '', $data['harga_jual']);
        }

        try {
            $this->produkService->createProduk($data);
            return redirect()->route('produk.index')->with('store', 'success');
        } catch (\Exception $e) {
            \Log::error('Error creating product: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan produk: ' . $e->getMessage()]);
        }
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
            'harga_modal' => ['required'],
            'harga_jual'  => ['required'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'pricing_type' => ['required', 'in:manual,margin'],
            'margin_percentage' => ['nullable', 'numeric', 'min:0', 'required_if:pricing_type,margin'],
        ]);

        // Konversi format Rupiah ke angka
        $data = $request->all();
        $data['harga_modal'] = (float) preg_replace('/[^\d]/', '', $data['harga_modal']);
        $data['harga_jual'] = (float) preg_replace('/[^\d]/', '', $data['harga_jual']);

        // Jika menggunakan margin, hitung harga jual otomatis
        if ($data['pricing_type'] === 'margin' && isset($data['margin_percentage'])) {
            $hargaModal = $data['harga_modal'];
            $margin = (float) $data['margin_percentage'];
            $data['harga_jual'] = $hargaModal + ($hargaModal * $margin / 100);
        }

        $this->produkService->updateProduk($produk, $data);

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
