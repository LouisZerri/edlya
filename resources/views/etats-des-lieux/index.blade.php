@extends('layouts.app')

@section('title', 'États des lieux - Edlya')

@section('content')
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <h1 class="text-2xl font-semibold text-slate-800">États des lieux</h1>
        <div class="flex gap-2 sm:gap-3">
            <a href="{{ route('etats-des-lieux.import') }}" class="inline-flex items-center justify-center flex-1 sm:flex-initial px-3 sm:px-4 py-2.5 border border-primary-600 text-primary-600 rounded-lg hover:bg-primary-50 transition-colors text-sm sm:text-base min-h-[44px]">
                <svg class="w-4 h-4 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                </svg>
                <span class="hidden sm:inline">Importer PDF</span>
                <span class="sm:hidden">Importer</span>
            </a>
            <a href="{{ route('etats-des-lieux.create') }}" class="inline-flex items-center justify-center flex-1 sm:flex-initial bg-primary-600 text-white px-3 sm:px-4 py-2.5 rounded-lg hover:bg-primary-700 transition-colors text-sm sm:text-base min-h-[44px]">
                <svg class="w-4 h-4 mr-1 sm:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                <span class="hidden sm:inline">Nouvel état des lieux</span>
                <span class="sm:hidden">Nouveau</span>
            </a>
        </div>
    </div>

    @if($etatsDesLieux->isEmpty())
        <div class="bg-white p-8 rounded-lg border border-slate-200 text-center">
            <p class="text-slate-500 mb-4">Vous n'avez pas encore d'état des lieux.</p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('etats-des-lieux.import') }}" class="text-primary-600 hover:underline">Importer un PDF</a>
                <span class="text-slate-300">|</span>
                <a href="{{ route('etats-des-lieux.create') }}" class="text-primary-600 hover:underline">Créer manuellement</a>
            </div>
        </div>
    @else
        {{-- Desktop: Table view --}}
        <div class="hidden md:block bg-white rounded-lg border border-slate-200 overflow-hidden">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8fafc; border-bottom: 1px solid #e2e8f0;">
                        <th style="padding: 12px 24px; text-align: left; font-size: 14px; font-weight: 500; color: #475569;">Logement</th>
                        <th style="padding: 12px 24px; text-align: left; font-size: 14px; font-weight: 500; color: #475569;">Type</th>
                        <th style="padding: 12px 24px; text-align: left; font-size: 14px; font-weight: 500; color: #475569;">Locataire</th>
                        <th style="padding: 12px 24px; text-align: left; font-size: 14px; font-weight: 500; color: #475569;">Date</th>
                        <th style="padding: 12px 24px; text-align: left; font-size: 14px; font-weight: 500; color: #475569;">Statut</th>
                        <th style="padding: 12px 24px; width: 60px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($etatsDesLieux as $edl)
                        <tr class="hover:bg-slate-50" style="border-bottom: 1px solid #f1f5f9;">
                            <td style="padding: 16px 24px; text-align: left;">
                                <span class="font-medium text-slate-800">{{ $edl->logement->nom }}</span>
                                <span class="block text-sm text-slate-500">{{ $edl->logement->ville }}</span>
                            </td>
                            <td style="padding: 16px 24px; text-align: left;">
                                <span class="inline-flex px-2 py-1 text-xs rounded {{ $edl->type === 'entree' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ $edl->type_libelle }}
                                </span>
                            </td>
                            <td style="padding: 16px 24px; text-align: left;" class="text-sm text-slate-600">{{ $edl->locataire_nom }}</td>
                            <td style="padding: 16px 24px; text-align: left;" class="text-sm text-slate-600">{{ $edl->date_realisation->format('d/m/Y') }}</td>
                            <td style="padding: 16px 24px; text-align: left;">
                                <span class="inline-flex px-2 py-1 text-xs rounded {{ $edl->statut_couleur }}">
                                    {{ $edl->statut_libelle }}
                                </span>
                            </td>
                            <td style="padding: 16px 24px; text-align: right;">
                                <a href="{{ route('etats-des-lieux.show', $edl) }}" class="text-sm text-primary-600 hover:underline">Voir</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile: Card view --}}
        <div class="md:hidden space-y-3">
            @foreach($etatsDesLieux as $edl)
                <a href="{{ route('etats-des-lieux.show', $edl) }}"
                   class="block bg-white rounded-lg border border-slate-200 p-4 hover:border-primary-300 hover:shadow-sm transition-all active:bg-slate-50">
                    <div class="flex items-start justify-between gap-3 mb-3">
                        <div class="min-w-0 flex-1">
                            <h3 class="font-medium text-slate-800 truncate">{{ $edl->logement->nom }}</h3>
                            <p class="text-sm text-slate-500 truncate">{{ $edl->logement->ville }}</p>
                        </div>
                        <div class="flex flex-col items-end gap-1 flex-shrink-0">
                            <span class="inline-flex px-2 py-1 text-xs rounded {{ $edl->type === 'entree' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                                {{ $edl->type_libelle }}
                            </span>
                            <span class="inline-flex px-2 py-1 text-xs rounded {{ $edl->statut_couleur }}">
                                {{ $edl->statut_libelle }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-slate-600">{{ $edl->locataire_nom }}</span>
                        <span class="text-slate-500">{{ $edl->date_realisation->format('d/m/Y') }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection