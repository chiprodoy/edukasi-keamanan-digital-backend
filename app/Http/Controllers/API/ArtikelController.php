<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArtikelRequest;
use App\Models\Artikel;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; // Import class Request
use Illuminate\Support\Str;

class ArtikelController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');
        $kategori = $request->query('kategori');

        // Pinned alert selalu ditempatkan paling atas (REQ-03)
        $artikel = Artikel::where('status', 'published')
        ->with('kategori:id,nama_kategori') // Eager load relasi kategori dan admin
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('judul', 'like', "%{$search}%")
                      ->orWhere('konten', 'like', "%{$search}%");
                });
            })
            ->when($kategori, function ($query, $kategori) {
                $query->where('kategori_artikel_id', $kategori);
            })
            ->orderBy('is_pinned', 'desc')
            ->latest()
            ->get();

        return response()->json(['data' => $artikel], 200);
    }

    public function show(string $slug): JsonResponse
    {
        $artikel = Artikel::where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Increment Views secara atomic (REQ-04)
        $artikel->increment('views_count');

        return response()->json(['data' => $artikel], 200);
    }

    public function store(StoreArtikelRequest $request): JsonResponse
    {
        $admin = auth()->user()->admin;

        $artikel = Artikel::create([
            'admin_id'  => $admin->id,
            'judul'     => $request->judul,
            'slug'      => Str::slug($request->judul) . '-' . Str::random(5),
            'kategori_artikel_id'  => $request->kategori_artikel_id,
            'konten'    => $request->konten,
            'thumbnail' => $request->thumbnail,
            'is_pinned' => $request->is_pinned ?? false,
            'status'    => $request->status,
        ]);

        return response()->json([
            'message' => 'Artikel / Alert berhasil dibuat.',
            'data'    => $artikel,
        ], 201);
    }

    /**
     * Perbarui data artikel berdasarkan ID (Update).
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $artikel = Artikel::find($id);

        if (!$artikel) {
            return response()->json([
                'success' => false,
                'message' => 'Artikel tidak ditemukan.',
            ], 404);
        }

        $request->validate([
            'judul'               => 'sometimes|required|string|max:255',
            'kategori_artikel_id' => 'sometimes|required|exists:kategori_artikels,id',
            'konten'              => 'sometimes|required|string',
            'thumbnail'           => 'nullable|string',
            'is_pinned'           => 'nullable|boolean',
            'status'              => 'nullable|string',
        ]);

        // Buat slug baru jika judul berubah
        if ($request->has('judul') && $request->judul !== $artikel->judul) {
            $artikel->slug = Str::slug($request->judul) . '-' . Str::random(5);
        }

        $artikel->update([
            'judul'               => $request->judul ?? $artikel->judul,
            'kategori_artikel_id' => $request->kategori_artikel_id ?? $request->kategori_id ?? $artikel->kategori_artikel_id,
            'konten'              => $request->konten ?? $artikel->konten,
            'thumbnail'           => $request->thumbnail ?? $artikel->thumbnail,
            'is_pinned'           => $request->has('is_pinned') ? $request->is_pinned : $artikel->is_pinned,
            'status'              => $request->status ?? $artikel->status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Artikel berhasil diperbarui.',
            'data'    => $artikel->fresh()->load('kategori'),
        ], 200);
    }

    /**
     * Hapus artikel berdasarkan ID (Delete).
     */
    public function destroy(string $id): JsonResponse
    {
        $artikel = Artikel::find($id);

        if (!$artikel) {
            return response()->json([
                'success' => false,
                'message' => 'Artikel tidak ditemukan.',
            ], 404);
        }

        $artikel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Artikel berhasil dihapus.',
        ], 200);
    }
}
