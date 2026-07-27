<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use App\Models\Materi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumTest extends TestCase
{
    use RefreshDatabase;

    /** @var \App\Models\User */
    private $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminUser = User::factory()->create(['role' => 'admin']);
        Admin::create([
            'user_id' => $this->adminUser->id,
            'nip'     => '199001012022011001',
            'jabatan' => 'Staf Literasi Digital',
        ]);
    }

    public function test_admin_can_create_outcome_with_rubrik(): void
    {
        $payload = [
            'kode_outcome'     => 'OUT-01',
            'judul_kompetensi' => 'Keamanan Identitas Digital',
            'deskripsi'        => 'Mampu mengamankan akun pribadi dari kejahatan siber.',
            'rubriks'          => [
                ['batas_bawah' => 0, 'batas_atas' => 50, 'deskripsi' => 'Perlu Pendampingan'],
                ['batas_bawah' => 51, 'batas_atas' => 100, 'deskripsi' => 'Sangat Baik'],
            ]
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/v1/admin/outcomes', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('outcomes', ['kode_outcome' => 'OUT-01']);
        $this->assertDatabaseHas('rubrik_penilaian', ['deskripsi_capaian' => 'Sangat Baik']);
    }

    public function test_admin_cannot_create_kuis_without_exactly_one_correct_option(): void
    {
        $materi = Materi::create([
            'admin_id' => $this->adminUser->admin->id,
            'judul'    => 'Modul Phishing',
            'slug'     => 'modul-phishing',
            'kategori' => 'Siber',
            'konten'   => 'Konten edukasi phishing',
        ]);

        // Scenario 1: Dua jawaban benar (Aturan REQ-11 Melanggar)
        $invalidPayload = [
            'materi_id' => $materi->id,
            'teks_soal' => 'Apa itu Phishing?',
            'poin'      => 10,
            'opsi'      => [
                ['teks_pilihan' => 'Penipuan online', 'is_benar' => true],
                ['teks_pilihan' => 'Pencurian akun', 'is_benar' => true], // Multiple TRUE
            ]
        ];

        $response = $this->actingAs($this->adminUser)
            ->postJson('/api/v1/admin/kuis', $invalidPayload);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['opsi']);
    }
}
