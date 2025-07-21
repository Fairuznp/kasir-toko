<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        \App\Models\User::create([
            'nama' => 'Administrator',
            'username' => 'admin',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        \App\Models\User::create([
            'nama' => 'Petugas',
            'username' => 'petugas',
            'role' => 'petugas',
            'password' => bcrypt('password'),
        ]);

        \App\Models\Pelanggan::create([
            'nama' => 'Dodo Sidodo',
            'alamat' => 'Padaherang',
            'nomor_tlp' => '082288866677',
        ]);

        \App\Models\Pelanggan::create([
            'nama' => 'Hanifah',
            'alamat' => 'Kalipucang',
            'nomor_tlp' => '082288866677',
        ]);

        \App\Models\Kategori::create([
            'nama_kategori' => 'Makanan',
        ]);

        \App\Models\Kategori::create([
            'nama_kategori' => 'Minuman',
        ]);

        \App\Models\Produk::create([
            'kategori_id' => 1,
            'kode_produk' => '1001',
            'nama_produk' => 'Chiki Taro',
            'harga' => 5000
        ]);

        \App\Models\Produk::create([
            'kategori_id' => 2,
            'kode_produk' => '1002',
            'nama_produk' => 'Le Mineral',
            'harga' => 3500
        ]);
    }
}
