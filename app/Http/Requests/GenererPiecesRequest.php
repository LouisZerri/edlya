<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class GenererPiecesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'typologie' => ['required', 'string'],
            'remplacer' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'typologie.required' => 'La typologie est requise.',
        ];
    }
}
