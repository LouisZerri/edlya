@extends('layouts.app')

@section('title', 'Modifier état des lieux - Edlya')

@section('content')
    <div class="mb-6">
        <a href="{{ route('etats-des-lieux.show', $etatDesLieux) }}" class="text-sm text-slate-500 hover:text-primary-600 transition-colors">
            ← Retour à l'état des lieux
        </a>
    </div>

    <h1 class="text-2xl font-semibold text-slate-800 mb-6">Modifier l'état des lieux</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Colonne gauche : Infos générales --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg border border-slate-200 sticky top-6">
                <h2 class="font-medium text-slate-800 mb-4">Informations générales</h2>

                <form method="POST" action="{{ route('etats-des-lieux.update', $etatDesLieux) }}">
                    @csrf
                    @method('PUT')

                    <x-form.select name="logement_id" label="Logement" :options="$logements->pluck('nom', 'id')->toArray()" :value="$etatDesLieux->logement_id" :required="true" />

                    <x-form.select name="type" label="Type" :options="['entree' => 'Entrée', 'sortie' => 'Sortie']" :value="$etatDesLieux->type" :required="true" />

                    <x-form.input name="date_realisation" type="date" label="Date" :value="$etatDesLieux->date_realisation->format('Y-m-d')" :required="true" />

                    <x-form.input name="locataire_nom" label="Locataire" :value="$etatDesLieux->locataire_nom" :required="true" />

                    <x-form.input name="locataire_email" type="email" label="Email" :value="$etatDesLieux->locataire_email" />

                    <x-form.input name="locataire_telephone" type="tel" label="Téléphone" :value="$etatDesLieux->locataire_telephone" />

                    <div class="mb-4">
                        <label for="observations_generales" class="block text-sm font-medium text-slate-700 mb-1">Observations</label>
                        <textarea name="observations_generales" id="observations_generales" rows="3"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none transition-colors text-sm">{{ old('observations_generales', $etatDesLieux->observations_generales) }}</textarea>
                    </div>

                    <x-form.button>Enregistrer</x-form.button>
                </form>
            </div>
        </div>

        {{-- Colonne droite : Pièces --}}
        <div class="lg:col-span-2">
            <div class="bg-white p-6 rounded-lg border border-slate-200">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="font-medium text-slate-800">Pièces</h2>
                    <span class="text-sm text-slate-500">{{ $etatDesLieux->pieces->count() }} pièce(s)</span>
                </div>

                {{-- Pré-remplissage par typologie - Seulement si aucune pièce --}}
                @if($etatDesLieux->pieces->count() === 0)
                    <div class="mb-6">
                        <div class="bg-amber-50 border border-amber-200 rounded-lg p-5">
                            <div class="flex items-start gap-3">
                                <div class="flex-shrink-0">
                                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-amber-800 mb-1">Aucune pièce pour le moment</p>
                                    <p class="text-sm text-amber-700 mb-4">Générez rapidement les pièces selon la typologie du bien ou ajoutez-les manuellement ci-dessous.</p>
                                    
                                    <div class="flex gap-3">
                                        <select id="typologie-select" class="flex-1 px-4 py-2.5 border border-amber-300 rounded-lg text-sm focus:ring-2 focus:ring-amber-100 focus:border-amber-500 outline-none bg-white">
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
                                        <button type="button" 
                                            id="btn-generer-pieces" 
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
                @else
                    {{-- Sommaire des pièces (si plus de 2 pièces) --}}
                    @if($etatDesLieux->pieces->count() > 2)
                        <div class="mb-6 p-4 bg-slate-50 rounded-lg">
                            <p class="text-xs text-slate-500 mb-2">Accès rapide :</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach($etatDesLieux->pieces as $piece)
                                    <a href="#piece-{{ $piece->id }}" class="text-sm px-3 py-1 bg-white border border-slate-200 rounded-full hover:border-primary-300 hover:text-primary-600 transition-colors">
                                        {{ $piece->nom }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif

                {{-- Liste des pièces --}}
                @forelse ($etatDesLieux->pieces as $piece)
                    @php
                        $allPhotos = $piece->elements->flatMap(fn($e) => $e->photos)->values();
                    @endphp

                    <div id="piece-{{ $piece->id }}" class="border border-slate-200 rounded-lg mb-6 scroll-mt-6">
                        {{-- En-tête de la pièce --}}
                        <div class="px-5 py-4 bg-slate-50 rounded-t-lg flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold text-slate-800">{{ $piece->nom }}</h3>
                                <p class="text-sm text-slate-500">{{ $piece->elements->count() }} élément(s) · {{ $allPhotos->count() }} photo(s)</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <button type="button" 
                                    data-analyse-piece="{{ $piece->id }}" 
                                    data-analyse-piece-name="{{ $piece->nom }}"
                                    class="text-sm text-primary-600 hover:text-primary-700 cursor-pointer px-3 py-1.5 rounded-lg hover:bg-primary-50 transition-colors border border-primary-200">
                                    Analyse IA
                                </button>
                                <form method="POST" action="{{ route('pieces.destroy', $piece) }}" onsubmit="return confirm('Supprimer cette pièce et tous ses éléments ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 cursor-pointer p-2 rounded-lg hover:bg-red-50 transition-colors" title="Supprimer la pièce">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Contenu de la pièce --}}
                        <div class="p-5">
                            {{-- Éléments existants --}}
                            @if ($piece->elements->isNotEmpty())
                                <div class="space-y-4 mb-6">
                                    @foreach ($piece->elements as $element)
                                        <div class="border border-slate-200 rounded-lg p-4 bg-white">
                                            {{-- Bouton supprimer --}}
                                            <div class="flex justify-end mb-2">
                                                <form method="POST" action="{{ route('elements.destroy', $element) }}" onsubmit="return confirm('Supprimer cet élément ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-400 hover:text-red-600 cursor-pointer p-1.5 rounded hover:bg-red-50 transition-colors" title="Supprimer l'élément">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>

                                            {{-- Formulaire modification --}}
                                            <form method="POST" action="{{ route('elements.update', $element) }}" class="space-y-3">
                                                @csrf
                                                @method('PUT')
                                                
                                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                                    <div>
                                                        <label class="block text-xs text-slate-500 mb-1">Type</label>
                                                        <select name="type" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
                                                            <option value="sol" {{ $element->type === 'sol' ? 'selected' : '' }}>Sol</option>
                                                            <option value="mur" {{ $element->type === 'mur' ? 'selected' : '' }}>Mur</option>
                                                            <option value="plafond" {{ $element->type === 'plafond' ? 'selected' : '' }}>Plafond</option>
                                                            <option value="menuiserie" {{ $element->type === 'menuiserie' ? 'selected' : '' }}>Menuiserie</option>
                                                            <option value="electricite" {{ $element->type === 'electricite' ? 'selected' : '' }}>Électricité</option>
                                                            <option value="plomberie" {{ $element->type === 'plomberie' ? 'selected' : '' }}>Plomberie</option>
                                                            <option value="chauffage" {{ $element->type === 'chauffage' ? 'selected' : '' }}>Chauffage</option>
                                                            <option value="equipement" {{ $element->type === 'equipement' ? 'selected' : '' }}>Équipement</option>
                                                            <option value="mobilier" {{ $element->type === 'mobilier' ? 'selected' : '' }}>Mobilier</option>
                                                            <option value="electromenager" {{ $element->type === 'electromenager' ? 'selected' : '' }}>Électroménager</option>
                                                            <option value="autre" {{ $element->type === 'autre' ? 'selected' : '' }}>Autre</option>
                                                        </select>
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-slate-500 mb-1">Nom</label>
                                                        <input type="text" name="nom" value="{{ $element->nom }}" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none">
                                                    </div>
                                                    <div>
                                                        <label class="block text-xs text-slate-500 mb-1">État</label>
                                                        <select name="etat" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
                                                            <option value="neuf" {{ $element->etat === 'neuf' ? 'selected' : '' }}>Neuf</option>
                                                            <option value="tres_bon" {{ $element->etat === 'tres_bon' ? 'selected' : '' }}>Très bon</option>
                                                            <option value="bon" {{ $element->etat === 'bon' ? 'selected' : '' }}>Bon</option>
                                                            <option value="usage" {{ $element->etat === 'usage' ? 'selected' : '' }}>Usagé</option>
                                                            <option value="mauvais" {{ $element->etat === 'mauvais' ? 'selected' : '' }}>Mauvais</option>
                                                            <option value="hors_service" {{ $element->etat === 'hors_service' ? 'selected' : '' }}>Hors service</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                
                                                <div>
                                                    <label class="block text-xs text-slate-500 mb-1">Observations (les références photos s'ajoutent automatiquement)</label>
                                                    <div class="flex gap-2">
                                                        <input type="text" name="observations" value="{{ $element->observations }}" placeholder="Ex: Légère rayure, trace d'usure..." class="flex-1 px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none">
                                                        <button type="submit" class="bg-slate-100 text-slate-700 px-4 py-2 rounded-lg text-sm hover:bg-slate-200 transition-colors cursor-pointer">
                                                            Sauvegarder
                                                        </button>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-8 mb-6 bg-slate-50 rounded-lg border border-dashed border-slate-300">
                                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="text-slate-500 mb-1">Aucun élément dans cette pièce</p>
                                    <p class="text-sm text-slate-400">Utilisez "Analyse IA" ou ajoutez manuellement ci-dessous</p>
                                </div>
                            @endif

                            {{-- Formulaire ajout élément --}}
                            <div class="bg-slate-50 rounded-lg p-4 mb-6">
                                <p class="text-sm font-medium text-slate-700 mb-3">Ajouter un élément</p>
                                <form method="POST" action="{{ route('elements.store', $piece) }}">
                                    @csrf
                                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                                        <select name="type" required class="px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
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
                                        <input type="text" name="nom" placeholder="Nom de l'élément" required class="px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
                                        <select name="etat" required class="px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
                                            <option value="">État</option>
                                            <option value="neuf">Neuf</option>
                                            <option value="tres_bon">Très bon</option>
                                            <option value="bon" selected>Bon</option>
                                            <option value="usage">Usagé</option>
                                            <option value="mauvais">Mauvais</option>
                                            <option value="hors_service">Hors service</option>
                                        </select>
                                        <button type="submit" class="bg-primary-600 text-white px-4 py-2.5 rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors cursor-pointer">
                                            Ajouter
                                        </button>
                                    </div>
                                </form>
                            </div>

                            {{-- Photos regroupées en bas de la pièce --}}
                            <div class="border-t border-slate-200 pt-5">
                                <p class="text-sm font-medium text-slate-700 mb-4">Photos de la pièce ({{ $allPhotos->count() }})</p>
                                
                                @if ($allPhotos->isNotEmpty())
                                    <div class="grid grid-cols-4 sm:grid-cols-6 gap-3 mb-4">
                                        @foreach ($allPhotos as $index => $photo)
                                            <div class="text-center relative">
                                                <a href="{{ $photo->url }}" 
                                                    data-lightbox="piece-{{ $piece->id }}"
                                                    data-src="{{ $photo->url }}"
                                                    data-caption="Photo {{ $index + 1 }}"
                                                    class="block cursor-pointer">
                                                    <img src="{{ $photo->url }}" 
                                                        alt="Photo {{ $index + 1 }}" 
                                                        class="w-full aspect-square object-cover rounded-lg hover:opacity-90 transition-opacity border border-slate-200">
                                                </a>
                                                <form method="POST" action="{{ route('photos.destroy', $photo) }}" class="absolute -top-2 -right-2">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white rounded-full w-6 h-6 text-sm cursor-pointer flex items-center justify-center shadow-md transition-colors" onclick="return confirm('Supprimer cette photo ?')" title="Supprimer">×</button>
                                                </form>
                                                <p class="text-xs text-slate-600 mt-1 font-medium">Photo {{ $index + 1 }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Formulaire ajout photo --}}
                                @if ($piece->elements->isNotEmpty())
                                    <div class="bg-slate-50 rounded-lg p-4">
                                        <p class="text-xs text-slate-500 mb-3">Ajouter une photo (la référence sera ajoutée automatiquement dans les observations)</p>
                                        <form method="POST" action="{{ route('pieces.photos.store', $piece) }}" enctype="multipart/form-data">
                                            @csrf
                                            <div class="flex items-center gap-3">
                                                <select name="element_id" required class="px-3 py-2.5 border border-slate-300 rounded-lg text-sm bg-white">
                                                    @foreach ($piece->elements as $element)
                                                        <option value="{{ $element->id }}">{{ $element->nom }}</option>
                                                    @endforeach
                                                </select>
                                                <label class="flex-1 cursor-pointer">
                                                    <div class="flex items-center gap-3 px-4 py-2.5 bg-white border border-slate-200 border-dashed rounded-lg hover:bg-slate-50 hover:border-slate-300 transition-colors">
                                                        <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        </svg>
                                                        <span class="text-sm text-slate-600 truncate file-label">Choisir une photo</span>
                                                    </div>
                                                    <input type="file" name="photo" accept="image/*" required class="hidden file-input">
                                                </label>
                                                <input type="hidden" name="latitude" class="geolocation-lat">
                                                <input type="hidden" name="longitude" class="geolocation-lng">
                                                <button type="submit" class="bg-primary-600 text-white px-4 py-2.5 rounded-lg hover:bg-primary-700 cursor-pointer transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                @else
                                    <p class="text-sm text-slate-400 italic">Ajoutez d'abord des éléments pour pouvoir ajouter des photos.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    {{-- Message si aucune pièce (après le bloc de pré-remplissage) --}}
                    <div class="text-center py-12 bg-slate-50 rounded-lg border border-dashed border-slate-300">
                        <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                        <p class="text-slate-500 mb-1">Aucune pièce pour le moment</p>
                        <p class="text-sm text-slate-400">Utilisez le pré-remplissage ci-dessus ou ajoutez manuellement ci-dessous</p>
                    </div>
                @endforelse

                {{-- Formulaire ajout pièce --}}
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <p class="text-sm font-medium text-slate-700 mb-3">Ajouter une nouvelle pièce</p>
                    <form method="POST" action="{{ route('pieces.store', $etatDesLieux) }}">
                        @csrf
                        <div class="flex gap-3">
                            <input type="text" name="nom" placeholder="Nom de la pièce (ex: Salon, Chambre 1, Cuisine...)" required class="flex-1 px-4 py-3 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none">
                            <button type="submit" class="bg-primary-600 text-white px-6 py-3 rounded-lg text-sm font-medium hover:bg-primary-700 transition-colors cursor-pointer">
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