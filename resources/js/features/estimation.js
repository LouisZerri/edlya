document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('lignes-container');
    const template = document.getElementById('ligne-template');
    const addBtn = document.getElementById('add-ligne');
    const totalHT = document.getElementById('total-ht');
    const totalTVA = document.getElementById('total-tva');
    const totalTTC = document.getElementById('total-ttc');

    // Modal IA
    const modal = document.getElementById('ia-result-modal');
    const modalClose = document.getElementById('ia-modal-close');
    const iaLoading = document.getElementById('ia-loading');
    const iaResult = document.getElementById('ia-result');
    const iaError = document.getElementById('ia-error');
    const iaErrorMessage = document.getElementById('ia-error-message');
    const iaCommentaire = document.getElementById('ia-commentaire');
    const iaDegradations = document.getElementById('ia-degradations');
    const iaApply = document.getElementById('ia-apply');
    const iaCancel = document.getElementById('ia-cancel');

    if (!container || !template) return;

    let ligneIndex = 0;
    let currentPiece = '';
    let currentAnalyse = null;

    // Ajouter une ligne
    function addLigne(data = {}) {
        const clone = template.content.cloneNode(true);
        const html = clone.querySelector('.ligne-devis').outerHTML.replace(/INDEX/g, ligneIndex);
        
        container.insertAdjacentHTML('beforeend', html);
        
        const newLigne = container.lastElementChild;
        
        // Pré-remplir si data
        if (data.piece) {
            newLigne.querySelector('select[name*="[piece]"]').value = data.piece;
        }
        if (data.description) {
            newLigne.querySelector('input[name*="[description]"]').value = data.description;
        }
        if (data.quantite) {
            newLigne.querySelector('input[name*="[quantite]"]').value = data.quantite;
        }
        if (data.unite) {
            newLigne.querySelector('select[name*="[unite]"]').value = data.unite;
        }
        if (data.prix) {
            newLigne.querySelector('input[name*="[prix_unitaire]"]').value = data.prix;
        }

        // Events
        newLigne.querySelector('.remove-ligne').addEventListener('click', function() {
            newLigne.remove();
            updateTotals();
        });

        newLigne.querySelector('.ligne-quantite').addEventListener('input', updateTotals);
        newLigne.querySelector('.ligne-prix').addEventListener('input', updateTotals);

        ligneIndex++;
        updateTotals();
    }

    // Calculer les totaux
    function updateTotals() {
        let ht = 0;

        container.querySelectorAll('.ligne-devis').forEach(ligne => {
            const qte = parseFloat(ligne.querySelector('.ligne-quantite').value) || 0;
            const prix = parseFloat(ligne.querySelector('.ligne-prix').value) || 0;
            const total = qte * prix;

            ligne.querySelector('.ligne-total').textContent = formatCurrency(total);
            ht += total;
        });

        const tva = ht * 0.20;
        const ttc = ht + tva;

        totalHT.textContent = formatCurrency(ht);
        totalTVA.textContent = formatCurrency(tva);
        totalTTC.textContent = formatCurrency(ttc);
    }

    function formatCurrency(value) {
        return value.toLocaleString('fr-FR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';
    }

    // Ajouter ligne vide
    addBtn?.addEventListener('click', function() {
        addLigne();
    });

    // Suggestions
    document.querySelectorAll('.suggestion-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            addLigne({
                description: this.dataset.nom + (this.dataset.description ? ' - ' + this.dataset.description : ''),
                unite: this.dataset.unite,
                prix: this.dataset.prix,
                quantite: 1,
            });
        });
    });

    // Analyse IA des dégradations
    document.querySelectorAll('.analyse-ia-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const item = this.closest('.degradation-item');
            const photoPath = item.dataset.photoPath;
            
            if (!photoPath) {
                alert('Aucune photo disponible pour cette dégradation.');
                return;
            }

            currentPiece = item.dataset.piece;
            
            // Ouvrir modal et afficher loading
            modal.classList.remove('hidden');
            iaLoading.classList.remove('hidden');
            iaResult.classList.add('hidden');
            iaError.classList.add('hidden');

            try {
                const response = await fetch('/analyse/degradation-path', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        photo_path: photoPath,
                        element_nom: item.dataset.elementNom,
                        element_type: item.dataset.elementType,
                        etat_entree: item.dataset.etatEntree,
                        etat_sortie: item.dataset.etatSortie,
                        observations: item.dataset.observations || '',
                    }),
                });

                const data = await response.json();

                if (!response.ok) {
                    throw new Error(data.error || 'Erreur lors de l\'analyse');
                }

                currentAnalyse = data.analyse;
                displayAnalyseResult(data.analyse);

            } catch (error) {
                iaLoading.classList.add('hidden');
                iaError.classList.remove('hidden');
                iaErrorMessage.textContent = error.message;
            }
        });
    });

    function displayAnalyseResult(analyse) {
        iaLoading.classList.add('hidden');
        iaResult.classList.remove('hidden');

        // Commentaire général
        if (analyse.commentaire_general) {
            iaCommentaire.classList.remove('hidden');
            iaCommentaire.querySelector('p').textContent = analyse.commentaire_general;
        } else {
            iaCommentaire.classList.add('hidden');
        }

        // Dégradations détectées
        iaDegradations.innerHTML = '';

        if (analyse.degradations && analyse.degradations.length > 0) {
            analyse.degradations.forEach((deg, index) => {
                const graviteColors = {
                    'legere': 'bg-yellow-100 text-yellow-800',
                    'moyenne': 'bg-orange-100 text-orange-800',
                    'importante': 'bg-red-100 text-red-800',
                };
                const graviteColor = graviteColors[deg.gravite] || 'bg-slate-100 text-slate-800';
                const uniteLabel = deg.unite === 'm2' ? 'm²' : deg.unite;

                iaDegradations.innerHTML += `
                    <div class="ia-degradation border border-slate-200 rounded-lg p-4 bg-white" data-index="${index}">
                        <div class="flex items-start gap-3">
                            <input type="checkbox" checked class="mt-1 rounded border-slate-300 text-primary-600 focus:ring-primary-500">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="font-medium text-slate-800">${deg.description}</span>
                                    <span class="text-xs px-2 py-0.5 rounded-full ${graviteColor}">${deg.gravite}</span>
                                </div>
                                <p class="text-sm text-slate-600 mb-2">${deg.reparation}</p>
                                <div class="flex items-center gap-4 text-sm">
                                    <div>
                                        <label class="text-xs text-slate-500">Quantité</label>
                                        <input type="number" value="${deg.quantite_estimee}" step="0.01" min="0" class="ia-qte w-20 px-2 py-1 border border-slate-300 rounded text-sm">
                                        <span class="text-slate-500">${uniteLabel}</span>
                                    </div>
                                    <div>
                                        <label class="text-xs text-slate-500">Prix unitaire</label>
                                        <input type="number" value="${deg.prix_unitaire_estime}" step="0.01" min="0" class="ia-prix w-20 px-2 py-1 border border-slate-300 rounded text-sm">
                                        <span class="text-slate-500">€</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            iaDegradations.innerHTML = '<p class="text-slate-500 text-center py-4">Aucune dégradation détectée par l\'IA.</p>';
        }
    }

    // Fermer modal
    modalClose?.addEventListener('click', closeModal);
    iaCancel?.addEventListener('click', closeModal);
    modal?.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    function closeModal() {
        modal.classList.add('hidden');
        currentAnalyse = null;
    }

    // Appliquer les résultats IA au devis
    iaApply?.addEventListener('click', function() {
        const checkedItems = iaDegradations.querySelectorAll('.ia-degradation input[type="checkbox"]:checked');
        
        checkedItems.forEach(checkbox => {
            const item = checkbox.closest('.ia-degradation');
            const index = parseInt(item.dataset.index);
            const deg = currentAnalyse.degradations[index];
            
            const qte = item.querySelector('.ia-qte').value;
            const prix = item.querySelector('.ia-prix').value;

            addLigne({
                piece: currentPiece,
                description: deg.reparation,
                quantite: qte,
                unite: deg.unite,
                prix: prix,
            });
        });

        closeModal();
    });

    // Ajouter une ligne vide au départ
    addLigne();
});