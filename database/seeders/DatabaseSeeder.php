<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $faker = Faker::create('id_ID');

        // Create 10 Users (Admin dan Petugas)
        \App\Models\User::create([
            'nama' => 'Administrator',
            'username' => 'admin',
            'role' => 'admin',
            'password' => bcrypt('password'),
        ]);

        for ($i = 1; $i <= 9; $i++) {
            \App\Models\User::create([
                'nama' => $faker->name,
                'username' => 'user' . $i,
                'role' => $faker->randomElement(['admin', 'petugas']),
                'password' => bcrypt('password'),
            ]);
        }

        // Create 10 Pelanggan
        for ($i = 1; $i <= 10; $i++) {
            \App\Models\Pelanggan::create([
                'nama' => $faker->name,
                'alamat' => $faker->address,
                'nomor_tlp' => $faker->phoneNumber,
            ]);
        }

        // Create 8 Kategori
        $kategoris = [
            'Makanan',
            'Minuman',
            'Kemasan',
            'Kebutuhan Pokok',
            'Ice Cream',
            'Elektronik',
            'Kecantikan',
            'Kesehatan'
        ];

        foreach ($kategoris as $kategori) {
            \App\Models\Kategori::create([
                'nama_kategori' => $kategori,
            ]);
        }

        // Create products with specific data for each category
        $produkData = [
            // Makanan
            1 => [
                ['nama' => 'Chiki Taro', 'harga_modal' => 3500, 'harga_jual' => 5000],
                ['nama' => 'Indomie Goreng', 'harga_modal' => 2100, 'harga_jual' => 3000],
                ['nama' => 'Biskuit Oreo', 'harga_modal' => 8400, 'harga_jual' => 12000],
                ['nama' => 'Keripik Kentang', 'harga_modal' => 7000, 'harga_jual' => 10000],
                ['nama' => 'Cokelat Silverqueen', 'harga_modal' => 10500, 'harga_jual' => 15000],
                ['nama' => 'Permen Yupi', 'harga_modal' => 1750, 'harga_jual' => 2500],
                ['nama' => 'Wafer Tango', 'harga_modal' => 2800, 'harga_jual' => 4000],
                ['nama' => 'Sereal Energen', 'harga_modal' => 10500, 'harga_jual' => 15000],
                ['nama' => 'Roti Tawar', 'harga_modal' => 7000, 'harga_jual' => 10000],
                ['nama' => 'Mie Sedaap', 'harga_modal' => 2100, 'harga_jual' => 3000]
            ],
            // Minuman
            2 => [
                ['nama' => 'Le Mineral 600ml', 'harga_modal' => 2100, 'harga_jual' => 3000],
                ['nama' => 'Aqua 600ml', 'harga_modal' => 2800, 'harga_jual' => 4000],
                ['nama' => 'Coca Cola 390ml', 'harga_modal' => 4200, 'harga_jual' => 6000],
                ['nama' => 'Fanta 390ml', 'harga_modal' => 4200, 'harga_jual' => 6000],
                ['nama' => 'Sprite 390ml', 'harga_modal' => 4200, 'harga_jual' => 6000],
                ['nama' => 'Teh Botol Sosro', 'harga_modal' => 3500, 'harga_jual' => 5000],
                ['nama' => 'Kopi Kapal Api', 'harga_modal' => 7000, 'harga_jual' => 10000],
                ['nama' => 'Susu Ultra 250ml', 'harga_modal' => 3500, 'harga_jual' => 5000],
                ['nama' => 'Yakult', 'harga_modal' => 2100, 'harga_jual' => 3000],
                ['nama' => 'Pocari Sweat', 'harga_modal' => 5600, 'harga_jual' => 8000]
            ],
            // Kemasan
            3 => [
                ['nama' => 'Box Kertas', 'harga_modal' => 3500, 'harga_jual' => 5000],
                ['nama' => 'Plastik Kiloan', 'harga_modal' => 7000, 'harga_jual' => 10000],
                ['nama' => 'Kantong Kresek', 'harga_modal' => 1750, 'harga_jual' => 2500],
                ['nama' => 'Styrofoam', 'harga_modal' => 2800, 'harga_jual' => 4000],
                ['nama' => 'Bubble Wrap', 'harga_modal' => 10500, 'harga_jual' => 15000],
                ['nama' => 'Lakban', 'harga_modal' => 7000, 'harga_jual' => 10000],
                ['nama' => 'Kardus Besar', 'harga_modal' => 14000, 'harga_jual' => 20000],
                ['nama' => 'Paper Bag', 'harga_modal' => 3500, 'harga_jual' => 5000],
                ['nama' => 'Aluminium Foil', 'harga_modal' => 21000, 'harga_jual' => 30000],
                ['nama' => 'Plastic Wrap', 'harga_modal' => 14000, 'harga_jual' => 20000]
            ],
            // Kebutuhan Pokok
            4 => [
                ['nama' => 'Telur 1 kg', 'harga_modal' => 21000, 'harga_jual' => 30000],
                ['nama' => 'Beras 5 kg', 'harga_modal' => 56000, 'harga_jual' => 80000],
                ['nama' => 'Gula Pasir 1 kg', 'harga_modal' => 10500, 'harga_jual' => 15000],
                ['nama' => 'Minyak Goreng 1L', 'harga_modal' => 14000, 'harga_jual' => 20000],
                ['nama' => 'Garam 500g', 'harga_modal' => 3500, 'harga_jual' => 5000],
                ['nama' => 'Tepung Terigu 1kg', 'harga_modal' => 7000, 'harga_jual' => 10000],
                ['nama' => 'Kecap Manis', 'harga_modal' => 10500, 'harga_jual' => 15000],
                ['nama' => 'Saos Tomat', 'harga_modal' => 7000, 'harga_jual' => 10000],
                ['nama' => 'Mentega 200g', 'harga_modal' => 17500, 'harga_jual' => 25000],
                ['nama' => 'Susu Kental Manis', 'harga_modal' => 8400, 'harga_jual' => 12000]
            ],
            // Ice Cream
            5 => [
                ['nama' => 'Magnum Classic', 'harga_modal' => 17500, 'harga_jual' => 25000],
                ['nama' => 'Walls Cornetto', 'harga_modal' => 10500, 'harga_jual' => 15000],
                ['nama' => 'Es Krim Aice', 'harga_modal' => 3500, 'harga_jual' => 5000],
                ['nama' => 'Paddle Pop', 'harga_modal' => 7000, 'harga_jual' => 10000],
                ['nama' => 'Vienetta', 'harga_modal' => 35000, 'harga_jual' => 50000],
                ['nama' => 'Ben & Jerry 473ml', 'harga_modal' => 105000, 'harga_jual' => 150000],
                ['nama' => 'Haagen Dazs 473ml', 'harga_modal' => 140000, 'harga_jual' => 200000],
                ['nama' => 'Baskin Robbins', 'harga_modal' => 21000, 'harga_jual' => 30000],
                ['nama' => 'Es Mambo', 'harga_modal' => 2100, 'harga_jual' => 3000],
                ['nama' => 'Es Lilin', 'harga_modal' => 1050, 'harga_jual' => 1500]
            ],
            // Elektronik
            6 => [
                ['nama' => 'LG 43" UQ7500 Smart UHD TV', 'harga_modal' => 3000000, 'harga_jual' => 3599000],
                ['nama' => 'Xiaomi Redmi Note 12 6/128GB', 'harga_modal' => 1800000, 'harga_jual' => 2199000],
                ['nama' => 'Samsung Galaxy A14 4/128GB', 'harga_modal' => 1900000, 'harga_jual' => 2299000],
                ['nama' => 'Xiaomi Powerbank 20000mAh 18W', 'harga_modal' => 240000, 'harga_jual' => 299000],
                ['nama' => 'TP-Link TL-WR845N Router N300', 'harga_modal' => 180000, 'harga_jual' => 215000]
            ],
            // Kecantikan
            7 => [
                ['nama' => 'ESQA Minimalist Blurring Serum Skin Tint SPF 35', 'harga_modal' => 135000, 'harga_jual' => 169000],
                ['nama' => 'Wardah Lightening Day Cream 30g', 'harga_modal' => 40000, 'harga_jual' => 50500],
                ['nama' => 'Emina Bright Stuff Moisturizing Cream 20ml', 'harga_modal' => 18000, 'harga_jual' => 22900],
                ['nama' => 'Ponds Age Miracle Day Cream 50g', 'harga_modal' => 96000, 'harga_jual' => 120000],
                ['nama' => 'Mother Of Pearl Anti Cakey Lock Gripping Primer', 'harga_modal' => 100000, 'harga_jual' => 127090]
            ],
            // Kesehatans
            8 => [
                ['nama' => 'Blackmores Ultimate Omega Odourless 60 kapsul', 'harga_modal' => 350000, 'harga_jual' => 435100],
                ['nama' => 'Youvit Multivitamin Gummy Kids 30 gummies', 'harga_modal' => 60000, 'harga_jual' => 75918],
                ['nama' => 'Panadol Extra 1 strip 10 tablet', 'harga_modal' => 12000, 'harga_jual' => 15750],
                ['nama' => 'Enervon-C 30 tablet', 'harga_modal' => 35000, 'harga_jual' => 43500],
                ['nama' => 'Vitacimin Vitamin C 10 tablet', 'harga_modal' => 6000, 'harga_jual' => 8500]
            ]
        ];

        $productId = 1;
        foreach ($produkData as $kategoriId => $products) {
            foreach ($products as $productData) {
                \App\Models\Produk::create([
                    'kategori_id' => $kategoriId,
                    'kode_produk' => str_pad($productId, 4, '0', STR_PAD_LEFT),
                    'nama_produk' => $productData['nama'],
                    'harga_modal' => $productData['harga_modal'],
                    'harga_jual' => $productData['harga_jual'],
                    'stok' => $faker->numberBetween(100, 500)
                ]);

                // Create stock entry for each product
                \App\Models\Stok::create([
                    'produk_id' => $productId,
                    'nama_suplier' => $faker->company,
                    'jumlah' => $faker->numberBetween(100, 500),
                    'tanggal' => $faker->dateTimeBetween('-3 months', 'now')->format('Y-m-d')
                ]);

                $productId++;
            }
        }

        // Create 10 transactions with minimum 50k and 2 products
        for ($i = 1; $i <= 10; $i++) {
            $userId = $faker->numberBetween(1, 10);
            $pelangganId = $faker->numberBetween(1, 10);
            $tanggal = $faker->dateTimeBetween('-2 months', 'now');

            // Generate unique transaction number
            $nomorTransaksi = $tanggal->format('Ymd') . str_pad($i, 4, '0', STR_PAD_LEFT);

            // Select random products and calculate subtotal
            $selectedProducts = [];
            $subtotal = 0;
            $productCount = $faker->numberBetween(2, 5); // 2-5 products per transaction

            for ($j = 0; $j < $productCount; $j++) {
                $productId = $faker->numberBetween(1, 55); // Updated to new product count
                $product = \App\Models\Produk::find($productId);
                $jumlah = $faker->numberBetween(1, 3);

                $selectedProducts[] = [
                    'produk_id' => $productId,
                    'jumlah' => $jumlah,
                    'harga_jual' => $product->harga_jual,
                    'subtotal' => $product->harga_jual * $jumlah
                ];

                $subtotal += $product->harga_jual * $jumlah;
            }

            // Ensure minimum 50k
            if ($subtotal < 50000) {
                $additionalProduct = \App\Models\Produk::where('harga_jual', '>=', 50000 - $subtotal)->first();
                if ($additionalProduct) {
                    $selectedProducts[] = [
                        'produk_id' => $additionalProduct->id,
                        'jumlah' => 1,
                        'harga_jual' => $additionalProduct->harga_jual,
                        'subtotal' => $additionalProduct->harga_jual
                    ];
                    $subtotal += $additionalProduct->harga_jual;
                }
            }

            $pajak = $subtotal * 0.1; // 10% tax
            $total = $subtotal + $pajak;
            $tunai = $total + $faker->numberBetween(0, 20000); // Add some change
            $kembalian = $tunai - $total;

            // Create transaction
            $penjualan = \App\Models\Penjualan::create([
                'user_id' => $userId,
                'pelanggan_id' => $pelangganId,
                'nomor_transaksi' => $nomorTransaksi,
                'tanggal' => $tanggal->format('Y-m-d H:i:s'),
                'subtotal' => $subtotal,
                'pajak' => $pajak,
                'total' => $total,
                'tunai' => $tunai,
                'kembalian' => $kembalian,
                'status' => 'selesai'
            ]);

            // Create transaction details
            foreach ($selectedProducts as $item) {
                \App\Models\DetilPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $item['produk_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_jual' => $item['harga_jual'],
                    'subtotal' => $item['subtotal'],
                ]);
            }
        }

        // Add 4 new transactions with products from specific categories
        $specificCategories = [6, 7, 8]; // Elektronik, Kecantikan, Kesehatan
        for ($i = 1; $i <= 4; $i++) {
            $userId = $faker->numberBetween(1, 10);
            $pelangganId = $faker->numberBetween(1, 10);
            $tanggal = now(); // Current date

            // Generate unique transaction number
            $nomorTransaksi = $tanggal->format('Ymd') . str_pad($i + 10, 4, '0', STR_PAD_LEFT);

            // Select random products from specific categories and calculate subtotal
            $selectedProducts = [];
            $subtotal = 0;
            $productCount = $faker->numberBetween(2, 5); // 2-5 products per transaction

            for ($j = 0; $j < $productCount; $j++) {
                $product = \App\Models\Produk::whereIn('kategori_id', $specificCategories)->inRandomOrder()->first();
                $jumlah = $faker->numberBetween(1, 3);

                $selectedProducts[] = [
                    'produk_id' => $product->id,
                    'jumlah' => $jumlah,
                    'harga_jual' => $product->harga_jual,
                    'subtotal' => $product->harga_jual * $jumlah
                ];

                $subtotal += $product->harga_jual * $jumlah;
            }

            // Ensure minimum 50k
            if ($subtotal < 50000) {
                $additionalProduct = \App\Models\Produk::whereIn('kategori_id', $specificCategories)
                    ->where('harga_jual', '>=', 50000 - $subtotal)
                    ->first();
                if ($additionalProduct) {
                    $selectedProducts[] = [
                        'produk_id' => $additionalProduct->id,
                        'jumlah' => 1,
                        'harga_jual' => $additionalProduct->harga_jual,
                        'subtotal' => $additionalProduct->harga_jual
                    ];
                    $subtotal += $additionalProduct->harga_jual;
                }
            }

            $pajak = $subtotal * 0.1; // 10% tax
            $total = $subtotal + $pajak;
            $tunai = $total + $faker->numberBetween(0, 20000); // Add some change
            $kembalian = $tunai - $total;

            // Create transaction
            $penjualan = \App\Models\Penjualan::create([
                'user_id' => $userId,
                'pelanggan_id' => $pelangganId,
                'nomor_transaksi' => $nomorTransaksi,
                'tanggal' => $tanggal->format('Y-m-d H:i:s'),
                'subtotal' => $subtotal,
                'pajak' => $pajak,
                'total' => $total,
                'tunai' => $tunai,
                'kembalian' => $kembalian,
                'status' => 'selesai'
            ]);

            // Create transaction details
            foreach ($selectedProducts as $item) {
                \App\Models\DetilPenjualan::create([
                    'penjualan_id' => $penjualan->id,
                    'produk_id' => $item['produk_id'],
                    'jumlah' => $item['jumlah'],
                    'harga_jual' => $item['harga_jual'],
                    'subtotal' => $item['subtotal'],
                ]);
            }
        }

        // Create some discounts
        for ($i = 1; $i <= 5; $i++) {
            \App\Models\Diskon::create([
                'kode_diskon' => 'DISC' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'jenis_diskon' => $faker->randomElement(['persen', 'nominal']),
                'jumlah_diskon' => $faker->numberBetween(5, 50),
                'minimal_pembelian' => $faker->numberBetween(50000, 200000),
                'tanggal_mulai' => $faker->dateTimeBetween('-1 month', 'now'),
                'tanggal_selesai' => $faker->dateTimeBetween('now', '+1 month'),
                'status' => true,
                'maksimal_pemakaian' => $faker->numberBetween(10, 100), // Random between 10-100 uses
                'jumlah_terpakai' => $faker->numberBetween(0, 5) // Some already used
            ]);
        }
    }
}
