@extends('layouts.app')

@section('title', 'Nouvel état des lieux - Edlya')

@section('content')
    <div class="mb-6">
        <a href="{{ route('etats-des-lieux.index') }}" class="text-sm text-slate-500 hover:text-primary-600 transition-colors">
            ← Retour aux états des lieux
        </a>
    </div>

    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-semibold text-slate-800 mb-6">Nouvel état des lieux</h1>

        @if($logements->isEmpty())
            <div class="bg-white p-8 rounded-lg border border-slate-200 text-center">
                <p class="text-slate-500 mb-4">Vous devez d'abord créer un logement.</p>
                <a href="{{ route('logements.create') }}" class="text-primary-600 hover:underline">Créer un logement</a>
            </div>
        @else
            <div class="bg-white p-6 rounded-lg border border-slate-200">
                <form method="POST" action="{{ route('etats-des-lieux.store') }}">
                    @csrf

                    <x-form.select
                        name="logement_id"
                        label="Logement"
                        :options="$logements->pluck('nom', 'id')->toArray()"
                        placeholder="Sélectionner un logement"
                        :value="$logementSelectionne"
                        :required="true"
                    />

                    <x-form.select
                        name="type"
                        label="Type d'état des lieux"
                        :options="['entree' => 'Entrée', 'sortie' => 'Sortie']"
                        placeholder="Sélectionner un type"
                        :required="true"
                    />

                    <x-form.input
                        name="date_realisation"
                        type="date"
                        label="Date de réalisation"
                        :value="date('Y-m-d')"
                        :required="true"
                    />

                    <div class="border-t border-slate-200 my-6 pt-6">
                        <h2 class="text-lg font-medium text-slate-800 mb-4">Informations locataire</h2>

                        <x-form.input
                            name="locataire_nom"
                            label="Nom du locataire"
                            :required="true"
                        />

                        <x-form.input
                            name="locataire_email"
                            type="email"
                            label="Email"
                            placeholder="Optionnel"
                        />

                        <x-form.input
                            name="locataire_telephone"
                            type="tel"
                            label="Téléphone"
                            placeholder="Optionnel"
                        />
                    </div>

                    <div class="mb-4">
                        <label for="observations_generales" class="block text-sm font-medium text-slate-700 mb-1">Observations générales</label>
                        <textarea
                            name="observations_generales"
                            id="observations_generales"
                            rows="3"
                            placeholder="Optionnel"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none transition-colors"
                        >{{ old('observations_generales') }}</textarea>
                        @error('observations_generales')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-form.button>Créer et ajouter les pièces</x-form.button>
                </form>
            </div>
        @endif
    </div>
@endsection