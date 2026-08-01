<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\RubrikPenilaian;
use Illuminate\Http\Request;

class RubrikPenilaianController extends Controller
{
    /**
     * Display a listing of rubrik penilaian (Supports Filtering & Search).
     */
    public function index(Request $request)
    {
        $query = RubrikPenilaian::with('outcome');

        // Filter berdasarkan outcome_id tertentu
        if ($request->filled('outcome_id')) {
            $query->where('outcome_id', $request->outcome_id);
        }

        // Search pada deskripsi capaian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('deskripsi_capaian', 'like', "%{$search}%");
        }

        $rubrik = $query->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $rubrik
        ], 200);
    }

    /**
     * Store or update a rubrik penilaian based on outcome_id and level.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rubriks' => 'required|array|min:1',
            'rubriks.*.outcome_id' => 'required|exists:outcomes,id',
            'rubriks.*.batas_bawah_skor' => 'required|integer|min:0',
            'rubriks.*.batas_atas_skor' => 'required|integer|min:0',
            'rubriks.*.label_level' => 'required|string|max:255',
            'rubriks.*.level' => 'required|integer',
            'rubriks.*.deskripsi_capaian' => 'required|string',
        ]);
        foreach ($validated['rubriks'] as $rubrikData) {
            // Cari berdasarkan outcome_id dan level, lalu update atau create record baru
            $rubrik = RubrikPenilaian::updateOrCreate(
                [
                    'outcome_id' => $rubrikData['outcome_id'],
                    'level' => $rubrikData['level'],
                ],
                [
                    'batas_bawah_skor' => $rubrikData['batas_bawah_skor'],
                    'batas_atas_skor' => $rubrikData['batas_atas_skor'],
                    'label_level' => $rubrikData['label_level'],
                    'deskripsi_capaian' => $rubrikData['deskripsi_capaian'],
                ]
            );

        }

        // Load relasi outcome untuk dikembalikan ke frontend
        $rubrik->load('outcome');

        // Cek apakah record baru dibuat atau diperbarui untuk menentukan pesan & HTTP status
        $isCreated = $rubrik->wasRecentlyCreated;

        return response()->json([
            'status' => 'success',
            'message' => $isCreated
                ? 'Rubrik penilaian berhasil ditambahkan'
                : 'Rubrik penilaian berhasil diperbarui',
            'data' => $rubrik
        ], $isCreated ? 201 : 200);
    }

    /**
     * Display the specified rubrik penilaian.
     */
    public function show($id)
    {
        $rubrik = RubrikPenilaian::with('outcome')->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $rubrik
        ], 200);
    }

    /**
     * Update the specified rubrik penilaian.
     */
    public function update(Request $request, $id)
    {
        $rubrik = RubrikPenilaian::findOrFail($id);

        $validated = $request->validate([
            'outcome_id' => 'required|exists:outcomes,id',
            'batas_bawah_skor' => 'required|integer|min:0',
            'batas_atas_skor' => 'required|integer|gte:batas_bawah_skor',
            'deskripsi_capaian' => 'required|string',
        ]);

        $rubrik->update($validated);
        $rubrik->load('outcome');

        return response()->json([
            'status' => 'success',
            'message' => 'Rubrik penilaian berhasil diperbarui',
            'data' => $rubrik
        ], 200);
    }

    /**
     * Remove the specified rubrik penilaian.
     */
    public function destroy($id)
    {
        $rubrik = RubrikPenilaian::findOrFail($id);
        $rubrik->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Rubrik penilaian berhasil dihapus'
        ], 200);
    }
}
