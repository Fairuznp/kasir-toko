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
        Schema::table('produks', function (Blueprint $table) {
            $table->enum('pricing_type', ['manual', 'margin'])->default('manual')->after('harga_jual');
            $table->decimal('margin_percentage', 5, 2)->nullable()->after('pricing_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('produks', function (Blueprint $table) {
            $table->dropColumn(['pricing_type', 'margin_percentage']);
        });
    }
};
