<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = DB::transaction(function () use ($request) {
            $newUser = User::create([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
                'role'     => 'warga',
            ]);

            Warga::create([
                'user_id'        => $newUser->id,
                'nik'            => $request->nik,
                'no_hp'          => $request->no_hp,
                'kecamatan'      => $request->kecamatan,
                'desa'           => $request->desa,
                'level_literasi' => 'Pemula',
            ]);

            return $newUser;
        });

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Registrasi warga berhasil.',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'data'         => $user->load('warga'),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Kredensial tidak valid.'], 401);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message'      => 'Login berhasil.',
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'data'         => $user->load(['warga', 'admin']),
        ], 200);
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Hapus token yang SEDANG DIGUNAKAN saat request ini (Best Practice API)
        //$user->currentAccessToken()->delete();
        $user->tokens()->delete();

        return response()->json(['message' => 'Logout berhasil.'], 200);
    }

    public function me(Request $request): JsonResponse
    {
    /** @var \App\Models\User $user */
        $user = $request->user();

        return response()->json([
            'data' => $user->load(['warga', 'admin']),
        ], 200);
        }
}
