<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use App\Models\Kuis;
use App\Models\Materi;
use App\Models\OpsiJawaban;
use App\Models\Outcome;
use App\Models\RubrikPenilaian;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MateriAndKuisTest extends TestCase
{
    use RefreshDatabase;

    public function test_kuis_options_do_not_leak_is_benar_flag(): void
    {
        $wargaUser = User::factory()->create(['role' => 'warga']);
        $adminUser = User::factory()->create(['role' => 'admin']);
        $admin = Admin::create(['user_id' => $adminUser->id, 'jabatan' => 'Admin']);

        $materi = Materi::create([
            'admin_id' => $admin->id,
            'judul'    => 'Modul Sandi Aman',
            'slug'     => 'modul-sandi-aman',
            'kategori' => 'Keamanan',
            'konten'   => 'Isi materi',
        ]);

        $kuis = Kuis::create(['materi_id' => $materi->id, 'teks_soal' => 'Soal 1', 'poin' => 10]);
        OpsiJawaban::create(['kuis_id' => $kuis->id, 'teks_pilihan' => 'A', 'is_benar' => true]);

        $response = $this->actingAs($wargaUser)
            ->getJson('/api/v1/kuis/materi/' . $materi->id);

        $response->assertStatus(200)
            ->assertJsonMissingPath('data.0.opsi_jawaban.0.is_benar'); // Memastikan REQ-07 aman
    }

    public function test_warga_can_submit_kuis_and_updates_obe_capaian(): void
    {
        // 1. Setup User Warga & Master Data OBE
        $wargaUser = User::factory()->create(['role' => 'warga']);
        $warga = Warga::create([
            'user_id' => $wargaUser->id, 'nik' => '1608011203900002',
            'kecamatan' => 'Baturaja Barat', 'desa' => 'Tanjung Agung'
        ]);

        $adminUser = User::factory()->create(['role' => 'admin']);
        $admin = Admin::create(['user_id' => $adminUser->id, 'jabatan' => 'Admin']);

        $outcome = Outcome::create([
            'kode_outcome' => 'OUT-SEC', 'judul_kompetensi' => 'Keamanan Siber', 'deskripsi' => 'Desk siber'
        ]);
        RubrikPenilaian::create([
            'outcome_id' => $outcome->id, 'batas_bawah_skor' => 0, 'batas_atas_skor' => 100, 'deskripsi_capaian' => 'Tercapai'
        ]);

        $materi = Materi::create([
            'admin_id' => $admin->id, 'judul' => 'Keamanan WhatsApp', 'slug' => 'wa-sec',
            'kategori' => 'Siber', 'konten' => 'Teks'
        ]);
        $materi->outcomes()->attach($outcome->id);

        $kuis = Kuis::create(['materi_id' => $materi->id, 'teks_soal' => 'Sebutkan cara 2FA?', 'poin' => 100]);
        $opsiBenar = OpsiJawaban::create(['kuis_id' => $kuis->id, 'teks_pilihan' => 'Pakai OTP', 'is_benar' => true]);

        // 2. Submit Jawaban Warga
        $submitPayload = [
            'materi_id' => $materi->id,
            'jawaban'   => [
                ['kuis_id' => $kuis->id, 'opsi_id' => $opsiBenar->id]
            ]
        ];

        $response = $this->actingAs($wargaUser)
            ->postJson('/api/v1/kuis/submit', $submitPayload);

        // 3. Verifikasi Hasil
        $response->assertStatus(200)
            ->assertJsonPath('data.total_skor', 100);

        $this->assertDatabaseHas('hasil_kuis', ['warga_id' => $warga->id, 'total_skor' => 100]);
        $this->assertDatabaseHas('capaian_outcome', ['warga_id' => $warga->id, 'skor_tertinggi' => 100]);
    }
}
