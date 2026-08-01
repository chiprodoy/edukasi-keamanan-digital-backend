<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Artikel;
use Illuminate\Database\Seeder;

class ArtikelSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::first();

        // 1. Pinned Cyber Alert
        Artikel::create([
            'admin_id'    => $admin->id,
            'judul'       => 'CYBER ALERT: Marak Penipuan Catut Nama Dinas Kominfo OKU',
            'slug'        => 'cyber-alert-marak-penipuan-catut-nama-dinas-kominfo-oku',
            'kategori_artikel_id'    => 1,
            'konten'      => 'Dihimbau kepada seluruh warga OKU untuk mewaspadai pesan singkat yang mengatasnamakan pejabat Diskominfo OKU...',
            'thumbnail'   => 'alerts/alert-kominfo.jpg',
            'is_pinned'   => true,
            'status'      => 'published',
            'views_count' => 125,
        ]);

        // 2. Artikel Umum
        Artikel::create([
            'admin_id'    => $admin->id,
            'judul'       => 'Tips Membuat Kombinasi Password Yang Aman dan Mudah Diingat',
            'slug'        => 'tips-membuat-kombinasi-password-yang-aman',
            'kategori_artikel_id'    => 3,
            'konten'      => 'Gunakan kalimat acak diselingi angka dan simbol untuk menciptakan proteksi akun yang kokoh...',
            'thumbnail'   => 'artikel/tips-password.jpg',
            'is_pinned'   => false,
            'status'      => 'published',
            'views_count' => 45,
        ]);
    }
}
