<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('aturan', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 10)->unique();
            $table->string('deskripsi', 255);
            $table->text('premis')->comment('Format: F001^F002');
            $table->string('konklusi', 10)->comment('Kode solusi atau fakta baru');
            $table->enum('jenis_konklusi', ['fakta', 'solusi']);
            $table->foreignId('solusi_id')->nullable()->constrained('solusi')->comment('ID Rekomendasi yang terkait');
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('solusi_id')
                ->references('id')
                ->on('solusi')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('aturan');
    }
};
