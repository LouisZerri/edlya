<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signature - État des lieux - Edlya</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 py-8">
        {{-- Header --}}
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-primary-600 mb-2">Edlya</h1>
            <p class="text-slate-500">Signature d'état des lieux</p>
        </div>

        {{-- Infos EDL --}}
        <div class="bg-white rounded-lg border border-slate-200 p-6 mb-6">
            <div class="flex items-center gap-3 mb-4">
                <span class="px-3 py-1 text-sm rounded-full {{ $etatDesLieux->type === 'entree' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                    État des lieux {{ $etatDesLieux->type === 'entree' ? "d'entrée" : "de sortie" }}
                </span>
                <span class="text-sm text-slate-500">{{ $etatDesLieux->date_realisation->format('d/m/Y') }}</span>
            </div>

            <h2 class="text-xl font-semibold text-slate-800 mb-2">{{ $etatDesLieux->logement->nom }}</h2>
            <p class="text-slate-600">{{ $etatDesLieux->logement->adresse }}</p>
            <p class="text-slate-600">{{ $etatDesLieux->logement->code_postal }} {{ $etatDesLieux->logement->ville }}</p>

            <div class="grid grid-cols-2 gap-4 mt-4 pt-4 border-t border-slate-200">
                <div>
                    <p class="text-sm text-slate-500">Bailleur</p>
                    <p class="font-medium text-slate-800">{{ $etatDesLieux->user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-slate-500">Locataire</p>
                    <p class="font-medium text-slate-800">{{ $etatDesLieux->locataire_nom }}</p>
                </div>
            </div>
        </div>

        {{-- Signature bailleur --}}
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
            <div class="flex items-center gap-2 mb-2">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-medium text-green-800">Signé par le bailleur</span>
            </div>
            <p class="text-sm text-green-700">
                {{ $etatDesLieux->user->name }} - {{ $etatDesLieux->date_signature_bailleur->format('d/m/Y à H:i') }}
            </p>
        </div>

        {{-- Résumé pièces --}}
        <div class="bg-white rounded-lg border border-slate-200 p-6 mb-6">
            <h3 class="font-semibold text-slate-800 mb-4">Résumé de l'état des lieux</h3>
            
            <div class="space-y-3">
                @foreach($etatDesLieux->pieces as $piece)
                    <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                        <span class="font-medium text-slate-700">{{ $piece->nom }}</span>
                        <span class="text-sm text-slate-500">{{ $piece->elements->count() }} élément(s)</span>
                    </div>
                @endforeach
            </div>

            @if($etatDesLieux->observations_generales)
                <div class="mt-4 pt-4 border-t border-slate-200">
                    <p class="text-sm text-slate-500 mb-1">Observations générales</p>
                    <p class="text-slate-700">{{ $etatDesLieux->observations_generales }}</p>
                </div>
            @endif
        </div>

        {{-- Section signature --}}
        <div class="bg-white rounded-lg border border-slate-200 p-6">
            <h3 class="font-semibold text-slate-800 mb-4">Votre signature</h3>

            @if(!$etatDesLieux->codeEstValide())
                {{-- Étape 1 : Validation email --}}
                <div class="text-center py-4">
                    <p class="text-slate-600 mb-4">
                        Pour signer, vous devez d'abord valider votre identité.<br>
                        Un code à 6 chiffres sera envoyé à <strong>{{ $etatDesLieux->locataire_email }}</strong>
                    </p>

                    <form method="POST" action="{{ route('signature.locataire.envoyer-code', ['token' => $token]) }}" class="mb-6">
                        @csrf
                        <button type="submit" class="bg-primary-600 text-white px-6 py-3 rounded-lg hover:bg-primary-700 font-medium cursor-pointer transition-colors">
                            Recevoir le code par email
                        </button>
                    </form>

                    @if($etatDesLieux->code_validation)
                        <div class="border-t border-slate-200 pt-6">
                            <p class="text-sm font-medium text-slate-700 mb-3">Entrez le code reçu :</p>
                            <form method="POST" action="{{ route('signature.locataire.verifier-code', ['token' => $token]) }}">
                                @csrf
                                <div class="flex justify-center gap-3">
                                    <input type="text" name="code" maxlength="6" placeholder="000000"
                                        class="w-40 px-4 py-3 border border-slate-300 rounded-lg text-center text-2xl font-mono tracking-widest focus:ring-2 focus:ring-primary-100 focus:border-primary-500 outline-none"
                                        pattern="[0-9]{6}" required>
                                    <button type="submit" class="bg-slate-800 text-white px-6 py-3 rounded-lg hover:bg-slate-900 font-medium cursor-pointer transition-colors">
                                        Valider
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            @else
                {{-- Étape 2 : Signature --}}
                <div class="bg-green-50 border border-green-200 rounded-lg p-3 mb-4">
                    <div class="flex items-center gap-2">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-sm text-green-700">Email vérifié le {{ $etatDesLieux->code_validation_verifie_at->format('d/m/Y à H:i') }}</span>
                    </div>
                </div>

                <form method="POST" action="{{ route('signature.locataire.signer', ['token' => $token]) }}" id="form-locataire">
                    @csrf
                    <input type="hidden" name="signature_locataire" id="input-signature-locataire">

                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-2">
                            <label class="text-sm font-medium text-slate-700">Signez dans le cadre ci-dessous</label>
                            <button type="button" id="clear-locataire" class="text-sm text-slate-500 hover:text-primary-600 cursor-pointer">
                                Effacer
                            </button>
                        </div>
                        <div class="border-2 border-dashed border-slate-300 rounded-lg bg-white">
                            <canvas id="signature-locataire" class="w-full" style="height: 200px; touch-action: none;"></canvas>
                        </div>
                    </div>

                    <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                        <p class="text-sm text-amber-800">
                            <strong>En signant</strong>, vous confirmez avoir pris connaissance de l'état des lieux et acceptez son contenu.
                        </p>
                    </div>

                    <button type="submit" class="w-full bg-green-600 text-white py-3 px-4 rounded-lg hover:bg-green-700 font-medium cursor-pointer transition-colors">
                        Signer l'état des lieux
                    </button>
                </form>
            @endif
        </div>

        {{-- Messages flash --}}
        @if(session('success'))
            <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
                {{ session('error') }}
            </div>
        @endif
    </div>
</body>
</html>