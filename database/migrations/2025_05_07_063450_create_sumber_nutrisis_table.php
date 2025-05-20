<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('sumber_nutrisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rekomendasi_nutrisi_id')->constrained('rekomendasi_nutrisi');
            $table->enum('jenis_sumber', ['suplemen', 'makanan']);
            $table->string('nama_sumber', 255);
            $table->string('takaran', 100)->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sumber_nutrisi');
    }
};

