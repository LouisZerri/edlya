document.addEventListener('DOMContentLoaded', function() {
    const btnPartager = document.getElementById('btn-partager');
    const modal = document.getElementById('partage-modal');
    const modalClose = document.getElementById('partage-modal-close');
    const form = document.getElementById('partage-form');
    const emailField = document.getElementById('email-field');
    const partageError = document.getElementById('partage-error');
    const partageResult = document.getElementById('partage-result');
    const partageSubmit = document.getElementById('partage-submit');
    const partageUrl = document.getElementById('partage-url');
    const partageExpire = document.getElementById('partage-expire');
    const partageSuccessMessage = document.getElementById('partage-success-message');
    const copyUrl = document.getElementById('copy-url');
    const partageNew = document.getElementById('partage-new');

    if (!btnPartager || !modal) return;

    // Ouvrir modal
    btnPartager.addEventListener('click', function() {
        modal.classList.remove('hidden');
        resetForm();
    });

    // Fermer modal
    modalClose?.addEventListener('click', closeModal);
    modal?.addEventListener('click', function(e) {
        if (e.target === modal) closeModal();
    });

    function closeModal() {
        modal.classList.add('hidden');
    }

    function resetForm() {
        form.classList.remove('hidden');
        partageResult.classList.add('hidden');
        partageError.classList.add('hidden');
        partageSubmit.disabled = false;
        partageSubmit.textContent = 'Envoyer';
    }

    // Toggle email field
    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'email') {
                emailField.classList.remove('hidden');
                partageSubmit.textContent = 'Envoyer';
            } else {
                emailField.classList.add('hidden');
                partageSubmit.textContent = 'Générer le lien';
            }
        });
    });

    // Submit
    form?.addEventListener('submit', async function(e) {
        e.preventDefault();

        const type = document.querySelector('input[name="type"]:checked').value;
        const email = document.querySelector('input[name="email"]').value;
        const duree = document.querySelector('select[name="duree"]').value;

        if (type === 'email' && !email) {
            partageError.textContent = 'Veuillez saisir une adresse email.';
            partageError.classList.remove('hidden');
            return;
        }

        partageError.classList.add('hidden');
        partageSubmit.disabled = true;
        partageSubmit.textContent = 'Envoi en cours...';

        try {
            const edlId = window.location.pathname.split('/').filter(Boolean).pop();
            
            const response = await fetch(`/etats-des-lieux/${edlId}/partage`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ type, email, duree }),
            });

            const data = await response.json();

            if (!response.ok) {
                throw new Error(data.error || 'Une erreur est survenue');
            }

            // Afficher résultat
            form.classList.add('hidden');
            partageResult.classList.remove('hidden');
            partageSuccessMessage.textContent = data.message;
            partageExpire.textContent = data.expire_at;
            partageUrl.value = data.url;

        } catch (error) {
            partageError.textContent = error.message;
            partageError.classList.remove('hidden');
            partageSubmit.disabled = false;
            partageSubmit.textContent = type === 'email' ? 'Envoyer' : 'Générer le lien';
        }
    });

    // Copier URL
    copyUrl?.addEventListener('click', function() {
        partageUrl.select();
        document.execCommand('copy');
        
        const originalText = this.textContent;
        this.textContent = 'Copié !';
        setTimeout(() => {
            this.textContent = originalText;
        }, 2000);
    });

    // Nouveau partage
    partageNew?.addEventListener('click', resetForm);
});