<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreOutcomeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'kode_outcome'            => 'required|string|max:20|unique:outcomes,kode_outcome',
            'judul_kompetensi'        => 'required|string|max:255',
            'deskripsi'               => 'required|string',
            'rubriks'                 => 'required|array|min:1',
            'rubriks.*.batas_bawah'  => 'required|integer|min:0|max:100',
            'rubriks.*.batas_atas'   => 'required|integer|min:0|max:100|gte:rubriks.*.batas_bawah',
            'rubriks.*.deskripsi'    => 'required|string',
        ];
    }
}
