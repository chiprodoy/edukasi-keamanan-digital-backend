<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Outcome;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class OutcomeController extends Controller
{
    /**
     * Display a listing of outcomes (Supports Search).
     */
    public function index(Request $request)
    {
        $query = Outcome::with('rubrikPenilaian');

        // Fitur pencarian berdasarkan kode, judul, atau deskripsi
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_outcome', 'like', "%{$search}%")
                  ->orWhere('judul_kompetensi', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%");
            });
        }

        $outcomes = $query->latest()->get();

        return response()->json([
            'status' => 'success',
            'data' => $outcomes
        ], 200);
    }

    /**
     * Store a newly created outcome.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_outcome' => 'required|string|max:20|unique:outcomes,kode_outcome',
            'judul_kompetensi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $outcome = Outcome::create($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data outcome berhasil ditambahkan',
            'data' => $outcome
        ], 201);
    }

    /**
     * Display the specified outcome.
     */
    public function show($id)
    {
        $outcome = Outcome::findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $outcome
        ], 200);
    }

    /**
     * Update the specified outcome.
     */
    public function update(Request $request, $id)
    {
        $outcome = Outcome::findOrFail($id);

        $validated = $request->validate([
            'kode_outcome' => [
                'required',
                'string',
                'max:20',
                Rule::unique('outcomes', 'kode_outcome')->ignore($outcome->id),
            ],
            'judul_kompetensi' => 'required|string|max:255',
            'deskripsi' => 'required|string',
        ]);

        $outcome->update($validated);

        return response()->json([
            'status' => 'success',
            'message' => 'Data outcome berhasil diperbarui',
            'data' => $outcome
        ], 200);
    }

    /**
     * Remove the specified outcome.
     */
    public function destroy($id)
    {
        $outcome = Outcome::findOrFail($id);
        $outcome->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Data outcome berhasil dihapus'
        ], 200);
    }
}
