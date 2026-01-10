document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('import-container');
    if (!container) return;

    const analyzeUrl = container.dataset.analyzeUrl;
    const storeUrl = container.dataset.storeUrl;
    const csrf = container.dataset.csrf;

    const dropzone = document.getElementById('dropzone');
    const pdfInput = document.getElementById('pdf-input');
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');
    const removeFile = document.getElementById('remove-file');
    const btnAnalyze = document.getElementById('btn-analyze');

    const stepUpload = document.getElementById('step-upload');
    const stepLoading = document.getElementById('step-loading');
    const stepPreview = document.getElementById('step-preview');
    const stepError = document.getElementById('step-error');

    let selectedFile = null;
    let extractedData = null;
    let existingLogement = null;

    // Drag & Drop
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, e => {
            e.preventDefault();
            e.stopPropagation();
        });
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.add('border-indigo-400', 'bg-indigo-50');
        });
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropzone.addEventListener(eventName, () => {
            dropzone.classList.remove('border-indigo-400', 'bg-indigo-50');
        });
    });

    dropzone.addEventListener('drop', e => {
        const files = e.dataTransfer.files;
        if (files.length && files[0].type === 'application/pdf') {
            handleFile(files[0]);
        }
    });

    pdfInput.addEventListener('change', e => {
        if (e.target.files.length) {
            handleFile(e.target.files[0]);
        }
    });

    function handleFile(file) {
        selectedFile = file;
        fileName.textContent = file.name;
        fileInfo.classList.remove('hidden');
        btnAnalyze.classList.remove('hidden');
    }

    removeFile.addEventListener('click', () => {
        selectedFile = null;
        pdfInput.value = '';
        fileInfo.classList.add('hidden');
        btnAnalyze.classList.add('hidden');
    });

    btnAnalyze.addEventListener('click', async () => {
        if (!selectedFile) return;

        showStep('loading');

        const formData = new FormData();
        formData.append('pdf', selectedFile);

        try {
            const response = await fetch(analyzeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf,
                },
                body: formData,
            });

            const result = await response.json();

            if (result.success) {
                extractedData = result.data;
                existingLogement = result.logement_existant;
                populatePreview();
                showStep('preview');
            } else {
                document.getElementById('error-message').textContent = result.error;
                showStep('error');
            }
        } catch (error) {
            document.getElementById('error-message').textContent = 'Erreur de connexion';
            showStep('error');
        }
    });

    function populatePreview() {
        document.getElementById('preview-type').value = extractedData.type || 'entree';
        document.getElementById('preview-date').value = extractedData.date_realisation || '';
        document.getElementById('preview-adresse').value = extractedData.logement?.adresse || '';
        document.getElementById('preview-type-bien').value = extractedData.logement?.type_bien || 'appartement';
        document.getElementById('preview-surface').value = extractedData.logement?.surface || '';
        document.getElementById('preview-locataire-nom').value = extractedData.locataire?.nom || '';
        document.getElementById('preview-locataire-email').value = extractedData.locataire?.email || '';
        document.getElementById('preview-locataire-telephone').value = extractedData.locataire?.telephone || '';

        // Logement existant
        if (existingLogement) {
            document.getElementById('logement-existant').classList.remove('hidden');
            document.getElementById('logement-existant-nom').textContent = existingLogement.nom + ' - ' + existingLogement.adresse;
        }

        // Pièces
        const piecesContainer = document.getElementById('preview-pieces');
        piecesContainer.innerHTML = '';

        const pieces = extractedData.pieces || [];
        document.getElementById('pieces-count').textContent = pieces.length;

        pieces.forEach((piece, index) => {
            const pieceDiv = document.createElement('div');
            pieceDiv.className = 'bg-white border border-gray-200 rounded-lg overflow-hidden mb-4';

            // Header de la pièce
            const headerDiv = document.createElement('div');
            headerDiv.className = 'bg-indigo-50 border-b border-indigo-100 px-4 py-3 flex items-center justify-between';

            const pieceInput = document.createElement('input');
            pieceInput.type = 'text';
            pieceInput.value = piece.nom;
            pieceInput.dataset.piece = index;
            pieceInput.dataset.field = 'nom';
            pieceInput.style.cssText = 'background: transparent; border: none; font-weight: 600; color: #312e81; font-size: 1rem; padding: 0; flex: 1; outline: none;';

            const countSpan = document.createElement('span');
            countSpan.className = 'text-sm text-indigo-600 bg-indigo-100 px-2 py-1 rounded-full ml-3 whitespace-nowrap';
            countSpan.textContent = (piece.elements?.length || 0) + ' éléments';

            headerDiv.appendChild(pieceInput);
            headerDiv.appendChild(countSpan);
            pieceDiv.appendChild(headerDiv);

            // Liste des éléments
            if (piece.elements && piece.elements.length > 0) {
                const elementsDiv = document.createElement('div');
                elementsDiv.className = 'p-2';

                piece.elements.forEach((el, elIndex) => {
                    const elDiv = document.createElement('div');
                    elDiv.style.cssText = 'display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 8px 12px; border-radius: 6px;';
                    elDiv.className = 'hover:bg-gray-50';

                    const nameSpan = document.createElement('span');
                    nameSpan.style.cssText = 'font-size: 14px; color: #374151; flex: 1;';
                    nameSpan.textContent = el.nom;

                    const select = document.createElement('select');
                    select.dataset.piece = index;
                    select.dataset.element = elIndex;
                    select.dataset.field = 'etat';
                    select.style.cssText = 'width: auto; font-size: 14px; padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; background: white;';

                    const etats = [
                        { value: 'neuf', label: 'Neuf' },
                        { value: 'bon_etat', label: 'Bon état' },
                        { value: 'etat_moyen', label: 'État moyen' },
                        { value: 'mauvais_etat', label: 'Mauvais état' }
                    ];

                    etats.forEach(etat => {
                        const option = document.createElement('option');
                        option.value = etat.value;
                        option.textContent = etat.label;
                        option.selected = el.etat === etat.value;
                        select.appendChild(option);
                    });

                    elDiv.appendChild(nameSpan);
                    elDiv.appendChild(select);
                    elementsDiv.appendChild(elDiv);
                });

                pieceDiv.appendChild(elementsDiv);
            }

            piecesContainer.appendChild(pieceDiv);
        });

        // Écouter les changements
        piecesContainer.querySelectorAll('[data-field="etat"]').forEach(select => {
            select.addEventListener('change', e => {
                const pieceIndex = parseInt(e.target.dataset.piece);
                const elementIndex = parseInt(e.target.dataset.element);
                extractedData.pieces[pieceIndex].elements[elementIndex].etat = e.target.value;
            });
        });

        piecesContainer.querySelectorAll('[data-field="nom"]').forEach(input => {
            input.addEventListener('change', e => {
                const pieceIndex = parseInt(e.target.dataset.piece);
                extractedData.pieces[pieceIndex].nom = e.target.value;
            });
        });
    }

    document.getElementById('btn-back').addEventListener('click', () => {
        showStep('upload');
        selectedFile = null;
        pdfInput.value = '';
        fileInfo.classList.add('hidden');
        btnAnalyze.classList.add('hidden');
    });

    document.getElementById('btn-retry').addEventListener('click', () => {
        showStep('upload');
    });

    document.getElementById('btn-import').addEventListener('click', async () => {
        const data = {
            type: document.getElementById('preview-type').value,
            date_realisation: document.getElementById('preview-date').value,
            logement: {
                nom: extractedData.logement?.nom || null,
                adresse: document.getElementById('preview-adresse').value,
                type_bien: document.getElementById('preview-type-bien').value,
                surface: document.getElementById('preview-surface').value || null,
            },
            locataire: {
                nom: document.getElementById('preview-locataire-nom').value,
                email: document.getElementById('preview-locataire-email').value || null,
                telephone: document.getElementById('preview-locataire-telephone').value || null,
            },
            pieces: extractedData.pieces,
            observations_generales: extractedData.observations_generales,
        };

        const useExistingCheckbox = document.getElementById('use-existing-logement');
        const useExisting = useExistingCheckbox ? useExistingCheckbox.checked : false;
        const logementId = useExisting && existingLogement ? existingLogement.id : null;

        const btnImport = document.getElementById('btn-import');
        btnImport.disabled = true;
        btnImport.textContent = 'Création en cours...';

        try {
            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrf,
                },
                body: JSON.stringify({ data, logement_id: logementId }),
            });

            const result = await response.json();

            if (result.success) {
                window.location.href = result.redirect;
            } else {
                alert('Erreur: ' + result.error);
                btnImport.disabled = false;
                btnImport.textContent = 'Créer l\'état des lieux';
            }
        } catch (error) {
            alert('Erreur de connexion');
            btnImport.disabled = false;
            btnImport.textContent = 'Créer l\'état des lieux';
        }
    });

    function showStep(step) {
        stepUpload.classList.add('hidden');
        stepLoading.classList.add('hidden');
        stepPreview.classList.add('hidden');
        stepError.classList.add('hidden');

        document.getElementById('step-' + step).classList.remove('hidden');
    }
});