@extends('layouts.app')

@section('title', 'État des lieux - ' . $etatDesLieux->logement->nom . ' - Edlya')

@section('content')
    <div class="mb-6">
        <a href="{{ route('etats-des-lieux.index') }}"
            class="text-sm text-slate-500 hover:text-primary-600 transition-colors">
            ← Retour à la liste
        </a>
    </div>

    {{-- En-tête --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <h1 class="text-2xl font-semibold text-slate-800">{{ $etatDesLieux->logement->nom }}</h1>
                <span
                    class="px-3 py-1 text-sm rounded-full {{ $etatDesLieux->type === 'entree' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                    {{ $etatDesLieux->type_libelle }}
                </span>
                <span class="px-3 py-1 text-sm rounded-full {{ $etatDesLieux->statut_couleur }}">
                    {{ $etatDesLieux->statut_libelle }}
                </span>
            </div>
            <p class="text-slate-500">{{ $etatDesLieux->logement->adresse_complete }}</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <a href="{{ route('etats-des-lieux.edit', $etatDesLieux) }}"
                class="inline-flex items-center gap-2 bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
                Modifier
            </a>

            {{-- Bouton Signer --}}
            @if ($etatDesLieux->statut !== 'signe')
                <a href="{{ route('etats-des-lieux.signature', $etatDesLieux) }}"
                    class="inline-flex items-center gap-2 bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Signer
                </a>
            @endif

            <a href="{{ route('etats-des-lieux.pdf', $etatDesLieux) }}"
                class="inline-flex items-center gap-2 bg-slate-800 text-white px-4 py-2 rounded-lg hover:bg-slate-900 transition-colors text-sm font-medium">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Télécharger PDF
            </a>

            @if ($etatDesLieux->type === 'sortie')
                <a href="{{ route('etats-des-lieux.comparatif', $etatDesLieux) }}"
                    class="inline-flex items-center gap-2 bg-amber-500 text-white px-4 py-2 rounded-lg hover:bg-amber-600 transition-colors text-sm font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    Comparatif
                </a>
            @endif
        </div>
    </div>

    {{-- Informations générales --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg border border-slate-200">
            <h2 class="font-medium text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Informations
            </h2>
            <dl class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <dt class="text-slate-500">Date</dt>
                    <dd class="font-medium text-slate-800">{{ $etatDesLieux->date_realisation->format('d/m/Y') }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Type</dt>
                    <dd class="font-medium text-slate-800">{{ $etatDesLieux->type_libelle }}</dd>
                </div>
                <div class="flex justify-between">
                    <dt class="text-slate-500">Statut</dt>
                    <dd><span
                            class="px-2 py-0.5 text-xs rounded-full {{ $etatDesLieux->statut_couleur }}">{{ $etatDesLieux->statut_libelle }}</span>
                    </dd>
                </div>
            </dl>
        </div>

        <div class="bg-white p-6 rounded-lg border border-slate-200">
            <h2 class="font-medium text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                Locataire
            </h2>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-slate-500">Nom</dt>
                    <dd class="font-medium text-slate-800">{{ $etatDesLieux->locataire_nom }}</dd>
                </div>
                @if ($etatDesLieux->locataire_email)
                    <div>
                        <dt class="text-slate-500">Email</dt>
                        <dd class="text-slate-800">{{ $etatDesLieux->locataire_email }}</dd>
                    </div>
                @endif
                @if ($etatDesLieux->locataire_telephone)
                    <div>
                        <dt class="text-slate-500">Téléphone</dt>
                        <dd class="text-slate-800">{{ $etatDesLieux->locataire_telephone }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="bg-white p-6 rounded-lg border border-slate-200">
            <h2 class="font-medium text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                Logement
            </h2>
            <dl class="space-y-3 text-sm">
                <div>
                    <dt class="text-slate-500">Adresse</dt>
                    <dd class="font-medium text-slate-800">{{ $etatDesLieux->logement->adresse }}</dd>
                    <dd class="text-slate-600">{{ $etatDesLieux->logement->code_postal }}
                        {{ $etatDesLieux->logement->ville }}</dd>
                </div>
                @if ($etatDesLieux->logement->surface)
                    <div class="flex justify-between">
                        <dt class="text-slate-500">Surface</dt>
                        <dd class="text-slate-800">{{ $etatDesLieux->logement->surface }} m²</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    {{-- Compteurs --}}
    @if ($etatDesLieux->compteurs->isNotEmpty())
        <div class="bg-white p-6 rounded-lg border border-slate-200 mb-8">
            <h2 class="font-medium text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                </svg>
                Relevé des compteurs
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @php
                    $iconsCompteurs = [
                        'electricite' => '⚡',
                        'eau_froide' => '💧',
                        'eau_chaude' => '🔥',
                        'gaz' => '🔵',
                    ];
                @endphp

                @foreach ($etatDesLieux->compteurs as $compteur)
                    <div class="bg-slate-50 rounded-lg p-4">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-lg">{{ $iconsCompteurs[$compteur->type] ?? '📊' }}</span>
                            <span class="font-medium text-slate-800">{{ $compteur->type_label }}</span>
                        </div>
                        @if ($compteur->numero)
                            <p class="text-xs text-slate-500">N° {{ $compteur->numero }}</p>
                        @endif
                        @if ($compteur->index)
                            <p class="text-lg font-bold text-slate-800 mt-1">{{ $compteur->index }}</p>
                        @else
                            <p class="text-sm text-slate-400 italic mt-1">Non relevé</p>
                        @endif
                        @if ($compteur->commentaire)
                            <p class="text-xs text-slate-500 mt-2">{{ $compteur->commentaire }}</p>
                        @endif
                        @if ($compteur->photo)
                            <a href="{{ Storage::url($compteur->photo) }}" target="_blank" class="block mt-2">
                                <img src="{{ Storage::url($compteur->photo) }}" alt="Compteur"
                                    class="w-full h-20 object-cover rounded-lg border border-slate-200 hover:opacity-90 transition-opacity">
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Clés --}}
    @if ($etatDesLieux->cles->isNotEmpty())
        <div class="bg-white p-6 rounded-lg border border-slate-200 mb-8">
            <h2 class="font-medium text-slate-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                </svg>
                Remise des clés
                <span class="text-sm font-normal text-slate-500">({{ $etatDesLieux->cles->sum('nombre') }} au
                    total)</span>
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach ($etatDesLieux->cles as $cle)
                    <div class="flex items-center gap-3 bg-slate-50 rounded-lg p-4">
                        @if ($cle->photo)
                            <a href="{{ Storage::url($cle->photo) }}" target="_blank" class="flex-shrink-0">
                                <img src="{{ Storage::url($cle->photo) }}" alt="Photo clé"
                                    class="w-14 h-14 object-cover rounded-lg border border-slate-200 hover:opacity-90 transition-opacity">
                            </a>
                        @else
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="font-medium text-slate-800">{{ $cle->type }}</p>
                            @if ($cle->commentaire)
                                <p class="text-xs text-slate-500 truncate">{{ $cle->commentaire }}</p>
                            @endif
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-bold text-slate-800">{{ $cle->nombre }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Observations générales --}}
    @if ($etatDesLieux->observations_generales)
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-6 mb-8">
            <h2 class="font-medium text-amber-800 mb-2 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Observations générales
            </h2>
            <p class="text-amber-900">{{ $etatDesLieux->observations_generales }}</p>
        </div>
    @endif

    {{-- Pièces --}}
    <div class="space-y-6">
        <h2 class="text-lg font-semibold text-slate-800">Détail des pièces ({{ $etatDesLieux->pieces->count() }})</h2>

        @forelse ($etatDesLieux->pieces as $piece)
            @php
                // Construire un mapping des photos avec leur numéro
                $allPhotos = $piece->elements->flatMap(fn($e) => $e->photos)->values();
                $photoNumberMap = [];
                $photoIndex = 1;
                foreach ($piece->elements as $element) {
                    foreach ($element->photos as $photo) {
                        $photoNumberMap[$photo->id] = $photoIndex;
                        $photoIndex++;
                    }
                }
            @endphp

            <div class="bg-white rounded-lg border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-200">
                    <h3 class="font-semibold text-slate-800">{{ $piece->nom }}</h3>
                    <p class="text-sm text-slate-500">{{ $piece->elements->count() }} élément(s) ·
                        {{ $allPhotos->count() }} photo(s)</p>
                </div>

                <div class="p-6">
                    @if ($piece->elements->isNotEmpty())
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200">
                                        <th class="text-left py-3 px-4 font-medium text-slate-600">Élément</th>
                                        <th class="text-left py-3 px-4 font-medium text-slate-600">Type</th>
                                        <th class="text-left py-3 px-4 font-medium text-slate-600">État</th>
                                        <th class="text-left py-3 px-4 font-medium text-slate-600">Dégradations</th>
                                        <th class="text-left py-3 px-4 font-medium text-slate-600">Observations</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($piece->elements as $element)
                                        <tr class="border-b border-slate-100">
                                            <td class="py-3 px-4 font-medium text-slate-800">{{ $element->nom }}</td>
                                            <td class="py-3 px-4 text-slate-600">{{ $element->type_libelle }}</td>
                                            <td class="py-3 px-4">
                                                <span
                                                    class="px-2 py-1 text-xs rounded-full whitespace-nowrap {{ $element->etat_couleur }}">
                                                    {{ $element->etat_libelle }}
                                                </span>
                                            </td>
                                            <td class="py-3 px-4">
                                                @if ($element->hasDegradations())
                                                    <div class="flex flex-wrap gap-1">
                                                        @foreach ($element->degradations as $degradation)
                                                            <span
                                                                class="inline-block px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700">
                                                                {{ $degradation }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <span class="text-slate-400">-</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-slate-600">
                                                @php
                                                    $observations = $element->observations ?: '';
                                                    $photoNumbers = $element->photos->map(fn($p) => 'Photo ' . $photoNumberMap[$p->id])->implode(', ');
                                                @endphp
                                                
                                                @if ($observations && $photoNumbers)
                                                    {{ $observations }}
                                                    <span class="inline-flex items-center gap-1 ml-2 text-primary-600 font-medium">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        {{ $photoNumbers }}
                                                    </span>
                                                @elseif ($photoNumbers)
                                                    <span class="inline-flex items-center gap-1 text-primary-600 font-medium">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                        </svg>
                                                        {{ $photoNumbers }}
                                                    </span>
                                                @elseif ($observations)
                                                    {{ $observations }}
                                                @else
                                                    <span class="text-slate-400">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Galerie photos de la pièce --}}
                        @if ($allPhotos->isNotEmpty())
                            <div class="mt-6 pt-6 border-t border-slate-200">
                                <h4 class="text-sm font-medium text-slate-700 mb-3">Photos de la pièce</h4>
                                <div class="grid grid-cols-2 sm:grid-cols-4 md:grid-cols-6 gap-3">
                                    @foreach ($allPhotos as $index => $photo)
                                        <a href="{{ Storage::url($photo->chemin) }}" target="_blank"
                                            class="group relative block">
                                            <img src="{{ Storage::url($photo->chemin) }}"
                                                alt="Photo {{ $index + 1 }}"
                                                class="w-full h-24 object-cover rounded-lg border border-slate-200 group-hover:opacity-90 transition-opacity">
                                            <span
                                                class="absolute bottom-1 left-1 bg-black/60 text-white text-xs px-1.5 py-0.5 rounded">
                                                {{ $index + 1 }}
                                            </span>
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @else
                        <p class="text-slate-500 text-center py-8">Aucun élément dans cette pièce</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-slate-50 rounded-lg p-12 text-center">
                <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                <p class="text-slate-500">Aucune pièce enregistrée</p>
                <a href="{{ route('etats-des-lieux.edit', $etatDesLieux) }}"
                    class="inline-block mt-4 text-primary-600 hover:text-primary-700 font-medium">
                    Ajouter des pièces →
                </a>
            </div>
        @endforelse
    </div>
@endsection