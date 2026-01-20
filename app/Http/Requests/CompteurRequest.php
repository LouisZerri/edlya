<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class CompteurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        $rules = [
            'numero' => ['nullable', 'string', 'max:255'],
            'index' => ['nullable', 'string', 'max:255'],
            'commentaire' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'image', 'max:10240'],
        ];

        // Le type est requis uniquement lors de la création (store)
        if ($this->isMethod('POST') && $this->route()->getName() === 'compteurs.store') {
            $rules['type'] = ['required', 'in:electricite,eau_froide,eau_chaude,gaz'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Le type de compteur est requis.',
            'type.in' => 'Le type de compteur n\'est pas valide.',
            'photo.image' => 'Le fichier doit être une image.',
            'photo.max' => 'L\'image ne doit pas dépasser 10 Mo.',
        ];
    }
}
