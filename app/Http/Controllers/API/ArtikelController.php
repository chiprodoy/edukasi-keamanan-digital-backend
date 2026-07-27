<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Artikel;
use Illuminate\Http\JsonResponse;

class ArtikelController extends Controller
{
    public function index(): JsonResponse
    {
        // Pinned alert selalu ditempatkan paling atas (REQ-03)
        $artikel = Artikel::where('status', 'published')
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
}
