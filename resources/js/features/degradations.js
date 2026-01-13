document.addEventListener('DOMContentLoaded', () => {
    initDegradationToggles();
    initCustomDegradations();
});

/**
 * Toggle des badges de dégradation
 */
function initDegradationToggles() {
    document.querySelectorAll('.degradation-badge').forEach(badge => {
        badge.addEventListener('click', function(e) {
            e.preventDefault();
            const checkbox = this.querySelector('input[type="checkbox"]');
            checkbox.checked = !checkbox.checked;
            updateBadgeStyle(this, checkbox.checked);
        });
    });
}

/**
 * Met à jour le style du badge
 */
function updateBadgeStyle(badge, isChecked) {
    if (isChecked) {
        badge.classList.remove('bg-slate-100', 'text-slate-600', 'border-slate-200', 'hover:bg-slate-200');
        badge.classList.add('bg-red-100', 'text-red-700', 'border-red-300');
    } else {
        badge.classList.remove('bg-red-100', 'text-red-700', 'border-red-300');
        badge.classList.add('bg-slate-100', 'text-slate-600', 'border-slate-200', 'hover:bg-slate-200');
    }
}

/**
 * Ajout de dégradations personnalisées
 */
function initCustomDegradations() {
    document.querySelectorAll('.add-custom-degradation').forEach(btn => {
        btn.addEventListener('click', function() {
            const container = this.closest('.degradations-section');
            const input = container.querySelector('.custom-degradation-input');
            const list = container.querySelector('.degradations-list');
            
            addCustomDegradation(input, list);
        });
    });

    // Touche Entrée
    document.querySelectorAll('.custom-degradation-input').forEach(input => {
        input.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const container = this.closest('.degradations-section');
                const list = container.querySelector('.degradations-list');
                addCustomDegradation(this, list);
            }
        });
    });
}

/**
 * Ajoute une dégradation personnalisée
 */
function addCustomDegradation(input, container) {
    const value = input.value.trim();
    if (!value) return;
    
    // Vérifier si déjà existant
    const existing = container.querySelector(`input[value="${value}"]`);
    if (existing) {
        input.value = '';
        return;
    }
    
    // Créer le badge
    const badge = document.createElement('label');
    badge.className = 'degradation-badge cursor-pointer inline-flex items-center px-3 py-1.5 text-xs rounded-full border transition-all bg-red-100 text-red-700 border-red-300';
    badge.innerHTML = `
        <input type="checkbox" name="degradations[]" value="${value}" class="hidden" checked>
        <span>${value}</span>
    `;
    
    badge.addEventListener('click', function(e) {
        e.preventDefault();
        const checkbox = this.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;
        updateBadgeStyle(this, checkbox.checked);
    });
    
    container.appendChild(badge);
    input.value = '';
}

export { initDegradationToggles, initCustomDegradations };