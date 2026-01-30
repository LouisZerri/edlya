@extends('layouts.app')

@section('title', 'Estimation réparations - ' . $edlSortie->logement->nom . ' - Edlya')

@section('content')
    <div class="mb-6">
        <a href="{{ route('etats-des-lieux.comparatif', $edlSortie) }}" class="text-sm text-slate-500 hover:text-primary-600 transition-colors">
            ← Retour au comparatif
        </a>
    </div>

    {{-- En-tête --}}
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-slate-800 mb-2">Estimation des réparations</h1>
        <p class="text-slate-500">{{ $edlSortie->logement->nom }} — {{ $edlSortie->locataire_nom }}</p>
    </div>

    <form method="POST" action="{{ route('etats-des-lieux.estimation.pdf', $edlSortie) }}" id="estimation-form">
        @csrf

        {{-- Résumé dégradations --}}
        <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-4">
                <h2 class="font-medium text-slate-800">Dégradations constatées</h2>
                @if(count($degradations) > 0)
                    <span class="text-sm text-slate-500">{{ count($degradations) }} dégradation(s)</span>
                @endif
            </div>
            
            @if(count($degradations) > 0)
                <div class="space-y-3">
                    @foreach($degradations as $index => $degradation)
                        @php
                            $hasPhotos = $degradation['element']->photos->isNotEmpty();
                            $firstPhoto = $hasPhotos ? $degradation['element']->photos->first() : null;
                        @endphp
                        <div class="degradation-item flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-4 p-4 bg-red-50 border border-red-200 rounded-lg"
                             data-element-id="{{ $degradation['element']->id }}"
                             data-element-nom="{{ $degradation['element']->nom }}"
                             data-element-type="{{ $degradation['element']->type }}"
                             data-etat-entree="{{ $degradation['entree']->etat }}"
                             data-etat-sortie="{{ $degradation['element']->etat }}"
                             data-observations="{{ $degradation['element']->observations }}"
                             data-piece="{{ $degradation['piece'] }}"
                             @if($firstPhoto) data-photo-path="{{ $firstPhoto->chemin }}" @endif>
                            <div class="hidden sm:block w-3 h-3 rounded-full bg-red-500 mt-1.5 shrink-0"></div>
                            <div class="flex-1">
                                <div class="flex items-start gap-2">
                                    <div class="sm:hidden w-3 h-3 rounded-full bg-red-500 mt-1 shrink-0"></div>
                                    <div class="flex-1">
                                        <p class="font-medium text-slate-800">
                                            {{ $degradation['piece'] }} — {{ $degradation['element']->nom }}
                                        </p>
                                        <p class="text-sm text-slate-600">
                                            {{ $degradation['entree']->etat_libelle }} → {{ $degradation['element']->etat_libelle }}
                                            <span class="text-red-600 font-medium">({{ abs($degradation['evolution']) }} niveau(x) en moins)</span>
                                        </p>
                                    </div>
                                </div>
                                @if($degradation['element']->observations)
                                    <p class="text-sm text-red-700 mt-1 italic">{{ $degradation['element']->observations }}</p>
                                @endif

                                {{-- Photos et bouton IA --}}
                                <div class="mt-3 flex flex-wrap items-center gap-2 sm:gap-3">
                                    @if($hasPhotos)
                                        <div class="flex gap-2">
                                            @foreach($degradation['element']->photos->take(3) as $photo)
                                                <img src="{{ $photo->url }}" alt="Photo" class="w-10 h-10 sm:w-12 sm:h-12 object-cover rounded border border-red-300">
                                            @endforeach
                                        </div>
                                        <button type="button"
                                                class="analyse-ia-btn text-xs bg-primary-600 text-white px-3 py-1.5 rounded-lg hover:bg-primary-700 transition-colors cursor-pointer flex items-center gap-1 min-h-[36px]">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                                            </svg>
                                            <span class="hidden sm:inline">Analyser avec IA</span>
                                            <span class="sm:hidden">IA</span>
                                        </button>
                                    @else
                                        <span class="text-xs text-slate-500 italic">Aucune photo disponible pour l'analyse IA</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-slate-500">Aucune dégradation constatée.</p>
            @endif
        </div>

        {{-- Devis --}}
        <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-6 mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
                <h2 class="font-medium text-slate-800">Devis de réparations</h2>
                <button type="button" id="add-ligne" class="text-sm text-primary-600 hover:text-primary-700 cursor-pointer flex items-center gap-1 min-h-[44px] sm:min-h-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Ajouter une ligne
                </button>
            </div>

            {{-- En-têtes tableau --}}
            <div class="hidden md:grid md:grid-cols-12 gap-3 mb-3 text-xs font-medium text-slate-500 uppercase tracking-wide px-2">
                <div class="col-span-2">Pièce</div>
                <div class="col-span-4">Description</div>
                <div class="col-span-1">Qté</div>
                <div class="col-span-1">Unité</div>
                <div class="col-span-2">Prix unit.</div>
                <div class="col-span-1">Total</div>
                <div class="col-span-1"></div>
            </div>

            {{-- Lignes du devis --}}
            <div id="lignes-container" class="space-y-3">
                {{-- Les lignes seront ajoutées ici par JS --}}
            </div>

            {{-- Totaux --}}
            <div class="mt-6 pt-6 border-t border-slate-200">
                <div class="flex flex-col items-end space-y-2">
                    <div class="flex items-center gap-4 text-sm">
                        <span class="text-slate-600">Total HT :</span>
                        <span id="total-ht" class="font-semibold text-slate-800 w-24 text-right">0,00 €</span>
                    </div>
                    <div class="flex items-center gap-4 text-sm">
                        <span class="text-slate-600">TVA (20%) :</span>
                        <span id="total-tva" class="font-semibold text-slate-800 w-24 text-right">0,00 €</span>
                    </div>
                    <div class="flex items-center gap-4 text-lg">
                        <span class="text-slate-800 font-medium">Total TTC :</span>
                        <span id="total-ttc" class="font-bold text-primary-600 w-24 text-right">0,00 €</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Réparations suggérées --}}
        <div class="bg-white rounded-lg border border-slate-200 p-4 sm:p-6 mb-6">
            <h2 class="font-medium text-slate-800 mb-4">Réparations suggérées</h2>
            <p class="text-sm text-slate-500 mb-4">Cliquez sur une réparation pour l'ajouter au devis.</p>

            <div class="space-y-4">
                @foreach($couts as $type => $items)
                    <div>
                        <p class="text-sm font-medium text-slate-700 mb-2 capitalize">{{ $type }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($items as $cout)
                                <button type="button" 
                                    class="suggestion-btn text-xs px-3 py-1.5 bg-slate-100 hover:bg-primary-100 hover:text-primary-700 rounded-full transition-colors cursor-pointer"
                                    data-nom="{{ $cout->nom }}"
                                    data-description="{{ $cout->description }}"
                                    data-unite="{{ $cout->unite }}"
                                    data-prix="{{ $cout->prix_unitaire }}"
                                    data-type="{{ $cout->type_element }}">
                                    {{ $cout->nom }} ({{ $cout->prix_format }}/{{ $cout->unite_libelle }})
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 mt-8 pb-8">
            <a href="{{ route('etats-des-lieux.comparatif', $edlSortie) }}" class="text-center bg-slate-200 text-slate-800 px-6 py-3 rounded-lg hover:bg-slate-300 transition-colors font-medium">
                Retour
            </a>
            <button type="submit" class="text-center bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 transition-colors font-medium flex items-center justify-center gap-2 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Générer le devis PDF
            </button>
        </div>
    </form>

    {{-- Modale résultat IA --}}
    <div id="ia-result-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-end sm:items-center justify-center min-h-screen px-0 sm:px-4">
            <div class="fixed inset-0 bg-slate-900/50 transition-opacity"></div>
            <div class="relative bg-white rounded-t-xl sm:rounded-lg shadow-xl w-full sm:max-w-2xl p-4 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-slate-800">Analyse IA des dégradations</h3>
                    <button type="button" id="ia-modal-close" class="text-slate-400 hover:text-slate-600 cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                {{-- Loading --}}
                <div id="ia-loading" class="py-8 text-center">
                    <div class="inline-block w-8 h-8 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin mb-4"></div>
                    <p class="text-slate-600">Analyse en cours...</p>
                    <p class="text-sm text-slate-500">L'IA examine les dégradations sur la photo.</p>
                </div>

                {{-- Résultat --}}
                <div id="ia-result" class="hidden">
                    <div id="ia-commentaire" class="bg-slate-50 p-4 rounded-lg mb-4">
                        <p class="text-sm text-slate-700"></p>
                    </div>
                    <div id="ia-degradations" class="space-y-3 mb-4 max-h-80 overflow-y-auto">
                        {{-- Dégradations générées par JS --}}
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" id="ia-cancel" class="px-4 py-2 text-slate-600 hover:text-slate-800 cursor-pointer">
                            Annuler
                        </button>
                        <button type="button" id="ia-apply" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 cursor-pointer">
                            Ajouter au devis
                        </button>
                    </div>
                </div>

                {{-- Erreur --}}
                <div id="ia-error" class="hidden py-8 text-center">
                    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <p class="text-slate-600">Une erreur est survenue lors de l'analyse.</p>
                    <p id="ia-error-message" class="text-sm text-red-600 mt-2"></p>
                </div>
            </div>
        </div>
    </div>

    {{-- Template ligne (caché) --}}
    <template id="ligne-template">
        <div class="ligne-devis bg-slate-50 rounded-lg p-4 border border-slate-200">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
                <div class="md:col-span-2">
                    <label class="md:hidden text-xs text-slate-500 mb-1 block">Pièce</label>
                    <select name="lignes[INDEX][piece]" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
                        <option value="">-- Pièce --</option>
                        @foreach($edlSortie->pieces as $piece)
                            <option value="{{ $piece->nom }}">{{ $piece->nom }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-4">
                    <label class="md:hidden text-xs text-slate-500 mb-1 block">Description</label>
                    <input type="text" name="lignes[INDEX][description]" placeholder="Description de la réparation" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none">
                </div>
                <div class="md:col-span-1">
                    <label class="md:hidden text-xs text-slate-500 mb-1 block">Quantité</label>
                    <input type="number" name="lignes[INDEX][quantite]" placeholder="Qté" step="0.01" min="0" class="ligne-quantite w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none">
                </div>
                <div class="md:col-span-1">
                    <label class="md:hidden text-xs text-slate-500 mb-1 block">Unité</label>
                    <select name="lignes[INDEX][unite]" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none bg-white">
                        <option value="unite">unité</option>
                        <option value="m2">m²</option>
                        <option value="ml">ml</option>
                        <option value="forfait">forfait</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="md:hidden text-xs text-slate-500 mb-1 block">Prix unitaire</label>
                    <div class="relative">
                        <input type="number" name="lignes[INDEX][prix_unitaire]" placeholder="Prix" step="0.01" min="0" class="ligne-prix w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none pr-8">
                        <span class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">€</span>
                    </div>
                </div>
                <div class="md:col-span-1">
                    <label class="md:hidden text-xs text-slate-500 mb-1 block">Total</label>
                    <p class="ligne-total font-semibold text-slate-800 py-2 text-right">0,00 €</p>
                </div>
                <div class="md:col-span-1 flex justify-end">
                    <button type="button" class="remove-ligne text-red-500 hover:text-red-700 p-2 cursor-pointer" title="Supprimer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </template>
@endsection