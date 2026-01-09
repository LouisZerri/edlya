<div id="analyse-modal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 bg-slate-900/50 transition-opacity"></div>

        <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 id="analyse-modal-title" class="text-lg font-medium text-slate-800">Analyse IA</h3>
                <button type="button" id="analyse-modal-close" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            {{-- Formulaire upload --}}
            <form id="analyse-form-upload">
                <input type="hidden" id="analyse-piece-id">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Photo de la pièce</label>
                    <input type="file" id="analyse-photo" accept="image/*" required 
                        class="w-full text-sm text-slate-500 file:mr-2 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 file:cursor-pointer">
                    <p class="mt-1 text-xs text-slate-500">Prenez une photo globale de la pièce pour une meilleure détection.</p>
                </div>

                <div id="analyse-preview" class="hidden mb-4"></div>

                <div id="analyse-error" class="hidden mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <p id="analyse-error-message" class="text-sm text-red-600"></p>
                </div>

                <button type="submit" id="analyse-btn-analyser" class="w-full bg-primary-600 text-white px-4 py-2 rounded-lg hover:bg-primary-700 transition-colors cursor-pointer">
                    Analyser avec l'IA
                </button>
            </form>

            {{-- Loading --}}
            <div id="analyse-loading" class="hidden py-8 text-center">
                <div class="inline-block w-8 h-8 border-4 border-primary-200 border-t-primary-600 rounded-full animate-spin mb-4"></div>
                <p class="text-slate-600">Analyse en cours...</p>
                <p class="text-sm text-slate-500">Cela peut prendre quelques secondes.</p>
            </div>

            {{-- Résultats --}}
            <div id="analyse-results" class="hidden">
                <div class="mb-4">
                    <h4 class="text-sm font-medium text-slate-700 mb-2">Éléments détectés</h4>
                    <p class="text-xs text-slate-500 mb-3">Cochez les éléments à ajouter et ajustez si nécessaire.</p>
                    <div id="analyse-elements" class="max-h-80 overflow-y-auto"></div>
                </div>
            </div>

            {{-- Formulaire validation --}}
            <form id="analyse-form-validation" class="hidden">
                <button type="submit" id="analyse-btn-appliquer" class="w-full bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition-colors cursor-pointer">
                    Appliquer les éléments sélectionnés
                </button>
            </form>
        </div>
    </div>
</div>