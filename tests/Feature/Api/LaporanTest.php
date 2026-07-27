<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_filter_literacy_analytics_by_region(): void
    {
        $adminUser = User::factory()->create(['role' => 'admin']);

        // Seed Sample Warga
        $user1 = User::factory()->create();
        Warga::create(['user_id' => $user1->id, 'nik' => '1608011111111111', 'kecamatan' => 'Baturaja Timur', 'desa' => 'Sekarjaya', 'level_literasi' => 'Pemula']);

        $user2 = User::factory()->create();
        Warga::create(['user_id' => $user2->id, 'nik' => '1608012222222222', 'kecamatan' => 'Baturaja Timur', 'desa' => 'Sekarjaya', 'level_literasi' => 'Mahir']);

        $response = $this->actingAs($adminUser)
            ->getJson('/api/v1/admin/laporan/literasi?kecamatan=Baturaja+Timur&desa=Sekarjaya');

        $response->assertStatus(200)
            ->assertJsonPath('ringkasan.total_warga_terdaftar', 2)
            ->assertJsonPath('ringkasan.distribusi_level.Pemula', 1)
            ->assertJsonPath('ringkasan.distribusi_level.Mahir', 1);
    }
}
