@extends('layouts.app')

@section('title', $logement->nom . ' - Edlya')

@section('content')
    <div class="mb-6">
        <a href="{{ route('logements.index') }}" class="text-sm text-slate-500 hover:text-primary-600 transition-colors">
            ← Retour aux logements
        </a>
    </div>

    <div class="max-w-2xl mx-auto">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-slate-800">{{ $logement->nom }}</h1>
                <p class="text-slate-500">{{ $logement->adresse_complete }}</p>
            </div>
            <span class="text-sm bg-slate-100 text-slate-600 px-3 py-1 rounded">{{ ucfirst(str_replace('_', ' ', $logement->type)) }}</span>
        </div>

        <div class="bg-white p-6 rounded-lg border border-slate-200 mb-6">
            <h2 class="font-medium text-slate-800 mb-4">Informations</h2>
            
            <dl class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <dt class="text-slate-500">Surface</dt>
                    <dd class="text-slate-800">{{ $logement->surface ? $logement->surface . ' m²' : '-' }}</dd>
                </div>
                <div>
                    <dt class="text-slate-500">Nombre de pièces</dt>
                    <dd class="text-slate-800">{{ $logement->nb_pieces ?? '-' }}</dd>
                </div>
            </dl>

            @if($logement->description)
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <dt class="text-sm text-slate-500 mb-1">Description</dt>
                    <dd class="text-sm text-slate-800">{{ $logement->description }}</dd>
                </div>
            @endif
        </div>

        <div class="flex items-center space-x-4">
            <a href="{{ route('logements.edit', $logement) }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors">
                Modifier
            </a>
            <form method="POST" action="{{ route('logements.destroy', $logement) }}" onsubmit="return confirm('Supprimer ce logement ?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-red-600 hover:text-red-700 transition-colors cursor-pointer">
                    Supprimer
                </button>
            </form>
        </div>
    </div>
@endsection