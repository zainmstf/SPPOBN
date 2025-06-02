<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Tambah kolom kategori ke tabel solusi
        Schema::table('solusi', function (Blueprint $table) {
            $table->enum('kategori', [
                'start',
                'skrining_awal',
                'klasifikasi_risiko_frax',
                'penilaian_nutrisi',
                'preferensi_makanan'
            ])->after('kode')->nullable();

            // Tambah index untuk performa
            $table->index('kategori');
        });

        // Tambah kolom kategori ke tabel aturan
        Schema::table('aturan', function (Blueprint $table) {
            $table->enum('kategori', [
                'start',
                'skrining_awal',
                'klasifikasi_risiko_frax',
                'penilaian_nutrisi',
                'preferensi_makanan'
            ])->after('kode')->nullable();

            // Tambah index untuk performa
            $table->index('kategori');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solusi', function (Blueprint $table) {
            $table->dropIndex(['kategori']);
            $table->dropColumn('kategori');
        });

        Schema::table('aturan', function (Blueprint $table) {
            $table->dropIndex(['kategori']);
            $table->dropColumn('kategori');
        });
    }
};