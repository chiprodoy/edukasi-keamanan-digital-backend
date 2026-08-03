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
            'materi_id'     => $materi1->id,
            'judul'         => 'Evaluasi Dasar: Keamanan Akun & Kode OTP',
            'deskripsi'     => 'Kuis evaluasi untuk menguji pemahaman warga terkait kerahasiaan OTP.',
            'durasi_menit'  => 10,
            'passing_score' => 70,
            'is_active'     => true,
        ]);
        $soal1 = $kuis1->soal_kuis()->create([
            'teks_soal' => 'Apa yang sebaiknya dilakukan jika menerima pesan WhatsApp dari nomor asing yang mengirimkan file .APK?',
            'poin'      => 100,
        ]);
        OpsiJawaban::create(['soal_kuis_id' => $soal1->id, 'teks_jawaban' => 'Langsung mengunduh dan membukanya', 'poin' => 25, 'is_correct' => false]);
        OpsiJawaban::create(['soal_kuis_id' => $soal1->id, 'teks_jawaban' => 'Abaikan, hapus pesan, dan blokir nomor tersebut', 'poin' => 25, 'is_correct' => true]);
        OpsiJawaban::create(['soal_kuis_id' => $soal1->id, 'teks_jawaban' => 'Meneruskan pesan ke grup keluarga', 'poin' => 25, 'is_correct' => false]);
        OpsiJawaban::create(['soal_kuis_id' => $soal1->id, 'teks_jawaban' => 'Membalas pesan untuk bertanya asal pengirim', 'poin' => 25, 'is_correct' => false]);

        $kuis2 = Kuis::create([
            'materi_id' => $materi1->id,
            'judul'     => 'Evaluasi Lanjutan: Risiko Mengunduh APK Asing',
            'deskripsi' => 'Bahaya utama menginstall aplikasi buatan luar (*.APK) secara sembarangan adalah...',
            'durasi_menit' => 10,
            'passing_score' => 70,
            'is_active' => true,
        ]);

        $soal2 = $kuis2->soal_kuis()->create([
            'teks_soal' => 'Bahaya utama menginstall aplikasi buatan luar (*.APK) secara sembarangan adalah...',
            'poin'      => 100,
        ]);

        OpsiJawaban::create(['soal_kuis_id' => $soal2->id, 'teks_jawaban' => 'Aplikasi dapat mencuri SMS OTP dan data perbankan','poin' => 50, 'is_correct' => true]);
        OpsiJawaban::create(['soal_kuis_id' => $soal2->id, 'teks_jawaban' => 'Baterai HP menjadi lebih hemat', 'poin' => 50, 'is_correct' => false]);
        OpsiJawaban::create(['soal_kuis_id' => $soal2->id, 'teks_jawaban' => 'Sinyal HP meningkat drastis', 'poin' => 50, 'is_correct' => false]);
        OpsiJawaban::create(['soal_kuis_id' => $soal2->id, 'teks_jawaban' => 'Kamera HP terlindungi otomatis', 'poin' => 50, 'is_correct' => false]);
    }
}
