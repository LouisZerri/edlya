<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PartageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'in:email,lien'],
            'email' => ['required_if:type,email', 'nullable', 'email'],
            'duree' => ['required', 'integer', 'min:1', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Le type de partage est requis.',
            'type.in' => 'Le type de partage n\'est pas valide.',
            'email.required_if' => 'L\'email est requis pour un partage par email.',
            'email.email' => 'L\'adresse email n\'est pas valide.',
            'duree.required' => 'La durée de validité est requise.',
            'duree.min' => 'La durée minimale est de 1 jour.',
            'duree.max' => 'La durée maximale est de 30 jours.',
        ];
    }
}
