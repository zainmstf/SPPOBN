<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rekomendasi_nutrisi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solusi_id')->constrained('solusi');
            $table->string('nutrisi', 100);
            $table->text('kontraindikasi')->nullable()->comment('Kondisi yang menyebabkan nutrisi tidak direkomendasikan');
            $table->text('alternatif')->nullable()->comment('Alternatif nutrisi jika ada kontraindikasi');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('rekomendasi_nutrisi');
    }
};

