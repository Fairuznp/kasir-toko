<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdukExpired extends Model
{
    use HasFactory;

    protected $fillable = [
        'produk_id',
        'jumlah',
        'tanggal_expired',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_expired' => 'date'
    ];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
