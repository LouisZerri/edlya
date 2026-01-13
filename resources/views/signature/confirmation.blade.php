<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signature confirmée - Edlya</title>
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center">
    <div class="max-w-lg mx-auto px-4 py-8 text-center">
        <div class="bg-white rounded-lg border border-slate-200 p-8">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-slate-800 mb-2">État des lieux signé !</h1>
            <p class="text-slate-600 mb-6">
                Merci, votre signature a bien été enregistrée.
            </p>

            <div class="bg-slate-50 rounded-lg p-4 mb-6 text-left">
                <p class="text-sm text-slate-500 mb-1">Logement</p>
                <p class="font-medium text-slate-800">{{ $etatDesLieux->logement->nom }}</p>
                <p class="text-sm text-slate-600">{{ $etatDesLieux->logement->adresse_complete }}</p>
                
                <div class="mt-3 pt-3 border-t border-slate-200">
                    <p class="text-sm text-slate-500">Signé le</p>
                    <p class="font-medium text-slate-800">{{ $etatDesLieux->date_signature_locataire->format('d/m/Y à H:i') }}</p>
                </div>
            </div>

            <p class="text-sm text-slate-500">
                Un exemplaire vous sera transmis par email.<br>
                Vous pouvez fermer cette page.
            </p>
        </div>

        <p class="text-sm text-slate-400 mt-6">
            © {{ date('Y') }} Edlya - Gestion des états des lieux
        </p>
    </div>
</body>
</html>