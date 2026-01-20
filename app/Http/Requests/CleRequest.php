<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'max:255'],
            'nombre' => ['required', 'integer', 'min:1', 'max:99'],
            'commentaire' => ['nullable', 'string', 'max:500'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Le type de clé est requis.',
            'nombre.required' => 'Le nombre de clés est requis.',
            'nombre.min' => 'Le nombre minimum de clés est 1.',
            'nombre.max' => 'Le nombre maximum de clés est 99.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.max' => 'L\'image ne doit pas dépasser 5 Mo.',
        ];
    }
}
