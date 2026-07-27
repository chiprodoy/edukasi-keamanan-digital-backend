<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMateriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'judul'       => 'required|string|max:255',
            'kategori'    => 'required|string|max:100',
            'konten'      => 'required|string',
            'media_url'   => 'nullable|url',
            'outcome_ids' => 'required|array|min:1',
            'outcome_ids.*' => 'exists:outcomes,id',
        ];
    }
}
