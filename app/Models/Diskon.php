<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Diskon extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'kode_diskon',
        'jenis_diskon',
        'jumlah_diskon',
        'minimal_pembelian',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
        'kategori_id',
        'produk_id',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'status' => 'boolean',
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function isValid($subtotal, $items = [])
    {
        // Cek status aktif
        if (!$this->status) {
            return ['valid' => false, 'message' => 'Kode diskon tidak aktif'];
        }

        // Cek tanggal
        $now = now();
        if ($now < $this->tanggal_mulai || $now > $this->tanggal_selesai) {
            return ['valid' => false, 'message' => 'Kode diskon sudah tidak berlaku'];
        }

        // Cek minimal pembelian (gunakan subtotal sebelum pajak)
        if ($subtotal < $this->minimal_pembelian) {
            return ['valid' => false, 'message' => 'Minimal pembelian Rp ' . number_format($this->minimal_pembelian)];
        }

        // Cek kategori/produk jika ada
        if ($this->kategori_id || $this->produk_id) {
            $validItems = false;

            // PERBAIKAN: Handle baik array maupun objek Collection
            $itemsArray = $this->convertItemsToArray($items);

            foreach ($itemsArray as $item) {
                $produk = Produk::find($item['id']);
                if (!$produk) continue;

                // Jika diskon untuk kategori tertentu
                if ($this->kategori_id && $produk->kategori_id == $this->kategori_id) {
                    $validItems = true;
                    break;
                }

                // Jika diskon untuk produk tertentu
                if ($this->produk_id && $produk->id == $this->produk_id) {
                    $validItems = true;
                    break;
                }
            }

            if (!$validItems) {
                $target = $this->kategori_id ? 'kategori' : 'produk';
                return ['valid' => false, 'message' => "Diskon hanya berlaku untuk $target tertentu"];
            }
        }

        return ['valid' => true, 'message' => 'Diskon berhasil diterapkan'];
    }

    public function hitungNilaiDiskon($subtotal, $items = [])
    {
        $subtotalDiskon = 0;

        // PERBAIKAN: Convert items ke format yang konsisten
        $itemsArray = $this->convertItemsToArray($items);

        // Hitung subtotal hanya dari produk yang cocok
        if ($this->kategori_id || $this->produk_id) {
            foreach ($itemsArray as $item) {
                // Ambil produk dari database
                $produk = Produk::find($item['id']);
                if (!$produk) continue;

                // Cek kecocokan kategori atau produk
                $kategoriMatch = $this->kategori_id && $produk->kategori_id == $this->kategori_id;
                $produkMatch = $this->produk_id && $produk->id == $this->produk_id;

                if ($kategoriMatch || $produkMatch) {
                    // Gunakan subtotal item jika ada, jika tidak hitung manual
                    $itemSubtotal = $item['subtotal'] ?? ($item['price'] * $item['quantity']);
                    $subtotalDiskon += $itemSubtotal;
                }
            }
        } else {
            // Jika diskon berlaku umum
            $subtotalDiskon = $subtotal;
        }

        // Hitung diskon berdasarkan subtotalDiskon
        if ($this->jenis_diskon === 'persen') {
            $nilaiDiskon = ($subtotalDiskon * $this->jumlah_diskon) / 100;
            // Batasi diskon maksimal tidak boleh lebih dari subtotal yang berlaku
            return min(round($nilaiDiskon), $subtotalDiskon);
        }

        // Nominal: pastikan tidak lebih besar dari subtotal yang berlaku
        return min($this->jumlah_diskon, $subtotalDiskon);
    }

    /**
     * Method untuk mendapatkan subtotal yang memenuhi syarat diskon
     */
    public function getEligibleSubtotal($items = [])
    {
        $itemsArray = $this->convertItemsToArray($items);

        if (!$this->kategori_id && !$this->produk_id) {
            // Diskon berlaku untuk semua produk
            $total = 0;
            foreach ($itemsArray as $item) {
                $total += $item['subtotal'] ?? ($item['price'] * $item['quantity']);
            }
            return $total;
        }

        $eligibleSubtotal = 0;
        foreach ($itemsArray as $item) {
            $produk = Produk::find($item['id']);
            if (!$produk) continue;

            $kategoriMatch = $this->kategori_id && $produk->kategori_id == $this->kategori_id;
            $produkMatch = $this->produk_id && $produk->id == $this->produk_id;

            if ($kategoriMatch || $produkMatch) {
                $eligibleSubtotal += $item['subtotal'] ?? ($item['price'] * $item['quantity']);
            }
        }

        return $eligibleSubtotal;
    }

    /**
     * PERBAIKAN: Method untuk convert items ke format array yang konsisten
     * Menangani berbagai format input: objek, collection, atau array
     */
    private function convertItemsToArray($items)
    {
        if (empty($items)) {
            return [];
        }

        $result = [];

        // Jika items adalah Collection Laravel atau objek yang bisa di-loop
        if (is_object($items) && method_exists($items, 'toArray')) {
            $items = $items->toArray();
        }

        // Jika items adalah objek dengan properti yang bisa diakses
        if (is_object($items) && !is_array($items)) {
            // Coba convert ke array jika memungkinkan
            if (method_exists($items, 'all')) {
                $items = $items->all();
            } else {
                // Jika objek tunggal, wrap dalam array
                $items = [$items];
            }
        }

        foreach ($items as $item) {
            if (is_object($item)) {
                // Convert objek ke array
                $result[] = [
                    'id' => $item->id ?? null,
                    'quantity' => $item->quantity ?? 0,
                    'price' => $item->price ?? 0,
                    'subtotal' => $item->subtotal ?? ($item->price * $item->quantity),
                ];
            } elseif (is_array($item)) {
                // Jika sudah array, pastikan key yang dibutuhkan ada
                $result[] = [
                    'id' => $item['id'] ?? null,
                    'quantity' => $item['quantity'] ?? 0,
                    'price' => $item['price'] ?? 0,
                    'subtotal' => $item['subtotal'] ?? ($item['price'] * $item['quantity']),
                ];
            }
        }

        return $result;
    }
}
