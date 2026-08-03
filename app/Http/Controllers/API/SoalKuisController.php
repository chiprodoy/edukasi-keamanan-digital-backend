<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\OpsiJawaban;
use App\Models\SoalKuis;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SoalKuisController extends Controller
{
    /**
     * GET /api/soal-kuis
     * GET /api/soal-kuis?kuis_id=1
     * Mengambil daftar soal kuis (bisa difilter berdasarkan kuis_id)
     */
    public function index(Request $request): JsonResponse
    {
        $kuisId = $request->query('kuis_id');

        $soal = SoalKuis::when($kuisId, function ($query) use ($kuisId) {
                $query->where('kuis_id', $kuisId);
            })
            ->with('kuis:id,judul')
            ->latest()
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $soal,
        ], 200);
    }

    /**
     * POST /api/soal-kuis
     * Menambahkan soal kuis baru
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'kuis_id'   => 'required|exists:kuis,id',
            'teks_soal' => 'required|string',
            'poin'      => 'nullable|integer|min:0',
        ]);

        $soal = SoalKuis::create([
            'kuis_id'   => $validated['kuis_id'],
            'teks_soal' => $validated['teks_soal'],
            'poin'      => $validated['poin'] ?? 10,
        ]);

        foreach ($request->input('opsi_jawaban', []) as $opsi) {
            $soal->opsiJawaban()->create([
                'teks_jawaban' => $opsi['teks_jawaban'],
                'poin'         => $opsi['poin'] ?? 0,
                'is_correct'   => $opsi['is_correct'] ?? false,
            ]);
        }


        return response()->json([
            'status'  => 'success',
            'message' => 'Soal kuis berhasil ditambahkan.',
            'data'    => $soal,
        ], 201);
    }

    /**
     * GET /api/soal-kuis/{id}
     * Mengambil detail satu soal kuis
     */
    public function show($id): JsonResponse
    {
        $soal = SoalKuis::with('kuis:id,judul')->find($id);

        if (!$soal) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data soal tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $soal,
        ], 200);
    }

    /**
     * PUT/PATCH /api/soal-kuis/{id}
     * Memperbarui data soal kuis
     */
    public function update(Request $request, $id): JsonResponse
    {
        $soal = SoalKuis::find($id);

        if (!$soal) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data soal tidak ditemukan.',
            ], 404);
        }

        $validated = $request->validate([
            'kuis_id'   => 'sometimes|required|exists:kuis,id',
            'teks_soal' => 'sometimes|required|string',
            'poin'      => 'sometimes|integer|min:0',
        ]);

        $soal->update($validated);

        foreach ($request->input('opsi_jawaban', []) as $opsi) {
            OpsiJawaban::find($opsi['id'])->update([
                'teks_jawaban' => $opsi['teks_jawaban'],
                'poin'         => $opsi['poin'] ?? 0,
                'is_correct'   => $opsi['is_correct'] ?? false,
            ]);
            // $soal->opsiJawaban()->update([
            //     'teks_jawaban' => $opsi['teks_jawaban'],
            //     'poin'         => $opsi['poin'] ?? 0,
            //     'is_correct'   => $opsi['is_correct'] ?? false,
            // ]);
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Soal kuis berhasil diperbarui.',
            'data'    => $soal,
        ], 200);
    }

    /**
     * DELETE /api/soal-kuis/{id}
     * Menghapus soal kuis
     */
    public function destroy($id): JsonResponse
    {
        $soal = SoalKuis::find($id);

        if (!$soal) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Data soal tidak ditemukan.',
            ], 404);
        }

        $soal->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Soal kuis berhasil dihapus.',
        ], 200);
    }
}
