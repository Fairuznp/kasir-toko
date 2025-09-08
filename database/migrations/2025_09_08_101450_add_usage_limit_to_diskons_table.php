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
        Schema::table('diskons', function (Blueprint $table) {
            $table->integer('maksimal_pemakaian')->nullable()->after('status')->comment('Maksimal berapa kali diskon bisa digunakan');
            $table->integer('jumlah_terpakai')->default(0)->after('maksimal_pemakaian')->comment('Berapa kali diskon sudah digunakan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('diskons', function (Blueprint $table) {
            $table->dropColumn(['maksimal_pemakaian', 'jumlah_terpakai']);
        });
    }
};
