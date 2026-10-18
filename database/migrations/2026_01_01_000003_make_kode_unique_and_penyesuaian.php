<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Perkuat kolom kode: unique (anti duplikat nota) + dukung jenis penyesuaian.
     */
    public function up(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->unique('kode');
        });

        DB::statement("ALTER TABLE transaksis MODIFY jenis ENUM('restock', 'penjualan', 'penyesuaian') NOT NULL AFTER tanggal");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE transaksis MODIFY jenis ENUM('restock', 'penjualan') NOT NULL AFTER tanggal");

        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropUnique(['kode']);
        });
    }
};
