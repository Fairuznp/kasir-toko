<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\DetilPenjualan;
use App\Models\Pelanggan;
use App\Models\Produk;
use App\Models\User;
use App\Models\Penjualan;
use App\Models\Diskon;
use Jackiedo\Cart\Facades\Cart;

class TransaksiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $penjualans = Penjualan::join('users', 'users.id', 'penjualans.user_id')
            ->join('pelanggans', 'pelanggans.id', 'penjualans.pelanggan_id')
            ->select('penjualans.*', 'users.nama as nama_kasir', 'pelanggans.nama as nama_pelanggan')
            ->orderBy('id', 'desc')
            ->when($search, function ($q, $search) {
                return $q->where('nomor_transaksi', 'like', "%{$search}%");
            })
            ->paginate();

        if ($search) $penjualans->appends(['search' => $search]);

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
        $nilaiDiskon = 0;
        if (isset($extraInfo['diskon'])) {
            $diskon = Diskon::find($extraInfo['diskon']['id']);
            if ($diskon) {
                $validation = $diskon->isValid($cartDetails->get('subtotal'), $allItems);
                if ($validation['valid']) {
                    $nilaiDiskon = $diskon->hitungNilaiDiskon($cartDetails->get('subtotal'));
                }
            }
        }

        $totalFinal = $total - $nilaiDiskon;

        $request->validate([
            'pelanggan_id' => ['required', 'exists:pelanggans,id'],
            'cash' => ['required', 'numeric', 'gte:' . $totalFinal]
        ], [
            'pelanggan_id' => 'pelanggan',
            'cash.gte' => 'Cash harus minimal Rp ' . number_format($totalFinal)
        ]);

        // ✅ 1. Cek stok dulu sebelum buat transaksi
        foreach ($allItems as $item) {
            $produk = Produk::find($item->id);
            if (!$produk || $produk->stok < $item->quantity) {
                return redirect()->route('transaksi.create')
                    ->withErrors(['stok' => 'Stok produk "' . ($produk->nama_produk ?? 'Unknown') . '" tidak mencukupi.'])
                    ->withInput();
            }
        }

        // ✅ 2. Validasi ulang diskon jika ada
        $diskonId = null;

        if (isset($extraInfo['diskon'])) {
            $diskon = Diskon::find($extraInfo['diskon']['id']);
            if ($diskon) {
                $validation = $diskon->isValid($cartDetails->get('subtotal'), $allItems);
                if ($validation['valid']) {
                    $diskonId = $diskon->id;
                    $nilaiDiskon = $diskon->hitungNilaiDiskon($cartDetails->get('subtotal'));
                }
            }
        }

        // ✅ 3. Setelah semua stok cukup, baru buat transaksi
        $lastPenjualan = Penjualan::orderBy('id', 'desc')->first();
        $no = $lastPenjualan ? $lastPenjualan->id + 1 : 1;
        $no = sprintf("%04d", $no);

        // Pastikan nilai dalam range integer yang aman
        $totalFinal = $total - $nilaiDiskon;
        $kembalian = $request->cash - $totalFinal;

        // Validasi nilai tidak negatif
        if ($kembalian < 0) {
            return redirect()->route('transaksi.create')
                ->withErrors(['cash' => 'Cash tidak mencukupi'])
                ->withInput();
        }

        $penjualan = Penjualan::create([
            'user_id' => $user->id,
            'pelanggan_id' => $cart->getExtraInfo('pelanggan')['id'],
            'nomor_transaksi' => date('Ymd') . $no,
            'tanggal' => now(),
            'total' => (int) $totalFinal,
            'tunai' => (int) $request->cash,
            'kembalian' => (int) $kembalian,
            'pajak' => (int) $cartDetails->get('tax_amount'),
            'subtotal' => (int) $cartDetails->get('subtotal'),
            'diskon_id' => $diskonId,
            'nilai_diskon' => (int) $nilaiDiskon,
        ]);

        foreach ($allItems as $item) {
            DetilPenjualan::create([
                'penjualan_id' => $penjualan->id,
                'produk_id' => $item->id,
                'jumlah' => $item->quantity,
                'harga_produk' => $item->price,
                'subtotal' => $item->subtotal,
            ]);

            $produk = Produk::find($item->id);
            $produk->stok -= $item->quantity;
            $produk->save();
        }

        $cart->destroy();

        return redirect()->route('transaksi.show', ['transaksi' => $penjualan->id]);
    }

    public function show(Request $request, Penjualan $transaksi)
    {
        $pelanggan = Pelanggan::find($transaksi->pelanggan_id);
        $user = User::find($transaksi->user_id);
        $detilPenjualan = DetilPenjualan::join('produks', 'produks.id', 'detil_penjualans.produk_id')
            ->select('detil_penjualans.*', 'nama_produk')
            ->where('penjualan_id', $transaksi->id)->get();

        return view('transaksi.invoice', [
            'penjualan' => $transaksi,
            'pelanggan' => $pelanggan,
            'user' => $user,
            'detilPenjualan' => $detilPenjualan
        ]);
    }

    public function destroy(Request $request, Penjualan $transaksi)
    {
        if ($transaksi->status == "batal") {
            return back()->with('destroy', 'success');
        }

        $detail = DetilPenjualan::where('penjualan_id', $transaksi->id)->get();

        foreach ($detail as $item) {
            $produk = Produk::find($item->produk_id);
            if ($produk) {
                $produk->stok += $item->jumlah;
                $produk->save();
            }
        }

        $transaksi->update([
            'status' => 'batal'
        ]);

        return back()->with('destroy', 'success');
    }

    public function produk(Request $request)
    {
        $search = $request->search;
        $produks = Produk::select('id', 'kode_produk', 'nama_produk')
            ->when($search, function ($q, $search) {
                return $q->where('nama_produk', 'like', "%{$search}%");
            })
            ->orderBy('nama_produk')
            ->take(15)
            ->get();

        return response()->json($produks);
    }

    public function pelanggan(Request $request)
    {
        $search = $request->search;
        $pelanggans = Pelanggan::select('id', 'nama')
            ->when($search, function ($q, $search) {
                return $q->where('nama', 'like', "%{$search}%");
            })
            ->orderBy('nama')
            ->take(15)
            ->get();

        return response()->json($pelanggans);
    }

    public function addPelanggan(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:pelanggans']
        ]);
        $pelanggan = Pelanggan::find($request->id);

        $cart = Cart::name($request->user()->id);

        $cart->setExtraInfo([
            'pelanggan' => [
                'id' => $pelanggan->id,
                'nama' => $pelanggan->nama,
            ]
        ]);

        return response()->json(['message' => 'Berhasil.']);
    }

    public function cetak(Penjualan $transaksi)
    {
        $pelanggan = Pelanggan::find($transaksi->pelanggan_id);
        $user = User::find($transaksi->user_id);
        $detilPenjualan = DetilPenjualan::join('produks', 'produks.id', 'detil_penjualans.produk_id')
            ->select('detil_penjualans.*', 'nama_produk')
            ->where('penjualan_id', $transaksi->id)->get();

        return view('transaksi.cetak', [
            'penjualan' => $transaksi,
            'pelanggan' => $pelanggan,
            'user' => $user,
            'detilPenjualan' => $detilPenjualan
        ]);
    }
}
