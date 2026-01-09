<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PieceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'min:2', 'max:255'],
            'observations' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom de la pièce est requis.',
            'nom.min' => 'Le nom doit contenir au moins 2 caractères.',
            'observations.max' => 'Les observations ne peuvent pas dépasser 1000 caractères.',
        ];
    }
}