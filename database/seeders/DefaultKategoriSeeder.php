<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultKategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buat kategori default jika belum ada
        Kategori::firstOrCreate(
            ['nama_kategori' => 'Tidak Berkategori'],
            ['nama_kategori' => 'Tidak Berkategori']
        );
    }
}
