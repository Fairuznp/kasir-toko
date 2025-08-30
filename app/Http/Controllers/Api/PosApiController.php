<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Pelanggan;
use App\Models\Diskon;
use App\Services\TransaksiService;
use App\Services\CartService;
use Illuminate\Http\Request;

class PosApiController extends Controller
{
    protected $transaksiService;
    protected $cartService;

    public function __construct(TransaksiService $transaksiService, CartService $cartService)
    {
        $this->transaksiService = $transaksiService;
        $this->cartService = $cartService;
    }

    public function getProduk()
    {
        try {
            $produk = Produk::with('kategori')
                ->where('stok', '>', 0)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $produk,
                'count' => $produk->count()
            ]);
        } catch (\Exception $e) {
            // Fallback tanpa relasi jika ada error
            try {
                $produk = Produk::where('stok', '>', 0)->get();

                return response()->json([
                    'success' => true,
                    'data' => $produk,
                    'count' => $produk->count(),
                    'note' => 'Data tanpa relasi kategori'
                ]);
            } catch (\Exception $fallbackError) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error mengambil data produk: ' . $e->getMessage(),
                    'fallback_error' => $fallbackError->getMessage()
                ], 500);
            }
        }
    }

    public function getKategori()
    {
        $kategori = Kategori::all();

        return response()->json([
            'success' => true,
            'data' => $kategori
        ]);
    }

    public function getPelanggan()
    {
        $pelanggan = Pelanggan::all();

        return response()->json([
            'success' => true,
            'data' => $pelanggan
        ]);
    }

    public function getDiskon()
    {
        $diskon = Diskon::where('status', 'aktif')
            ->where('tanggal_mulai', '<=', now())
            ->where('tanggal_berakhir', '>=', now())
            ->get();

        return response()->json([
            'success' => true,
            'data' => $diskon
        ]);
    }

    public function createTransaksi(Request $request)
    {
        try {
            // Simulasi data user (karena API tidak menggunakan auth)
            $user = (object) ['id' => 1, 'nama' => 'API User'];

            // Buat cart dari request
            $cartData = $request->input('items', []);
            $extraInfo = $request->input('extra_info', []);

            // Validasi input
            if (empty($cartData)) {
                throw new \Exception('Items tidak boleh kosong');
            }

            // Validasi stok
            foreach ($cartData as $item) {
                if (!isset($item['produk_id']) || !isset($item['quantity'])) {
                    throw new \Exception('Format item tidak valid. Harus ada produk_id dan quantity');
                }

                $produk = Produk::find($item['produk_id']);
                if (!$produk) {
                    throw new \Exception('Produk dengan ID ' . $item['produk_id'] . ' tidak ditemukan');
                }
                if ($produk->stok < $item['quantity']) {
                    throw new \Exception('Stok produk "' . $produk->nama_produk . '" tidak mencukupi. Stok tersedia: ' . $produk->stok);
                }
            }

            // Hitung subtotal
            $subtotal = 0;
            foreach ($cartData as $item) {
                $produk = Produk::find($item['produk_id']);
                $subtotal += $produk->harga_jual * $item['quantity'];
            }

            // Hitung diskon jika ada
            $discount = $this->transaksiService->calculateDiscount($extraInfo, $subtotal, $cartData);

            // Buat transaksi
            $result = $this->transaksiService->createTransaction($user, $cartData, $request, $discount);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil',
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi gagal: ' . $e->getMessage(),
                'trace' => config('app.debug') ? $e->getTraceAsString() : null
            ], 500);
        }
    }

    public function cekStok(Request $request)
    {
        $produk = Produk::find($request->produk_id);

        if (!$produk) {
            return response()->json([
                'success' => false,
                'message' => 'Produk tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'produk_id' => $produk->id,
                'nama_produk' => $produk->nama_produk,
                'stok_tersedia' => $produk->stok,
                'harga_jual' => $produk->harga_jual
            ]
        ]);
    }

    public function applyDiskon(Request $request)
    {
        try {
            $kodeDiskon = $request->input('kode_diskon');
            $items = $request->input('items', []);

            // Cari diskon berdasarkan kode
            $diskon = Diskon::where('kode_diskon', $kodeDiskon)
                ->where('status', 'aktif')
                ->where('tanggal_mulai', '<=', now())
                ->where('tanggal_berakhir', '>=', now())
                ->first();

            if (!$diskon) {
                throw new \Exception('Kode diskon tidak valid atau sudah kadaluarsa');
            }

            // Hitung subtotal
            $subtotal = 0;
            foreach ($items as $item) {
                $produk = Produk::find($item['produk_id']);
                if ($produk) {
                    $subtotal += $produk->harga_jual * $item['quantity'];
                }
            }

            // Validasi diskon
            $validation = $diskon->isValid($subtotal, $items);

            if (!$validation['valid']) {
                throw new \Exception($validation['message']);
            }

            // Hitung nilai diskon
            $nilaiDiskon = 0;
            if ($diskon->tipe_diskon === 'persentase') {
                $nilaiDiskon = ($subtotal * $diskon->nilai_diskon) / 100;
            } else {
                $nilaiDiskon = $diskon->nilai_diskon;
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'diskon_id' => $diskon->id,
                    'kode_diskon' => $diskon->kode_diskon,
                    'nama_diskon' => $diskon->nama_diskon,
                    'nilai_diskon' => $nilaiDiskon,
                    'subtotal_setelah_diskon' => $subtotal - $nilaiDiskon
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
