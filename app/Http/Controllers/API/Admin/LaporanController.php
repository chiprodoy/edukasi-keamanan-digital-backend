<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Models\HasilKuis;
use App\Models\Warga;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function getAnalitikLiterasi(Request $request): JsonResponse
    {
        $kecamatan = $request->query('kecamatan');
        $desa      = $request->query('desa');

        // Query filter wilayah via relasi warga
        $queryWarga = Warga::query();

        if ($kecamatan) {
            $queryWarga->where('kecamatan', $kecamatan);
        }
        if ($desa) {
            $queryWarga->where('desa', $desa);
        }

        $wargaIds = $queryWarga->pluck('id');

        // Agregasi Statistik Skor Hasil Kuis
        $totalPartisipan = $wargaIds->count();
        $rataRataSkor    = HasilKuis::whereIn('warga_id', $wargaIds)->avg('total_skor') ?? 0;

        // Distribusi Level Literasi Warga Wilayah
        $distribusiLevel = [
            'Pemula'   => (clone $queryWarga)->where('level_literasi', 'Pemula')->count(),
            'Menengah' => (clone $queryWarga)->where('level_literasi', 'Menengah')->count(),
            'Mahir'    => (clone $queryWarga)->where('level_literasi', 'Mahir')->count(),
        ];

        return response()->json([
            'filter' => [
                'kecamatan' => $kecamatan ?? 'Semua Kecamatan',
                'desa'      => $desa ?? 'Semua Desa',
            ],
            'ringkasan' => [
                'total_warga_terdaftar' => $totalPartisipan,
                'rata_rata_skor_kuis'   => round($rataRataSkor, 2),
                'distribusi_level'      => $distribusiLevel,
            ],
        ], 200);
    }
}
