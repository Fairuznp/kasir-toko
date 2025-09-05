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

        // Create 8 Kategori (5 original + 3 baru)
        $kategoris = [
            'Makanan',
            'Minuman',
            'Kemasan',
            'Kebutuhan Pokok',
            'Ice Cream',
            'Laptop',
            'Handphone',
            'Mainan'
        ];

        foreach ($kategoris as $kategori) {
            \App\Models\Kategori::create([
                'nama_kategori' => $kategori,
            ]);
        }

        // Create 10 products per category (8 categories * 10 products = 80 products)
        $produkData = [
            // Makanan
            1 => ['Chiki Taro', 'Indomie', 'Biskuit Oreo', 'Keripik Kentang', 'Cokelat Silverqueen', 'Permen Yupi', 'Wafer Tango', 'Sereal Energen', 'Roti Tawar', 'Mie Sedaap'],
            // Minuman
            2 => ['Le Mineral', 'Aqua', 'Coca Cola', 'Fanta', 'Sprite', 'Teh Botol Sosro', 'Kopi Kapal Api', 'Susu Ultra', 'Yakult', 'Pocari Sweat'],
            // Kemasan
            3 => ['Box Kertas', 'Plastik Kiloan', 'Kantong Kresek', 'Styrofoam', 'Bubble Wrap', 'Lakban', 'Kardus Besar', 'Paper Bag', 'Aluminium Foil', 'Plastic Wrap'],
            // Kebutuhan Pokok
            4 => ['Telur', 'Beras', 'Gula Pasir', 'Minyak Goreng', 'Garam', 'Tepung Terigu', 'Kecap Manis', 'Saos Tomat', 'Mentega', 'Susu Kental Manis'],
            // Ice Cream
            5 => ['Magnum', 'Walls Cornetto', 'Es Krim Aice', 'Paddle Pop', 'Vienetta', 'Ben & Jerry', 'Haagen Dazs', 'Baskin Robbins', 'Es Mambo', 'Es Lilin'],
            // Laptop
            6 => ['Asus VivoBook', 'Lenovo ThinkPad', 'HP Pavilion', 'Dell Inspiron', 'Acer Aspire', 'MacBook Air', 'MSI Gaming', 'Toshiba Satellite', 'Samsung Galaxy Book', 'Surface Laptop'],
            // Handphone
            7 => ['iPhone 15', 'Samsung Galaxy S24', 'Xiaomi Redmi Note', 'Oppo Reno', 'Vivo V30', 'Realme GT', 'Huawei P60', 'OnePlus 12', 'Google Pixel 8', 'Nothing Phone'],
            // Mainan
            8 => ['Lego Classic', 'Barbie Doll', 'Hot Wheels', 'Puzzle 1000 pcs', 'Rubik Cube', 'Monopoly', 'UNO Cards', 'Beyblade', 'Pokemon Cards', 'Action Figure']
        ];

        $productId = 1;
        foreach ($produkData as $kategoriId => $products) {
            foreach ($products as $index => $productName) {
                $hargaModal = $faker->numberBetween(10000, 500000);
                $hargaJual = $hargaModal + ($hargaModal * 0.3); // Markup 30%

                \App\Models\Produk::create([
                    'kategori_id' => $kategoriId,
                    'kode_produk' => str_pad($productId, 4, '0', STR_PAD_LEFT),
                    'nama_produk' => $productName,
                    'harga_modal' => $hargaModal,
                    'harga_jual' => $hargaJual,
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
                $productId = $faker->numberBetween(1, 80);
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

        // Create some discounts
        for ($i = 1; $i <= 5; $i++) {
            \App\Models\Diskon::create([
                'kode_diskon' => 'DISC' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'jenis_diskon' => $faker->randomElement(['persen', 'nominal']),
                'jumlah_diskon' => $faker->numberBetween(5, 50),
                'minimal_pembelian' => $faker->numberBetween(50000, 200000),
                'tanggal_mulai' => $faker->dateTimeBetween('-1 month', 'now'),
                'tanggal_selesai' => $faker->dateTimeBetween('now', '+1 month'),
                'status' => true
            ]);
        }
    }
}
