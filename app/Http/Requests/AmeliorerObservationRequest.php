<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AmeliorerObservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'element' => ['required', 'string', 'max:100'],
            'etat' => ['required', 'string', 'max:50'],
            'observation' => ['nullable', 'string', 'max:500'],
            'degradations' => ['nullable', 'array'],
            'degradations.*' => ['string'],
        ];
    }

    public function messages(): array
    {
        return [
            'element.required' => 'L\'élément est requis.',
            'etat.required' => 'L\'état est requis.',
        ];
    }
}
