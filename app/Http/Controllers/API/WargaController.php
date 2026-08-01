<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Warga;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class WargaController extends Controller
{
    /**
     * Display a listing of the resource (Supports Search & Status Filter).
     */
    public function index(Request $request)
    {
        $query = Warga::with('user:id,name,email');

        // Search Filter (Nama, NIK, Email, No Telepon)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('no_hp', 'like', "%{$search}%");
            });
        }

        // Status Filter (aktif, pending, suspended)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $warga = $query->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $warga
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nik' => 'required|numeric|digits:16|unique:warga,nik',
            'no_hp' => 'required|string|max:20',
            'kecamatan' => 'required|string',
            'status' => 'required|in:aktif,pending,suspended',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        $warga = Warga::create(array_merge($validated, ['user_id' => $user->id]));

        return response()->json([
            'status' => 'success',
            'message' => 'Data warga berhasil ditambahkan',
            'data' => $warga
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $warga = Warga::with('user')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $warga
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $warga = Warga::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($warga->user_id)],
            'nik' => ['required', 'numeric', 'digits:16', Rule::unique('warga')->ignore($warga->id)],
            'no_hp' => 'required|string|max:20',
            'kecamatan' => 'required|string',
            'status' => 'required|in:aktif,pending,suspended',
            'password' => 'nullable|string|min:6',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user = User::findOrFail($warga->user_id);
        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'] ?? $user->password,
        ]);

        $warga->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data warga berhasil diperbarui',
            'data' => $warga
        ], 200);
    }

    /**
     * Update status status account only (Quick Toggle).
     */
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:aktif,pending,suspended',
        ]);

        $warga = Warga::findOrFail($id);
        $warga->update(['status' => $validated['status']]);

        return response()->json([
            'status' => 'success',
            'message' => 'Status akun warga berhasil diubah',
            'data' => $warga
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $warga = Warga::findOrFail($id);
        $warga->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data warga berhasil dihapus'
        ], 200);
    }
}
