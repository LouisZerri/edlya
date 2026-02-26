<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ImportStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'data' => ['required', 'array'],
            'logement_id' => ['nullable', 'exists:logement,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'data.required' => 'Les données importées sont requises.',
            'data.array' => 'Les données doivent être un tableau.',
            'logement_id.exists' => 'Le logement sélectionné n\'existe pas.',
        ];
    }
}
