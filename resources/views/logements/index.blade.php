@extends('layouts.app')

@section('title', 'Mes logements - Edlya')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-slate-800">Mes logements</h1>
        <a href="{{ route('logements.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors">
            Ajouter un logement
        </a>
    </div>

    @if($logements->isEmpty())
        <div class="bg-white p-8 rounded-lg border border-slate-200 text-center">
            <p class="text-slate-500 mb-4">Vous n'avez pas encore de logement.</p>
            <a href="{{ route('logements.create') }}" class="text-primary-600 hover:underline">Créer votre premier logement</a>
        </div>
    @else
        <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
            @foreach($logements as $logement)
                <a href="{{ route('logements.show', $logement) }}" class="bg-white p-6 rounded-lg border border-slate-200 hover:border-primary-300 transition-colors block">
                    <div class="flex items-start justify-between mb-2">
                        <h2 class="font-semibold text-slate-800">{{ $logement->nom }}</h2>
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-1 rounded">{{ ucfirst(str_replace('_', ' ', $logement->type)) }}</span>
                    </div>
                    <p class="text-sm text-slate-500 mb-3">{{ $logement->adresse_complete }}</p>
                    <div class="flex items-center space-x-4 text-sm text-slate-500">
                        @if($logement->surface)
                            <span>{{ $logement->surface }} m²</span>
                        @endif
                        @if($logement->nb_pieces)
                            <span>{{ $logement->nb_pieces }} pièce{{ $logement->nb_pieces > 1 ? 's' : '' }}</span>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection