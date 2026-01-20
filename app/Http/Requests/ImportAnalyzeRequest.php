<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ImportAnalyzeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'pdf' => ['required', 'file', 'mimes:pdf', 'max:20480'],
        ];
    }

    public function messages(): array
    {
        return [
            'pdf.required' => 'Le fichier PDF est requis.',
            'pdf.file' => 'Le fichier doit être valide.',
            'pdf.mimes' => 'Le fichier doit être au format PDF.',
            'pdf.max' => 'Le fichier ne doit pas dépasser 20 Mo.',
        ];
    }
}
