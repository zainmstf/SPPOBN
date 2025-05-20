<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('solusi', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('nama', 100);
            $table->text('deskripsi');
            $table->text('peringatan_konsultasi')->nullable()->comment('Peringatan untuk konsultasi dokter/ahli gizi');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('solusi');
    }
};
