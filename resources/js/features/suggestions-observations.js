const suggestionsByEtat = {
    neuf: [
        'Aucune remarque',
        'Parfait état',
        'Jamais utilisé',
    ],
    tres_bon: [
        'Très bon état général',
        'Quelques traces d\'usage mineures',
        'Entretien régulier visible',
    ],
    bon: [
        'Bon état général',
        'Usure normale',
        'Conforme à l\'ancienneté',
        'Légères traces d\'usage',
    ],
    usage: [
        'Usure normale correspondant à la durée de location',
        'Traces d\'usage visibles',
        'Vétusté apparente',
        'À surveiller',
    ],
    mauvais: [
        'Nécessite réparation',
        'Dégradations constatées',
        'À remplacer',
        'Travaux à prévoir',
    ],
    hors_service: [
        'Ne fonctionne plus',
        'À remplacer impérativement',
        'Hors d\'usage',
        'Remplacement nécessaire',
    ],
};

document.addEventListener('DOMContentLoaded', function() {
    // Écouter les changements d'état
    document.querySelectorAll('select[name="etat"]').forEach(select => {
        select.addEventListener('change', function() {
            const form = this.closest('form');
            const observationsInput = form?.querySelector('input[name="observations"]');
            
            if (!observationsInput) return;
            
            const etat = this.value;
            const suggestions = suggestionsByEtat[etat] || [];
            
            // Créer ou mettre à jour le dropdown de suggestions
            let dropdown = form.querySelector('.suggestions-dropdown');
            
            if (!dropdown && suggestions.length > 0) {
                dropdown = document.createElement('div');
                dropdown.className = 'suggestions-dropdown absolute z-10 mt-1 w-full bg-white border border-slate-200 rounded-lg shadow-lg max-h-40 overflow-y-auto';
                observationsInput.parentElement.classList.add('relative');
                observationsInput.parentElement.appendChild(dropdown);
            }
            
            if (dropdown) {
                if (suggestions.length === 0) {
                    dropdown.remove();
                    return;
                }
                
                dropdown.innerHTML = suggestions.map(s => `
                    <button type="button" class="suggestion-item w-full text-left px-3 py-2 text-sm text-slate-600 hover:bg-primary-50 hover:text-primary-700 transition-colors cursor-pointer">
                        ${s}
                    </button>
                `).join('');
                
                // Clic sur une suggestion
                dropdown.querySelectorAll('.suggestion-item').forEach(item => {
                    item.addEventListener('click', () => {
                        observationsInput.value = item.textContent.trim();
                        dropdown.remove();
                        observationsInput.focus();
                    });
                });
            }
        });
    });
    
    // Fermer les suggestions en cliquant ailleurs
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.suggestions-dropdown') && !e.target.matches('select[name="etat"]')) {
            document.querySelectorAll('.suggestions-dropdown').forEach(d => d.remove());
        }
    });
});