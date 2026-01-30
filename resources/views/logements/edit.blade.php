@extends('layouts.app')

@section('title', 'Modifier ' . $logement->nom . ' - Edlya')

@section('content')
    <div class="mb-6">
        <a href="{{ route('logements.show', $logement) }}" class="text-sm text-slate-500 hover:text-primary-600 transition-colors">
            ← Retour au logement
        </a>
    </div>

    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-semibold text-slate-800 mb-6">Modifier le logement</h1>

        <div class="bg-white p-6 rounded-lg border border-slate-200">
            <form method="POST" action="{{ route('logements.update', $logement) }}">
                @csrf
                @method('PUT')

                <x-form.input 
                    name="nom" 
                    label="Nom du logement" 
                    :value="$logement->nom"
                    :required="true" 
                    :autofocus="true"
                />

                <x-form.select 
                    name="type" 
                    label="Type de bien" 
                    :options="[
                        'appartement' => 'Appartement',
                        'maison' => 'Maison',
                        'studio' => 'Studio',
                        'local_commercial' => 'Local commercial',
                    ]"
                    :value="$logement->type"
                    :required="true"
                />

                <x-form.input 
                    name="adresse" 
                    label="Adresse" 
                    :value="$logement->adresse"
                    :required="true"
                />

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form.input
                        name="code_postal"
                        label="Code postal"
                        :value="$logement->code_postal"
                        :required="true"
                    />

                    <x-form.input
                        name="ville"
                        label="Ville"
                        :value="$logement->ville"
                        :required="true"
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-form.input
                        name="surface"
                        type="number"
                        label="Surface (m²)"
                        :value="$logement->surface"
                    />

                    <x-form.input
                        name="nb_pieces"
                        type="number"
                        label="Nombre de pièces"
                        :value="$logement->nb_pieces"
                    />
                </div>

                <div class="mb-4">
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-1">Description</label>
                    <textarea 
                        name="description" 
                        id="description" 
                        rows="3"
                        class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none transition-colors"
                    >{{ old('description', $logement->description) }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <x-form.button>Enregistrer les modifications</x-form.button>
            </form>
        </div>
    </div>
@endsection