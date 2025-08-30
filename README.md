# 🛒 Sistem Kasir Toko

Aplikasi Point of Sale (POS) berbasis Laravel dengan sistem API yang terintegrasi untuk transaksi multi-platform.

## 📋 Deskripsi Projek

**Kasir Toko** adalah sistem manajemen toko yang lengkap dengan fitur:
- **💰 Transaksi POS** dengan perhitungan pajak 10% otomatis
- **📦 Manajemen Produk** dan kategori
- **👥 Manajemen Pelanggan** dan sistem diskon
- **📊 Laporan Penjualan** dengan analytics
- **🔌 REST API** untuk integrasi dengan sistem eksternal

## 🚀 Fitur Utama

### **🏪 Sistem Kasir**
- ✅ **Transaksi Real-time** dengan validasi stok
- ✅ **Sistem Cart** menggunakan Jackiedo Cart
- ✅ **Pajak 10%** otomatis untuk semua produk
- ✅ **Sistem Diskon** dengan validasi kompleks
- ✅ **Multiple Payment Methods** (Tunai, Kartu, Transfer, E-Wallet)

### **📱 API Integration**
- ✅ **13 REST API Endpoints** siap pakai
- ✅ **Compatible** dengan aplikasi eksternal
- ✅ **Same Business Logic** dengan web interface
- ✅ **Real-time Stock Update** via API

### **📊 Management Features**
- ✅ **Dashboard Analytics** dengan chart
- ✅ **Laporan Harian/Bulanan** 
- ✅ **User Management** dengan role-based access
- ✅ **Backup & Export** data

## 🛠️ Tech Stack

- **Framework**: Laravel 10
- **Database**: MySQL
- **Frontend**: AdminLTE + Bootstrap
- **Cart System**: Jackiedo Cart
- **Charts**: Chart.js
- **API**: REST API dengan JSON response

## 📦 Instalasi

### **Requirements**
- PHP 8.1+
- Composer
- MySQL 5.7+
- Node.js & NPM

### **Setup**
```bash
# Clone repository
git clone https://github.com/Fairuznp/kasir-toko.git
cd kasir-toko

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database setup
php artisan migrate
php artisan db:seed

# Build assets
npm run build

# Run server
php artisan serve
```

## 🔧 Konfigurasi

### **Database Configuration**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=kasir_toko
DB_USERNAME=root
DB_PASSWORD=
```

### **API Configuration**
```env
# Untuk sistem eksternal
KASIR_API_URL=http://localhost:8000/api/pos
```

## 📚 Dokumentasi

### **Web Interface**
- **URL**: `http://localhost:8000`
- **Login**: Lihat seeder untuk kredensial default

### **API Documentation**
- **Base URL**: `http://localhost:8000/api/pos`
- **Documentation**: [README_API.md](README_API.md)
- **Endpoints**: 13 endpoint siap pakai

## 🎯 Penggunaan

### **Untuk Kasir (Web Interface)**
1. Login ke sistem
2. Tambah produk ke keranjang
3. Apply diskon jika ada
4. Pilih metode pembayaran
5. Selesaikan transaksi

### **Untuk Developer (API)**
```bash
# Get semua produk
GET /api/pos/produk

# Buat transaksi
POST /api/pos/transaksi
{
    "items": [{"produk_id": 1, "quantity": 2}],
    "pelanggan_id": 1,
    "metode_pembayaran": "tunai",
    "jumlah_bayar": 50000
}
```

## 🏗️ Arsitektur

```
kasir-toko/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/PosApiController.php    # API Endpoints
│   │   └── ...                         # Web Controllers
│   ├── Models/                         # Eloquent Models
│   ├── Services/                       # Business Logic
│   └── Repositories/                   # Data Layer
├── routes/
│   ├── web.php                         # Web Routes
│   └── api.php                         # API Routes
└── README_API.md                       # API Documentation
```

## 🔌 Integrasi dengan Projek Lain

API ini dirancang untuk digunakan oleh **sistem transaksi eksternal**:

1. **Setup Projek Laravel Baru** untuk interface transaksi
2. **Konsumsi API** menggunakan HTTP Client (Guzzle)
3. **Gunakan Endpoint** yang tersedia untuk transaksi
4. **Data Tersimpan** di database kasir_toko (terpusat)

### **Contoh Integration**
```php
// Di projek eksternal
$response = Http::post('http://localhost:8000/api/pos/transaksi', [
    'items' => [['produk_id' => 1, 'quantity' => 2]],
    'jumlah_bayar' => 50000
]);
```

## 🤝 Contributing

1. Fork projek
2. Buat feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

## 📝 License

Projek ini menggunakan [MIT License](LICENSE).

## 👨‍💻 Developer

- **Fairuz Nurul Pratama** - [Fairuznp](https://github.com/Fairuznp)

## 📞 Support

Jika ada pertanyaan atau issue:
- **Open Issue** di GitHub
- **Email**: [your-email@example.com]
- **Documentation**: [README_API.md](README_API.md)

---

**⭐ Star projek ini jika membantu!**
