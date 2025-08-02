<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('diskons', function (Blueprint $table) {
            $table->id();
            $table->string('kode_diskon')->unique();
            $table->enum('jenis_diskon', ['persen', 'nominal']);
            $table->integer('jumlah_diskon');
            $table->integer('minimal_pembelian')->default(0);
            $table->datetime('tanggal_mulai');
            $table->datetime('tanggal_selesai');
            $table->boolean('status')->default(true);
            $table->unsignedBigInteger('kategori_id')->nullable();
            $table->unsignedBigInteger('produk_id')->nullable();

            $table->foreign('kategori_id')->references('id')->on('kategoris')->onDelete('cascade');
            $table->foreign('produk_id')->references('id')->on('produks')->onDelete('cascade');
        });

        // Tambah kolom diskon ke tabel penjualans
        Schema::table('penjualans', function (Blueprint $table) {
            $table->unsignedBigInteger('diskon_id')->nullable()->after('pajak');
            $table->integer('nilai_diskon')->default(0)->after('diskon_id');

            $table->foreign('diskon_id')->references('id')->on('diskons')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('penjualans', function (Blueprint $table) {
            $table->dropForeign(['diskon_id']);
            $table->dropColumn(['diskon_id', 'nilai_diskon']);
        });

        Schema::dropIfExists('diskons');
    }
};
