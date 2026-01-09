@extends('layouts.app')

@section('title', 'États des lieux - Edlya')

@section('content')
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-slate-800">États des lieux</h1>
        <a href="{{ route('etats-des-lieux.create') }}" class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors cursor-pointer">
            Nouvel état des lieux
        </a>
    </div>

    @if($etatsDesLieux->isEmpty())
        <div class="bg-white p-8 rounded-lg border border-slate-200 text-center">
            <p class="text-slate-500 mb-4">Vous n'avez pas encore d'état des lieux.</p>
            <a href="{{ route('etats-des-lieux.create') }}" class="text-primary-600 hover:underline">Créer votre premier état des lieux</a>
        </div>
    @else
    <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
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
@endif
@endsection