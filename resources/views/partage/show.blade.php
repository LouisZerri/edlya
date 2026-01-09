<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>État des lieux - {{ $etatDesLieux->logement->nom }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {"50":"#eef2ff","100":"#e0e7ff","200":"#c7d2fe","300":"#a5b4fc","400":"#818cf8","500":"#6366f1","600":"#4f46e5","700":"#4338ca","800":"#3730a3","900":"#312e81"}
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-100 min-h-screen">
    {{-- Header --}}
    <header class="bg-white border-b border-slate-200 sticky top-0 z-10">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <div>
                <h1 class="text-xl font-semibold text-primary-600">Edlya</h1>
                <p class="text-xs text-slate-500">Propulsé par GEST'IMMO</p>
            </div>
            <a href="{{ route('partage.pdf', $partage->token) }}" 
               class="bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors text-sm font-medium flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                Télécharger PDF
            </a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 py-8">
        {{-- En-tête EDL --}}
        <div class="bg-white rounded-lg border border-slate-200 p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <h2 class="text-2xl font-semibold text-slate-800">{{ $etatDesLieux->logement->nom }}</h2>
                    <p class="text-slate-500">{{ $etatDesLieux->logement->adresse_complete }}</p>
                </div>
                <span class="px-3 py-1 text-sm rounded-full {{ $etatDesLieux->type === 'entree' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                    {{ $etatDesLieux->type_libelle }}
                </span>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                <div>
                    <p class="text-slate-500">Date</p>
                    <p class="font-medium text-slate-800">{{ $etatDesLieux->date_realisation->format('d/m/Y') }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Locataire</p>
                    <p class="font-medium text-slate-800">{{ $etatDesLieux->locataire_nom }}</p>
                </div>
                <div>
                    <p class="text-slate-500">Statut</p>
                    <p class="font-medium">
                        <span class="px-2 py-0.5 rounded text-xs {{ $etatDesLieux->statut_couleur }}">
                            {{ $etatDesLieux->statut_libelle }}
                        </span>
                    </p>
                </div>
                @if($etatDesLieux->logement->surface)
                    <div>
                        <p class="text-slate-500">Surface</p>
                        <p class="font-medium text-slate-800">{{ $etatDesLieux->logement->surface }} m²</p>
                    </div>
                @endif
            </div>

            @if($etatDesLieux->observations_generales)
                <div class="mt-4 pt-4 border-t border-slate-100">
                    <p class="text-sm text-slate-500 mb-1">Observations générales</p>
                    <p class="text-sm text-slate-700">{{ $etatDesLieux->observations_generales }}</p>
                </div>
            @endif
        </div>

        {{-- Pièces --}}
        @foreach($etatDesLieux->pieces as $piece)
            <div class="bg-white rounded-lg border border-slate-200 mb-4">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50 rounded-t-lg">
                    <h3 class="font-semibold text-slate-800">{{ $piece->nom }}</h3>
                    <p class="text-sm text-slate-500">{{ $piece->elements->count() }} élément(s)</p>
                </div>

                @if($piece->elements->isNotEmpty())
                    <div class="p-6 space-y-4">
                        @foreach($piece->elements as $element)
                            <div class="border border-slate-200 rounded-lg p-4 bg-slate-50">
                                <div class="flex items-start justify-between mb-2">
                                    <div>
                                        <span class="font-medium text-slate-800">{{ $element->nom }}</span>
                                        <span class="text-sm text-slate-500 ml-2">({{ $element->type }})</span>
                                    </div>
                                    <span class="px-2.5 py-1 text-xs rounded-full {{ $element->etat_couleur }}">
                                        {{ $element->etat_libelle }}
                                    </span>
                                </div>
                                @if($element->observations)
                                    <p class="text-sm text-slate-600 italic mt-2">{{ $element->observations }}</p>
                                @endif
                                @if($element->photos->isNotEmpty())
                                    <div class="flex flex-wrap gap-2 mt-3">
                                        @foreach($element->photos as $photo)
                                            <a href="{{ $photo->url }}" target="_blank">
                                                <img src="{{ $photo->url }}" 
                                                     alt="{{ $photo->legende ?? 'Photo' }}" 
                                                     class="w-20 h-20 object-cover rounded-lg border border-slate-200 hover:opacity-90 transition-opacity">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 text-sm text-slate-500">Aucun élément dans cette pièce.</div>
                @endif
            </div>
        @endforeach

        {{-- Signatures --}}
        @if($etatDesLieux->statut === 'signe')
            <div class="bg-white rounded-lg border border-slate-200 p-6">
                <h3 class="font-semibold text-slate-800 mb-4">Signatures</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($etatDesLieux->signature_bailleur)
                        <div class="text-center">
                            <p class="text-sm text-slate-500 mb-2">Bailleur / Agent</p>
                            <img src="{{ $etatDesLieux->signature_bailleur }}" alt="Signature bailleur" class="max-h-24 mx-auto border border-slate-200 rounded">
                            <p class="text-xs text-slate-400 mt-2">{{ $etatDesLieux->date_signature_bailleur->format('d/m/Y à H:i') }}</p>
                        </div>
                    @endif
                    @if($etatDesLieux->signature_locataire)
                        <div class="text-center">
                            <p class="text-sm text-slate-500 mb-2">Locataire</p>
                            <img src="{{ $etatDesLieux->signature_locataire }}" alt="Signature locataire" class="max-h-24 mx-auto border border-slate-200 rounded">
                            <p class="text-xs text-slate-400 mt-2">{{ $etatDesLieux->date_signature_locataire->format('d/m/Y à H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endif

        {{-- Footer info --}}
        <div class="mt-8 text-center text-sm text-slate-500">
            <p>Ce lien expire le {{ $partage->expire_at->format('d/m/Y à H:i') }}</p>
        </div>
    </main>

    <footer class="max-w-4xl mx-auto px-4 py-6 text-center text-xs text-slate-400">
        <p>© {{ date('Y') }} GEST'IMMO — Tous droits réservés</p>
    </footer>
</body>
</html>