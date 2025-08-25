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
        Schema::table('detil_penjualans', function (Blueprint $table) {
            $table->foreignId('diskon_id')->nullable()->after('produk_id')->constrained('diskons');
            $table->string('nama_diskon')->nullable()->after('diskon_id');
            $table->decimal('nilai_diskon', 5, 2)->default(0)->after('nama_diskon');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detil_penjualans', function (Blueprint $table) {
            $table->dropForeign(['diskon_id']);
            $table->dropColumn(['diskon_id', 'nama_diskon', 'nilai_diskon']);
        });
    }
};
