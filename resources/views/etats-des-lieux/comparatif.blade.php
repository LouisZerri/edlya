@extends('layouts.app')

@section('title', 'Comparatif - ' . $edlSortie->logement->nom . ' - Edlya')

@section('content')
    <div class="mb-6">
        <a href="{{ route('etats-des-lieux.show', $edlSortie) }}"
            class="text-sm text-slate-500 hover:text-primary-600 transition-colors">
            ← Retour à l'état des lieux de sortie
        </a>
    </div>

    {{-- En-tête --}}
    <div class="mb-8">
        <h1 class="text-2xl font-semibold text-slate-800 mb-2">Comparatif entrée / sortie</h1>
        <p class="text-slate-500">{{ $edlSortie->logement->nom }} — {{ $edlSortie->logement->adresse_complete }}</p>
    </div>

    {{-- Résumé --}}
    <div class="bg-white rounded-lg border border-slate-200 p-6 mb-8">
        <h2 class="font-medium text-slate-800 mb-4">Résumé du comparatif</h2>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-slate-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-slate-800">{{ $stats['total'] }}</p>
                <p class="text-sm text-slate-500">Éléments comparés</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-green-600">{{ $stats['identique'] + $stats['ameliore'] }}</p>
                <p class="text-sm text-green-700">Sans dégradation</p>
            </div>
            <div class="bg-red-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-red-600">{{ $stats['degrade'] }}</p>
                <p class="text-sm text-red-700">Dégradations</p>
            </div>
            <div class="bg-blue-50 rounded-lg p-4 text-center">
                <p class="text-3xl font-bold text-blue-600">{{ $stats['nouveau'] }}</p>
                <p class="text-sm text-blue-700">Nouveaux éléments</p>
            </div>
        </div>

        {{-- Infos EDL --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <p class="font-medium text-blue-800 mb-2">État des lieux d'entrée</p>
                <p class="text-blue-700">{{ $edlEntree->date_realisation->format('d/m/Y') }}</p>
                <p class="text-blue-600">Locataire : {{ $edlEntree->locataire_nom }}</p>
            </div>
            <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                <p class="font-medium text-orange-800 mb-2">État des lieux de sortie</p>
                <p class="text-orange-700">{{ $edlSortie->date_realisation->format('d/m/Y') }}</p>
                <p class="text-orange-600">Locataire : {{ $edlSortie->locataire_nom }}</p>
            </div>
        </div>
    </div>

    {{-- Filtres rapides --}}
    <div class="bg-white rounded-lg border border-slate-200 p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <span class="text-sm text-slate-600 font-medium">Filtrer :</span>
            <button type="button" data-filter="all"
                class="filter-btn active px-4 py-2 rounded-full text-sm border border-slate-300 bg-slate-800 text-white transition-colors cursor-pointer">
                Tout ({{ $stats['total'] }})
            </button>
            <button type="button" data-filter="degrade"
                class="filter-btn px-4 py-2 rounded-full text-sm border border-red-300 text-red-700 hover:bg-red-50 transition-colors cursor-pointer">
                Dégradations ({{ $stats['degrade'] }})
            </button>
            <button type="button" data-filter="identique"
                class="filter-btn px-4 py-2 rounded-full text-sm border border-green-300 text-green-700 hover:bg-green-50 transition-colors cursor-pointer">
                Sans changement ({{ $stats['identique'] + $stats['ameliore'] }})
            </button>
        </div>
    </div>

    {{-- Légende --}}
    <div class="bg-slate-50 rounded-lg p-4 mb-6 flex flex-wrap gap-6 text-sm">
        <span class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-green-500"></span>
            Identique / Amélioré
        </span>
        <span class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-amber-500"></span>
            Légèrement dégradé (-1 niveau)
        </span>
        <span class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-red-500"></span>
            Dégradé (-2 niveaux ou plus)
        </span>
        <span class="flex items-center gap-2">
            <span class="w-3 h-3 rounded-full bg-blue-500"></span>
            Nouvel élément
        </span>
    </div>

    {{-- Comparatif par pièce --}}
    @foreach ($comparatif as $piece)
        <div
            class="bg-white rounded-lg border border-slate-200 mb-6 piece-block {{ $piece['has_degradation'] ? 'ring-2 ring-red-200' : '' }}">
            {{-- En-tête pièce --}}
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-lg flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-slate-800 text-lg">{{ $piece['nom'] }}</h3>
                    <p class="text-sm text-slate-500">{{ count($piece['elements']) }} élément(s)</p>
                </div>
                @if ($piece['has_degradation'])
                    <span class="px-4 py-1.5 bg-red-100 text-red-700 text-sm font-medium rounded-full">
                        Dégradations constatées
                    </span>
                @else
                    <span class="px-4 py-1.5 bg-green-100 text-green-700 text-sm font-medium rounded-full">
                        RAS
                    </span>
                @endif
            </div>

            {{-- Éléments --}}
            <div class="p-6">
                <div class="space-y-6">
                    @foreach ($piece['elements'] as $element)
                        @php
                            $isDegrade = $element['status'] === 'degrade';
                            $statusColors = [
                                'identique' => 'border-green-200 bg-green-50/50',
                                'ameliore' => 'border-green-200 bg-green-50/50',
                                'degrade' => 'border-red-300 bg-red-50',
                                'nouveau' => 'border-blue-200 bg-blue-50/50',
                            ];
                            $dotColors = [
                                'identique' => 'bg-green-500',
                                'ameliore' => 'bg-green-500',
                                'degrade' => $element['evolution'] <= -2 ? 'bg-red-500' : 'bg-amber-500',
                                'nouveau' => 'bg-blue-500',
                            ];
                            $filterClass = $isDegrade ? 'degrade' : 'identique';
                        @endphp

                        <div class="element-item border-2 rounded-xl p-5 {{ $statusColors[$element['status']] }}"
                            data-status="{{ $filterClass }}">
                            {{-- En-tête élément --}}
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <span
                                        class="w-4 h-4 rounded-full {{ $dotColors[$element['status']] }} shrink-0"></span>
                                    <div>
                                        <h4 class="font-semibold text-slate-800">{{ $element['sortie']->nom }}</h4>
                                        <p class="text-sm text-slate-500">{{ $element['sortie']->type }}</p>
                                    </div>
                                </div>
                                @if ($element['status'] === 'degrade')
                                    <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-medium rounded-full">
                                        {{ abs($element['evolution']) }} niveau(x) en moins
                                    </span>
                                @elseif($element['status'] === 'ameliore')
                                    <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                                        +{{ $element['evolution'] }} niveau(x)
                                    </span>
                                @elseif($element['status'] === 'nouveau')
                                    <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                                        Nouvel élément
                                    </span>
                                @endif
                            </div>

                            {{-- Comparaison des états (côte à côte) --}}
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                {{-- ENTRÉE --}}
                                <div class="bg-white rounded-lg p-4 border border-slate-200">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                        <p class="text-sm text-slate-600 font-semibold uppercase tracking-wide">Entrée</p>
                                        <span
                                            class="text-xs text-slate-400">{{ $edlEntree->date_realisation->format('d/m/Y') }}</span>
                                    </div>
                                    @if ($element['entree'])
                                        <div class="mb-3">
                                            <span
                                                class="inline-block px-3 py-1.5 text-sm rounded-full {{ $element['entree']->etat_couleur }}">
                                                {{ $element['entree']->etat_libelle }}
                                            </span>
                                        </div>
                                        @if ($element['entree']->observations)
                                            <p class="text-sm text-slate-600 bg-slate-50 p-3 rounded-lg italic">
                                                {{ $element['entree']->observations }}</p>
                                        @else
                                            <p class="text-sm text-slate-400">Aucune observation</p>
                                        @endif

                                        {{-- Photos entrée --}}
                                        @if ($element['entree']->photos->isNotEmpty())
                                            <div class="mt-4">
                                                <p class="text-xs text-slate-500 mb-2">
                                                    {{ $element['entree']->photos->count() }} photo(s)</p>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach ($element['entree']->photos as $photo)
                                                        <a href="{{ $photo->url }}"
                                                            data-lightbox="entree-{{ $element['sortie']->id }}"
                                                            data-src="{{ $photo->url }}"
                                                            data-caption="Entrée - {{ $element['sortie']->nom }}"
                                                            class="block">
                                                            <img src="{{ $photo->url }}"
                                                                class="w-20 h-20 object-cover rounded-lg border-2 border-blue-200 hover:border-blue-400 transition-colors"
                                                                alt="Photo entrée">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @else
                                        <p class="text-sm text-slate-400 italic bg-slate-50 p-3 rounded-lg">Élément non
                                            présent à l'entrée</p>
                                    @endif
                                </div>

                                {{-- SORTIE --}}
                                <div
                                    class="bg-white rounded-lg p-4 border border-slate-200 {{ $isDegrade ? 'border-red-300' : '' }}">
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="w-2 h-2 rounded-full bg-orange-500"></span>
                                        <p class="text-sm text-slate-600 font-semibold uppercase tracking-wide">Sortie</p>
                                        <span
                                            class="text-xs text-slate-400">{{ $edlSortie->date_realisation->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="mb-3 flex items-center gap-2">
                                        <span
                                            class="inline-block px-3 py-1.5 text-sm rounded-full {{ $element['sortie']->etat_couleur }}">
                                            {{ $element['sortie']->etat_libelle }}
                                        </span>
                                        @if ($element['status'] === 'degrade')
                                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                            </svg>
                                        @endif
                                    </div>
                                    @if ($element['sortie']->observations)
                                        <p
                                            class="text-sm text-slate-600 bg-slate-50 p-3 rounded-lg italic {{ $isDegrade ? 'bg-red-50 text-red-800' : '' }}">
                                            {{ $element['sortie']->observations }}
                                        </p>
                                    @else
                                        <p class="text-sm text-slate-400">Aucune observation</p>
                                    @endif

                                    {{-- Photos sortie --}}
                                    @if ($element['sortie']->photos->isNotEmpty())
                                        <div class="mt-4">
                                            <p class="text-xs text-slate-500 mb-2">
                                                {{ $element['sortie']->photos->count() }} photo(s)</p>
                                            <div class="flex flex-wrap gap-2">
                                                @foreach ($element['sortie']->photos as $photo)
                                                    <a href="{{ $photo->url }}"
                                                        data-lightbox="sortie-{{ $element['sortie']->id }}"
                                                        data-src="{{ $photo->url }}"
                                                        data-caption="Sortie - {{ $element['sortie']->nom }}"
                                                        class="block">
                                                        <img src="{{ $photo->url }}"
                                                            class="w-20 h-20 object-cover rounded-lg border-2 border-orange-200 hover:border-orange-400 transition-colors"
                                                            alt="Photo sortie">
                                                    </a>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endforeach

    {{-- Actions --}}
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 mt-8 pb-8">
        <a href="{{ route('etats-des-lieux.show', $edlSortie) }}"
            class="text-center bg-slate-200 text-slate-800 px-6 py-3 rounded-lg hover:bg-slate-300 transition-colors font-medium">
            Retour
        </a>
        @if ($stats['degrade'] > 0)
            <a href="{{ route('etats-des-lieux.estimation', $edlSortie) }}"
                class="text-center bg-amber-500 text-white px-6 py-3 rounded-lg hover:bg-amber-600 transition-colors font-medium flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Estimer les réparations
            </a>
        @endif
        <a href="{{ route('etats-des-lieux.comparatif.pdf', $edlSortie) }}"
            class="text-center bg-slate-800 text-white px-6 py-3 rounded-lg hover:bg-slate-900 transition-colors font-medium flex items-center justify-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Télécharger le PDF comparatif
        </a>
    </div>
@endsection
