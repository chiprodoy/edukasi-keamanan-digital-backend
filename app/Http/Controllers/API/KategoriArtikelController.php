<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KategoriArtikel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class KategoriArtikelController extends Controller
{
    /**
     * Tampilkan daftar kategori artikel.
     */
    public function index()
    {
       // $kategori = KategoriArtikel::withCount('artikels')->latest()->get();
        $kategori = KategoriArtikel::latest()->get();

        return response()->json([
            'success' => true,
            'message' => 'Daftar Kategori Artikel berhasil diambil',
            'data'    => $kategori,
        ], 200);
    }

    /**
     * Simpan kategori artikel baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_kategori' => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
        ]);

        $kategori = KategoriArtikel::create([
            'nama_kategori' => $request->nama_kategori,
            'slug'          => Str::slug($request->nama_kategori),
            'deskripsi'     => $request->deskripsi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Kategori Artikel berhasil dibuat',
            'data'    => $kategori,
        ], 201);
    }

    /**
     * Tampilkan detail kategori artikel berdasarkan ID.
     */
    public function show($id)
    {
        $kategori = KategoriArtikel::with('artikels')->find($id);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori Artikel tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail Kategori Artikel',
            'data'    => $kategori,
        ], 200);
    }

    /**
     * Perbarui kategori artikel.
     */
    public function update(Request $request, $id)
    {
        $kategori = KategoriArtikel::find($id);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori Artikel tidak ditemukan',
            ], 404);
        }

        $request->validate([
            'nama_kategori' => 'sometimes|required|string|max:255',
            'deskripsi'     => 'nullable|string',
        ]);

        if ($request->has('nama_kategori')) {
            $kategori->nama_kategori = $request->nama_kategori;
            $kategori->slug = Str::slug($request->nama_kategori);
        }

        if ($request->has('deskripsi')) {
            $kategori->deskripsi = $request->deskripsi;
        }

        $kategori->save();

        return response()->json([
            'success' => true,
            'message' => 'Kategori Artikel berhasil diperbarui',
            'data'    => $kategori,
        ], 200);
    }

    /**
     * Hapus kategori artikel.
     */
    public function destroy($id)
    {
        $kategori = KategoriArtikel::find($id);

        if (!$kategori) {
            return response()->json([
                'success' => false,
                'message' => 'Kategori Artikel tidak ditemukan',
            ], 404);
        }

        $kategori->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kategori Artikel berhasil dihapus',
        ], 200);
    }
}
