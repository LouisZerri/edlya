<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SignerBailleurRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'signature_bailleur' => ['required', 'string'],
        ];
    }

    public function messages(): array
    {
        return [
            'signature_bailleur.required' => 'La signature du bailleur est requise.',
        ];
    }
}
