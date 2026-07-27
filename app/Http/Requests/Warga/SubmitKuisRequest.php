<?php

namespace App\Http\Requests\Warga;

use Illuminate\Foundation\Http\FormRequest;

class SubmitKuisRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'warga';
    }

    public function rules(): array
    {
        return [
            'materi_id'           => 'required|exists:materi,id',
            'jawaban'             => 'required|array|min:1',
            'jawaban.*.kuis_id'   => 'required|exists:kuis,id',
            'jawaban.*.opsi_id'   => 'required|exists:opsi_jawaban,id',
        ];
    }
}
