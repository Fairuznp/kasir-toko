# Kasir Toko API Documentation

## Base URL

```
http://localhost:8000/api/pos
```

## Authentication

### **POST /login-kasir**

**Description**: Login untuk kasir/petugas.

**Request Body**:

```json
{
    "username": "petugas1",
    "password": "password123"
}
```

**Response Success**:

```json
{
    "success": true,
    "message": "Login berhasil",
    "data": {
        "kasir_id": 2,
        "nama": "Petugas 1",
        "username": "petugas1",
        "role": "petugas"
    }
}
```

**Response Error**:

```json
{
    "success": false,
    "message": "Username tidak ditemukan"
}
```

---

## Endpoints

### 1. **GET /produk**

**Description**: Mengambil daftar produk yang tersedia.

**Response**:

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama_produk": "Chiki Taro",
            "kategori_id": 2,
            "stok": 100,
            "harga_jual": 5000,
            "kategori": {
                "id": 2,
                "nama_kategori": "Snack"
            }
        }
    ],
    "count": 1
}
```

---

### 2. **GET /kategori**

**Description**: Mengambil daftar kategori produk.

**Response**:

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama_kategori": "Minuman"
        },
        {
            "id": 2,
            "nama_kategori": "Snack"
        }
    ]
}
```

---

### 3. **GET /pelanggan**

**Description**: Mengambil daftar pelanggan.

**Response**:

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama": "John Doe",
            "email": "john@example.com"
        }
    ]
}
```

---

### 4. **GET /diskon**

**Description**: Mengambil daftar diskon aktif.

**Response**:

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "kode_diskon": "DISKON10",
            "jenis_diskon": "persentase",
            "jumlah_diskon": 10,
            "minimal_pembelian": 50000,
            "status": true,
            "kategori_id": null,
            "produk_id": null
        }
    ],
    "count": 1
}
```

---

### 5. **POST /transaksi**

**Description**: Membuat transaksi baru dengan sistem cart yang sama seperti kasir utama. **Wajib menyertakan kasir_id dari hasil login kasir**.

**Request Body**:

```json
{
    "kasir_id": 2,
    "items": [
        {
            "produk_id": 1,
            "quantity": 2
        },
        {
            "produk_id": 2,
            "quantity": 1
        }
    ],
    "extra_info": {
        "diskon": {
            "id": 1
        }
    },
    "pelanggan_id": 1,
    "metode_pembayaran": "tunai",
    "jumlah_bayar": 100000
}
```

**Response**:

```json
{
    "success": true,
    "message": "Transaksi berhasil",
    "data": {
        "transaksi_id": 123,
        "nomor_transaksi": "TRX-20250830-001",
        "subtotal": 45000,
        "pajak": 4500,
        "nilai_diskon": 5000,
        "total": 44500,
        "tunai": 100000,
        "kembalian": 55500,
        "tanggal": "2025-08-30 16:45:00"
    }
}
```

---

### 6. **POST /cek-stok**

**Description**: Memeriksa stok produk.

**Request Body**:

```json
{
    "produk_id": 1
}
```

**Response**:

```json
{
    "success": true,
    "data": {
        "produk_id": 1,
        "nama_produk": "Chiki Taro",
        "stok_tersedia": 100,
        "harga_jual": 5000
    }
}
```

---

### 7. **POST /apply-diskon**

**Description**: Menerapkan diskon pada keranjang belanja.

**Request Body**:

```json
{
    "kode_diskon": "DISKON10",
    "items": [
        {
            "produk_id": 1,
            "quantity": 10
        }
    ]
}
```

**Response**:

```json
{
    "success": true,
    "data": {
        "diskon_id": 1,
        "kode_diskon": "DISKON10",
        "jenis_diskon": "persentase",
        "jumlah_diskon": 10,
        "nilai_diskon": 5000,
        "subtotal_setelah_diskon": 45000
    }
}
```

---

### 8. **POST /calculate-cart**

**Description**: Menghitung total keranjang dengan pajak 10%.

**Request Body**:

```json
{
    "items": [
        {
            "produk_id": 1,
            "quantity": 2
        }
    ],
    "diskon_id": 1
}
```

**Response**:

```json
{
    "success": true,
    "data": {
        "items": [
            {
                "produk_id": 1,
                "nama_produk": "Chiki Taro",
                "harga_jual": 5000,
                "quantity": 2,
                "subtotal": 10000
            }
        ],
        "subtotal": 10000,
        "diskon": {
            "id": 1,
            "kode_diskon": "DISKON10",
            "jenis_diskon": "persentase",
            "jumlah_diskon": 10,
            "nilai_diskon": 1000
        },
        "diskon_amount": 1000,
        "subtotal_after_diskon": 9000,
        "pajak": {
            "rate": 10,
            "title": "Pajak PPN 10%",
            "amount": 900
        },
        "total": 9900
    }
}
```

---

### 9. **GET /produk/{id}**

**Description**: Mengambil detail produk berdasarkan ID.

**Response**:

```json
{
    "success": true,
    "data": {
        "id": 1,
        "nama_produk": "Chiki Taro",
        "kategori_id": 2,
        "stok": 100,
        "harga_jual": 5000,
        "kategori": {
            "id": 2,
            "nama_kategori": "Snack"
        }
    }
}
```

---

### 10. **GET /produk/kategori/{kategoriId}**

**Description**: Mengambil produk berdasarkan kategori.

**Response**:

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama_produk": "Chiki Taro",
            "kategori_id": 2,
            "stok": 100,
            "harga_jual": 5000,
            "kategori": {
                "id": 2,
                "nama_kategori": "Snack"
            }
        }
    ],
    "count": 1
}
```

---

### 11. **GET /search-produk**

**Description**: Mencari produk berdasarkan nama atau kode.

**Query Parameters**:

-   `q`: Kata kunci pencarian
-   `kategori_id`: Filter berdasarkan kategori (opsional)

**Response**:

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama_produk": "Chiki Taro",
            "kategori_id": 2,
            "stok": 100,
            "harga_jual": 5000,
            "kategori": {
                "id": 2,
                "nama_kategori": "Snack"
            }
        }
    ],
    "count": 1
}
```

---

### 12. **GET /metode-pembayaran**

**Description**: Mengambil daftar metode pembayaran.

**Response**:

```json
{
    "success": true,
    "data": [
        {
            "id": "tunai",
            "nama": "Tunai"
        },
        {
            "id": "kartu_kredit",
            "nama": "Kartu Kredit"
        },
        {
            "id": "transfer",
            "nama": "Transfer Bank"
        }
    ]
}
```

---

### 13. **POST /validate-transaksi**

**Description**: Memvalidasi data transaksi sebelum disimpan.

**Request Body**:

```json
{
    "items": [
        {
            "produk_id": 1,
            "quantity": 2
        }
    ]
}
```

**Response**:

```json
{
    "success": true,
    "message": "Validasi berhasil"
}
```

---

## Error Handling

-   Semua endpoint akan mengembalikan response dengan format berikut jika terjadi error:

```json
{
    "success": false,
    "message": "Deskripsi error"
}
```

## Catatan

-   Pastikan server Laravel berjalan di `http://localhost:8000` sebelum mengakses API.
-   Gunakan Postman atau tool sejenis untuk menguji endpoint.
-   Endpoint ini dirancang untuk digunakan oleh sistem transaksi eksternal.
-   API menggunakan sistem cart dan business logic yang sama dengan aplikasi kasir utama.

---

## Ringkasan API Endpoints

### **Master Data (8 Endpoints GET)**

1. `GET /produk` - Semua produk tersedia
2. `GET /produk/{id}` - Detail produk spesifik
3. `GET /produk/kategori/{kategoriId}` - Produk per kategori
4. `GET /search-produk?q=keyword` - Pencarian produk
5. `GET /kategori` - Daftar kategori
6. `GET /pelanggan` - Daftar pelanggan
7. `GET /diskon` - Diskon aktif
8. `GET /metode-pembayaran` - Opsi pembayaran

### **Transaksi & Cart (5 Endpoints POST)**

9. `POST /calculate-cart` - Hitung total + pajak 10%
10. `POST /validate-transaksi` - Validasi sebelum simpan
11. `POST /cek-stok` - Cek ketersediaan stok
12. `POST /apply-diskon` - Terapkan kode diskon
13. `POST /transaksi` - **Simpan transaksi ke database**

### **Fitur Utama**

-   ✅ **Pajak 10%** otomatis
-   ✅ **Sistem diskon** dengan validasi
-   ✅ **Update stok** real-time
-   ✅ **Cart management** identik dengan kasir utama
-   ✅ **Database sync** dengan sistem utama

---

## Contoh Penggunaan di Postman

### **Test Transaksi Lengkap - Step by Step**

#### **Step 1: Ambil Data Produk**

```
Method: GET
URL: http://localhost:8000/api/pos/produk
```

**Response**: Daftar produk dengan ID dan harga

#### **Step 2: Ambil Data Diskon (Opsional)**

```
Method: GET
URL: http://localhost:8000/api/pos/diskon
```

**Response**: Daftar diskon aktif dengan ID

#### **Step 3: Hitung Total Cart (Preview)**

```
Method: POST
URL: http://localhost:8000/api/pos/calculate-cart
Headers: Content-Type: application/json

Body:
{
    "items": [
        {
            "produk_id": 1,
            "quantity": 2
        }
    ],
    "diskon_id": 1
}
```

**Response**: Detail perhitungan dengan pajak 10%

#### **Step 4: Buat Transaksi**

```
Method: POST
URL: http://localhost:8000/api/pos/transaksi
Headers: Content-Type: application/json

Body:
{
    "items": [
        {
            "produk_id": 1,
            "quantity": 2
        }
    ],
    "extra_info": {
        "diskon": {
            "id": 1
        }
    },
    "pelanggan_id": 1,
    "metode_pembayaran": "tunai",
    "jumlah_bayar": 100000
}
```

**Response**: Detail transaksi dengan nomor transaksi dan kembalian

---

## Contoh Skenario Testing

### **Skenario 1: Transaksi Sederhana (Tanpa Diskon)**

```json
{
    "items": [
        {
            "produk_id": 1,
            "quantity": 3
        }
    ],
    "pelanggan_id": 1,
    "metode_pembayaran": "tunai",
    "jumlah_bayar": 50000
}
```

### **Skenario 2: Transaksi dengan Diskon**

```json
{
    "items": [
        {
            "produk_id": 1,
            "quantity": 5
        },
        {
            "produk_id": 2,
            "quantity": 2
        }
    ],
    "extra_info": {
        "diskon": {
            "id": 1
        }
    },
    "pelanggan_id": 1,
    "metode_pembayaran": "kartu_kredit",
    "jumlah_bayar": 100000
}
```

### **Skenario 3: Transaksi Multiple Items**

```json
{
    "items": [
        {
            "produk_id": 1,
            "quantity": 2
        },
        {
            "produk_id": 2,
            "quantity": 1
        },
        {
            "produk_id": 3,
            "quantity": 3
        }
    ],
    "pelanggan_id": 2,
    "metode_pembayaran": "transfer",
    "jumlah_bayar": 200000
}
```

---

## Alur Penggunaan API

### **Step 1: Login Kasir**
```bash
POST /api/pos/login-kasir
{
    "username": "petugas1",
    "password": "password123"
}
```

### **Step 2: Buat Transaksi**
```bash
POST /api/pos/transaksi
{
    "kasir_id": 2,  // dari hasil login
    "items": [
        {
            "produk_id": 1,
            "quantity": 2
        }
    ],
    "pelanggan_id": 1,  // opsional
    "metode_pembayaran": "tunai",
    "jumlah_bayar": 50000
}
```

---

## Expected Results

### **✅ Yang Akan Terjadi Setelah Transaksi Berhasil:**

1. **Database Update**: Data tersimpan di table `penjualans` dan `detil_penjualans`
2. **Stock Update**: Stok produk otomatis berkurang sesuai quantity
3. **Response Complete**: Mendapat nomor transaksi, total, pajak, dan kembalian
4. **Business Logic**: Pajak 10% dan diskon dihitung sesuai sistem kasir utama
5. **Kasir Recorded**: Nama kasir tercatat di transaksi

### **⚠️ Error yang Mungkin Terjadi:**

-   **Kasir tidak valid**: "ID Kasir wajib diisi" atau "Kasir tidak ditemukan"
-   **Role tidak sesuai**: "User tidak memiliki hak akses sebagai kasir"
-   **Stok tidak mencukupi**: "Stok produk tidak mencukupi"
-   **Cash kurang**: "Cash tidak mencukupi"
-   **Produk tidak ditemukan**: "Produk dengan ID {id} tidak ditemukan"
-   **Diskon tidak valid**: "Kode diskon tidak valid atau sudah kadaluarsa"
