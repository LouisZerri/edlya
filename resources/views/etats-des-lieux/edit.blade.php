@extends('layouts.app')

@section('title', 'Modifier état des lieux - Edlya')

@section('content')
    <div class="mb-6">
        <a href="{{ route('etats-des-lieux.show', $etatDesLieux) }}"
            class="text-sm text-slate-500 hover:text-primary-600 transition-colors">
            ← Retour à l'état des lieux
        </a>
    </div>

    <h1 class="text-2xl font-semibold text-slate-800 mb-6">Modifier l'état des lieux</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Colonne gauche : Navigation sticky + Infos générales --}}
        <div class="lg:col-span-1 space-y-6">
            {{-- Wrapper sticky pour progression + navigation uniquement --}}
            @if ($etatDesLieux->pieces->count() > 0)
                <div class="lg:sticky lg:top-6 space-y-4 z-10">
                    {{-- Indicateur de progression --}}
                    <x-progress-indicator :etatDesLieux="$etatDesLieux" />

                    {{-- Navigation rapide --}}
                    <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm">
                        <p class="text-xs text-slate-500 mb-3 font-medium uppercase tracking-wide">Navigation</p>
                        <nav class="flex flex-col gap-1 max-h-[35vh] overflow-y-auto" id="pieces-nav">
                            <a href="#infos-generales" class="nav-link text-sm px-3 py-2 rounded-lg hover:bg-slate-100 transition-colors flex items-center gap-2">
                                <span class="text-base">📋</span>
                                Infos générales
                            </a>
                            <a href="#compteurs" class="nav-link text-sm px-3 py-2 rounded-lg hover:bg-slate-100 transition-colors flex items-center gap-2">
                                <span class="text-base">⚡</span>
                                Compteurs
                                @if($etatDesLieux->compteurs->count() === 4)
                                    <span class="w-2 h-2 rounded-full bg-green-500 ml-auto"></span>
                                @elseif($etatDesLieux->compteurs->count() > 0)
                                    <span class="w-2 h-2 rounded-full bg-amber-500 ml-auto"></span>
                                @endif
                            </a>
                            <a href="#cles" class="nav-link text-sm px-3 py-2 rounded-lg hover:bg-slate-100 transition-colors flex items-center gap-2">
                                <span class="text-base">🔑</span>
                                Clés
                                @if($etatDesLieux->cles->isNotEmpty())
                                    <span class="w-2 h-2 rounded-full bg-green-500 ml-auto"></span>
                                @endif
                            </a>
                            <div class="border-t border-slate-200 my-2"></div>
                            @foreach ($etatDesLieux->pieces as $piece)
                                @php
                                    $piecePhotos = $piece->elements->flatMap(fn($e) => $e->photos);
                                    $hasElements = $piece->elements->isNotEmpty();
                                    $hasPhotos = $piecePhotos->isNotEmpty();
                                    $navPieceStatus = match(true) {
                                        $hasElements && $hasPhotos => 'bg-green-500',
                                        $hasElements => 'bg-amber-500',
                                        default => 'bg-slate-300',
                                    };
                                @endphp
                                <a href="#piece-{{ $piece->id }}"
                                   class="nav-link text-sm px-3 py-2 rounded-lg hover:bg-slate-100 transition-colors flex items-center gap-2"
                                   data-piece-nav="{{ $piece->id }}">
                                    <span class="w-2 h-2 rounded-full {{ $navPieceStatus }}"></span>
                                    {{ $piece->nom }}
                                </a>
                            @endforeach
                        </nav>
                    </div>
                </div>
            @else
                {{-- Sans pièces, afficher juste la progression --}}
                <x-progress-indicator :etatDesLieux="$etatDesLieux" />
            @endif
        </div>

        {{-- Colonne centrale : Infos générales --}}
        <div class="lg:col-span-2 space-y-6">
            {{-- Formulaire informations générales --}}
            <div id="infos-generales" class="bg-white p-6 rounded-lg border border-slate-200 scroll-mt-6">
                <h2 class="font-medium text-slate-800 mb-4">Informations générales</h2>

                <form method="POST" action="{{ route('etats-des-lieux.update', $etatDesLieux) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <x-form.select name="logement_id" label="Logement" :options="$logements->pluck('nom', 'id')->toArray()" :value="$etatDesLieux->logement_id"
                            :required="true" />

                        <x-form.select name="type" label="Type" :options="['entree' => 'Entrée', 'sortie' => 'Sortie']" :value="$etatDesLieux->type" :required="true" />

                        <x-form.input name="date_realisation" type="date" label="Date" :value="$etatDesLieux->date_realisation->format('Y-m-d')"
                            :required="true" />

                        <x-form.input name="locataire_nom" label="Locataire" :value="$etatDesLieux->locataire_nom" :required="true" />

                        <x-form.input name="locataire_email" type="email" label="Email" :value="$etatDesLieux->locataire_email" />

                        <x-form.input name="locataire_telephone" type="tel" label="Téléphone" :value="$etatDesLieux->locataire_telephone" />
                    </div>

                    <div class="mt-4 mb-4">
                        <label for="observations_generales"
                            class="block text-sm font-medium text-slate-700 mb-1">Observations</label>
                        <textarea name="observations_generales" id="observations_generales" rows="3"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none transition-colors text-sm">{{ old('observations_generales', $etatDesLieux->observations_generales) }}</textarea>
                    </div>

                    <x-form.button>Enregistrer</x-form.button>
                </form>
            </div>

            {{-- Section Compteurs (Collapsible) --}}
            @php
                $compteursComplete = $etatDesLieux->compteurs->count() === 4;
                $compteursEnCours = $etatDesLieux->compteurs->count() > 0 && !$compteursComplete;
            @endphp
            <div id="compteurs" class="bg-white rounded-lg border border-slate-200 scroll-mt-6">
                {{-- En-tête cliquable --}}
                <button type="button"
                        data-accordion-toggle="compteurs-content"
                        class="w-full px-6 py-4 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition-colors rounded-t-lg"
                        aria-expanded="true">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <h2 class="font-medium text-slate-800 flex items-center gap-2">
                                Relevé des compteurs
                                @if($compteursComplete)
                                    <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded-full">Complet</span>
                                @elseif($compteursEnCours)
                                    <span class="text-xs px-2 py-0.5 bg-amber-100 text-amber-700 rounded-full">En cours</span>
                                @endif
                            </h2>
                            <p class="text-sm text-slate-500">{{ $etatDesLieux->compteurs->count() }}/4 compteur(s) renseigné(s)</p>
                        </div>
                    </div>
                    <svg data-accordion-icon class="w-5 h-5 text-slate-400 transition-transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- Contenu repliable --}}
                <div id="compteurs-content" class="px-6 pb-6">
                    <div class="mb-4">
                        <x-aide-tooltip
                            texte="Relevez l'index de chaque compteur et prenez une photo comme preuve. Les index permettront de calculer la consommation entre l'entrée et la sortie." />
                    </div>

                    @php
                        $typesCompteurs = [
                            'electricite' => ['label' => 'Électricité', 'icon' => '⚡'],
                            'eau_froide' => ['label' => 'Eau froide', 'icon' => '💧'],
                            'eau_chaude' => ['label' => 'Eau chaude', 'icon' => '🔥'],
                            'gaz' => ['label' => 'Gaz', 'icon' => '🔵'],
                        ];
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($typesCompteurs as $type => $config)
                            @php
                                $compteur = $etatDesLieux->compteurs->where('type', $type)->first();
                            @endphp

                            <div class="border border-slate-200 rounded-lg p-4 bg-slate-50">
                                <div class="flex items-center justify-between mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">{{ $config['icon'] }}</span>
                                        <span class="font-medium text-slate-800">{{ $config['label'] }}</span>
                                    </div>
                                    @if ($compteur)
                                        <span
                                            class="text-xs px-2 py-1 bg-green-100 text-green-700 rounded-full">Renseigné</span>
                                    @endif
                                </div>

                                <form method="POST"
                                    action="{{ $compteur ? route('compteurs.update', $compteur) : route('compteurs.store', $etatDesLieux) }}"
                                    enctype="multipart/form-data">
                                    @csrf
                                    @if ($compteur)
                                        @method('PUT')
                                    @else
                                        <input type="hidden" name="type" value="{{ $type }}">
                                    @endif

                                    <div class="space-y-3">
                                        <div class="grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs text-slate-500 mb-1">N° compteur</label>
                                                <input type="text" name="numero" value="{{ $compteur?->numero }}"
                                                    placeholder="Ex: 12345678"
                                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
                                            </div>
                                            <div>
                                                <label class="block text-xs text-slate-500 mb-1 flex items-center">
                                                    Index / Relevé
                                                    <x-aide-tooltip
                                                        texte="Notez tous les chiffres affichés sur le compteur. Pour l'électricité, notez HP et HC séparément si applicable."
                                                        position="bottom" />
                                                </label>
                                                <input type="text" name="index" value="{{ $compteur?->index }}"
                                                    placeholder="Ex: 45678"
                                                    class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
                                            </div>
                                        </div>

                                        <div>
                                            <label class="block text-xs text-slate-500 mb-1">Commentaire</label>
                                            <input type="text" name="commentaire" value="{{ $compteur?->commentaire }}"
                                                placeholder="Optionnel"
                                                class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
                                        </div>

                                        {{-- Photos du compteur --}}
                                        <div>
                                            <label class="block text-xs text-slate-500 mb-1">Photo(s) du compteur</label>
                                            @if ($compteur?->photos && count($compteur->photos) > 0)
                                                <div class="flex flex-wrap gap-2 mb-2">
                                                    @foreach ($compteur->photos_urls as $index => $photoUrl)
                                                        <div class="relative group">
                                                            <a href="{{ $photoUrl }}" target="_blank" class="block">
                                                                <img src="{{ $photoUrl }}"
                                                                    alt="Compteur {{ $config['label'] }} - Photo {{ $index + 1 }}"
                                                                    class="w-16 h-16 object-cover rounded-lg border border-slate-200">
                                                            </a>
                                                            <button type="button"
                                                                class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 text-xs cursor-pointer flex items-center justify-center shadow-md opacity-0 group-hover:opacity-100 transition-opacity"
                                                                onclick="if(confirm('Supprimer cette photo ?')) { document.getElementById('delete-photo-{{ $type }}-{{ $index }}').submit(); }"
                                                                title="Supprimer">
                                                                ×
                                                            </button>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                            <label class="cursor-pointer block">
                                                <div
                                                    class="flex items-center gap-2 px-3 py-2 bg-white border border-slate-200 border-dashed rounded-lg hover:bg-slate-50 transition-colors">
                                                    <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                    </svg>
                                                    <span class="text-xs text-slate-600 compteur-file-label"
                                                        data-type="{{ $type }}">Ajouter une photo</span>
                                                </div>
                                                <input type="file" name="photo" accept="image/*"
                                                    class="hidden compteur-file-input" data-type="{{ $type }}">
                                            </label>
                                        </div>

                                        <button type="submit"
                                            class="w-full bg-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium hover:bg-slate-300 transition-colors cursor-pointer">
                                            {{ $compteur ? 'Mettre à jour' : 'Enregistrer' }}
                                        </button>
                                    </div>
                                </form>

                                @if ($compteur)
                                    {{-- Formulaires pour supprimer chaque photo individuellement --}}
                                    @if ($compteur->photos)
                                        @foreach ($compteur->photos as $index => $photo)
                                            <form method="POST"
                                                action="{{ route('compteurs.delete-photo', ['compteur' => $compteur, 'index' => $index]) }}"
                                                id="delete-photo-{{ $type }}-{{ $index }}" class="hidden">
                                                @csrf
                                                @method('DELETE')
                                            </form>
                                        @endforeach
                                    @endif

                                    {{-- Formulaire pour supprimer le compteur --}}
                                    <form method="POST" action="{{ route('compteurs.destroy', $compteur) }}"
                                        class="mt-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="w-full text-xs text-red-600 hover:text-red-700 py-1 cursor-pointer"
                                            onclick="return confirm('Supprimer ce compteur ?')">
                                            Supprimer ce compteur
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Section Clés (Collapsible) --}}
            @php
                $clesComplete = $etatDesLieux->cles->isNotEmpty();
            @endphp
            <div id="cles" class="bg-white rounded-lg border border-slate-200 scroll-mt-6">
                {{-- En-tête cliquable --}}
                <button type="button"
                        data-accordion-toggle="cles-content"
                        class="w-full px-6 py-4 flex items-center justify-between cursor-pointer hover:bg-slate-50 transition-colors rounded-t-lg"
                        aria-expanded="true">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                            </svg>
                        </div>
                        <div class="text-left">
                            <h2 class="font-medium text-slate-800 flex items-center gap-2">
                                Remise des clés
                                @if($clesComplete)
                                    <span class="text-xs px-2 py-0.5 bg-green-100 text-green-700 rounded-full">Complet</span>
                                @endif
                            </h2>
                            <p class="text-sm text-slate-500">{{ $etatDesLieux->cles->sum('nombre') }} clé(s) au total</p>
                        </div>
                    </div>
                    <svg data-accordion-icon class="w-5 h-5 text-slate-400 transition-transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>

                {{-- Contenu repliable --}}
                <div id="cles-content" class="px-6 pb-6">
                    <div class="mb-4">
                        <x-aide-tooltip
                            texte="Listez toutes les clés remises avec leur quantité. En cas de perte, le locataire devra rembourser le remplacement des clés et éventuellement de la serrure." />
                    </div>

                    {{-- Liste des clés existantes --}}
                    @if ($etatDesLieux->cles->isNotEmpty())
                        <div class="space-y-4 mb-6">
                            @foreach ($etatDesLieux->cles as $cle)
                                <div class="p-4 bg-slate-50 rounded-lg">
                                    <form method="POST" action="{{ route('cles.update', $cle) }}"
                                        enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')

                                        <div class="flex items-start gap-4">
                                            {{-- Photo --}}
                                            <div class="flex-shrink-0">
                                                @if ($cle->photo)
                                                    <div class="relative">
                                                        <a href="{{ $cle->photo_url }}" target="_blank">
                                                            <img src="{{ $cle->photo_url }}" alt="Photo clé"
                                                                class="w-20 h-20 object-cover rounded-lg border border-slate-200">
                                                        </a>
                                                        <button type="button"
                                                            class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 text-xs cursor-pointer flex items-center justify-center shadow-md"
                                                            onclick="if(confirm('Supprimer cette photo ?')) { document.getElementById('delete-photo-cle-{{ $cle->id }}').submit(); }">
                                                            ×
                                                        </button>
                                                    </div>
                                                @else
                                                    <label class="cursor-pointer block">
                                                        <div
                                                            class="w-20 h-20 bg-white border-2 border-dashed border-slate-300 rounded-lg flex flex-col items-center justify-center hover:border-amber-400 hover:bg-amber-50 transition-colors">
                                                            <svg class="w-6 h-6 text-slate-400" fill="none"
                                                                stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                            </svg>
                                                            <span class="text-xs text-slate-400 mt-1">Photo</span>
                                                        </div>
                                                        <input type="file" name="photo" accept="image/*"
                                                            class="hidden cle-file-input" data-id="{{ $cle->id }}">
                                                    </label>
                                                @endif
                                            </div>

                                            {{-- Champs --}}
                                            <div class="flex-1 grid grid-cols-1 sm:grid-cols-4 gap-3">
                                                <div class="sm:col-span-1">
                                                    <label class="block text-xs text-slate-500 mb-1">Type</label>
                                                    <input type="text" name="type" value="{{ $cle->type }}"
                                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white"
                                                        placeholder="Type de clé" required>
                                                </div>

                                                <div class="sm:col-span-1">
                                                    <label class="block text-xs text-slate-500 mb-1">Nombre</label>
                                                    <input type="number" name="nombre" value="{{ $cle->nombre }}"
                                                        min="1" max="99"
                                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white text-center"
                                                        required>
                                                </div>

                                                <div class="sm:col-span-2">
                                                    <label class="block text-xs text-slate-500 mb-1">Commentaire</label>
                                                    <input type="text" name="commentaire" value="{{ $cle->commentaire }}"
                                                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white"
                                                        placeholder="Optionnel">
                                                </div>
                                            </div>

                                            {{-- Actions --}}
                                            <div class="flex flex-col gap-2">
                                                <button type="submit"
                                                    class="p-2 text-slate-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors cursor-pointer"
                                                    title="Sauvegarder">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </form>

                                    {{-- Formulaires séparés pour supprimer --}}
                                    <form method="POST" action="{{ route('cles.delete-photo', $cle) }}"
                                        id="delete-photo-cle-{{ $cle->id }}" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>

                                    <div class="mt-3 pt-3 border-t border-slate-200 flex justify-end">
                                        <form method="POST" action="{{ route('cles.destroy', $cle) }}"
                                            onsubmit="return confirm('Supprimer cette clé ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="text-xs text-red-500 hover:text-red-700 cursor-pointer">
                                                Supprimer cette clé
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    {{-- Formulaire ajout clé --}}
                    <div class="bg-slate-50 rounded-lg p-4">
                        <p class="text-sm font-medium text-slate-700 mb-3">Ajouter une clé</p>
                        <form method="POST" action="{{ route('cles.store', $etatDesLieux) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="flex items-start gap-4">
                                {{-- Photo --}}
                                <div class="flex-shrink-0">
                                    <label class="cursor-pointer block">
                                        <div class="w-20 h-20 bg-white border-2 border-dashed border-slate-300 rounded-lg flex flex-col items-center justify-center hover:border-amber-400 hover:bg-amber-50 transition-colors"
                                            id="new-cle-photo-preview">
                                            <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            </svg>
                                            <span class="text-xs text-slate-400 mt-1">Photo</span>
                                        </div>
                                        <input type="file" name="photo" accept="image/*" class="hidden"
                                            id="new-cle-photo-input">
                                    </label>
                                </div>

                                {{-- Champs --}}
                                <div class="flex-1 grid grid-cols-1 sm:grid-cols-4 gap-3">
                                    <div class="sm:col-span-1">
                                        <label class="block text-xs text-slate-500 mb-1">Type</label>
                                        <select name="type" required
                                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
                                            <option value="">Choisir...</option>
                                            @foreach (\App\Models\Cle::getTypesCommuns() as $typeCommun)
                                                <option value="{{ $typeCommun }}">{{ $typeCommun }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="sm:col-span-1">
                                        <label class="block text-xs text-slate-500 mb-1">Nombre</label>
                                        <input type="number" name="nombre" value="1" min="1" max="99"
                                            required
                                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white text-center">
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label class="block text-xs text-slate-500 mb-1">Commentaire</label>
                                        <input type="text" name="commentaire" placeholder="Optionnel"
                                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
                                    </div>
                                </div>

                                {{-- Bouton --}}
                                <div class="flex-shrink-0">
                                    <button type="submit"
                                        class="bg-primary-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors cursor-pointer mt-5">
                                        Ajouter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Section Pièces --}}
            <div class="bg-white p-6 rounded-lg border border-slate-200">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-medium text-slate-800 flex items-center">
                        Pièces
                        <x-aide-tooltip
                            texte="Ajoutez les pièces du logement puis leurs éléments. Pour chaque élément, indiquez l'état et les éventuelles dégradations." />
                    </h2>
                    <span class="text-sm text-slate-500">{{ $etatDesLieux->pieces->count() }} pièce(s)</span>
                </div>

                {{-- Pré-remplissage par typologie - Seulement si aucune pièce --}}
                @if ($etatDesLieux->pieces->count() === 0)
                    <div class="mb-6">
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-5">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-amber-800 mb-1">Aucune pièce pour le moment</p>
                                    <p class="text-sm text-amber-700 mb-4">Générez rapidement les pièces selon la typologie
                                        du bien ou ajoutez-les manuellement ci-dessous.</p>

                                    <div class="flex gap-3">
                                        <select id="typologie-select"
                                            class="flex-1 px-4 py-2.5 border border-amber-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-100 focus:border-amber-500 outline-none bg-white">
                                            <option value="">Choisir une typologie...</option>
                                            <optgroup label="Appartements">
                                                <option value="studio">Studio</option>
                                                <option value="f1">F1 / T1</option>
                                                <option value="f2">F2 / T2</option>
                                                <option value="f3">F3 / T3</option>
                                                <option value="f4">F4 / T4</option>
                                                <option value="f5">F5 / T5</option>
                                            </optgroup>
                                            <optgroup label="Maisons">
                                                <option value="maison_t3">Maison T3</option>
                                                <option value="maison_t4">Maison T4</option>
                                                <option value="maison_t5">Maison T5</option>
                                            </optgroup>
                                        </select>
                                        <button type="button" id="btn-generer-pieces"
                                            data-url="{{ route('etats-des-lieux.generer-pieces', $etatDesLieux) }}"
                                            class="bg-amber-600 text-white px-5 py-2.5 rounded-lg text-sm font-medium hover:bg-amber-700 transition-colors cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed"
                                            disabled>
                                            Générer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Liste des pièces avec accordéons --}}
                @forelse ($etatDesLieux->pieces as $index => $piece)
                    @php
                        $allPhotos = $piece->elements->flatMap(fn($e) => $e->photos)->sortBy('id')->values();
                        $hasElements = $piece->elements->isNotEmpty();
                        $hasPhotos = $allPhotos->isNotEmpty();
                        $pieceStatus = match(true) {
                            $hasElements && $hasPhotos => 'bg-green-500',
                            $hasElements => 'bg-amber-500',
                            default => 'bg-slate-300',
                        };
                    @endphp

                    <div id="piece-{{ $piece->id }}" class="border border-slate-200 rounded-lg mb-6 scroll-mt-6">
                        {{-- En-tête cliquable --}}
                        <button type="button"
                                data-accordion-toggle="piece-content-{{ $piece->id }}"
                                class="w-full px-5 py-4 bg-slate-50 rounded-t-lg flex items-center justify-between cursor-pointer hover:bg-slate-100 transition-colors"
                                aria-expanded="true">
                            <div class="flex items-center gap-3">
                                <span class="w-2.5 h-2.5 rounded-full {{ $pieceStatus }}"></span>
                                <div class="text-left">
                                    <h3 class="font-semibold text-slate-800">{{ $piece->nom }}</h3>
                                    <p class="text-sm text-slate-500">{{ $piece->elements->count() }} élément(s) ·
                                        {{ $allPhotos->count() }} photo(s)</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="text-sm text-primary-600 hover:text-primary-700 px-3 py-1.5 rounded-lg hover:bg-primary-50 transition-colors border border-primary-200"
                                      data-analyse-piece="{{ $piece->id }}"
                                      data-analyse-piece-name="{{ $piece->nom }}"
                                      onclick="event.stopPropagation();">
                                    Analyse IA
                                </span>
                                <svg data-accordion-icon class="w-5 h-5 text-slate-400 transition-transform rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </div>
                        </button>

                        {{-- Contenu repliable --}}
                        <div id="piece-content-{{ $piece->id }}" class="p-5">
                            {{-- Bouton supprimer pièce --}}
                            <div class="flex justify-end mb-4">
                                <form method="POST" action="{{ route('pieces.destroy', $piece) }}"
                                    onsubmit="return confirm('Supprimer cette pièce et tous ses éléments ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="text-red-500 hover:text-red-700 cursor-pointer p-2 rounded-lg hover:bg-red-50 transition-colors text-sm flex items-center gap-1"
                                        title="Supprimer la pièce">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                        Supprimer la pièce
                                    </button>
                                </form>
                            </div>

                            {{-- Éléments existants --}}
                            @if ($piece->elements->isNotEmpty())
                                <div class="space-y-4 mb-6">
                                    @foreach ($piece->elements as $element)
                                        <div class="border border-slate-200 rounded-lg p-4 bg-white">
                                            <div class="flex justify-end mb-2">
                                                <form method="POST" action="{{ route('elements.destroy', $element) }}"
                                                    onsubmit="return confirm('Supprimer cet élément ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-red-400 hover:text-red-600 cursor-pointer p-1.5 rounded hover:bg-red-50 transition-colors"
                                                        title="Supprimer l'élément">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>

                                            <form method="POST" action="{{ route('elements.update', $element) }}"
                                                class="space-y-4"
                                                data-auto-save
                                                id="element-form-{{ $element->id }}">
                                                @csrf
                                                @method('PUT')

                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                    <div>
                                                        <label class="block text-xs text-slate-500 mb-1">Type</label>
                                                        <select name="type"
                                                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
                                                            <option value="sol"
                                                                {{ $element->type === 'sol' ? 'selected' : '' }}>Sol
                                                            </option>
                                                            <option value="mur"
                                                                {{ $element->type === 'mur' ? 'selected' : '' }}>Mur
                                                            </option>
                                                            <option value="plafond"
                                                                {{ $element->type === 'plafond' ? 'selected' : '' }}>
                                                                Plafond</option>
                                                            <option value="menuiserie"
                                                                {{ $element->type === 'menuiserie' ? 'selected' : '' }}>
                                                                Menuiserie</option>
                                                            <option value="electricite"
                                                                {{ $element->type === 'electricite' ? 'selected' : '' }}>
                                                                Électricité</option>
                                                            <option value="plomberie"
                                                                {{ $element->type === 'plomberie' ? 'selected' : '' }}>
                                                                Plomberie</option>
                                                            <option value="chauffage"
                                                                {{ $element->type === 'chauffage' ? 'selected' : '' }}>
                                                                Chauffage</option>
                                                            <option value="equipement"
                                                                {{ $element->type === 'equipement' ? 'selected' : '' }}>
                                                                Équipement</option>
                                                            <option value="mobilier"
                                                                {{ $element->type === 'mobilier' ? 'selected' : '' }}>
                                                                Mobilier</option>
                                                            <option value="electromenager"
                                                                {{ $element->type === 'electromenager' ? 'selected' : '' }}>
                                                                Électroménager</option>
                                                            <option value="autre"
                                                                {{ $element->type === 'autre' ? 'selected' : '' }}>Autre
                                                            </option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-slate-500 mb-1">Nom</label>
                                                        <input type="text" name="nom" value="{{ $element->nom }}"
                                                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-slate-500 mb-1 flex items-center">
                                                            État
                                                            <x-aide-tooltip
                                                                texte="Neuf = jamais utilisé. Très bon = quasi neuf. Bon = usure légère. Usagé = usure normale. Mauvais = dégradations. Hors service = non fonctionnel."
                                                                position="bottom" />
                                                        </label>
                                                        <select name="etat"
                                                            class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
                                                            <option value="neuf"
                                                                {{ $element->etat === 'neuf' ? 'selected' : '' }}>Neuf
                                                            </option>
                                                            <option value="tres_bon"
                                                                {{ $element->etat === 'tres_bon' ? 'selected' : '' }}>Très
                                                                bon</option>
                                                            <option value="bon"
                                                                {{ $element->etat === 'bon' ? 'selected' : '' }}>Bon
                                                            </option>
                                                            <option value="usage"
                                                                {{ $element->etat === 'usage' ? 'selected' : '' }}>Usagé
                                                            </option>
                                                            <option value="mauvais"
                                                                {{ $element->etat === 'mauvais' ? 'selected' : '' }}>
                                                                Mauvais</option>
                                                            <option value="hors_service"
                                                                {{ $element->etat === 'hors_service' ? 'selected' : '' }}>
                                                                Hors service</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                {{-- Section Dégradations --}}
                                                <div class="degradations-section">
                                                    <label class="block text-xs text-slate-500 mb-2 flex items-center">
                                                        Dégradations constatées
                                                        <x-aide-tooltip
                                                            texte="Sélectionnez les dégradations constatées. L'usure normale (vétusté) est à la charge du bailleur, les dégradations anormales à la charge du locataire." />
                                                    </label>

                                                    @php
                                                        $suggerees = $element->getDegradationsSuggerees();
                                                        $selectionnees = $element->degradations ?? [];
                                                    @endphp

                                                    <div class="degradations-list flex flex-wrap gap-2 mb-3">
                                                        {{-- Dégradations suggérées --}}
                                                        @foreach ($suggerees as $degradation)
                                                            @php
                                                                $isSelected = in_array($degradation, $selectionnees);
                                                            @endphp
                                                            <label
                                                                class="degradation-badge cursor-pointer inline-flex items-center px-3 py-1.5 text-xs rounded-full border transition-all {{ $isSelected ? 'bg-red-100 text-red-700 border-red-300' : 'bg-slate-100 text-slate-600 border-slate-200 hover:bg-slate-200' }}">
                                                                <input type="checkbox" name="degradations[]"
                                                                    value="{{ $degradation }}" class="hidden"
                                                                    {{ $isSelected ? 'checked' : '' }}>
                                                                <span>{{ $degradation }}</span>
                                                            </label>
                                                        @endforeach

                                                        {{-- Dégradations personnalisées (non dans les suggestions) --}}
                                                        @foreach ($selectionnees as $degradation)
                                                            @if (!in_array($degradation, $suggerees))
                                                                <label
                                                                    class="degradation-badge cursor-pointer inline-flex items-center px-3 py-1.5 text-xs rounded-full border transition-all bg-red-100 text-red-700 border-red-300">
                                                                    <input type="checkbox" name="degradations[]"
                                                                        value="{{ $degradation }}" class="hidden"
                                                                        checked>
                                                                    <span>{{ $degradation }}</span>
                                                                </label>
                                                            @endif
                                                        @endforeach
                                                    </div>

                                                    {{-- Ajout personnalisé --}}
                                                    <div class="flex gap-2">
                                                        <input type="text"
                                                            class="custom-degradation-input flex-1 px-3 py-1.5 text-xs border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none"
                                                            placeholder="Ajouter une dégradation personnalisée...">
                                                        <button type="button"
                                                            class="add-custom-degradation px-3 py-1.5 text-xs bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg transition-colors cursor-pointer">
                                                            + Ajouter
                                                        </button>
                                                    </div>
                                                </div>

                                                <div>
                                                    <label class="block text-xs text-slate-500 mb-1 flex items-center">
                                                        Observations
                                                        <x-aide-tooltip
                                                            texte="Décrivez l'état de l'élément de manière factuelle. Vous pouvez utiliser l'assistant IA (💡) pour améliorer votre texte."
                                                            position="bottom" />
                                                    </label>
                                                    <div class="flex gap-2">
                                                        <div class="flex-1 relative">
                                                            <input type="text" name="observations"
                                                                value="{{ $element->observations }}"
                                                                placeholder="Remarques complémentaires..."
                                                                class="w-full px-3 py-2 pr-10 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none">
                                                            <button type="button"
                                                                class="btn-assistant-ia absolute right-2 top-1/2 -translate-y-1/2 p-1.5 text-slate-400 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-colors cursor-pointer"
                                                                title="Améliorer avec l'IA">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2"
                                                                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Indicateur de sauvegarde auto --}}
                                                <div class="save-indicator text-xs text-right"></div>

                                                {{-- Photos associées à cet élément --}}
                                                @if ($element->photos->isNotEmpty())
                                                    <div class="flex items-center gap-2 pt-2">
                                                        <span class="text-xs text-slate-500">Photos :</span>
                                                        <div class="flex flex-wrap gap-1">
                                                            @foreach ($element->photos as $photo)
                                                                @php
                                                                    // Calculer l'index global de la photo dans la pièce
                                                                    $globalIndex =
                                                                        $allPhotos->search(
                                                                            fn($p) => $p->id === $photo->id,
                                                                        ) + 1;
                                                                @endphp
                                                                <a href="{{ $photo->url }}" target="_blank"
                                                                    class="inline-block bg-green-100 text-green-700 px-2 py-0.5 rounded text-xs font-medium hover:bg-green-200 transition-colors">
                                                                    Photo {{ $globalIndex }}
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div
                                    class="text-center py-8 mb-6 bg-slate-50 rounded-lg border border-dashed border-slate-300">
                                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="text-slate-500 mb-1">Aucun élément dans cette pièce</p>
                                    <p class="text-sm text-slate-400">Utilisez "Analyse IA" ou ajoutez manuellement
                                        ci-dessous</p>
                                </div>
                            @endif

                            {{-- Formulaire ajout élément --}}
                            <div class="bg-slate-50 rounded-lg p-4 mb-6">
                                <p class="text-sm font-medium text-slate-700 mb-3">Ajouter un élément</p>
                                <form method="POST" action="{{ route('elements.store', $piece) }}">
                                    @csrf
                                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                        <select name="type" required
                                            class="px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
                                            <option value="">Type</option>
                                            <option value="sol">Sol</option>
                                            <option value="mur">Mur</option>
                                            <option value="plafond">Plafond</option>
                                            <option value="menuiserie">Menuiserie</option>
                                            <option value="electricite">Électricité</option>
                                            <option value="plomberie">Plomberie</option>
                                            <option value="chauffage">Chauffage</option>
                                            <option value="equipement">Équipement</option>
                                            <option value="mobilier">Mobilier</option>
                                            <option value="electromenager">Électroménager</option>
                                            <option value="autre">Autre</option>
                                        </select>
                                        <input type="text" name="nom" placeholder="Nom de l'élément" required
                                            class="px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
                                        <select name="etat" required
                                            class="px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
                                            <option value="">État</option>
                                            <option value="neuf">Neuf</option>
                                            <option value="tres_bon">Très bon</option>
                                            <option value="bon" selected>Bon</option>
                                            <option value="usage">Usagé</option>
                                            <option value="mauvais">Mauvais</option>
                                            <option value="hors_service">Hors service</option>
                                        </select>
                                        <button type="submit"
                                            class="bg-primary-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors cursor-pointer">
                                            Ajouter
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- Photos --}}
                            <div class="border-t border-slate-200 pt-5">
                                <p class="text-sm font-medium text-slate-700 mb-4 flex items-center">
                                    Photos de la pièce ({{ $allPhotos->count() }})
                                    <x-aide-tooltip
                                        texte="Photographiez chaque élément important, les dégradations constatées et les vues d'ensemble. Les photos servent de preuves en cas de litige." />
                                </p>

                                @if ($allPhotos->isNotEmpty())
                                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-3 mb-4">
                                        @foreach ($allPhotos as $index => $photo)
                                            <div class="text-center relative">
                                                <a href="{{ $photo->url }}" target="_blank"
                                                    class="block cursor-pointer">
                                                    <img src="{{ $photo->url }}" alt="Photo {{ $index + 1 }}"
                                                        class="w-full aspect-square object-cover rounded-lg hover:opacity-90 transition-opacity border border-slate-200">
                                                </a>
                                                <form method="POST" action="{{ route('photos.destroy', $photo) }}"
                                                    class="absolute -top-2 -right-2">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 text-sm cursor-pointer flex items-center justify-center shadow-md transition-colors"
                                                        onclick="return confirm('Supprimer cette photo ?')"
                                                        title="Supprimer">×</button>
                                                </form>
                                                <p class="text-xs text-slate-600 mt-1 font-medium">Photo
                                                    {{ $index + 1 }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                @if ($piece->elements->isNotEmpty())
                                    <div class="bg-slate-50 rounded-lg p-4">
                                        <p class="text-xs text-slate-500 mb-3">Ajouter une photo</p>
                                        <form method="POST" action="{{ route('pieces.photos.store', $piece) }}"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <div class="flex items-center gap-3">
                                                <select name="element_id" required
                                                    class="px-3 py-2.5 border border-slate-300 rounded-lg text-sm bg-white">
                                                    @foreach ($piece->elements as $element)
                                                        <option value="{{ $element->id }}">{{ $element->nom }}</option>
                                                    @endforeach
                                                </select>
                                                <label class="flex-1 cursor-pointer">
                                                    <div
                                                        class="flex items-center gap-3 px-4 py-2.5 bg-white border border-slate-200 border-dashed rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-colors">
                                                        <svg class="w-5 h-5 text-slate-400" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        <span class="text-sm text-slate-600 truncate file-label">Choisir
                                                            une photo</span>
                                                    </div>
                                                    <input type="file" name="photo" accept="image/*" required
                                                        class="hidden file-input">
                                                </label>
                                                <button type="submit"
                                                    class="bg-primary-600 text-white px-4 py-2.5 rounded-lg hover:bg-primary-700 cursor-pointer transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    <p class="text-sm text-slate-400 italic">Ajoutez d'abord des éléments pour pouvoir
                                        ajouter des photos.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-slate-50 rounded-lg border border-dashed border-slate-300">
                        <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <p class="text-slate-500 mb-1">Aucune pièce pour le moment</p>
                        <p class="text-sm text-slate-400">Utilisez le pré-remplissage ci-dessus ou ajoutez manuellement
                            ci-dessous</p>
                    </div>
                @endforelse

                {{-- Formulaire ajout pièce --}}
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <p class="text-sm font-medium text-slate-700 mb-3">Ajouter une nouvelle pièce</p>
                    <form method="POST" action="{{ route('pieces.store', $etatDesLieux) }}">
                        @csrf
                        <div class="flex gap-3">
                            <input type="text" name="nom"
                                placeholder="Nom de la pièce (ex: Salon, Chambre 1, Cuisine...)" required
                                class="flex-1 px-4 py-3 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none">
                            <button type="submit"
                                class="bg-primary-600 text-white px-6 py-3 rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors cursor-pointer">
                                Ajouter la pièce
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <x-analyse-modal />
@endsection
