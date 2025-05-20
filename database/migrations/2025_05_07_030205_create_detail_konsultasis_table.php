<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('detail_konsultasi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('konsultasi_id')->constrained('konsultasi');
            $table->foreignId('fakta_id')->constrained('fakta');
            $table->enum('jawaban', ['ya', 'tidak']);
            $table->timestamps();
            $table->foreignId('fakta_id')
                ->constrained('fakta')
                ->onDelete('cascade')
                ->change();
        });

    }

    public function down()
    {
        Schema::dropIfExists('detail_konsultasi');
    }
};
