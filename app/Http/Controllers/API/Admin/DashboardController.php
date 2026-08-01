<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Materi;
use App\Models\Kuis;
use App\Models\QuizAttempt; // Tabel riwayat pengerjaan kuis oleh warga
use App\Models\Warga;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * GET /api/v1/admin/dashboard
     * Menyediakan ringkasan data statistik & grafik untuk Dashboard Admin.
     */
    public function index(): JsonResponse
    {
        // 1. Ringkasan Statistik Utama (Card Stats)
        $totalWarga = User::where('role', 'warga')->count();
        $totalMateri = Materi::count();
        $totalKuis = Kuis::count();
        $totalPercobaanKuis = QuizAttempt::count();
        $rataRataNilai = round(QuizAttempt::avg('score') ?? 0, 1);

        // 2. Data Grafik Penyelesaian Kuis (7 Hari Terakhir)
        $grafikMingguan = QuizAttempt::select(
                DB::raw('DATE(created_at) as tanggal'),
                DB::raw('COUNT(*) as total_peserta'),
                DB::raw('SUM(CASE WHEN is_passed = 1 THEN 1 ELSE 0 END) as lulus'),
                DB::raw('SUM(CASE WHEN is_passed = 0 THEN 1 ELSE 0 END) as tidak_lulus')
            )
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'ASC')
            ->get();

        // 3. Aktivitas Pengerjaan Kuis Terbaru oleh Warga (5 Terakhir)
        $aktivitasTerbaru = QuizAttempt::with(['user:id,name,email', 'quiz:id,judul'])
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($attempt) {
                return [
                    'id' => $attempt->id,
                    'nama_warga' => $attempt->user->name ?? 'Warga Anonymous',
                    'email_warga' => $attempt->user->email ?? '-',
                    'judul_kuis' => $attempt->quiz->judul ?? 'Kuis Terhapus',
                    'skor' => $attempt->score,
                    'status' => $attempt->is_passed ? 'Lulus' : 'Tidak Lulus',
                    'tanggal' => $attempt->created_at->diffForHumans(),
                ];
            });

        // 4. Modul Materi Populer / Sering Diakses
        $materiPopuler = Materi::withCount('kuis')
            ->latest()
            ->take(5)
            ->get(['id', 'judul', 'kategori', 'tipe', 'created_at']);

        $recent_wargas = Warga::with(['user:id,name,email'])
            ->latest()
            ->take(5)
            ->get(['id', 'user_id', 'no_hp', 'kecamatan', 'level_literasi', 'created_at']);

        // Structuring Response JSON
        return response()->json([
            'success' => true,
            'message' => 'Data dashboard admin berhasil dimuat.',
            'data' => [
                'stats' => [
                    'total_warga' => $totalWarga,
                    'total_materi' => $totalMateri,
                    'total_kuis' => $totalKuis,
                    'total_percobaan_kuis' => $totalPercobaanKuis,
                    'rata_rata_nilai' => $rataRataNilai,
                ],
                'grafik_mingguan' => $grafikMingguan,
                'aktivitas_terbaru' => $aktivitasTerbaru,
                'materi_populer' => $materiPopuler,
                'recent_warga' => $recent_wargas,
            ],
        ]);
    }
}
