<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AnalysePhotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'photo_path' => ['required', 'string'],
            'piece_id' => ['required', 'exists:piece,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'photo_path.required' => 'Le chemin de la photo est requis.',
            'piece_id.required' => 'La pièce est requise.',
            'piece_id.exists' => 'La pièce sélectionnée n\'existe pas.',
        ];
    }
}
