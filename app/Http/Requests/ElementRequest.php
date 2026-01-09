<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ElementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:sol,mur,plafond,menuiserie,electricite,plomberie,equipement,autre'],
            'nom' => ['required', 'string', 'min:2', 'max:255'],
            'etat' => ['required', 'in:neuf,tres_bon,bon,usage,mauvais,hors_service'],
            'observations' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Le type d\'élément est requis.',
            'type.in' => 'Le type d\'élément n\'est pas valide.',
            'nom.required' => 'Le nom de l\'élément est requis.',
            'nom.min' => 'Le nom doit contenir au moins 2 caractères.',
            'etat.required' => 'L\'état est requis.',
            'etat.in' => 'L\'état sélectionné n\'est pas valide.',
            'observations.max' => 'Les observations ne peuvent pas dépasser 1000 caractères.',
        ];
    }
}