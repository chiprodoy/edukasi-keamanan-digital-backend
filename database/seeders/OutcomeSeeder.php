<?php

namespace Database\Seeders;

use App\Models\Outcome;
use App\Models\RubrikPenilaian;
use Illuminate\Database\Seeder;

class OutcomeSeeder extends Seeder
{
    public function run(): void
    {
        // Outcome 1: Keamanan Akun & Privasi
        $outcome1 = Outcome::create([
            'kode_outcome'     => 'OUT-SEC-01',
            'judul_kompetensi' => 'Pengamanan Identitas & Privasi Digital',
            'deskripsi'        => 'Mampu mengkonfigurasi pengamanan akun pribadi dan melindungi data sensitif dari kebocoran.',
        ]);

        RubrikPenilaian::create([
            'outcome_id'        => $outcome1->id,
            'batas_bawah_skor'  => 0,
            'batas_atas_skor'   => 50,
            'deskripsi_capaian' => 'Pemula: Memahami konsep dasar sandi namun belum menerapkan otentikasi dua langkah (2FA).',
        ]);

        RubrikPenilaian::create([
            'outcome_id'        => $outcome1->id,
            'batas_bawah_skor'  => 51,
            'batas_atas_skor'   => 100,
            'deskripsi_capaian' => 'Mahir: Mampu mengamankan seluruh akun pribadi dengan kombinasi sandi kuat dan fitur 2FA.',
        ]);

        // Outcome 2: Pengenalan Modus Penipuan Siber
        $outcome2 = Outcome::create([
            'kode_outcome'     => 'OUT-SEC-02',
            'judul_kompetensi' => 'Deteksi Rekayasa Sosial (Phishing/Social Engineering)',
            'deskripsi'        => 'Mampu mengenali dan menghindari berbagai bentuk manipulasi informasi dan link jebakan siber.',
        ]);

        RubrikPenilaian::create([
            'outcome_id'        => $outcome2->id,
            'batas_bawah_skor'  => 0,
            'batas_atas_skor'   => 50,
            'deskripsi_capaian' => 'Pemula: Rentan terkecoh oleh link atau file palsu (.APK) yang dikirim melalui aplikasi pesan.',
        ]);

        RubrikPenilaian::create([
            'outcome_id'        => $outcome2->id,
            'batas_bawah_skor'  => 51,
            'batas_atas_skor'   => 100,
            'deskripsi_capaian' => 'Mahir: Mampu menganalisis indikator kecurangan tautan/file serta memverifikasi sumber pesan.',
        ]);
    }
}
