<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Pelanggan;
use App\Models\Diskon;
use App\Models\User;
use App\Services\TransaksiService;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Jackiedo\Cart\Facades\Cart;

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
        try {
            // Menggunakan kolom yang sesuai dengan model Diskon
            $diskon = Diskon::select('id', 'kode_diskon', 'jenis_diskon', 'jumlah_diskon', 'minimal_pembelian', 'status', 'kategori_id', 'produk_id')
                ->where('status', true)
                ->where('tanggal_mulai', '<=', now())
                ->where('tanggal_selesai', '>=', now())
                ->get();

            return response()->json([
                'success' => true,
                'data' => $diskon,
                'count' => $diskon->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil data diskon: ' . $e->getMessage()
            ], 500);
        }
    }

    public function loginKasir(Request $request)
    {
        try {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string'
            ]);

            // Cari user berdasarkan username
            $user = User::where('username', $request->username)->first();

            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Username tidak ditemukan'
                ], 401);
            }

            // Verifikasi password
            if (!Hash::check($request->password, $user->password)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Password salah'
                ], 401);
            }

            // Validasi role kasir
            if ($user->role !== 'petugas' && $user->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak memiliki hak akses sebagai kasir'
                ], 403);
            }

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'data' => [
                    'kasir_id' => $user->id,
                    'nama' => $user->nama,
                    'username' => $user->username,
                    'role' => $user->role
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login gagal: ' . $e->getMessage()
            ], 500);
        }
    }

    public function createTransaksi(Request $request)
    {
        try {
            // Validasi dan ambil data kasir dari request
            $kasirId = $request->input('kasir_id');
            if (!$kasirId) {
                throw new \Exception('ID Kasir wajib diisi');
            }

            // Cari user/kasir berdasarkan ID
            $user = User::find($kasirId);
            if (!$user) {
                throw new \Exception('Kasir tidak ditemukan');
            }

            // Validasi role kasir (opsional, sesuaikan dengan sistem role Anda)
            if ($user->role !== 'petugas' && $user->role !== 'admin') {
                throw new \Exception('User tidak memiliki hak akses sebagai kasir');
            }

            $userId = 'api_user_' . $kasirId . '_' . time(); // Unique cart identifier

            // Buat cart dari request
            $cartData = $request->input('items', []);
            $extraInfo = $request->input('extra_info', []);
            $pelangganId = $request->input('pelanggan_id');
            $metodePembayaran = $request->input('metode_pembayaran', 'tunai');
            $jumlahBayar = $request->input('jumlah_bayar', 0);

            // Validasi input
            if (empty($cartData)) {
                throw new \Exception('Items tidak boleh kosong');
            }

            if ($jumlahBayar <= 0) {
                throw new \Exception('Jumlah bayar harus lebih dari 0');
            }

            // Clear existing cart dan buat cart baru
            $cart = Cart::name($userId);
            $cart->destroy();

            // Tambahkan items ke cart
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

                // Tambah ke cart
                $cart->addItem([
                    'id' => $produk->id,
                    'title' => $produk->nama_produk,
                    'quantity' => $item['quantity'],
                    'price' => $produk->harga_jual
                ]);
            }

            // Apply tax (10%)
            $cart->applyTax([
                'id' => 1,
                'rate' => 10,
                'title' => 'Pajak PPN 10%'
            ]);

            // Set extra info (pelanggan, diskon)
            if ($pelangganId) {
                $extraInfo['pelanggan'] = ['id' => $pelangganId];
            }
            $cart->setExtraInfo($extraInfo);

            // Hitung diskon jika ada
            $discount = $this->transaksiService->calculateDiscount($extraInfo, $cart->getDetails()->get('subtotal'), $cart->getDetails()->get('items'));

            // Buat request object untuk compatibility dengan service
            $requestObj = new \Illuminate\Http\Request();
            $requestObj->merge([
                'cash' => $jumlahBayar,
                'metode_pembayaran' => $metodePembayaran
            ]);

            // Buat transaksi menggunakan service yang ada
            $result = $this->transaksiService->createTransaction($user, $cart, $requestObj, $discount);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil',
                'data' => [
                    'transaksi_id' => $result->id,
                    'nomor_transaksi' => $result->nomor_transaksi,
                    'subtotal' => $result->subtotal,
                    'pajak' => $result->pajak,
                    'nilai_diskon' => $result->nilai_diskon,
                    'total' => $result->total,
                    'tunai' => $result->tunai,
                    'kembalian' => $result->kembalian,
                    'tanggal' => $result->tanggal
                ]
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

    public function calculateCart(Request $request)
    {
        try {
            $items = $request->input('items', []);
            $diskonId = $request->input('diskon_id');

            if (empty($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Items tidak boleh kosong'
                ], 400);
            }

            $subtotal = 0;
            $cartDetails = [];

            // Hitung subtotal dan detail items
            foreach ($items as $item) {
                $produk = Produk::find($item['produk_id']);
                if (!$produk) {
                    continue;
                }

                $itemSubtotal = $produk->harga_jual * $item['quantity'];
                $subtotal += $itemSubtotal;

                $cartDetails[] = [
                    'produk_id' => $produk->id,
                    'nama_produk' => $produk->nama_produk,
                    'harga_jual' => $produk->harga_jual,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal
                ];
            }

            // Hitung diskon jika ada
            $diskonAmount = 0;
            $diskonInfo = null;
            if ($diskonId && $subtotal > 0) {
                $diskon = Diskon::find($diskonId);
                if ($diskon && $diskon->status == 'aktif') {
                    $validation = $diskon->isValid($subtotal, $items);
                    if ($validation['valid']) {
                        $diskonAmount = $diskon->hitungNilaiDiskon($subtotal, $items);
                        $diskonInfo = [
                            'id' => $diskon->id,
                            'kode_diskon' => $diskon->kode_diskon,
                            'nama_diskon' => $diskon->nama_diskon,
                            'nilai_diskon' => $diskonAmount
                        ];
                    }
                }
            }

            $subtotalAfterDiskon = $subtotal - $diskonAmount;

            // Pajak 10% dari subtotal setelah diskon (sesuai CartService)
            $pajakPersen = 10;
            $pajakAmount = ($subtotalAfterDiskon * $pajakPersen) / 100;

            // Total akhir
            $total = $subtotalAfterDiskon + $pajakAmount;

            return response()->json([
                'success' => true,
                'data' => [
                    'items' => $cartDetails,
                    'subtotal' => $subtotal,
                    'diskon' => $diskonInfo,
                    'diskon_amount' => $diskonAmount,
                    'subtotal_after_diskon' => $subtotalAfterDiskon,
                    'pajak' => [
                        'rate' => $pajakPersen,
                        'title' => 'Pajak PPN 10%',
                        'amount' => $pajakAmount
                    ],
                    'total' => $total
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error calculating cart: ' . $e->getMessage()
            ], 500);
        }
    }

    public function applyDiskon(Request $request)
    {
        try {
            $kodeDiskon = $request->input('kode_diskon');
            $items = $request->input('items', []);

            if (!$kodeDiskon) {
                throw new \Exception('Kode diskon tidak boleh kosong');
            }

            // Cari diskon berdasarkan kode
            $diskon = Diskon::where('kode_diskon', $kodeDiskon)
                ->where('status', true)
                ->where('tanggal_mulai', '<=', now())
                ->where('tanggal_selesai', '>=', now())
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
            $nilaiDiskon = $diskon->hitungNilaiDiskon($subtotal, $items);

            return response()->json([
                'success' => true,
                'data' => [
                    'diskon_id' => $diskon->id,
                    'kode_diskon' => $diskon->kode_diskon,
                    'jenis_diskon' => $diskon->jenis_diskon,
                    'jumlah_diskon' => $diskon->jumlah_diskon,
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

    public function getProdukByKategori($kategoriId)
    {
        try {
            $produk = Produk::with('kategori')
                ->where('kategori_id', $kategoriId)
                ->where('stok', '>', 0)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $produk,
                'count' => $produk->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil produk: ' . $e->getMessage()
            ], 500);
        }
    }

    public function searchProduk(Request $request)
    {
        try {
            $query = $request->input('q', '');
            $kategoriId = $request->input('kategori_id');

            $produk = Produk::with('kategori')
                ->where('stok', '>', 0)
                ->when($query, function ($q) use ($query) {
                    return $q->where('nama_produk', 'like', '%' . $query . '%')
                        ->orWhere('kode_produk', 'like', '%' . $query . '%');
                })
                ->when($kategoriId, function ($q) use ($kategoriId) {
                    return $q->where('kategori_id', $kategoriId);
                })
                ->limit(20)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $produk,
                'count' => $produk->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error search produk: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getDetailProduk($id)
    {
        try {
            $produk = Produk::with('kategori')->find($id);

            if (!$produk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $produk
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil detail produk: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getMetodePembayaran()
    {
        try {
            $metode = [
                ['id' => 'tunai', 'nama' => 'Tunai'],
                ['id' => 'kartu_kredit', 'nama' => 'Kartu Kredit'],
                ['id' => 'kartu_debit', 'nama' => 'Kartu Debit'],
                ['id' => 'transfer', 'nama' => 'Transfer Bank'],
                ['id' => 'e_wallet', 'nama' => 'E-Wallet']
            ];

            return response()->json([
                'success' => true,
                'data' => $metode
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error mengambil metode pembayaran: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validateTransaksi(Request $request)
    {
        try {
            $items = $request->input('items', []);
            $errors = [];

            if (empty($items)) {
                $errors[] = 'Items transaksi tidak boleh kosong';
            }

            foreach ($items as $index => $item) {
                if (!isset($item['produk_id']) || !isset($item['quantity'])) {
                    $errors[] = "Item ke-" . ($index + 1) . " harus memiliki produk_id dan quantity";
                    continue;
                }

                $produk = Produk::find($item['produk_id']);
                if (!$produk) {
                    $errors[] = "Produk dengan ID {$item['produk_id']} tidak ditemukan";
                    continue;
                }

                if ($produk->stok < $item['quantity']) {
                    $errors[] = "Stok produk '{$produk->nama_produk}' tidak mencukupi (tersedia: {$produk->stok})";
                }
            }

            if (!empty($errors)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validasi gagal',
                    'errors' => $errors
                ], 400);
            }

            return response()->json([
                'success' => true,
                'message' => 'Validasi berhasil'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error validasi: ' . $e->getMessage()
            ], 500);
        }
    }
}
