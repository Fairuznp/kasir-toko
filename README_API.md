# Kasir Toko API Documentation

## Base URL

```
http://localhost:8000/api/pos
```

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
            "nama_diskon": "Diskon 10%",
            "nilai_diskon": 10
        }
    ]
}
```

---

### 5. **POST /transaksi**

**Description**: Membuat transaksi baru.

**Request Body**:

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
        "total": 95000,
        "kembalian": 5000
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
        "nama_diskon": "Diskon 10%",
        "nilai_diskon": 5000,
        "subtotal_setelah_diskon": 45000
    }
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
