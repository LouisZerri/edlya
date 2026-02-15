@extends('layouts.app')

@section('title', 'Comparatif - ' . $edlSortie->logement->nom . ' - Edlya')

@section('content')
    <div class="mb-6">
        <a href="{{ route('etats-des-lieux.show', $edlSortie) }}"
            class="text-sm text-slate-500 hover:text-primary-600 transition-colors">
            ← Retour à l'état des lieux
        </a>
    </div>

    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-semibold text-slate-800 mb-2">Comparatif entrée / sortie</h1>
            <p class="text-slate-500">{{ $edlSortie->logement->adresse_complete }}</p>
        </div>

        <div class="flex flex-col sm:flex-row gap-2 sm:gap-3">
            <a href="{{ route('etats-des-lieux.comparatif.pdf', $edlSortie) }}"
                class="inline-flex items-center justify-center gap-2 bg-slate-800 text-white px-4 py-2 rounded-lg hover:bg-slate-900 transition-colors text-sm font-medium min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Télécharger PDF
            </a>
            <a href="{{ route('etats-des-lieux.estimation', $edlSortie) }}"
                class="inline-flex items-center justify-center gap-2 bg-amber-500 text-white px-4 py-2 rounded-lg hover:bg-amber-600 transition-colors text-sm font-medium min-h-[44px]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                Estimation des coûts
            </a>
        </div>
    </div>

    {{-- Infos EDL --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-blue-800">État des lieux d'entrée</h2>
                    <p class="text-sm text-blue-600">{{ $edlEntree->date_realisation->format('d/m/Y') }}</p>
                </div>
            </div>
            <p class="text-sm text-blue-700">Locataire : {{ $edlEntree->locataire_nom }}</p>
        </div>

        <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-orange-800">État des lieux de sortie</h2>
                    <p class="text-sm text-orange-600">{{ $edlSortie->date_realisation->format('d/m/Y') }}</p>
                </div>
            </div>
            <p class="text-sm text-orange-700">Locataire : {{ $edlSortie->locataire_nom }}</p>
        </div>
    </div>

    {{-- Comparatif Compteurs --}}
    @if ($edlEntree->compteurs->isNotEmpty() || $edlSortie->compteurs->isNotEmpty())
        <div class="bg-white rounded-lg border border-slate-200 mb-8">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Comparatif des compteurs
                </h2>
            </div>
            <div class="p-4 sm:p-6">
                @php
                    $typesCompteurs = [
                        'electricite' => '⚡ Électricité',
                        'eau_froide' => '💧 Eau froide',
                        'eau_chaude' => '🔥 Eau chaude',
                        'gaz' => '🔵 Gaz',
                    ];
                    $unites = [
                        'electricite' => 'kWh',
                        'eau_froide' => 'm³',
                        'eau_chaude' => 'm³',
                        'gaz' => 'm³',
                    ];
                @endphp

                {{-- Vue mobile : Cards --}}
                <div class="md:hidden space-y-3">
                    @foreach ($typesCompteurs as $type => $label)
                        @php
                            $compteurEntree = $edlEntree->compteurs->where('type', $type)->first();
                            $compteurSortie = $edlSortie->compteurs->where('type', $type)->first();
                            $indexEntree = $compteurEntree?->index_numerique;
                            $indexSortie = $compteurSortie?->index_numerique;
                            $consommation = $indexEntree !== null && $indexSortie !== null ? $indexSortie - $indexEntree : null;
                        @endphp
                        @if ($compteurEntree || $compteurSortie)
                            <div class="bg-slate-50 rounded-lg p-4">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="font-medium text-slate-800">{{ $label }}</span>
                                    @if ($consommation !== null)
                                        <span class="font-bold text-sm {{ $consommation >= 0 ? 'text-slate-800' : 'text-red-600' }}">
                                            {{ number_format($consommation, 0, ',', ' ') }} {{ $unites[$type] }}
                                        </span>
                                    @endif
                                </div>
                                <div class="grid grid-cols-2 gap-3 text-sm">
                                    <div class="bg-blue-50 rounded p-2 text-center">
                                        <p class="text-xs text-blue-600 mb-1">Entrée</p>
                                        <p class="font-semibold">{{ $compteurEntree?->index ?? '-' }}</p>
                                    </div>
                                    <div class="bg-orange-50 rounded p-2 text-center">
                                        <p class="text-xs text-orange-600 mb-1">Sortie</p>
                                        <p class="font-semibold">{{ $compteurSortie?->index ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                {{-- Vue desktop : Table --}}
                <div class="hidden md:block">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="text-left py-3 px-4 font-medium text-slate-600">Type</th>
                                <th class="text-left py-3 px-4 font-medium text-slate-600">N° Compteur</th>
                                <th class="text-center py-3 px-4 font-medium text-blue-600 bg-blue-50">Index entrée</th>
                                <th class="text-center py-3 px-4 font-medium text-orange-600 bg-orange-50">Index sortie</th>
                                <th class="text-center py-3 px-4 font-medium text-slate-600">Consommation</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($typesCompteurs as $type => $label)
                                @php
                                    $compteurEntree = $edlEntree->compteurs->where('type', $type)->first();
                                    $compteurSortie = $edlSortie->compteurs->where('type', $type)->first();
                                    $indexEntree = $compteurEntree?->index_numerique;
                                    $indexSortie = $compteurSortie?->index_numerique;
                                    $consommation = $indexEntree !== null && $indexSortie !== null ? $indexSortie - $indexEntree : null;
                                @endphp
                                @if ($compteurEntree || $compteurSortie)
                                    <tr class="border-b border-slate-100">
                                        <td class="py-3 px-4 font-medium">{{ $label }}</td>
                                        <td class="py-3 px-4 text-slate-500 text-xs">{{ $compteurEntree?->numero ?? ($compteurSortie?->numero ?? '-') }}</td>
                                        <td class="py-3 px-4 text-center bg-blue-50 font-semibold">{{ $compteurEntree?->index ?? '-' }}</td>
                                        <td class="py-3 px-4 text-center bg-orange-50 font-semibold">{{ $compteurSortie?->index ?? '-' }}</td>
                                        <td class="py-3 px-4 text-center">
                                            @if ($consommation !== null)
                                                <span class="font-bold {{ $consommation >= 0 ? 'text-slate-800' : 'text-red-600' }}">
                                                    {{ number_format($consommation, 0, ',', ' ') }} {{ $unites[$type] }}
                                                </span>
                                            @else
                                                <span class="text-slate-400">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- Comparatif Clés --}}
    @if ($edlEntree->cles->isNotEmpty() || $edlSortie->cles->isNotEmpty())
        <div class="bg-white rounded-lg border border-slate-200 mb-8">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="font-semibold text-slate-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                    </svg>
                    Comparatif des clés
                </h2>
            </div>
            <div class="p-4 sm:p-6">
                @php
                    $tousTypesClés = $edlEntree->cles
                        ->pluck('type')
                        ->merge($edlSortie->cles->pluck('type'))
                        ->unique();
                    $totalEntree = $edlEntree->cles->sum('nombre');
                    $totalSortie = $edlSortie->cles->sum('nombre');
                    $differenceTotal = $totalSortie - $totalEntree;
                @endphp

                {{-- Vue mobile : Cards --}}
                <div class="md:hidden space-y-3">
                    @foreach ($tousTypesClés as $type)
                        @php
                            $cleEntree = $edlEntree->cles->where('type', $type)->first();
                            $cleSortie = $edlSortie->cles->where('type', $type)->first();
                            $nbEntree = $cleEntree?->nombre ?? 0;
                            $nbSortie = $cleSortie?->nombre ?? 0;
                            $difference = $nbSortie - $nbEntree;
                        @endphp
                        <div class="rounded-lg p-4 {{ $difference < 0 ? 'bg-red-50 border border-red-200' : 'bg-slate-50' }}">
                            <div class="flex items-center justify-between mb-3">
                                <span class="font-medium text-slate-800">🔑 {{ $type }}</span>
                                @if ($difference < 0)
                                    <span class="inline-flex items-center gap-1 text-red-600 font-bold text-sm">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                        {{ $difference }}
                                    </span>
                                @elseif($difference > 0)
                                    <span class="text-green-600 font-bold text-sm">+{{ $difference }}</span>
                                @else
                                    <span class="text-slate-400 text-sm">=</span>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="bg-blue-50 rounded p-3 text-center">
                                    <p class="text-xs text-blue-600 mb-2">Entrée</p>
                                    @if ($cleEntree?->photo)
                                        <a href="{{ $cleEntree->photo_url }}" target="_blank">
                                            <img src="{{ $cleEntree->photo_url }}" alt="Photo" class="w-10 h-10 object-cover rounded mx-auto border border-slate-200 hover:opacity-80 mb-1">
                                        </a>
                                    @endif
                                    <p class="font-semibold">{{ $nbEntree > 0 ? $nbEntree : '-' }}</p>
                                </div>
                                <div class="bg-orange-50 rounded p-3 text-center">
                                    <p class="text-xs text-orange-600 mb-2">Sortie</p>
                                    @if ($cleSortie?->photo)
                                        <a href="{{ $cleSortie->photo_url }}" target="_blank">
                                            <img src="{{ $cleSortie->photo_url }}" alt="Photo" class="w-10 h-10 object-cover rounded mx-auto border border-slate-200 hover:opacity-80 mb-1">
                                        </a>
                                    @endif
                                    <p class="font-semibold">{{ $nbSortie > 0 ? $nbSortie : '-' }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                    {{-- Total mobile --}}
                    <div class="bg-slate-100 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <span class="font-semibold text-slate-800">Total</span>
                            <div class="flex items-center gap-4 text-sm">
                                <span class="text-blue-600">Entrée: <strong>{{ $totalEntree }}</strong></span>
                                <span class="text-orange-600">Sortie: <strong>{{ $totalSortie }}</strong></span>
                            </div>
                        </div>
                        @if ($differenceTotal < 0)
                            <p class="text-red-600 font-bold text-sm mt-2">{{ $differenceTotal }} manquante(s)</p>
                        @elseif($differenceTotal > 0)
                            <p class="text-green-600 text-sm mt-2">+{{ $differenceTotal }}</p>
                        @else
                            <p class="text-green-600 text-sm mt-2">✓ Complet</p>
                        @endif
                    </div>
                </div>

                {{-- Vue desktop : Table --}}
                <div class="hidden md:block">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="text-left py-3 px-4 font-medium text-slate-600">Type de clé</th>
                                <th class="text-center py-3 px-4 font-medium text-blue-600 bg-blue-50">Photo entrée</th>
                                <th class="text-center py-3 px-4 font-medium text-blue-600 bg-blue-50">Qté entrée</th>
                                <th class="text-center py-3 px-4 font-medium text-orange-600 bg-orange-50">Photo sortie</th>
                                <th class="text-center py-3 px-4 font-medium text-orange-600 bg-orange-50">Qté sortie</th>
                                <th class="text-center py-3 px-4 font-medium text-slate-600">Différence</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($tousTypesClés as $type)
                                @php
                                    $cleEntree = $edlEntree->cles->where('type', $type)->first();
                                    $cleSortie = $edlSortie->cles->where('type', $type)->first();
                                    $nbEntree = $cleEntree?->nombre ?? 0;
                                    $nbSortie = $cleSortie?->nombre ?? 0;
                                    $difference = $nbSortie - $nbEntree;
                                @endphp
                                <tr class="border-b border-slate-100 {{ $difference < 0 ? 'bg-red-50' : '' }}">
                                    <td class="py-3 px-4 font-medium">🔑 {{ $type }}</td>
                                    <td class="py-3 px-4 text-center bg-blue-50">
                                        @if ($cleEntree?->photo)
                                            <a href="{{ $cleEntree->photo_url }}" target="_blank">
                                                <img src="{{ $cleEntree->photo_url }}" alt="Photo" class="w-10 h-10 object-cover rounded mx-auto border border-slate-200 hover:opacity-80">
                                            </a>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center bg-blue-50 font-semibold">{{ $nbEntree > 0 ? $nbEntree : '-' }}</td>
                                    <td class="py-3 px-4 text-center bg-orange-50">
                                        @if ($cleSortie?->photo)
                                            <a href="{{ $cleSortie->photo_url }}" target="_blank">
                                                <img src="{{ $cleSortie->photo_url }}" alt="Photo" class="w-10 h-10 object-cover rounded mx-auto border border-slate-200 hover:opacity-80">
                                            </a>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-4 text-center bg-orange-50 font-semibold">{{ $nbSortie > 0 ? $nbSortie : '-' }}</td>
                                    <td class="py-3 px-4 text-center">
                                        @if ($difference < 0)
                                            <span class="inline-flex items-center gap-1 text-red-600 font-bold">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                {{ $difference }}
                                            </span>
                                        @elseif($difference > 0)
                                            <span class="text-green-600 font-bold">+{{ $difference }}</span>
                                        @else
                                            <span class="text-slate-400">=</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 font-semibold">
                                <td class="py-3 px-4">Total</td>
                                <td class="py-3 px-4 bg-blue-100"></td>
                                <td class="py-3 px-4 text-center bg-blue-100">{{ $totalEntree }}</td>
                                <td class="py-3 px-4 bg-orange-100"></td>
                                <td class="py-3 px-4 text-center bg-orange-100">{{ $totalSortie }}</td>
                                <td class="py-3 px-4 text-center">
                                    @if ($differenceTotal < 0)
                                        <span class="text-red-600 font-bold">{{ $differenceTotal }} manquante(s)</span>
                                    @elseif($differenceTotal > 0)
                                        <span class="text-green-600">+{{ $differenceTotal }}</span>
                                    @else
                                        <span class="text-green-600">✓ Complet</span>
                                    @endif
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                @if ($differenceTotal < 0)
                    <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                        <p class="text-sm text-red-700 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            <strong>Attention :</strong> {{ abs($differenceTotal) }} clé(s) non restituée(s). Le coût de
                            remplacement pourra être facturé au locataire.
                        </p>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- Résumé des dégradations --}}
    @if ($stats['degrade'] > 0)
        <div class="bg-red-50 border border-red-200 rounded-lg p-6 mb-8">
            <h2 class="font-semibold text-red-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                {{ $stats['degrade'] }} dégradation(s) constatée(s)
            </h2>
            <div class="space-y-3">
                @foreach ($comparatif as $pieceData)
                    @foreach ($pieceData['elements'] as $element)
                        @if ($element['status'] === 'degrade')
                            <div class="bg-white rounded-lg px-4 py-3">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                                    <div>
                                        <span class="font-medium text-slate-800">{{ $element['sortie']->nom }}</span>
                                        <span class="text-slate-500 text-sm">dans {{ $pieceData['nom'] }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 sm:gap-3 text-sm">
                                        <span class="px-2 py-1 rounded-full text-xs {{ $element['entree']->etat_couleur }}">{{ $element['entree']->etat_libelle }}</span>
                                        <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                        <span class="px-2 py-1 rounded-full text-xs {{ $element['sortie']->etat_couleur }}">{{ $element['sortie']->etat_libelle }}</span>
                                    </div>
                                </div>
                                
                                {{-- Comparatif dégradations entrée vs sortie --}}
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                                    <div class="bg-blue-50 rounded-lg p-3">
                                        <p class="text-xs text-blue-600 font-medium mb-2">Dégradations à l'entrée</p>
                                        @if($element['entree']->hasDegradations())
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($element['entree']->degradations as $degradation)
                                                    <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">
                                                        {{ $degradation }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-xs">Aucune</span>
                                        @endif
                                    </div>
                                    <div class="bg-orange-50 rounded-lg p-3">
                                        <p class="text-xs text-orange-600 font-medium mb-2">Dégradations à la sortie</p>
                                        @if($element['sortie']->hasDegradations())
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($element['sortie']->degradations as $degradation)
                                                    @php
                                                        $isNew = !$element['entree']->hasDegradations() || !in_array($degradation, $element['entree']->degradations ?? []);
                                                    @endphp
                                                    <span class="inline-block px-2 py-0.5 text-xs rounded-full {{ $isNew ? 'bg-red-100 text-red-700 font-semibold' : 'bg-orange-100 text-orange-700' }}">
                                                        {{ $degradation }}{{ $isNew ? ' ●' : '' }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-400 text-xs">Aucune</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                @endforeach
            </div>
            <p class="text-xs text-red-600 mt-4">● = Nouvelle dégradation (imputable au locataire)</p>
        </div>
    @else
        <div class="bg-green-50 border border-green-200 rounded-lg p-6 mb-8">
            <h2 class="font-semibold text-green-800 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Aucune dégradation constatée
            </h2>
            <p class="text-green-700 mt-2">Le logement est rendu dans un état conforme à l'état des lieux d'entrée.</p>
        </div>
    @endif

    {{-- Détail par pièce --}}
    <h2 class="text-lg font-semibold text-slate-800 mb-6">Détail par pièce</h2>

    @foreach ($comparatif as $pieceData)
        <div class="bg-white rounded-lg border {{ $pieceData['has_degradation'] ? 'border-red-200' : 'border-slate-200' }} mb-6">
            <div class="px-6 py-4 border-b {{ $pieceData['has_degradation'] ? 'border-red-200 bg-red-50' : 'border-slate-200 bg-slate-50' }}">
                <h3 class="font-semibold {{ $pieceData['has_degradation'] ? 'text-red-800' : 'text-slate-800' }}">
                    {{ $pieceData['nom'] }}
                    @if ($pieceData['has_degradation'])
                        <span class="ml-2 text-sm font-normal text-red-600">⚠️ Dégradation(s)</span>
                    @endif
                </h3>
            </div>

            <div class="p-4 sm:p-6">
                {{-- Vue mobile : Cards --}}
                <div class="md:hidden space-y-3">
                    @foreach ($pieceData['elements'] as $element)
                        <div class="rounded-lg p-4 {{ $element['status'] === 'degrade' ? 'bg-red-50 border border-red-200' : 'bg-slate-50' }}">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <span class="font-medium text-slate-800">{{ $element['sortie']->nom }}</span>
                                @if ($element['status'] === 'degrade')
                                    <span class="inline-flex items-center gap-1 text-red-600 font-medium text-xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                        </svg>
                                        Dégradé
                                    </span>
                                @elseif($element['status'] === 'ameliore')
                                    <span class="inline-flex items-center gap-1 text-green-600 font-medium text-xs">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                        </svg>
                                        Amélioré
                                    </span>
                                @elseif($element['status'] === 'nouveau')
                                    <span class="text-blue-600 font-medium text-xs">Nouveau</span>
                                @endif
                            </div>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="bg-blue-50 rounded p-3">
                                    <p class="text-xs text-blue-600 mb-2">Entrée</p>
                                    @if ($element['entree'])
                                        <span class="px-2 py-1 text-xs rounded-full {{ $element['entree']->etat_couleur }}">
                                            {{ $element['entree']->etat_libelle }}
                                        </span>
                                        @if ($element['entree']->hasDegradations())
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                @foreach($element['entree']->degradations as $degradation)
                                                    <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">{{ $degradation }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    @else
                                        <span class="text-slate-400">-</span>
                                    @endif
                                </div>
                                <div class="bg-orange-50 rounded p-3">
                                    <p class="text-xs text-orange-600 mb-2">Sortie</p>
                                    <span class="px-2 py-1 text-xs rounded-full {{ $element['sortie']->etat_couleur }}">
                                        {{ $element['sortie']->etat_libelle }}
                                    </span>
                                    @if($element['sortie']->hasDegradations())
                                        <div class="flex flex-wrap gap-1 mt-2">
                                            @foreach($element['sortie']->degradations as $degradation)
                                                @php
                                                    $isNew = !$element['entree'] || !$element['entree']->hasDegradations() || !in_array($degradation, $element['entree']->degradations ?? []);
                                                @endphp
                                                <span class="inline-block px-2 py-0.5 text-xs rounded-full {{ $isNew ? 'bg-red-100 text-red-700 font-semibold' : 'bg-orange-100 text-orange-700' }}">
                                                    {{ $degradation }}{{ $isNew ? ' ●' : '' }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Vue desktop : Table --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200">
                                <th class="text-left py-3 px-3 font-medium text-slate-600">Élément</th>
                                <th class="text-center py-3 px-3 font-medium text-blue-600 bg-blue-50">État entrée</th>
                                <th class="text-left py-3 px-3 font-medium text-blue-600 bg-blue-50">Dégradations entrée</th>
                                <th class="text-center py-3 px-3 font-medium text-orange-600 bg-orange-50">État sortie</th>
                                <th class="text-left py-3 px-3 font-medium text-orange-600 bg-orange-50">Dégradations sortie</th>
                                <th class="text-center py-3 px-3 font-medium text-slate-600">Évolution</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($pieceData['elements'] as $element)
                                <tr class="border-b border-slate-100 {{ $element['status'] === 'degrade' ? 'bg-red-50' : '' }}">
                                    <td class="py-3 px-3 font-medium text-slate-800">{{ $element['sortie']->nom }}</td>
                                    <td class="py-3 px-3 text-center bg-blue-50">
                                        @if ($element['entree'])
                                            <span class="px-2 py-1 text-xs rounded-full {{ $element['entree']->etat_couleur }}">
                                                {{ $element['entree']->etat_libelle }}
                                            </span>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 bg-blue-50">
                                        @if ($element['entree'] && $element['entree']->hasDegradations())
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($element['entree']->degradations as $degradation)
                                                    <span class="inline-block px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">{{ $degradation }}</span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-center bg-orange-50">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $element['sortie']->etat_couleur }}">
                                            {{ $element['sortie']->etat_libelle }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 bg-orange-50">
                                        @if($element['sortie']->hasDegradations())
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($element['sortie']->degradations as $degradation)
                                                    @php
                                                        $isNew = !$element['entree'] || !$element['entree']->hasDegradations() || !in_array($degradation, $element['entree']->degradations ?? []);
                                                    @endphp
                                                    <span class="inline-block px-2 py-0.5 text-xs rounded-full {{ $isNew ? 'bg-red-100 text-red-700 font-semibold' : 'bg-orange-100 text-orange-700' }}">
                                                        {{ $degradation }}{{ $isNew ? ' ●' : '' }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="text-slate-400">-</span>
                                        @endif
                                    </td>
                                    <td class="py-3 px-3 text-center">
                                        @if ($element['status'] === 'degrade')
                                            <span class="inline-flex items-center gap-1 text-red-600 font-medium">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                                </svg>
                                                Dégradé
                                            </span>
                                        @elseif($element['status'] === 'ameliore')
                                            <span class="inline-flex items-center gap-1 text-green-600 font-medium">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18" />
                                                </svg>
                                                Amélioré
                                            </span>
                                        @elseif($element['status'] === 'nouveau')
                                            <span class="text-blue-600 font-medium">Nouveau</span>
                                        @else
                                            <span class="text-slate-400">=</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach
    
    <p class="text-xs text-slate-500 mb-8">● = Nouvelle dégradation (imputable au locataire)</p>
@endsection