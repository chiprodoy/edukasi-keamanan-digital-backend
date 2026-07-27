<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreArtikelRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'judul'     => 'required|string|max:255',
            'kategori'  => 'required|string|max:100',
            'konten'    => 'required|string',
            'thumbnail' => 'nullable|string',
            'is_pinned' => 'boolean',
            'status'    => 'required|in:published,draft',
        ];
    }
}
