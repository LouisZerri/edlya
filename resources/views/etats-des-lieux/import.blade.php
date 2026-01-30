@extends('layouts.app')

@section('title', 'Importer un état des lieux')

@section('content')
    <div class="max-w-4xl mx-auto">
        <div class="mb-6">
            <a href="{{ route('etats-des-lieux.index') }}" class="text-indigo-600 hover:text-indigo-800">
                ← Retour à la liste
            </a>
        </div>

        <div class="bg-white rounded-lg shadow-sm p-6" id="import-container"
            data-analyze-url="{{ route('etats-des-lieux.import.analyze') }}"
            data-store-url="{{ route('etats-des-lieux.import.store') }}" data-csrf="{{ csrf_token() }}">

            <h1 class="text-2xl font-bold text-gray-900 mb-6">Importer un état des lieux</h1>

            <!-- Étape 1: Upload -->
            <div id="step-upload">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-12 text-center hover:border-indigo-400 transition-colors"
                    id="dropzone">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                    </svg>
                    <p class="mt-4 text-lg text-gray-600">Glissez-déposez votre PDF ici</p>
                    <p class="mt-2 text-sm text-gray-500">ou</p>
                    <label
                        class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg cursor-pointer hover:bg-indigo-700">
                        <span>Parcourir</span>
                        <input type="file" accept=".pdf" class="hidden" id="pdf-input">
                    </label>
                    <p class="mt-4 text-xs text-gray-400">PDF uniquement, max 20 Mo</p>
                </div>

                <div id="file-info" class="hidden mt-4 p-4 bg-gray-50 rounded-lg flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="h-8 w-8 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z"
                                clip-rule="evenodd" />
                        </svg>
                        <span id="file-name" class="text-gray-700"></span>
                    </div>
                    <button type="button" id="remove-file" class="text-gray-400 hover:text-red-500 cursor-pointer">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <button type="button" id="btn-analyze"
                    class="hidden mt-6 w-full py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                    Analyser le PDF
                </button>
            </div>

            <!-- Étape 2: Loading -->
            <div id="step-loading" class="hidden text-center py-12">
                <div class="animate-spin rounded-full h-16 w-16 border-b-2 border-indigo-600 mx-auto"></div>
                <p class="mt-6 text-lg text-gray-600">Analyse en cours...</p>
                <p class="mt-2 text-sm text-gray-500">L'IA extrait les informations du document</p>
            </div>

            <!-- Étape 3: Prévisualisation -->
            <div id="step-preview" class="hidden">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-semibold text-gray-900">Données extraites</h2>
                    <span class="text-sm text-green-600 bg-green-100 px-3 py-1 rounded-full">✓ Extraction réussie</span>
                </div>

                <!-- Infos générales -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select id="preview-type" class="w-full border-gray-300 rounded-lg">
                            <option value="entree">Entrée</option>
                            <option value="sortie">Sortie</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date de réalisation</label>
                        <input type="date" id="preview-date" class="w-full border-gray-300 rounded-lg">
                    </div>
                </div>

                <!-- Logement -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Logement</h3>
                    <div id="logement-existant" class="hidden mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <p class="text-blue-800 text-sm">
                            <strong>Logement existant détecté :</strong> <span id="logement-existant-nom"></span>
                        </p>
                        <label class="mt-2 flex items-center gap-2">
                            <input type="checkbox" id="use-existing-logement" checked
                                class="rounded border-gray-300 text-indigo-600">
                            <span class="text-sm text-gray-700">Utiliser ce logement existant</span>
                        </label>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Adresse (rue)</label>
                            <input type="text" id="preview-adresse" class="w-full border-gray-300 rounded-lg"
                                placeholder="Numéro et rue uniquement">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Code postal</label>
                            <input type="text" id="preview-code-postal" class="w-full border-gray-300 rounded-lg"
                                placeholder="33480">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Ville</label>
                            <input type="text" id="preview-ville" class="w-full border-gray-300 rounded-lg"
                                placeholder="Castelnau de Médoc">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type de bien</label>
                            <select id="preview-type-bien" class="w-full border-gray-300 rounded-lg">
                                <option value="appartement">Appartement</option>
                                <option value="maison">Maison</option>
                                <option value="studio">Studio</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Surface (m²)</label>
                            <input type="number" id="preview-surface" class="w-full border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Locataire -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Locataire</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                            <input type="text" id="preview-locataire-nom" class="w-full border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" id="preview-locataire-email" class="w-full border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input type="tel" id="preview-locataire-telephone"
                                class="w-full border-gray-300 rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Compteurs -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Compteurs (<span id="compteurs-count">0</span>)</h3>
                    <div id="preview-compteurs" class="space-y-3">
                        <!-- Généré dynamiquement -->
                    </div>
                    <p id="no-compteurs" class="text-sm text-gray-500 italic">Aucun compteur détecté</p>
                </div>

                <!-- Clés -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Clés (<span id="cles-count">0</span>)</h3>
                    <div id="preview-cles" class="space-y-3">
                        <!-- Généré dynamiquement -->
                    </div>
                    <p id="no-cles" class="text-sm text-gray-500 italic">Aucune clé détectée</p>
                </div>

                <!-- Pièces -->
                <div class="mb-6">
                    <h3 class="font-semibold text-gray-900 mb-3">Pièces et éléments (<span id="pieces-count">0</span>)
                    </h3>
                    <div id="preview-pieces" class="space-y-0 max-h-[500px] overflow-y-auto pr-2">
                        <!-- Généré dynamiquement -->
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 mt-8">
                    <button type="button" id="btn-back"
                        class="flex-1 py-3 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 cursor-pointer min-h-[44px]">
                        Recommencer
                    </button>
                    <button type="button" id="btn-import"
                        class="flex-1 py-3 bg-indigo-600 text-white rounded-lg font-semibold hover:bg-indigo-700 cursor-pointer min-h-[44px]">
                        Créer l'état des lieux
                    </button>
                </div>
            </div>

            <!-- Erreur -->
            <div id="step-error" class="hidden text-center py-12">
                <svg class="mx-auto h-16 w-16 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="mt-4 text-lg text-gray-900">Erreur lors de l'analyse</p>
                <p id="error-message" class="mt-2 text-sm text-red-600"></p>
                <button type="button" id="btn-retry"
                    class="mt-6 px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 cursor-pointer">
                    Réessayer
                </button>
            </div>
        </div>
    </div>
@endsection
