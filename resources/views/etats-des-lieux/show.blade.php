@extends('layouts.app')

@section('title', 'État des lieux - ' . $etatDesLieux->logement->nom . ' - Edlya')

@section('content')
    <div class="mb-6">
        <a href="{{ route('etats-des-lieux.index') }}"
            class="text-sm text-slate-500 hover:text-primary-600 transition-colors">
            ← Retour aux états des lieux
        </a>
    </div>

    {{-- En-tête --}}
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800">{{ $etatDesLieux->logement->nom }}</h1>
            <p class="text-slate-500">{{ $etatDesLieux->logement->adresse_complete }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <span
                class="px-3 py-1 text-sm rounded {{ $etatDesLieux->type === 'entree' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                {{ $etatDesLieux->type_libelle }}
            </span>
            <span class="px-3 py-1 text-sm rounded {{ $etatDesLieux->statut_couleur }}">
                {{ $etatDesLieux->statut_libelle }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Colonne gauche : Infos générales --}}
        <div class="lg:col-span-1">
            <div class="bg-white p-6 rounded-lg border border-slate-200 sticky top-6">
                <h2 class="font-medium text-slate-800 mb-4">Informations générales</h2>
                <dl class="space-y-4 text-sm">
                    <div>
                        <dt class="text-slate-500">Date</dt>
                        <dd class="text-slate-800 font-medium">{{ $etatDesLieux->date_realisation->format('d/m/Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Locataire</dt>
                        <dd class="text-slate-800 font-medium">{{ $etatDesLieux->locataire_nom }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Email</dt>
                        <dd class="text-slate-800">{{ $etatDesLieux->locataire_email ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-slate-500">Téléphone</dt>
                        <dd class="text-slate-800">{{ $etatDesLieux->locataire_telephone ?? '-' }}</dd>
                    </div>
                </dl>
                @if ($etatDesLieux->observations_generales)
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <dt class="text-sm text-slate-500 mb-1">Observations générales</dt>
                        <dd class="text-sm text-slate-800">{{ $etatDesLieux->observations_generales }}</dd>
                    </div>
                @endif

                {{-- Signatures --}}
                @if ($etatDesLieux->statut === 'signe')
                    <div class="mt-4 pt-4 border-t border-slate-100">
                        <p class="text-sm text-slate-500 mb-3">Signatures</p>
                        <div class="space-y-3">
                            @if ($etatDesLieux->signature_bailleur)
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <p class="text-xs text-green-700 font-medium">Bailleur / Agent</p>
                                    <p class="text-xs text-green-600">Signé le
                                        {{ $etatDesLieux->date_signature_bailleur->format('d/m/Y à H:i') }}</p>
                                </div>
                            @endif
                            @if ($etatDesLieux->signature_locataire)
                                <div class="bg-green-50 p-3 rounded-lg">
                                    <p class="text-xs text-green-700 font-medium">Locataire</p>
                                    <p class="text-xs text-green-600">Signé le
                                        {{ $etatDesLieux->date_signature_locataire->format('d/m/Y à H:i') }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="mt-6 pt-4 border-t border-slate-100 space-y-2">
                    @if ($etatDesLieux->statut !== 'signe')
                        <a href="{{ route('etats-des-lieux.signature', $etatDesLieux) }}"
                            class="block w-full text-center bg-green-600 text-white px-4 py-2.5 rounded-lg hover:bg-green-700 transition-colors font-medium">
                            Signer
                        </a>
                    @endif
                    <a href="{{ route('etats-des-lieux.pdf', $etatDesLieux) }}"
                        class="block w-full text-center bg-slate-800 text-white px-4 py-2.5 rounded-lg hover:bg-slate-900 transition-colors font-medium">
                        Télécharger PDF
                    </a>
                    <a href="{{ route('etats-des-lieux.edit', $etatDesLieux) }}"
                        class="block w-full text-center bg-primary-600 text-white px-4 py-2.5 rounded-lg hover:bg-primary-700 transition-colors font-medium">
                        Modifier
                    </a>
                    <button type="button" id="btn-partager"
                        class="block w-full text-center bg-purple-600 text-white px-4 py-2.5 rounded-lg hover:bg-purple-700 transition-colors font-medium cursor-pointer">
                        Partager
                    </button>
                    @if ($etatDesLieux->type === 'sortie')
                        <a href="{{ route('etats-des-lieux.comparatif', $etatDesLieux) }}"
                            class="block w-full text-center bg-amber-500 text-white px-4 py-2.5 rounded-lg hover:bg-amber-600 transition-colors font-medium">
                            Comparer avec l'entrée
                        </a>
                    @endif
                    <form method="POST" action="{{ route('etats-des-lieux.destroy', $etatDesLieux) }}"
                        onsubmit="return confirm('Supprimer cet état des lieux ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full text-center text-red-600 hover:text-red-700 transition-colors cursor-pointer py-2">
                            Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Colonne droite : Pièces --}}
        <div class="lg:col-span-2">
            {{-- Sommaire des pièces (si plus de 2 pièces) --}}
            @if ($etatDesLieux->pieces->count() > 2)
                <div class="mb-6 p-4 bg-white rounded-lg border border-slate-200">
                    <p class="text-sm text-slate-500 mb-3">Accès rapide aux pièces :</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($etatDesLieux->pieces as $piece)
                            <a href="#piece-{{ $piece->id }}"
                                class="text-sm px-3 py-1.5 bg-slate-50 border border-slate-200 rounded-full hover:border-primary-300 hover:text-primary-600 transition-colors">
                                {{ $piece->nom }}
                                <span class="text-slate-400 ml-1">({{ $piece->elements->count() }})</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($etatDesLieux->pieces->isNotEmpty())
                @foreach ($etatDesLieux->pieces as $piece)
                    @php
                        $allPhotos = $piece->elements->flatMap(fn($e) => $e->photos)->values();
                    @endphp

                    <div id="piece-{{ $piece->id }}"
                        class="bg-white rounded-lg border border-slate-200 mb-6 scroll-mt-6">
                        {{-- En-tête de la pièce --}}
                        <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-lg">
                            <h3 class="font-semibold text-slate-800">{{ $piece->nom }}</h3>
                            <p class="text-sm text-slate-500">{{ $piece->elements->count() }} élément(s) · {{ $allPhotos->count() }} photo(s)</p>
                            @if ($piece->observations)
                                <p class="text-sm text-slate-600 mt-2 italic">{{ $piece->observations }}</p>
                            @endif
                        </div>

                        {{-- Éléments --}}
                        @if ($piece->elements->isNotEmpty())
                            <div class="p-6">
                                <div class="space-y-4">
                                    @foreach ($piece->elements as $element)
                                        <div class="border border-slate-200 rounded-lg p-4 bg-white hover:shadow-sm transition-shadow">
                                            {{-- En-tête de l'élément --}}
                                            <div class="flex items-start justify-between mb-2">
                                                <div>
                                                    <div class="flex items-center gap-2 mb-1">
                                                        <span class="font-medium text-slate-800">{{ $element->nom }}</span>
                                                        <span class="px-2.5 py-1 text-xs rounded-full {{ $element->etat_couleur }}">{{ $element->etat_libelle }}</span>
                                                    </div>
                                                    <p class="text-sm text-slate-500">{{ $element->type }}</p>
                                                </div>
                                            </div>

                                            @if ($element->observations)
                                                <p class="text-sm text-slate-600 mt-2 italic bg-slate-50 p-3 rounded-lg">
                                                    {{ $element->observations }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>

                                {{-- Photos regroupées en bas de la pièce --}}
                                @if ($allPhotos->isNotEmpty())
                                    <div class="mt-6 pt-6 border-t border-slate-200">
                                        <p class="text-sm font-medium text-slate-700 mb-4">Photos ({{ $allPhotos->count() }})</p>
                                        <div class="grid grid-cols-4 sm:grid-cols-6 md:grid-cols-8 gap-3">
                                            @foreach ($allPhotos as $index => $photo)
                                                <div class="text-center">
                                                    <a href="{{ $photo->url }}"
                                                        data-lightbox="piece-{{ $piece->id }}"
                                                        data-src="{{ $photo->url }}"
                                                        data-caption="Photo {{ $index + 1 }}{{ $photo->legende ? ' - ' . $photo->legende : '' }}"
                                                        class="block cursor-pointer">
                                                        <img src="{{ $photo->url }}"
                                                            alt="Photo {{ $index + 1 }}"
                                                            class="w-full aspect-square object-cover rounded-lg hover:opacity-90 transition-opacity border border-slate-200">
                                                    </a>
                                                    <p class="text-xs text-slate-600 mt-1 font-medium">Photo {{ $index + 1 }}</p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @else
                            <div class="p-6">
                                <div class="text-center py-8 bg-slate-50 rounded-lg border border-dashed border-slate-300">
                                    <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                    </svg>
                                    <p class="text-slate-500">Aucun élément dans cette pièce</p>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="bg-white p-12 rounded-lg border border-slate-200 text-center">
                    <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                    <p class="text-slate-500 mb-2">Aucune pièce ajoutée</p>
                    <a href="{{ route('etats-des-lieux.edit', $etatDesLieux) }}"
                        class="inline-block text-primary-600 hover:text-primary-700 font-medium">
                        Ajouter des pièces
                    </a>
                </div>
            @endif
        </div>
    </div>

    {{-- Modale Partage --}}
    <div id="partage-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-slate-900/50 transition-opacity"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-lg w-full p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-slate-800">Partager l'état des lieux</h3>
                    <button type="button" id="partage-modal-close"
                        class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Formulaire partage --}}
                <form id="partage-form">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Mode de partage</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="email" checked
                                    class="text-primary-600 focus:ring-primary-500">
                                <span class="text-sm text-slate-700">Envoyer par email</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="type" value="lien"
                                    class="text-primary-600 focus:ring-primary-500">
                                <span class="text-sm text-slate-700">Générer un lien</span>
                            </label>
                        </div>
                    </div>

                    <div id="email-field" class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Email du destinataire</label>
                        <input type="email" name="email" value="{{ $etatDesLieux->locataire_email }}"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none"
                            placeholder="email@exemple.com">
                    </div>

                    <div class="mb-6">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Durée de validité</label>
                        <select name="duree"
                            class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none">
                            <option value="7">7 jours</option>
                            <option value="14">14 jours</option>
                            <option value="30" selected>30 jours</option>
                        </select>
                    </div>

                    <div id="partage-error"
                        class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600"></div>

                    <button type="submit" id="partage-submit"
                        class="w-full bg-primary-600 text-white px-4 py-2.5 rounded-lg hover:bg-primary-700 transition-colors font-medium cursor-pointer">
                        Envoyer
                    </button>
                </form>

                {{-- Résultat --}}
                <div id="partage-result" class="hidden">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <p id="partage-success-message" class="text-green-700 text-sm mb-2"></p>
                        <p class="text-xs text-green-600">Expire le <span id="partage-expire"></span></p>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Lien de partage</label>
                        <div class="flex gap-2">
                            <input type="text" id="partage-url" readonly
                                class="flex-1 px-4 py-2 border border-slate-300 rounded-lg bg-slate-50 text-sm">
                            <button type="button" id="copy-url"
                                class="bg-slate-200 text-slate-700 px-4 py-2 rounded-lg hover:bg-slate-300 transition-colors cursor-pointer text-sm">
                                Copier
                            </button>
                        </div>
                    </div>

                    <button type="button" id="partage-new"
                        class="w-full text-primary-600 hover:text-primary-700 text-sm cursor-pointer">
                        Créer un nouveau partage
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection