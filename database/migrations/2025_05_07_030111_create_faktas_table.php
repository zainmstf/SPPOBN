<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('fakta', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->text('deskripsi');
            $table->enum('kategori', ['risiko_osteoporosis', 'asupan_nutrisi', 'preferensi_makanan']);
            $table->text('pertanyaan');
            $table->string('next_if_yes', 10)->nullable()->comment('Kode fakta/pertanyaan berikutnya jika jawaban ya');
            $table->string('next_if_no', 10)->nullable()->comment('Kode fakta/pertanyaan berikutnya jika jawaban tidak');
            $table->boolean('is_first')->default(false)->comment('Menandai pertanyaan pertama untuk setiap kategori');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('fakta');
    }
};

