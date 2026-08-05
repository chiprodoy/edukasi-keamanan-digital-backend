<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use Illuminate\Http\JsonResponse;

class MateriController extends Controller
{
    public function index(): JsonResponse
    {
        $materi = Materi::where('status', 'active')
            ->with('outcomes:id,kode_outcome,judul_kompetensi')
            ->latest()
            ->get();

        return response()->json(['data' => $materi], 200);
    }

    public function show(string $slug): JsonResponse
    {
        $materi = Materi::where('slug', $slug)
            ->where('status', 'active')
            ->with(['outcomes', 'kuis' ])
            ->firstOrFail();

        return response()->json(['data' => $materi], 200);
    }
}
