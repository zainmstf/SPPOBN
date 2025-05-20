<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('konten_edukasi', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 255);
            $table->string('slug', 255)->unique();
            $table->enum('jenis', ['artikel', 'video', 'infografis']);
            $table->enum('kategori', ['osteoporosis_dasar', 'nutrisi_tulang', 'pencegahan', 'pengobatan']);
            $table->text('deskripsi');
            $table->longText('konten')->nullable();
            $table->string('path', 255)->nullable();
            $table->string('thumbnail', 255)->nullable();
            $table->boolean('status')->default(true);
            $table->bigInteger('view_count')->default(0);
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('konten_edukasi');
    }
};

