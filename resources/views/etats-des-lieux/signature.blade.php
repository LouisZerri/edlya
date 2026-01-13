@extends('layouts.app')

@section('title', 'Signature - Edlya')

@section('content')
    <div class="mb-6">
        <a href="{{ route('etats-des-lieux.show', $etatDesLieux) }}" class="text-sm text-slate-500 hover:text-primary-600 transition-colors">
            ← Retour à l'état des lieux
        </a>
    </div>

    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-semibold text-slate-800 mb-2">Signature de l'état des lieux</h1>
        <p class="text-slate-500 mb-6">{{ $etatDesLieux->logement->nom }} - {{ $etatDesLieux->type_libelle }}</p>

        {{-- Étapes --}}
        <div class="mb-8">
            <div class="flex items-center justify-between">
                @php
                    $etape = $etatDesLieux->etape_signature;
                @endphp
                
                {{-- Étape 1 --}}
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold {{ $etape >= 1 ? ($etape > 1 ? 'bg-green-500 text-white' : 'bg-primary-600 text-white') : 'bg-slate-200 text-slate-500' }}">
                        @if($etape > 1)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            1
                        @endif
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $etape >= 1 ? 'text-slate-800' : 'text-slate-400' }}">Signature bailleur</span>
                </div>

                <div class="flex-1 h-1 mx-4 {{ $etape > 1 ? 'bg-green-500' : 'bg-slate-200' }}"></div>

                {{-- Étape 2 --}}
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold {{ $etape >= 2 ? ($etape > 2 ? 'bg-green-500 text-white' : 'bg-primary-600 text-white') : 'bg-slate-200 text-slate-500' }}">
                        @if($etape > 2)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            2
                        @endif
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $etape >= 2 ? 'text-slate-800' : 'text-slate-400' }}">Envoi au locataire</span>
                </div>

                <div class="flex-1 h-1 mx-4 {{ $etape > 2 ? 'bg-green-500' : 'bg-slate-200' }}"></div>

                {{-- Étape 3 --}}
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-semibold {{ $etape >= 3 ? ($etape > 3 ? 'bg-green-500 text-white' : 'bg-primary-600 text-white') : 'bg-slate-200 text-slate-500' }}">
                        @if($etape > 3)
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        @else
                            3
                        @endif
                    </div>
                    <span class="ml-2 text-sm font-medium {{ $etape >= 3 ? 'text-slate-800' : 'text-slate-400' }}">Signature locataire</span>
                </div>
            </div>
        </div>

        {{-- Étape 1 : Signature Bailleur --}}
        @if($etape === 1)
            <div class="bg-white p-6 rounded-lg border border-slate-200">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-primary-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-800">Signature du bailleur / agent</h2>
                        <p class="text-sm text-slate-500">{{ $etatDesLieux->user->name }}</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('etats-des-lieux.signature.bailleur', $etatDesLieux) }}" id="form-bailleur">
                    @csrf
                    <input type="hidden" name="signature_bailleur" id="input-signature-bailleur">

                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-medium text-slate-700">Signez dans le cadre ci-dessous</label>
                            <button type="button" id="clear-bailleur" class="text-sm text-slate-500 hover:text-primary-600 cursor-pointer">
                                Effacer
                            </button>
                        </div>
                        <div class="border-2 border-dashed border-slate-300 rounded-lg bg-white">
                            <canvas id="signature-bailleur" class="w-full" style="height: 200px; touch-action: none;"></canvas>
                        </div>
                    </div>

                    <button type="submit" class="w-full bg-primary-600 text-white py-3 px-4 rounded-lg hover:bg-primary-700 font-medium cursor-pointer transition-colors">
                        Valider ma signature
                    </button>
                </form>
            </div>
        @endif

        {{-- Étape 2 : Envoi du lien --}}
        @if($etape === 2)
            <div class="grid md:grid-cols-2 gap-6">
                {{-- Signature bailleur (lecture seule) --}}
                <div class="bg-green-50 p-6 rounded-lg border border-green-200">
                    <div class="flex items-center gap-2 mb-4">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <h2 class="font-semibold text-green-800">Bailleur - Signé</h2>
                    </div>
                    <p class="text-sm text-green-700 mb-3">{{ $etatDesLieux->user->name }}</p>
                    <p class="text-xs text-green-600">Signé le {{ $etatDesLieux->date_signature_bailleur->format('d/m/Y à H:i') }}</p>
                    @if($etatDesLieux->signature_bailleur)
                        <img src="{{ $etatDesLieux->signature_bailleur }}" alt="Signature bailleur" class="mt-3 max-h-24 border border-green-200 rounded bg-white p-2">
                    @endif
                </div>

                {{-- Envoi du lien --}}
                <div class="bg-white p-6 rounded-lg border border-slate-200">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 bg-amber-100 rounded-lg flex items-center justify-center">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="font-semibold text-slate-800">Envoi au locataire</h2>
                            <p class="text-sm text-slate-500">{{ $etatDesLieux->locataire_nom }}</p>
                        </div>
                    </div>

                    @if(empty($etatDesLieux->locataire_email))
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                            <p class="text-sm text-red-700">
                                <strong>Attention :</strong> L'email du locataire n'est pas renseigné. 
                                <a href="{{ route('etats-des-lieux.edit', $etatDesLieux) }}" class="underline">Modifier l'état des lieux</a>
                            </p>
                        </div>
                    @else
                        <p class="text-sm text-slate-600 mb-4">
                            Un email sera envoyé à <strong>{{ $etatDesLieux->locataire_email }}</strong> avec un lien sécurisé pour consulter et signer l'état des lieux.
                        </p>

                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                            <p class="text-sm text-blue-700">
                                <strong>Le locataire pourra :</strong>
                            </p>
                            <ul class="text-sm text-blue-700 mt-2 space-y-1">
                                <li>• Consulter l'état des lieux complet</li>
                                <li>• Valider son identité par code email</li>
                                <li>• Signer électroniquement</li>
                            </ul>
                        </div>

                        <form method="POST" action="{{ route('etats-des-lieux.signature.envoyer-lien', $etatDesLieux) }}">
                            @csrf
                            <button type="submit" class="w-full bg-amber-500 text-white py-3 px-4 rounded-lg hover:bg-amber-600 font-medium cursor-pointer transition-colors">
                                Envoyer le lien de signature
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        {{-- Étape 3 : Attente signature locataire --}}
        @if($etape === 3)
            <div class="bg-white p-8 rounded-lg border border-slate-200 text-center">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-slate-800 mb-2">En attente de signature</h2>
                <p class="text-slate-600 mb-6">
                    Un lien de signature a été envoyé à <strong>{{ $etatDesLieux->locataire_email }}</strong>.<br>
                    Le locataire doit cliquer sur le lien pour signer.
                </p>

                @if($etatDesLieux->signature_token_expire_at)
                    <p class="text-sm text-slate-500 mb-6">
                        Lien valide jusqu'au {{ $etatDesLieux->signature_token_expire_at->format('d/m/Y à H:i') }}
                    </p>
                @endif

                <form method="POST" action="{{ route('etats-des-lieux.signature.envoyer-lien', $etatDesLieux) }}" class="inline">
                    @csrf
                    <button type="submit" class="text-primary-600 hover:text-primary-700 font-medium cursor-pointer">
                        Renvoyer le lien
                    </button>
                </form>
            </div>
        @endif

        {{-- Étape 4 : Terminé --}}
        @if($etape === 4)
            <div class="bg-green-50 border border-green-200 rounded-lg p-8 text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h2 class="text-xl font-semibold text-green-800 mb-2">État des lieux signé !</h2>
                <p class="text-green-700 mb-6">Les deux parties ont signé le document.</p>

                <div class="grid md:grid-cols-2 gap-6 text-left max-w-2xl mx-auto">
                    <div class="bg-white rounded-lg p-4 border border-green-200">
                        <p class="text-sm font-medium text-slate-800 mb-1">{{ $etatDesLieux->user->name }}</p>
                        <p class="text-xs text-slate-500 mb-2">Signé le {{ $etatDesLieux->date_signature_bailleur->format('d/m/Y à H:i') }}</p>
                        @if($etatDesLieux->signature_bailleur)
                            <img src="{{ $etatDesLieux->signature_bailleur }}" alt="Signature" class="max-h-16">
                        @endif
                    </div>
                    <div class="bg-white rounded-lg p-4 border border-green-200">
                        <p class="text-sm font-medium text-slate-800 mb-1">{{ $etatDesLieux->locataire_nom }}</p>
                        <p class="text-xs text-slate-500 mb-2">Signé le {{ $etatDesLieux->date_signature_locataire->format('d/m/Y à H:i') }}</p>
                        @if($etatDesLieux->signature_locataire)
                            <img src="{{ $etatDesLieux->signature_locataire }}" alt="Signature" class="max-h-16">
                        @endif
                    </div>
                </div>

                @if($etatDesLieux->signature_ip)
                    <p class="text-xs text-slate-500 mt-6">
                        Traçabilité : IP {{ $etatDesLieux->signature_ip }} • {{ $etatDesLieux->date_signature_locataire->format('d/m/Y à H:i') }}
                    </p>
                @endif

                <div class="mt-6">
                    <a href="{{ route('etats-des-lieux.pdf', $etatDesLieux) }}" class="inline-flex items-center gap-2 bg-slate-800 text-white px-6 py-3 rounded-lg hover:bg-slate-900 transition-colors font-medium">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Télécharger le PDF signé
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection