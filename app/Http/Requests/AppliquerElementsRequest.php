<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AppliquerElementsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'piece_id' => ['required', 'exists:piece,id'],
            'elements' => ['required', 'array'],
            'elements.*.type' => ['required', 'string'],
            'elements.*.nom' => ['required', 'string'],
            'elements.*.etat' => ['required', 'string'],
            'elements.*.observations' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'piece_id.required' => 'La pièce est requise.',
            'piece_id.exists' => 'La pièce sélectionnée n\'existe pas.',
            'elements.required' => 'Les éléments sont requis.',
            'elements.*.type.required' => 'Le type de l\'élément est requis.',
            'elements.*.nom.required' => 'Le nom de l\'élément est requis.',
            'elements.*.etat.required' => 'L\'état de l\'élément est requis.',
        ];
    }
}
