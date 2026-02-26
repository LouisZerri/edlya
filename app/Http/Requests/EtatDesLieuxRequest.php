<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class EtatDesLieuxRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'logement_id' => ['required', 'exists:logement,id'],
            'type' => ['required', 'in:entree,sortie'],
            'date_realisation' => ['required', 'date', 'before_or_equal:today'],
            'locataire_nom' => ['required', 'string', 'min:2', 'max:255'],
            'locataire_email' => ['nullable', 'email', 'max:255'],
            'locataire_telephone' => ['nullable', 'string', 'max:20'],
            'observations_generales' => ['nullable', 'string', 'max:2000'],
            'autres_locataires' => ['nullable', 'array'],
            'autres_locataires.*' => ['string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'logement_id.required' => 'Le logement est requis.',
            'logement_id.exists' => 'Le logement sélectionné n\'existe pas.',
            'type.required' => 'Le type d\'état des lieux est requis.',
            'type.in' => 'Le type doit être "entrée" ou "sortie".',
            'date_realisation.required' => 'La date de réalisation est requise.',
            'date_realisation.date' => 'La date n\'est pas valide.',
            'date_realisation.before_or_equal' => 'La date ne peut pas être dans le futur.',
            'locataire_nom.required' => 'Le nom du locataire est requis.',
            'locataire_nom.min' => 'Le nom doit contenir au moins 2 caractères.',
            'locataire_email.email' => 'L\'adresse email n\'est pas valide.',
            'observations_generales.max' => 'Les observations ne peuvent pas dépasser 2000 caractères.',
        ];
    }
}