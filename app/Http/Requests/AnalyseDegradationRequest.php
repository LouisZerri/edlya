<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class AnalyseDegradationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'element_id' => ['required', 'exists:element,id'],
            'photo_id' => ['required', 'exists:photo,id'],
            'etat_entree' => ['required', 'string'],
            'observations' => ['nullable', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'element_id.required' => 'L\'élément est requis.',
            'element_id.exists' => 'L\'élément sélectionné n\'existe pas.',
            'photo_id.required' => 'La photo est requise.',
            'photo_id.exists' => 'La photo sélectionnée n\'existe pas.',
            'etat_entree.required' => 'L\'état à l\'entrée est requis.',
        ];
    }
}
