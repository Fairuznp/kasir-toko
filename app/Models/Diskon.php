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

            foreach ($items as $item) {
                $produk = Produk::find($item->id);
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

    public function hitungNilaiDiskon($subtotal)
    {
        if ($this->jenis_diskon == 'persen') {
            return ($subtotal * $this->jumlah_diskon) / 100;
        }

        return $this->jumlah_diskon;
    }
}
