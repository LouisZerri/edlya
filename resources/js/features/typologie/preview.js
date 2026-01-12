document.addEventListener('DOMContentLoaded', function() {
    const typologieSelect = document.getElementById('typologie');
    const preview = document.getElementById('typologie-preview');
    const piecesContainer = document.getElementById('typologie-pieces');

    if (!typologieSelect) return;

    // Configuration des typologies (doit correspondre à config/typologies.php)
    const typologies = {
        'studio': ['Entrée', 'Pièce principale', 'Coin cuisine', 'Salle de bain / WC'],
        'f1': ['Entrée', 'Séjour', 'Cuisine', 'Salle de bain', 'WC'],
        'f2': ['Entrée', 'Séjour', 'Cuisine', 'Chambre', 'Salle de bain', 'WC'],
        'f3': ['Entrée', 'Séjour', 'Cuisine', 'Chambre 1', 'Chambre 2', 'Salle de bain', 'WC'],
        'f4': ['Entrée', 'Séjour', 'Cuisine', 'Chambre 1', 'Chambre 2', 'Chambre 3', 'Salle de bain', 'WC'],
        'f5': ['Entrée', 'Séjour', 'Cuisine', 'Chambre 1', 'Chambre 2', 'Chambre 3', 'Chambre 4', 'Salle de bain 1', 'Salle de bain 2', 'WC'],
        'maison_t3': ['Entrée', 'Séjour', 'Cuisine', 'Chambre 1', 'Chambre 2', 'Salle de bain', 'WC', 'Garage', 'Jardin / Extérieur'],
        'maison_t4': ['Entrée', 'Séjour', 'Cuisine', 'Chambre 1', 'Chambre 2', 'Chambre 3', 'Salle de bain', 'WC', 'Garage', 'Jardin / Extérieur'],
        'maison_t5': ['Entrée', 'Séjour', 'Cuisine', 'Chambre 1', 'Chambre 2', 'Chambre 3', 'Chambre 4', 'Salle de bain 1', 'Salle de bain 2', 'WC 1', 'WC 2', 'Garage', 'Jardin / Extérieur'],
    };

    typologieSelect.addEventListener('change', function() {
        const typologie = this.value;

        if (typologie && typologies[typologie]) {
            const pieces = typologies[typologie];
            
            piecesContainer.innerHTML = pieces.map(piece => 
                `<span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-sm rounded-full">${piece}</span>`
            ).join('');
            
            preview.classList.remove('hidden');
        } else {
            preview.classList.add('hidden');
            piecesContainer.innerHTML = '';
        }
    });

    // Trigger au chargement si une valeur est déjà sélectionnée
    if (typologieSelect.value) {
        typologieSelect.dispatchEvent(new Event('change'));
    }
});