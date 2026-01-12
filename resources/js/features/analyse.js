document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('analyse-modal');
    const modalClose = document.getElementById('analyse-modal-close');
    const modalTitle = document.getElementById('analyse-modal-title');
    const formUpload = document.getElementById('analyse-form-upload');
    const formValidation = document.getElementById('analyse-form-validation');
    const inputPhoto = document.getElementById('analyse-photo');
    const inputPieceId = document.getElementById('analyse-piece-id');
    const previewContainer = document.getElementById('analyse-preview');
    const resultsContainer = document.getElementById('analyse-results');
    const loadingContainer = document.getElementById('analyse-loading');
    const errorContainer = document.getElementById('analyse-error');
    const errorMessage = document.getElementById('analyse-error-message');
    const btnAnalyser = document.getElementById('analyse-btn-analyser');
    const elementsContainer = document.getElementById('analyse-elements');
    const btnAppliquer = document.getElementById('analyse-btn-appliquer');

    let currentPhotoPath = null;

    // Ouvrir la modale
    document.querySelectorAll('[data-analyse-piece]').forEach(button => {
        button.addEventListener('click', function() {
            const pieceId = this.dataset.analysePiece;
            const pieceName = this.dataset.analysePieceName;

            inputPieceId.value = pieceId;
            modalTitle.textContent = 'Analyse IA - ' + pieceName;
            
            resetModal();
            modal.classList.remove('hidden');
        });
    });

    // Fermer la modale
    if (modalClose) {
        modalClose.addEventListener('click', closeModal);
    }

    modal?.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Preview de l'image
    inputPhoto?.addEventListener('change', function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewContainer.innerHTML = '<img src="' + e.target.result + '" class="max-h-48 rounded-lg mx-auto">';
                previewContainer.classList.remove('hidden');
            };
            reader.readAsDataURL(file);
        }
    });

    // Soumettre pour analyse
    formUpload?.addEventListener('submit', async function(e) {
        e.preventDefault();

        const file = inputPhoto.files[0];
        if (!file) return;

        showLoading();

        try {
            // D'abord upload la photo temporairement
            const formData = new FormData();
            formData.append('photo', file);
            formData.append('piece_id', inputPieceId.value);

            const uploadResponse = await fetch('/analyse/upload', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: formData,
            });

            const uploadData = await uploadResponse.json();

            if (!uploadResponse.ok) {
                throw new Error(uploadData.error || 'Erreur lors de l\'upload');
            }

            currentPhotoPath = uploadData.path;

            // Ensuite analyser
            const analyseResponse = await fetch('/analyse/photo', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    photo_path: currentPhotoPath,
                    piece_id: inputPieceId.value,
                }),
            });

            const analyseData = await analyseResponse.json();

            if (!analyseResponse.ok) {
                throw new Error(analyseData.error || 'Erreur lors de l\'analyse');
            }

            displayResults(analyseData.elements);

        } catch (error) {
            showError(error.message);
        }
    });

    // Appliquer les éléments sélectionnés
    formValidation?.addEventListener('submit', async function(e) {
        e.preventDefault();

        const checkedElements = elementsContainer.querySelectorAll('input[type="checkbox"]:checked');
        const elements = [];

        checkedElements.forEach(checkbox => {
            const row = checkbox.closest('[data-element]');
            elements.push({
                type: row.querySelector('[data-type]').value,
                nom: row.querySelector('[data-nom]').value,
                etat: row.querySelector('[data-etat]').value,
                observations: row.querySelector('[data-observations]').value,
            });
        });

        if (elements.length === 0) {
            showError('Sélectionnez au moins un élément');
            return;
        }

        btnAppliquer.disabled = true;
        btnAppliquer.textContent = 'Application...';

        try {
            const response = await fetch('/analyse/appliquer', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    piece_id: inputPieceId.value,
                    elements: elements,
                }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Erreur lors de l\'application');
            }

            // Recharger la page pour voir les nouveaux éléments
            window.location.reload();

        } catch (error) {
            showError(error.message);
            btnAppliquer.disabled = false;
            btnAppliquer.textContent = 'Appliquer les éléments sélectionnés';
        }
    });

    function resetModal() {
        formUpload.classList.remove('hidden');
        formValidation.classList.add('hidden');
        resultsContainer.classList.add('hidden');
        loadingContainer.classList.add('hidden');
        errorContainer.classList.add('hidden');
        previewContainer.classList.add('hidden');
        previewContainer.innerHTML = '';
        inputPhoto.value = '';
        elementsContainer.innerHTML = '';
        currentPhotoPath = null;
    }

    function closeModal() {
        modal.classList.add('hidden');
        resetModal();
    }

    function showLoading() {
        formUpload.classList.add('hidden');
        loadingContainer.classList.remove('hidden');
        errorContainer.classList.add('hidden');
    }

    function showError(message) {
        loadingContainer.classList.add('hidden');
        errorContainer.classList.remove('hidden');
        errorMessage.textContent = message;
        formUpload.classList.remove('hidden');
    }

    function displayResults(elements) {
        loadingContainer.classList.add('hidden');
        formUpload.classList.add('hidden');
        resultsContainer.classList.remove('hidden');
        formValidation.classList.remove('hidden');

        if (!elements || elements.length === 0) {
            elementsContainer.innerHTML = '<p class="text-slate-500 text-sm">Aucun élément détecté dans cette image.</p>';
            btnAppliquer.classList.add('hidden');
            return;
        }

        btnAppliquer.classList.remove('hidden');

        const typeLabels = {
            sol: 'Sol',
            mur: 'Mur',
            plafond: 'Plafond',
            menuiserie: 'Menuiserie',
            electricite: 'Électricité',
            plomberie: 'Plomberie',
            chauffage: 'Chauffage',
            equipement: 'Équipement',
        };

        const etatLabels = {
            neuf: 'Neuf',
            tres_bon: 'Très bon',
            bon: 'Bon',
            usage: 'Usagé',
            mauvais: 'Mauvais',
            hors_service: 'Hors service',
        };

        let html = '';

        elements.forEach((element, index) => {
            html += `
                <div data-element class="border border-slate-200 rounded-lg p-3 mb-2">
                    <div class="flex items-start gap-3">
                        <input type="checkbox" checked class="mt-1 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                        <div class="flex-1 grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Type</label>
                                <select data-type class="w-full px-2 py-1 border border-slate-300 rounded text-sm">
                                    ${Object.entries(typeLabels).map(([value, label]) => 
                                        `<option value="${value}" ${element.type === value ? 'selected' : ''}>${label}</option>`
                                    ).join('')}
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Nom</label>
                                <input type="text" data-nom value="${escapeHtml(element.nom)}" class="w-full px-2 py-1 border border-slate-300 rounded text-sm">
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">État</label>
                                <select data-etat class="w-full px-2 py-1 border border-slate-300 rounded text-sm">
                                    ${Object.entries(etatLabels).map(([value, label]) => 
                                        `<option value="${value}" ${element.etat === value ? 'selected' : ''}>${label}</option>`
                                    ).join('')}
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs text-slate-500 mb-1">Observations</label>
                                <input type="text" data-observations value="${escapeHtml(element.observations || '')}" class="w-full px-2 py-1 border border-slate-300 rounded text-sm" placeholder="Optionnel">
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        elementsContainer.innerHTML = html;
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
});