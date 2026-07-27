<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('detail_hasil_kuis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hasil_kuis_id')->constrained('hasil_kuis')->onDelete('cascade');
            $table->foreignId('kuis_id')->constrained('kuis')->onDelete('cascade');
            $table->foreignId('opsi_dipilih_id')->constrained('opsi_jawaban')->onDelete('cascade');
            $table->boolean('is_benar');
            $table->integer('poin_didapat');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_hasil_kuis');
    }
};
