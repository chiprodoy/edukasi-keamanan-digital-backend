<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Kuis;
use App\Models\Materi;
use App\Models\OpsiJawaban;
use App\Models\Outcome;
use Illuminate\Database\Seeder;

class MateriSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::first();
        $outcomeSec1 = Outcome::where('kode_outcome', 'OUT-SEC-01')->first();
        $outcomeSec2 = Outcome::where('kode_outcome', 'OUT-SEC-02')->first();

        // Materi 1: Modus Phishing APK
        $materi1 = Materi::create([
            'admin_id'  => $admin->id,
            'judul'     => 'Waspada Modus Penipuan File .APK di WhatsApp',
            'slug'      => 'waspada-modus-penipuan-file-apk-whatsapp',
            'kategori'  => 'Keamanan Siber',
            'konten'    => 'Penipuan berbasis file .APK marak terjadi melalui pengiriman undangan pernikahan atau kurir paket palsu...',
            'media_url' => 'https://youtube.com/watch?v=example1',
            'status'    => 'active',
        ]);

        $materi1->outcomes()->attach([$outcomeSec1->id, $outcomeSec2->id]);

        // Kuis untuk Materi 1
        $kuis1 = Kuis::create([
            'materi_id' => $materi1->id,
            'teks_soal' => 'Apa yang harus dilakukan jika menerima file berformat .APK dari nomor tidak dikenal di WhatsApp?',
            'poin'      => 50,
        ]);

        OpsiJawaban::create(['kuis_id' => $kuis1->id, 'teks_pilihan' => 'Langsung mengunduh dan membukanya', 'is_benar' => false]);
        OpsiJawaban::create(['kuis_id' => $kuis1->id, 'teks_pilihan' => 'Abaikan, hapus pesan, dan blokir nomor tersebut', 'is_benar' => true]);
        OpsiJawaban::create(['kuis_id' => $kuis1->id, 'teks_pilihan' => 'Meneruskan pesan ke grup keluarga', 'is_benar' => false]);
        OpsiJawaban::create(['kuis_id' => $kuis1->id, 'teks_pilihan' => 'Membalas pesan untuk bertanya asal pengirim', 'is_benar' => false]);

        $kuis2 = Kuis::create([
            'materi_id' => $materi1->id,
            'teks_soal' => 'Bahaya utama menginstall aplikasi buatan luar (*.APK) secara sembarangan adalah...',
            'poin'      => 50,
        ]);

        OpsiJawaban::create(['kuis_id' => $kuis2->id, 'teks_pilihan' => 'Aplikasi dapat mencuri SMS OTP dan data perbankan', 'is_benar' => true]);
        OpsiJawaban::create(['kuis_id' => $kuis2->id, 'teks_pilihan' => 'Baterai HP menjadi lebih hemat', 'is_benar' => false]);
        OpsiJawaban::create(['kuis_id' => $kuis2->id, 'teks_pilihan' => 'Sinyal HP meningkat drastis', 'is_benar' => false]);
        OpsiJawaban::create(['kuis_id' => $kuis2->id, 'teks_pilihan' => 'Kamera HP terlindungi otomatis', 'is_benar' => false]);
    }
}
