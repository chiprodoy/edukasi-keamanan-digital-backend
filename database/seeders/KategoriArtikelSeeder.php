<?php

namespace Database\Seeders;

use App\Models\KategoriArtikel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriArtikelSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nama_kategori' => 'Panduan Keamanan',
                'deskripsi'     => 'Berita dan tutorial seputar dunia teknologi terupdate.',
            ],
            [
                'nama_kategori' => 'Tips Praktis',
                'deskripsi'     => 'Tips hidup sehat, nutrisi, dan gaya hidup seimbang.',
            ],
            [
                'nama_kategori' => 'Edukasi Siber',
                'deskripsi'     => 'Artikel seputar pendidikan dan ilmu pengetahuan.',
            ],
        ];

        foreach ($categories as $cat) {
            KategoriArtikel::create([
                'nama_kategori' => $cat['nama_kategori'],
                'slug'          => Str::slug($cat['nama_kategori']),
                'deskripsi'     => $cat['deskripsi'],
            ]);
        }
    }
}
