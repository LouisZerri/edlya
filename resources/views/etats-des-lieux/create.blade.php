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

                        {{-- Autres locataires (colocation) --}}
                        <div class="col-span-full" id="colocataires-create">
                            <div id="colocataires-badges-create" class="flex flex-wrap gap-2 mb-2" style="display:none;"></div>
                            <div id="colocataires-hiddens-create"></div>
                            <div id="colocataires-input-create" class="flex gap-2" style="display:none;">
                                <input type="text" id="colocataire-nom-create" placeholder="Nom de l'occupant"
                                    class="flex-1 px-4 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none">
                                <button type="button" onclick="addColocataire('create')" class="px-4 py-2 bg-primary-600 text-white rounded-lg text-sm hover:bg-primary-700">
                                    Ajouter
                                </button>
                            </div>
                            <button type="button" id="colocataires-toggle-create" onclick="document.getElementById('colocataires-input-create').style.display='flex';this.style.display='none';" class="flex items-center gap-2 text-sm text-primary-600 hover:text-primary-700 font-medium py-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                                Ajouter un occupant
                            </button>
                            <p id="colocataires-hint-create" class="text-xs text-slate-400 mt-1" style="display:none;">Le locataire principal signe pour tous</p>
                        </div>
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

                    {{-- Pré-remplissage par typologie --}}
                    <div class="border-t border-slate-200 my-6 pt-6">
                        <h2 class="text-lg font-medium text-slate-800 mb-2">Pré-remplissage des pièces</h2>
                        <p class="text-sm text-slate-500 mb-4">Choisissez une typologie pour générer automatiquement les pièces correspondantes.</p>

                        <div class="mb-4">
                            <label for="typologie" class="block text-sm font-medium text-slate-700 mb-1">Typologie du bien</label>
                            <select 
                                name="typologie" 
                                id="typologie" 
                                class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none transition-colors bg-white"
                            >
                                <option value="">Aucun pré-remplissage (ajouter manuellement)</option>
                                <optgroup label="Appartements">
                                    <option value="studio" {{ old('typologie') === 'studio' ? 'selected' : '' }}>Studio</option>
                                    <option value="f1" {{ old('typologie') === 'f1' ? 'selected' : '' }}>F1 / T1</option>
                                    <option value="f2" {{ old('typologie') === 'f2' ? 'selected' : '' }}>F2 / T2</option>
                                    <option value="f3" {{ old('typologie') === 'f3' ? 'selected' : '' }}>F3 / T3</option>
                                    <option value="f4" {{ old('typologie') === 'f4' ? 'selected' : '' }}>F4 / T4</option>
                                    <option value="f5" {{ old('typologie') === 'f5' ? 'selected' : '' }}>F5 / T5</option>
                                </optgroup>
                                <optgroup label="Maisons">
                                    <option value="maison_t3" {{ old('typologie') === 'maison_t3' ? 'selected' : '' }}>Maison T3</option>
                                    <option value="maison_t4" {{ old('typologie') === 'maison_t4' ? 'selected' : '' }}>Maison T4</option>
                                    <option value="maison_t5" {{ old('typologie') === 'maison_t5' ? 'selected' : '' }}>Maison T5</option>
                                </optgroup>
                            </select>
                        </div>

                        {{-- Aperçu des pièces --}}
                        <div id="typologie-preview" class="hidden">
                            <p class="text-sm font-medium text-slate-700 mb-2">Pièces qui seront créées :</p>
                            <div id="typologie-pieces" class="flex flex-wrap gap-2"></div>
                        </div>
                    </div>

                    <x-form.button>Créer l'état des lieux</x-form.button>
                </form>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        const colocatairesData = {};

        function initColocataires(ctx) {
            if (!colocatairesData[ctx]) colocatairesData[ctx] = [];
        }

        function addColocataire(ctx) {
            initColocataires(ctx);
            const input = document.getElementById('colocataire-nom-' + ctx);
            const nom = input.value.trim();
            if (!nom) return;
            colocatairesData[ctx].push(nom);
            input.value = '';
            renderColocataires(ctx);
            input.focus();
        }

        function removeColocataire(ctx, index) {
            colocatairesData[ctx].splice(index, 1);
            renderColocataires(ctx);
            if (colocatairesData[ctx].length === 0) {
                document.getElementById('colocataires-input-' + ctx).style.display = 'none';
                document.getElementById('colocataires-toggle-' + ctx).style.display = '';
            }
        }

        function renderColocataires(ctx) {
            const badges = document.getElementById('colocataires-badges-' + ctx);
            const hiddens = document.getElementById('colocataires-hiddens-' + ctx);
            const hint = document.getElementById('colocataires-hint-' + ctx);
            const list = colocatairesData[ctx] || [];

            badges.style.display = list.length > 0 ? 'flex' : 'none';
            hint.style.display = list.length > 0 ? '' : 'none';

            badges.innerHTML = list.map((nom, i) =>
                '<span class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary-50 text-primary-700 rounded-full text-sm">' +
                    nom +
                    '<button type="button" onclick="removeColocataire(\'' + ctx + '\',' + i + ')" class="ml-1 w-4 h-4 rounded-full bg-primary-200 hover:bg-primary-300 inline-flex items-center justify-center">' +
                        '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>' +
                    '</button>' +
                '</span>'
            ).join('');

            hiddens.innerHTML = list.map((nom, i) =>
                '<input type="hidden" name="autres_locataires[' + i + ']" value="' + nom.replace(/"/g, '&quot;') + '">'
            ).join('');
        }

        document.addEventListener('DOMContentLoaded', function() {
            ['create', 'edit'].forEach(function(ctx) {
                const input = document.getElementById('colocataire-nom-' + ctx);
                if (input) {
                    input.addEventListener('keydown', function(e) {
                        if (e.key === 'Enter') { e.preventDefault(); addColocataire(ctx); }
                    });
                }
            });
        });
    </script>
    @endpush
@endsection