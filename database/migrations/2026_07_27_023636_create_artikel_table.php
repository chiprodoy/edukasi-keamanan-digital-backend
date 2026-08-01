<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\KategoriArtikel;
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('artikel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('admin_id')->constrained('admin')->onDelete('cascade');
            $table->string('judul');
            $table->string('slug')->unique();
        // Menambahkan foreign key berelasi ke kategori_artikels
            $table->foreignIdFor(KategoriArtikel::class, 'kategori_artikel_id')->onDelete('cascade');
            $table->longText('konten');
            $table->string('thumbnail')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->enum('status', ['published', 'draft'])->default('published');
            $table->unsignedBigInteger('views_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('artikel');
    }
};
