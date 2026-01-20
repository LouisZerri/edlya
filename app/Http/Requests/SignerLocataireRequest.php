<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SignerLocataireRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'signature_locataire' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'signature_locataire.required' => 'La signature du locataire est requise.',
        ];
    }
}
