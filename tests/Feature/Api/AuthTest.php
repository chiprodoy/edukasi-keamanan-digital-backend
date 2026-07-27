<?php

namespace Tests\Feature\Api;

use App\Models\User;
use App\Models\Warga;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_warga_can_register_successfully(): void
    {
        $payload = [
            'name'      => 'Budi Santoso',
            'email'     => 'budi@oku.go.id',
            'password'  => 'password123',
            'nik'       => '1608011203900001',
            'no_hp'     => '081234567890',
            'kecamatan' => 'Baturaja Timur',
            'desa'      => 'Kemalaraja',
        ];

        $response = $this->postJson('/api/v1/auth/register', $payload);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'access_token',
                'token_type',
                'data' => ['id', 'name', 'email', 'warga' => ['nik', 'kecamatan', 'desa']]
            ]);

        $this->assertDatabaseHas('users', ['email' => 'budi@oku.go.id']);
        $this->assertDatabaseHas('warga', ['nik' => '1608011203900001']);
    }

    public function test_user_can_login_and_receive_token(): void
    {
        $user = User::factory()->create([
            'email'    => 'warga@oku.go.id',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->postJson('/api/v1/auth/login', [
            'email'    => 'warga@oku.go.id',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure(['access_token', 'token_type']);
    }

    public function test_authenticated_user_can_get_profile_and_logout(): void
    {
        $user = User::factory()->create();
        $token = $user->createToken('test_token')->plainTextToken;

        // Test Get Profile (/me)
        $meResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/auth/me');

        $meResponse->assertStatus(200)
            ->assertJsonPath('data.email', $user->email);

        // Test Logout
        $logoutResponse = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/auth/logout');

        $logoutResponse->assertStatus(200)
            ->assertJson(['message' => 'Logout berhasil.']);

        $this->assertCount(0, $user->fresh()->tokens);
    }
}
