<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Admin Kominfo OKU
        $adminUser = User::create([
            'name'     => 'Admin Literasi OKU',
            'email'    => 'admin@oku.go.id',
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        Admin::create([
            'user_id' => $adminUser->id,
            'nip'     => '198507122010011002',
            'jabatan' => 'Pranata Komputer Muda',
        ]);

        // 2. Akun Warga 1 (Baturaja Timur)
        $wargaUser1 = User::create([
            'name'     => 'Ahmad Rivai',
            'email'    => 'ahmad@gmail.com',
            'password' => Hash::make('password123'),
            'role'     => 'warga',
        ]);

        Warga::create([
            'user_id'        => $wargaUser1->id,
            'nik'            => '1608011508920001',
            'no_hp'          => '081271829301',
            'kecamatan'      => 'Baturaja Timur',
            'level_literasi' => 'Pemula',
        ]);

        // 3. Akun Warga 2 (Baturaja Barat)
        $wargaUser2 = User::create([
            'name'     => 'Siti Rahmah',
            'email'    => 'siti@gmail.com',
            'password' => Hash::make('password123'),
            'role'     => 'warga',
        ]);

        Warga::create([
            'user_id'        => $wargaUser2->id,
            'nik'            => '1608025210950003',
            'no_hp'          => '085267182938',
            'kecamatan'      => 'Baturaja Barat',
            'level_literasi' => 'Menengah',
        ]);
    }
}
