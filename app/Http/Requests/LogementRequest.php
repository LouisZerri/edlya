<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class LogementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'nom' => ['required', 'string', 'min:3', 'max:255'],
            'adresse' => ['required', 'string', 'min:5', 'max:255'],
            'code_postal' => ['required', 'string', 'regex:/^[0-9]{5}$/'],
            'ville' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[a-zA-ZÀ-ÿ\s\-]+$/'],
            'type' => ['nullable', 'in:appartement,maison,studio,local_commercial'],
            'surface' => ['nullable', 'numeric', 'min:5', 'max:10000'],
            'nb_pieces' => ['nullable', 'integer', 'min:1', 'max:50'],
            'description' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nom.required' => 'Le nom du logement est requis.',
            'nom.min' => 'Le nom doit contenir au moins 3 caractères.',
            'adresse.required' => 'L\'adresse est requise.',
            'adresse.min' => 'L\'adresse doit contenir au moins 5 caractères.',
            'code_postal.required' => 'Le code postal est requis.',
            'code_postal.regex' => 'Le code postal doit contenir 5 chiffres.',
            'ville.required' => 'La ville est requise.',
            'ville.min' => 'La ville doit contenir au moins 2 caractères.',
            'ville.regex' => 'La ville ne doit contenir que des lettres.',
            'type.required' => 'Le type de logement est requis.',
            'type.in' => 'Le type de logement n\'est pas valide.',
            'surface.numeric' => 'La surface doit être un nombre.',
            'surface.min' => 'La surface doit être d\'au moins 5 m².',
            'surface.max' => 'La surface ne peut pas dépasser 10 000 m².',
            'nb_pieces.integer' => 'Le nombre de pièces doit être un entier.',
            'nb_pieces.min' => 'Le logement doit avoir au moins 1 pièce.',
            'nb_pieces.max' => 'Le nombre de pièces ne peut pas dépasser 50.',
            'description.max' => 'La description ne peut pas dépasser 1000 caractères.',
        ];
    }
}