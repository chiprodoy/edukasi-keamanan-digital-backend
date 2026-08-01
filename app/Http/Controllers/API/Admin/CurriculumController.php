<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreArtikelRequest;
use App\Http\Requests\Admin\StoreKuisRequest;
use App\Http\Requests\Admin\StoreMateriRequest;
use App\Http\Requests\Admin\StoreOutcomeRequest;
use App\Models\Artikel;
use App\Models\Kuis;
use App\Models\Materi;
use App\Models\OpsiJawaban;
use App\Models\Outcome;
use App\Models\RubrikPenilaian;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CurriculumController extends Controller
{
    public function storeOutcome(StoreOutcomeRequest $request): JsonResponse
    {
        $outcome = DB::transaction(function () use ($request) {
            $newOutcome = Outcome::create([
                'kode_outcome'     => $request->kode_outcome,
                'judul_kompetensi' => $request->judul_kompetensi,
                'deskripsi'        => $request->deskripsi,
            ]);

            foreach ($request->rubriks as $rubrik) {
                RubrikPenilaian::create([
                    'outcome_id'        => $newOutcome->id,
                    'batas_bawah_skor'  => $rubrik['batas_bawah'],
                    'batas_atas_skor'   => $rubrik['batas_atas'],
                    'deskripsi_capaian' => $rubrik['deskripsi'],
                ]);
            }

            return $newOutcome;
        });

        return response()->json([
            'message' => 'Outcome OBE dan Rubrik berhasil disimpan.',
            'data'    => $outcome->load('rubrikPenilaian'),
        ], 201);
    }

    public function storeMateri(StoreMateriRequest $request): JsonResponse
    {
        $admin = auth()->user()->admin;

        $materi = DB::transaction(function () use ($request, $admin) {
            $newMateri = Materi::create([
                'admin_id'  => $admin->id,
                'judul'     => $request->judul,
                'slug'      => Str::slug($request->judul) . '-' . Str::random(5),
                'kategori'  => $request->kategori,
                'konten'    => $request->konten,
                'media_url' => $request->media_url,
                'status'    => 'active',
            ]);

            // Sinkronisasi pivot materi_outcome
            $newMateri->outcomes()->sync($request->outcome_ids);

            return $newMateri;
        });

        return response()->json([
            'message' => 'Modul Materi berhasil diterbitkan.',
            'data'    => $materi->load('outcomes'),
        ], 201);
    }

    public function storeKuis(StoreKuisRequest $request): JsonResponse
    {
        $kuis = DB::transaction(function () use ($request) {
            $newKuis = Kuis::create([
                'materi_id' => $request->materi_id,
                'teks_soal' => $request->teks_soal,
                'poin'      => $request->poin,
            ]);

            foreach ($request->opsi as $item) {
                OpsiJawaban::create([
                    'kuis_id'      => $newKuis->id,
                    'teks_pilihan' => $item['teks_pilihan'],
                    'is_benar'     => $item['is_benar'],
                ]);
            }

            return $newKuis;
        });

        return response()->json([
            'message' => 'Soal kuis berhasil ditambahkan.',
            'data'    => $kuis->load('opsiJawaban'),
        ], 201);
    }


}
