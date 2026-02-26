<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'photo' => ['required', 'image', 'max:10240'],
            'legende' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'element_id' => ['sometimes', 'exists:element,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'photo.required' => 'La photo est requise.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.max' => 'L\'image ne doit pas dépasser 10 Mo.',
        ];
    }
}
