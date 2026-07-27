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
use Illuminate\Support\Facades\DB;

class KuisController extends Controller
{
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
}
