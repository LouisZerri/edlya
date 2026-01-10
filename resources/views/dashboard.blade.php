@extends('layouts.app')

@section('title', 'Tableau de bord - Edlya')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-slate-800">Tableau de bord</h1>
       <div class="flex gap-3">
            <a href="{{ route('etats-des-lieux.import') }}" class="inline-flex items-center px-4 py-2 border border-primary-600 text-primary-600 rounded-lg hover:bg-primary-50 transition-colors">
                Importer PDF
            </a>
            <a href="{{ route('etats-des-lieux.create') }}" class="inline-flex items-center bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors">
                Nouvel état des lieux
            </a>
        </div>
    </div>

    {{-- Stats principales --}}
    <div class="grid gap-6 md:grid-cols-4 mb-8">
        <div class="bg-white p-6 rounded-lg border border-slate-200">
            <h2 class="text-sm font-medium text-slate-500">Logements</h2>
            <p class="text-3xl font-semibold text-slate-800 mt-2">{{ $stats['logements'] }}</p>
        </div>

        <div class="bg-white p-6 rounded-lg border border-slate-200">
            <h2 class="text-sm font-medium text-slate-500">États des lieux</h2>
            <p class="text-3xl font-semibold text-slate-800 mt-2">{{ $stats['etats_des_lieux'] }}</p>
        </div>

        <div class="bg-white p-6 rounded-lg border border-slate-200">
            <h2 class="text-sm font-medium text-slate-500">En attente</h2>
            <p class="text-3xl font-semibold text-amber-600 mt-2">{{ $stats['en_attente'] }}</p>
        </div>

        <div class="bg-white p-6 rounded-lg border border-slate-200">
            <h2 class="text-sm font-medium text-slate-500">Signés</h2>
            <p class="text-3xl font-semibold text-green-600 mt-2">{{ $stats['signes'] }}</p>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        {{-- Répartition --}}
        <div class="bg-white p-6 rounded-lg border border-slate-200">
            <h2 class="text-lg font-medium text-slate-800 mb-4">Répartition</h2>
            @if($repartition['entree'] + $repartition['sortie'] > 0)
                <div class="space-y-3">
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-600">Entrées</span>
                            <span class="text-slate-800 font-medium">{{ $repartition['entree'] }}</span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width: {{ $stats['etats_des_lieux'] > 0 ? ($repartition['entree'] / $stats['etats_des_lieux']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-slate-600">Sorties</span>
                            <span class="text-slate-800 font-medium">{{ $repartition['sortie'] }}</span>
                        </div>
                        <div class="h-2 bg-slate-100 rounded-full overflow-hidden">
                            <div class="h-full bg-orange-500 rounded-full" style="width: {{ $stats['etats_des_lieux'] > 0 ? ($repartition['sortie'] / $stats['etats_des_lieux']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                </div>
            @else
                <p class="text-slate-500 text-sm">Aucun état des lieux réalisé.</p>
            @endif
        </div>

        {{-- Derniers états des lieux --}}
        <div class="bg-white p-6 rounded-lg border border-slate-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-medium text-slate-800">Derniers états des lieux</h2>
                <a href="{{ route('etats-des-lieux.index') }}" class="text-sm text-primary-600 hover:text-primary-700">Voir tout</a>
            </div>
            @if($derniersEdl->isNotEmpty())
                <div class="space-y-3">
                    @foreach($derniersEdl as $edl)
                        <a href="{{ route('etats-des-lieux.show', $edl) }}" class="flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition-colors">
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $edl->logement->nom }}</p>
                                <p class="text-xs text-slate-500">{{ $edl->type_libelle }} - {{ $edl->date_realisation->format('d/m/Y') }}</p>
                            </div>
                            <span class="px-2 py-1 text-xs rounded {{ $edl->statut_couleur }}">
                                {{ $edl->statut_libelle }}
                            </span>
                        </a>
                    @endforeach
                </div>
            @else
                <p class="text-slate-500 text-sm">Aucun état des lieux réalisé.</p>
            @endif
        </div>
    </div>

    {{-- Logements sans EDL --}}
    @if($logementsVides->isNotEmpty())
        <div class="bg-white p-6 rounded-lg border border-slate-200 mt-6">
            <h2 class="text-lg font-medium text-slate-800 mb-4">Logements sans état des lieux</h2>
            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($logementsVides as $logement)
                    <div class="flex items-center justify-between p-3 bg-slate-50 rounded-lg">
                        <div>
                            <p class="text-sm font-medium text-slate-800">{{ $logement->nom }}</p>
                            <p class="text-xs text-slate-500">{{ $logement->ville }}</p>
                        </div>
                        <a href="{{ route('etats-des-lieux.create', ['logement' => $logement->id]) }}" class="text-xs text-primary-600 hover:text-primary-700">
                            Créer EDL
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
@endsection