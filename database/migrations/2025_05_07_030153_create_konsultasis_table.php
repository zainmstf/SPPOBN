<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('konsultasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('pertanyaan_terakhir', 10)->nullable()->comment('Kode fakta/pertanyaan terakhir yang dijawab');
            $table->enum('status', ['selesai', 'belum_selesai', 'sedang_berjalan'])->default('sedang_berjalan');
            $table->integer('sesi')->default(1);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('konsultasi');
    }
};
