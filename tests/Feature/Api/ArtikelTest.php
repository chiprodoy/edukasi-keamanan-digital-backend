<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use App\Models\Artikel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArtikelTest extends TestCase
{
    use RefreshDatabase;

    public function test_pinned_cyber_alerts_are_ordered_first_and_views_incremented(): void
    {
        $adminUser = User::factory()->create(['role' => 'admin']);
        $admin = Admin::create(['user_id' => $adminUser->id, 'jabatan' => 'Admin']);

        $artikelBiasa = Artikel::create([
            'admin_id' => $admin->id, 'judul' => 'Tips Internet', 'slug' => 'tips-internet',
            'kategori' => 'Edukasi', 'konten' => 'Teks', 'is_pinned' => false, 'status' => 'published'
        ]);

        $cyberAlert = Artikel::create([
            'admin_id' => $admin->id, 'judul' => 'DARURAT PHISHING', 'slug' => 'darurat-phishing',
            'kategori' => 'Cyber Alert', 'konten' => 'Teks Alert', 'is_pinned' => true, 'status' => 'published'
        ]);

        // 1. Test Order Priority (REQ-03)
        $listResponse = $this->getJson('/api/v1/artikel');
        $listResponse->assertStatus(200)
            ->assertJsonPath('data.0.slug', 'darurat-phishing'); // Pinned alert harus di index 0

        // 2. Test Views Increment (REQ-04)
        $detailResponse = $this->getJson('/api/v1/artikel/' . $artikelBiasa->slug);
        $detailResponse->assertStatus(200);

        $this->assertEquals(1, $artikelBiasa->fresh()->views_count);
    }
}
