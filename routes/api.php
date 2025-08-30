<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PosApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// POS API Routes
Route::prefix('pos')->group(function () {
    // Master Data
    Route::get('/produk', [PosApiController::class, 'getProduk']);
    Route::get('/kategori', [PosApiController::class, 'getKategori']);
    Route::get('/pelanggan', [PosApiController::class, 'getPelanggan']);
    Route::get('/diskon', [PosApiController::class, 'getDiskon']);

    // Cart & Transaksi
    Route::post('/calculate-cart', [PosApiController::class, 'calculateCart']);
    Route::post('/transaksi', [PosApiController::class, 'createTransaksi']);
    Route::post('/cek-stok', [PosApiController::class, 'cekStok']);
    Route::post('/apply-diskon', [PosApiController::class, 'applyDiskon']);
});
