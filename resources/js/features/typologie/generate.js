document.addEventListener('DOMContentLoaded', function() {
    const typologieSelect = document.getElementById('typologie-select');
    const btnGenerer = document.getElementById('btn-generer-pieces');
    const remplacerCheckbox = document.getElementById('remplacer-pieces');

    if (!typologieSelect || !btnGenerer) return;

    const generateUrl = btnGenerer.dataset.url;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    typologieSelect.addEventListener('change', function() {
        btnGenerer.disabled = !this.value;
    });

    btnGenerer.addEventListener('click', async function() {
        const typologie = typologieSelect.value;
        if (!typologie) return;

        const remplacer = remplacerCheckbox?.checked || false;

        if (remplacer && !confirm('Êtes-vous sûr de vouloir supprimer toutes les pièces existantes ?')) {
            return;
        }

        btnGenerer.disabled = true;
        btnGenerer.textContent = 'Génération...';

        try {
            const response = await fetch(generateUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({
                    typologie: typologie,
                    remplacer: remplacer,
                }),
            });

            const result = await response.json();

            if (result.success) {
                window.location.reload();
            } else {
                alert('Erreur: ' + result.error);
                btnGenerer.disabled = false;
                btnGenerer.textContent = 'Générer les pièces';
            }
        } catch (error) {
            alert('Erreur: ' + error.message);
            btnGenerer.disabled = false;
            btnGenerer.textContent = 'Générer les pièces';
        }
    });
});