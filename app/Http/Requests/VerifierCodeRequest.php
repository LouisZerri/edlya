<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VerifierCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'size:6'],
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Le code de validation est requis.',
            'code.size' => 'Le code doit contenir 6 caractères.',
        ];
    }
}
