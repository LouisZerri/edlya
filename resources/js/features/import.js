document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('import-container');
    if (!container) return;

    const analyzeUrl = container.dataset.analyzeUrl;
    const storeUrl = container.dataset.storeUrl;
    const csrf = container.dataset.csrf;

    // Éléments
    const pdfInput = document.getElementById('pdf-input');
    const dropzone = document.getElementById('dropzone');
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');
    const removeFile = document.getElementById('remove-file');
    const btnAnalyze = document.getElementById('btn-analyze');

    const stepUpload = document.getElementById('step-upload');
    const stepLoading = document.getElementById('step-loading');
    const stepPreview = document.getElementById('step-preview');
    const stepError = document.getElementById('step-error');

    const btnBack = document.getElementById('btn-back');
    const btnImport = document.getElementById('btn-import');
    const btnRetry = document.getElementById('btn-retry');
    const errorMessage = document.getElementById('error-message');

    let selectedFile = null;
    let extractedData = null;
    let logementExistant = null;

    // Drag & Drop
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('border-indigo-400', 'bg-indigo-50');
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('border-indigo-400', 'bg-indigo-50');
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('border-indigo-400', 'bg-indigo-50');
        const files = e.dataTransfer.files;
        if (files.length > 0 && files[0].type === 'application/pdf') {
            handleFileSelect(files[0]);
        }
    });

    // File input
    pdfInput.addEventListener('change', (e) => {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });

    function handleFileSelect(file) {
        selectedFile = file;
        fileName.textContent = file.name;
        fileInfo.classList.remove('hidden');
        btnAnalyze.classList.remove('hidden');
    }

    // Remove file
    removeFile.addEventListener('click', () => {
        selectedFile = null;
        pdfInput.value = '';
        fileInfo.classList.add('hidden');
        btnAnalyze.classList.add('hidden');
    });

    // Analyze
    btnAnalyze.addEventListener('click', async () => {
        if (!selectedFile) return;

        showStep('loading');

        const formData = new FormData();
        formData.append('pdf', selectedFile);
        formData.append('_token', csrf);

        try {
            const response = await fetch(analyzeUrl, {
                method: 'POST',
                body: formData,
            });

            const result = await response.json();

            if (result.success) {
                extractedData = result.data;
                logementExistant = result.logement_existant;
                showPreview(result.data, result.logement_existant, result.photos_count || 0);
                showStep('preview');
            } else {
                errorMessage.textContent = result.error;
                showStep('error');
            }
        } catch (error) {
            errorMessage.textContent = error.message;
            showStep('error');
        }
    });

    // Back button
    btnBack.addEventListener('click', () => {
        extractedData = null;
        logementExistant = null;
        showStep('upload');
    });

    // Retry button
    btnRetry.addEventListener('click', () => {
        showStep('upload');
    });

    // Import button
    btnImport.addEventListener('click', async () => {
        if (!extractedData) return;

        showStep('loading');

        // Récupérer les valeurs modifiées
        const useExisting = document.getElementById('use-existing-logement')?.checked;
        
        // Mettre à jour extractedData avec les modifications
        extractedData.type = document.getElementById('preview-type').value;
        extractedData.date_realisation = document.getElementById('preview-date').value;
        extractedData.logement = extractedData.logement || {};
        extractedData.logement.adresse = document.getElementById('preview-adresse').value;
        extractedData.logement.code_postal = document.getElementById('preview-code-postal')?.value || null;
        extractedData.logement.ville = document.getElementById('preview-ville')?.value || null;
        extractedData.logement.type_bien = document.getElementById('preview-type-bien').value;
        extractedData.logement.surface = document.getElementById('preview-surface').value || null;
        extractedData.locataire = extractedData.locataire || {};
        extractedData.locataire.nom = document.getElementById('preview-locataire-nom').value;
        extractedData.locataire.email = document.getElementById('preview-locataire-email').value || null;
        extractedData.locataire.telephone = document.getElementById('preview-locataire-telephone').value || null;

        try {
            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({
                    data: extractedData,
                    logement_id: useExisting && logementExistant ? logementExistant.id : null,
                }),
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = result.redirect;
            } else {
                errorMessage.textContent = result.error;
                showStep('error');
            }
        } catch (error) {
            errorMessage.textContent = error.message;
            showStep('error');
        }
    });

    function showStep(step) {
        stepUpload.classList.add('hidden');
        stepLoading.classList.add('hidden');
        stepPreview.classList.add('hidden');
        stepError.classList.add('hidden');

        switch (step) {
            case 'upload':
                stepUpload.classList.remove('hidden');
                break;
            case 'loading':
                stepLoading.classList.remove('hidden');
                break;
            case 'preview':
                stepPreview.classList.remove('hidden');
                break;
            case 'error':
                stepError.classList.remove('hidden');
                break;
        }
    }

    function showPreview(data, existingLogement, photosCount) {
        // Type et date
        document.getElementById('preview-type').value = data.type || 'entree';
        document.getElementById('preview-date').value = data.date_realisation || '';

        // Logement
        document.getElementById('preview-adresse').value = data.logement?.adresse || '';
        document.getElementById('preview-type-bien').value = data.logement?.type_bien || 'appartement';
        document.getElementById('preview-surface').value = data.logement?.surface || '';
        
        // Code postal et ville
        const codePostalInput = document.getElementById('preview-code-postal');
        const villeInput = document.getElementById('preview-ville');
        if (codePostalInput) codePostalInput.value = data.logement?.code_postal || '';
        if (villeInput) villeInput.value = data.logement?.ville || '';

        // Logement existant
        const logementExistantDiv = document.getElementById('logement-existant');
        if (existingLogement) {
            logementExistantDiv.classList.remove('hidden');
            document.getElementById('logement-existant-nom').textContent = existingLogement.nom + ' - ' + existingLogement.adresse;
        } else {
            logementExistantDiv.classList.add('hidden');
        }

        // Locataire
        document.getElementById('preview-locataire-nom').value = data.locataire?.nom || '';
        document.getElementById('preview-locataire-email').value = data.locataire?.email || '';
        document.getElementById('preview-locataire-telephone').value = data.locataire?.telephone || '';

        // Compteurs
        const compteursContainer = document.getElementById('preview-compteurs');
        const compteursCount = document.getElementById('compteurs-count');
        const noCompteurs = document.getElementById('no-compteurs');

        // Convertir l'objet compteurs en array
        let compteursArray = [];
        if (data.compteurs) {
            if (Array.isArray(data.compteurs)) {
                compteursArray = data.compteurs;
            } else {
                // C'est un objet avec les types comme clés
                for (const [type, compteur] of Object.entries(data.compteurs)) {
                    if (compteur && (compteur.numero || compteur.index)) {
                        compteursArray.push({ type, ...compteur });
                    }
                }
            }
        }

        if (compteursArray.length > 0) {
            compteursCount.textContent = compteursArray.length;
            noCompteurs.classList.add('hidden');
            
            const typeLabels = {
                'electricite': '⚡ Électricité',
                'eau_froide': '💧 Eau froide',
                'eau_chaude': '🔥 Eau chaude',
                'gaz': '🔵 Gaz',
            };
            
            let html = '<div class="grid grid-cols-1 md:grid-cols-2 gap-3">';
            compteursArray.forEach(compteur => {
                const label = typeLabels[compteur.type] || compteur.type;
                html += `
                    <div class="bg-white border border-gray-200 rounded-lg p-3">
                        <div class="font-medium text-gray-800 mb-1">${label}</div>
                        <div class="text-sm text-gray-600">
                            ${compteur.numero ? `<span class="mr-3">N° ${compteur.numero}</span>` : ''}
                            ${compteur.index ? `<span class="font-mono bg-gray-100 px-2 py-0.5 rounded">${compteur.index}</span>` : '<span class="text-gray-400">Index non renseigné</span>'}
                        </div>
                        ${compteur.commentaire ? `<div class="text-xs text-gray-500 mt-1">${compteur.commentaire}</div>` : ''}
                    </div>
                `;
            });
            html += '</div>';
            compteursContainer.innerHTML = html;
        } else {
            compteursCount.textContent = '0';
            noCompteurs.classList.remove('hidden');
            compteursContainer.innerHTML = '';
        }

        // Clés
        const clesContainer = document.getElementById('preview-cles');
        const clesCount = document.getElementById('cles-count');
        const noCles = document.getElementById('no-cles');

        if (data.cles && data.cles.length > 0) {
            const totalCles = data.cles.reduce((sum, cle) => sum + (cle.nombre || 1), 0);
            clesCount.textContent = totalCles;
            noCles.classList.add('hidden');
            
            let html = '<div class="flex flex-wrap gap-2">';
            data.cles.forEach(cle => {
                html += `
                    <div class="inline-flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2">
                        <span class="text-lg">🔑</span>
                        <span class="font-medium text-gray-800">${cle.type}</span>
                        <span class="bg-amber-100 text-amber-700 text-xs font-medium px-2 py-0.5 rounded-full">×${cle.nombre || 1}</span>
                    </div>
                `;
            });
            html += '</div>';
            clesContainer.innerHTML = html;
        } else {
            clesCount.textContent = '0';
            noCles.classList.remove('hidden');
            clesContainer.innerHTML = '';
        }

        // Pièces
        const piecesContainer = document.getElementById('preview-pieces');
        const piecesCount = document.getElementById('pieces-count');
        piecesCount.textContent = data.pieces?.length || 0;

        if (data.pieces && data.pieces.length > 0) {
            let html = '';

            data.pieces.forEach((piece, index) => {
                const elementsCount = piece.elements?.length || 0;
                const elementsWithObs = piece.elements?.filter(e => e.observations).length || 0;
                const elementsWithPhotos = piece.elements?.filter(e => e.photo_indices?.length > 0).length || 0;

                html += `
                    <div class="border border-gray-200 rounded-lg overflow-hidden ${index > 0 ? 'mt-3' : ''}">
                        <div class="bg-gray-100 px-4 py-3 flex items-center justify-between cursor-pointer hover:bg-gray-150" onclick="this.nextElementSibling.classList.toggle('hidden')">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                                <span class="font-medium text-gray-800">${piece.nom}</span>
                            </div>
                            <div class="flex items-center gap-3 text-xs">
                                <span class="text-gray-500">${elementsCount} élément(s)</span>
                                ${elementsWithObs > 0 ? `<span class="text-amber-600 font-medium">${elementsWithObs} obs.</span>` : ''}
                                ${elementsWithPhotos > 0 ? `<span class="text-green-600 font-medium">📷 ${elementsWithPhotos}</span>` : ''}
                            </div>
                        </div>
                        <div class="p-4 ${index > 0 ? 'hidden' : ''}">
                `;

                if (piece.elements && piece.elements.length > 0) {
                    html += `
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-500 border-b">
                                    <th class="pb-2 font-medium">Élément</th>
                                    <th class="pb-2 font-medium">Type</th>
                                    <th class="pb-2 font-medium">État</th>
                                    <th class="pb-2 font-medium">Observations</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    piece.elements.forEach(element => {
                        const etatLabels = {
                            'neuf': '<span class="inline-block px-2 py-0.5 text-xs rounded-full bg-green-100 text-green-700">Neuf</span>',
                            'bon_etat': '<span class="inline-block px-2 py-0.5 text-xs rounded-full bg-blue-100 text-blue-700">Bon</span>',
                            'etat_moyen': '<span class="inline-block px-2 py-0.5 text-xs rounded-full bg-amber-100 text-amber-700">Moyen</span>',
                            'mauvais_etat': '<span class="inline-block px-2 py-0.5 text-xs rounded-full bg-red-100 text-red-700">Mauvais</span>',
                        };
                        const etatLabel = etatLabels[element.etat] || `<span class="text-gray-500">${element.etat || '-'}</span>`;

                        const typeLabels = {
                            'sol': 'Sol',
                            'mur': 'Mur',
                            'plafond': 'Plafond',
                            'menuiserie': 'Menuiserie',
                            'electricite': 'Électricité',
                            'plomberie': 'Plomberie',
                            'chauffage': 'Chauffage',
                            'equipement': 'Équipement',
                            'mobilier': 'Mobilier',
                            'electromenager': 'Électroménager',
                            'autre': 'Autre',
                        };
                        const typeLabel = typeLabels[element.type] || element.type;

                        const photosBadge = element.photo_indices?.length > 0 
                            ? `<span class="ml-1 text-green-600">📷 ${element.photo_indices.join(', ')}</span>` 
                            : '';

                        const obsText = element.observations 
                            ? `<span class="text-gray-700">${element.observations}</span>${photosBadge}`
                            : `<span class="text-gray-400">-</span>${photosBadge}`;

                        html += `
                            <tr class="border-b border-gray-100">
                                <td class="py-2.5 font-medium text-gray-900">${element.nom}</td>
                                <td class="py-2.5 text-gray-500">${typeLabel}</td>
                                <td class="py-2.5">${etatLabel}</td>
                                <td class="py-2.5 text-xs max-w-xs">${obsText}</td>
                            </tr>
                        `;
                    });

                    html += `
                            </tbody>
                        </table>
                    `;
                } else {
                    html += `<p class="text-gray-400 text-center py-4">Aucun élément détecté</p>`;
                }

                html += `
                        </div>
                    </div>
                `;
            });

            // Info photos
            if (photosCount > 0) {
                html = `
                    <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                        <p class="text-green-800 text-sm font-medium flex items-center gap-2">
                            <span>📷</span>
                            <span>${photosCount} photo(s) détectée(s) dans le PDF</span>
                        </p>
                        <p class="text-green-600 text-xs mt-1">
                            Les photos seront automatiquement associées aux éléments selon les références trouvées.
                        </p>
                    </div>
                ` + html;
            }

            piecesContainer.innerHTML = html;
        } else {
            piecesContainer.innerHTML = `
                <div class="text-center py-8 text-gray-400">
                    <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <p>Aucune pièce détectée dans le document</p>
                </div>
            `;
        }
    }
});