<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Materi;
use App\Models\Artikel;
use App\Models\Kuis;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserDashboardController extends Controller
{
    /**
     * GET /api/v1/user/dashboard
     * Mengambil seluruh data statistik, materi berjalan, rekomendasi, dan artikel untuk Dashboard Warga
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        // 1. Informasi Profile Warga
        $wargaData = [
            'nama'           => $user->name,
            'nik'            => $user->warga->nik ?? '-',
            'rt_rw'          => $user->warga->rt_rw ?? 'RT 00 / RW 00',
            'level_literasi' => $user->warga->level_literasi ?? 'Warga Waspada Siber',
        ];


        // 2. Statistik Progress & Sertifikat
        $totalMateri = Materi::where('status', 'active')->count();

        $totalArtikel = Artikel::where('status', 'published')->count();

        $totalKuis = Kuis::where('is_active', true)->count();

        // $materiSelesaiCount = Materi::where('user_id', $user->id)
        //     ->where('is_completed', true)
        //     ->count();

        // $progressKeseluruhan = $totalMateri > 0
        //     ? round(($materiSelesaiCount / $totalMateri) * 100)
        //     : 0;

       // $sertifikatCount = Sertifikat::where('user_id', $user->id)->count();

        $stats = [
           // 'progress_keseluruhan' => $progressKeseluruhan,
           // 'materi_selesai'       => $materiSelesaiCount,
            'total_materi'         => $totalMateri,
            'total_artikel'        => $totalArtikel,
            'total_kuis'           => $totalKuis,
            // 'sertifikat_diraih'    => $sertifikatCount,
            'skor_keamanan'        => $user->skor_keamanan ?? 85,
        ];

        // 3. Materi Berjalan (In-Progress Learning)
        $materiBerjalanProgress = Materi::latest('updated_at')
            ->get();

        $materiBerjalan = $materiBerjalanProgress->map(function ($progress) {
            //$materi = $progress->materi;
            $materi = $progress;

            return [
                'id'             => $materi->id,
                'judul'          => $materi->judul,
                'slug'           => $materi->slug,
                'kategori'       => $materi->kategori->nama ?? 'Keamanan Umum',
                'progress'       => $progress->percent_complete ?? 0,
                'modul_terakhir' => $progress->lastModul->judul ?? 'Modul 1: Pengenalan',
                'total_durasi'   => ($materi->durasi_menit ?? 20) . ' menit',
                'thumbnail'      => $materi->thumbnail_url ?? 'https://images.unsplash.com/photo-1563986768609-322da13575f3?w=800&auto=format&fit=crop&q=80',
            ];
        });

        // 4. Rekomendasi Materi (Belum Diambil / Populer)
       // $takenMateriIds = UserMateriProgress::where('user_id', $user->id)->pluck('materi_id');

        $rekomendasiMateriData = Materi::where('status', 'active')
           // ->whereNotIn('id', $takenMateriIds)
            ->latest()
            ->take(4)
            ->get();

        $rekomendasiMateri = $rekomendasiMateriData->map(function ($materi) {
            return [
                'id'       => $materi->id,
                'judul'    => $materi->judul,
                'slug'     => $materi->slug,
                'kategori' => $materi->kategori->nama ?? 'Privasi Data',
               // 'tingkat'  => $materi->tingkat_kesulitan ?? 'Dasar',
                'durasi'   => ($materi->durasi_menit ?? 15) . ' menit',
            ];
        });

        // 5. Artikel & Tips Siber Terbaru
        $artikelData = Artikel::latest()
            ->take(3)
            ->get();

        $artikelTerbaru = $artikelData->map(function ($art) {
            return [
                'id'         => $art->id,
                'judul'      => $art->judul,
                'created_at' => $art->created_at->translatedFormat('d M Y'),
                'read_time'  => ($art->estimasi_baca ?? 3) . ' menit',
            ];
        });

        // 6. Return Response JSON
        return response()->json([
            'status' => 'success',
            'data'   => [
                'warga'              => $wargaData,
                'stats'              => $stats,
                'materi_berjalan'    => $materiBerjalan,
                'rekomendasi_materi' => $rekomendasiMateri,
                'artikel_terbaru'    => $artikelTerbaru,
            ],
        ], 200);
    }
}
