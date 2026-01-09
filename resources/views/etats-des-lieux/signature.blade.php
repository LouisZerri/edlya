@extends('layouts.app')

@section('title', 'Signature - Edlya')

@section('content')
    <div class="mb-6">
        <a href="{{ route('etats-des-lieux.show', $etatDesLieux) }}" class="text-sm text-slate-500 hover:text-primary-600 transition-colors">
            ← Retour à l'état des lieux
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-semibold text-slate-800 mb-2">Signature de l'état des lieux</h1>
        <p class="text-slate-500 mb-6">{{ $etatDesLieux->logement->nom }} - {{ $etatDesLieux->type_libelle }}</p>

        <form method="POST" action="{{ route('etats-des-lieux.signature', $etatDesLieux) }}" id="signature-form">
            @csrf
            <input type="hidden" name="signature_bailleur" id="input-signature-bailleur">
            <input type="hidden" name="signature_locataire" id="input-signature-locataire">

            <div class="grid md:grid-cols-2 gap-6">
                {{-- Signature Bailleur --}}
                <div class="bg-white p-6 rounded-lg border border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-medium text-slate-800">Bailleur / Agent</h2>
                        <button type="button" id="clear-bailleur" class="text-sm text-slate-500 hover:text-primary-600 cursor-pointer">
                            Effacer
                        </button>
                    </div>
                    <p class="text-sm text-slate-500 mb-3">{{ $etatDesLieux->user->name }}</p>
                    <div style="border: 1px solid #e2e8f0; border-radius: 4px; background: #fff;">
                        <canvas id="signature-bailleur" style="width: 100%; height: 200px; touch-action: none;"></canvas>
                    </div>
                </div>

                {{-- Signature Locataire --}}
                <div class="bg-white p-6 rounded-lg border border-slate-200">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-medium text-slate-800">Locataire</h2>
                        <button type="button" id="clear-locataire" class="text-sm text-slate-500 hover:text-primary-600 cursor-pointer">
                            Effacer
                        </button>
                    </div>
                    <p class="text-sm text-slate-500 mb-3">{{ $etatDesLieux->locataire_nom }}</p>
                    <div style="border: 1px solid #e2e8f0; border-radius: 4px; background: #fff;">
                        <canvas id="signature-locataire" style="width: 100%; height: 200px; touch-action: none;"></canvas>
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <button type="submit" class="w-full bg-primary-600 text-white py-3 px-4 rounded-lg hover:bg-primary-700 font-medium cursor-pointer">
                    Valider et signer l'état des lieux
                </button>
            </div>
        </form>
    </div>
@endsection