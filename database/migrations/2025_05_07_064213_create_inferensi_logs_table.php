<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('inferensi_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konsultasi_id')->constrained('konsultasi');
            $table->foreignId('aturan_id')->constrained('aturan');
            $table->string('fakta_terbentuk', 10);
            $table->text('premis_terpenuhi')->comment('Fakta yang memicu');
            $table->timestamp('waktu')->useCurrent();

            $table->index('konsultasi_id', 'idx_konsultasi');
        });
    }

    public function down()
    {
        Schema::dropIfExists('inferensi_log');
    }
};

