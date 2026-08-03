<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Warga\SubmitKuisRequest;
use App\Models\CapaianOutcome;
use App\Models\DetailHasilKuis;
use App\Models\HasilKuis;
use App\Models\Kuis;
use App\Models\Materi;
use App\Models\OpsiJawaban;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KuisController extends Controller
{

/**
     * GET /api/admin/kuis
     * Mengambil daftar kuis beserta nama materi terhubung & jumlah total soal.
     */
    public function index(Request $request): JsonResponse
    {
        $search = $request->query('search');

        $quizzes = Kuis::with('materi:id,judul')
            ->withCount('soal_kuis as total_soal')
            ->when($search, function ($query, $search) {
                $query->where('judul', 'like', "%{$search}%")
                      ->orWhereHas('materi', function ($q) use ($search) {
                          $q->where('judul', 'like', "%{$search}%");
                      });
            })
            ->latest()
            ->get()
            ->map(function ($quiz) {
                return [
                    'id'            => $quiz->id,
                    'materi_id'     => $quiz->materi_id,
                    'materi_title'  => $quiz->materi ? $quiz->materi->judul : 'Modul Umum',
                    'judul'         => $quiz->judul,
                    'deskripsi'     => $quiz->deskripsi,
                    'durasi_menit'  => $quiz->durasi_menit,
                    'passing_score' => $quiz->passing_score,
                    'is_active'     => (bool) $quiz->is_active,
                    'total_soal'    => $quiz->total_soal ?? 0,
                    'created_at'    => $quiz->created_at,
                ];
            });

        return response()->json([
            'status' => 'success',
            'data'   => $quizzes,
        ], 200);
    }
    /**
     * GET /api/admin/kuis/{id}
     * Mengambil detail kuis beserta nama materi terhubung & jumlah total soal.
     */
    public function show($id,Request $request): JsonResponse
    {
        $search = $request->query('search');

        $quiz = Kuis::with('materi:id,judul')
            ->withCount('soal_kuis as total_soal')
            ->where('id', $id)
            ->first();

        $kuis = [
                    'id'            => $quiz->id,
                    'materi_id'     => $quiz->materi_id,
                    'materi_title'  => $quiz->materi ? $quiz->materi->judul : 'Modul Umum',
                    'judul'         => $quiz->judul,
                    'deskripsi'     => $quiz->deskripsi,
                    'durasi_menit'  => $quiz->durasi_menit,
                    'passing_score' => $quiz->passing_score,
                    'is_active'     => (bool) $quiz->is_active,
                    'total_soal'    => $quiz->total_soal ?? 0,
                    'created_at'    => $quiz->created_at,
        ];

        return response()->json([
            'status' => 'success',
            'data'   => $kuis,
        ], 200);
    }
    /**
     * GET /api/admin/materi-options
     * Mengambil daftar materi aktif untuk pilihan dropdown modal kuis.
     */
    public function materiOptions(): JsonResponse
    {
        $materiList = Materi::select('id', 'judul', 'kategori')
            ->where('status', 'active')
            ->orderBy('judul', 'asc')
            ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $materiList,
        ], 200);
    }

    /**
     * POST /api/admin/quizzes
     * Menyimpan data kuis baru.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'materi_id'     => 'required|exists:materi,id',
            'judul'         => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'durasi_menit'  => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'is_active'     => 'boolean',
        ]);

        $quiz = Kuis::create($validated);
        $quiz->load('materi:id,judul');

        return response()->json([
            'status'  => 'success',
            'message' => 'Kuis berhasil dibuat.',
            'data'    => [
                'id'            => $quiz->id,
                'materi_id'     => $quiz->materi_id,
                'materi_title'  => $quiz->materi ? $quiz->materi->judul : null,
                'judul'         => $quiz->judul,
                'deskripsi'     => $quiz->deskripsi,
                'durasi_menit'  => $quiz->durasi_menit,
                'passing_score' => $quiz->passing_score,
                'is_active'     => (bool) $quiz->is_active,
                'total_soal'    => 0,
            ]
        ], 201);
    }

    /**
     * PUT/PATCH /api/admin/quizzes/{id}
     * Memperbarui data kuis yang sudah ada.
     */
    public function update(Request $request, $id): JsonResponse
    {
        $quiz = Kuis::findOrFail($id);

        $validated = $request->validate([
            'materi_id'     => 'required|exists:materi,id',
            'judul'         => 'required|string|max:255',
            'deskripsi'     => 'nullable|string',
            'durasi_menit'  => 'required|integer|min:1',
            'passing_score' => 'required|integer|min:0|max:100',
            'is_active'     => 'boolean',
        ]);

        $quiz->update($validated);
        $quiz->load('materi:id,judul');

        return response()->json([
            'status'  => 'success',
            'message' => 'Data kuis berhasil diperbarui.',
            'data'    => [
                'id'            => $quiz->id,
                'materi_id'     => $quiz->materi_id,
                'materi_title'  => $quiz->materi ? $quiz->materi->judul : null,
                'judul'         => $quiz->judul,
                'deskripsi'     => $quiz->deskripsi,
                'durasi_menit'  => $quiz->durasi_menit,
                'passing_score' => $quiz->passing_score,
                'is_active'     => (bool) $quiz->is_active,
                'total_soal'    => $quiz->opsiJawaban()->count(),
            ]
        ], 200);
    }

    /**
     * DELETE /api/admin/quizzes/{id}
     * Menghapus data kuis beserta seluruh soal terhubung.
     */
    public function destroy($id): JsonResponse
    {
        $quiz = Kuis::findOrFail($id);
        $quiz->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Kuis berhasil dihapus.',
        ], 200);
    }

    public function getKuisByMateri(int $materiId): JsonResponse
    {
        $kuis = Kuis::where('materi_id', $materiId)
            ->with(['opsiJawaban' => function ($q) {
                // REQ-07: Proteksi payload — hindari kebocoran field `is_benar`
                $q->select('id', 'kuis_id', 'teks_pilihan');
            }])
            ->get();

        return response()->json(['data' => $kuis], 200);
    }

    public function submitKuis(SubmitKuisRequest $request): JsonResponse
    {
        $warga = auth()->user()->warga;
        if (!$warga) {
            return response()->json(['message' => 'Akses khusus warga.'], 403);
        }

        $result = DB::transaction(function () use ($request, $warga) {
            $totalSkor = 0;
            $detailSubmissions = [];

            foreach ($request->jawaban as $item) {
                $kuis = Kuis::findOrFail($item['kuis_id']);
                $opsi = OpsiJawaban::where('id', $item['opsi_id'])
                    ->where('kuis_id', $kuis->id)
                    ->firstOrFail();

                $isBenar = $opsi->is_benar;
                $poinDidapat = $isBenar ? $kuis->poin : 0;
                $totalSkor += $poinDidapat;

                $detailSubmissions[] = [
                    'kuis_id'         => $kuis->id,
                    'opsi_dipilih_id' => $opsi->id,
                    'is_benar'        => $isBenar,
                    'poin_didapat'    => $poinDidapat,
                ];
            }

            // 1. Simpan Header Hasil Kuis
            $hasilKuis = HasilKuis::create([
                'warga_id'      => $warga->id,
                'materi_id'     => $request->materi_id,
                'total_skor'    => $totalSkor,
                'waktu_selesai' => now(),
            ]);

            // 2. Simpan Detail Hasil Kuis
            foreach ($detailSubmissions as $detail) {
                DetailHasilKuis::create(array_merge($detail, [
                    'hasil_kuis_id' => $hasilKuis->id,
                ]));
            }

            // 3. Update / Upsert Capaian Outcome OBE Warga (REQ-10)
            $materi = Materi::with('outcomes')->findOrFail($request->materi_id);
            foreach ($materi->outcomes as $outcome) {
                $capaianExist = CapaianOutcome::where('warga_id', $warga->id)
                    ->where('outcome_id', $outcome->id)
                    ->first();

                if (!$capaianExist) {
                    CapaianOutcome::create([
                        'warga_id'      => $warga->id,
                        'outcome_id'    => $outcome->id,
                        'skor_tertinggi' => $totalSkor,
                    ]);
                } elseif ($totalSkor > $capaianExist->skor_tertinggi) {
                    $capaianExist->update(['skor_tertinggi' => $totalSkor]);
                }
            }

            return $hasilKuis;
        });

        return response()->json([
            'message' => 'Kuis berhasil diselesaikan.',
            'data'    => $result->load(['detailHasil.kuis', 'detailHasil.opsiDipilih']),
        ], 200);
    }

    public function questionsByKuis(int $kuisId): JsonResponse
    {
        //  $kuis = Kuis::with(['soal_kuis', 'opsiJawaban' => function ($q) {
        //      // REQ-07: Proteksi payload — hindari kebocoran field `is_benar`
        //      $q->select('id', 'kuis_id', 'teks_jawaban','poin');
        //  }])->findOrFail($kuisId);

          $kuis = Kuis::with(['soal_kuis' => function($q) {
              $q->with('opsiJawaban');
          }])->findOrFail($kuisId);

        return response()->json(['status' => 'success','data' => $kuis], 200);
    }
}
