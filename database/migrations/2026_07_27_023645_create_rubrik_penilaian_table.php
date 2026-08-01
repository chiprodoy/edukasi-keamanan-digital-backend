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
        Schema::create('rubrik_penilaian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('outcome_id')->constrained('outcomes')->onDelete('cascade');
            $table->integer('batas_bawah_skor');
            $table->integer('batas_atas_skor');
            $table->string('label_level');
            $table->integer('level');
            $table->text('deskripsi_capaian');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rubrik_penilaian');
    }
};
