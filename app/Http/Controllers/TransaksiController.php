<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TransaksiService;
use App\Models\Penjualan;
use Jackiedo\Cart\Facades\Cart;

class TransaksiController extends Controller
{
    protected $transaksiService;

    public function __construct(TransaksiService $transaksiService)
    {
        $this->transaksiService = $transaksiService;
    }

    public function index(Request $request)
    {
        $search = $request->search;
        $penjualans = $this->transaksiService->getAllTransaksi($search);

        return view('transaksi.index', [
            'penjualans' => $penjualans
        ]);
    }

    public function create(Request $request)
    {
        return view('transaksi.create', [
            'nama_kasir' => $request->user()->nama,
            'tanggal' => date('d F Y')
        ]);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $cart = Cart::name($user->id);
        $cartDetails = $cart->getDetails();
        $total = $cartDetails->get('total');
        $allItems = $cartDetails->get('items');
        $extraInfo = $cart->getExtraInfo();

        // Hitung diskon terlebih dahulu
        $discount = $this->transaksiService->calculateDiscount(
            $extraInfo,
            $cartDetails->get('subtotal'),
            $allItems
        );

        $totalFinal = $total - $discount['nilai_diskon'];

        $request->validate([
            'pelanggan_id' => ['required', 'exists:pelanggans,id'],
            'cash' => ['required', 'numeric', 'gte:' . $totalFinal]
        ], [
            'pelanggan_id' => 'pelanggan',
            'cash.gte' => 'Cash harus minimal Rp ' . number_format($totalFinal)
        ]);

        try {
            // ✅ 1. Cek stok dulu sebelum buat transaksi
            $this->transaksiService->validateStock($allItems);

            // ✅ 2. Setelah semua stok cukup, baru buat transaksi
            $penjualan = $this->transaksiService->createTransaction($user, $cart, $request, $discount);

            return redirect()->route('transaksi.show', ['transaksi' => $penjualan->id]);
        } catch (\Exception $e) {
            return redirect()->route('transaksi.create')
                ->withErrors(['stok' => $e->getMessage()])
                ->withInput();
        }
    }

    public function show(Request $request, Penjualan $transaksi)
    {
        $data = $this->transaksiService->getTransactionDetails($transaksi);

        return view('transaksi.invoice', $data);
    }

    public function destroy(Request $request, Penjualan $transaksi)
    {
        $this->transaksiService->cancelTransaction($transaksi);

        return back()->with('destroy', 'success');
    }

    public function produk(Request $request)
    {
        $produks = $this->transaksiService->searchProduk($request->search);

        return response()->json($produks);
    }

    public function pelanggan(Request $request)
    {
        $pelanggans = $this->transaksiService->searchPelanggan($request->search);

        return response()->json($pelanggans);
    }

    public function addPelanggan(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:pelanggans']
        ]);

        try {
            $this->transaksiService->addPelangganToCart($request->id, $request->user()->id);
            return response()->json(['message' => 'Berhasil.']);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function cetak(Penjualan $transaksi)
    {
        $data = $this->transaksiService->getTransactionDetails($transaksi);

        return view('transaksi.cetak', $data);
    }
}
