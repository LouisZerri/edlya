<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AnalyseUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'photo' => ['required', 'image', 'max:10240'],
            'piece_id' => ['required', 'exists:piece,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'photo.required' => 'La photo est requise.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.max' => 'L\'image ne doit pas dépasser 10 Mo.',
            'piece_id.required' => 'La pièce est requise.',
            'piece_id.exists' => 'La pièce sélectionnée n\'existe pas.',
        ];
    }
}
