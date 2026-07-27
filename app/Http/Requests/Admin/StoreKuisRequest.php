<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreKuisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'materi_id'            => 'required|exists:materi,id',
            'teks_soal'            => 'required|string',
            'poin'                 => 'required|integer|min:1',
            'opsi'                 => 'required|array|min:2',
            'opsi.*.teks_pilihan'  => 'required|string',
            'opsi.*.is_benar'      => 'required|boolean',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $opsi = $this->input('opsi', []);
            $jumlahBenar = collect($opsi)->where('is_benar', true)->count();

            if ($jumlahBenar !== 1) {
                $validator->errors()->add('opsi', 'Opsi jawaban wajib memiliki tepat 1 kunci jawaban yang benar.');
            }
        });
    }
}
