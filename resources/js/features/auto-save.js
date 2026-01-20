/**
 * Auto-Save - Automatic form submission with debouncing and visual feedback
 */
class AutoSave {
    constructor() {
        this.debounceTimers = new Map();
        this.init();
    }

    init() {
        // Observer les formulaires avec data-auto-save
        document.querySelectorAll('form[data-auto-save]').forEach(form => {
            this.setupForm(form);
        });
    }

    setupForm(form) {
        // Observer les changements sur tous les inputs
        form.querySelectorAll('input, select, textarea').forEach(input => {
            // Pour les checkboxes (dégradations), utiliser change
            if (input.type === 'checkbox') {
                input.addEventListener('change', () => this.scheduleSubmit(form));
            }
            // Pour les autres inputs, utiliser input (plus réactif) et change (fallback)
            else {
                input.addEventListener('input', () => this.scheduleSubmit(form));
                input.addEventListener('change', () => this.scheduleSubmit(form));
            }
        });

        // Empêcher la soumission classique (fallback si JS désactivé)
        form.addEventListener('submit', (e) => {
            // Ne pas empêcher si c'est une soumission explicite (bouton avec formnovalidate par exemple)
            if (e.submitter && e.submitter.hasAttribute('data-explicit-submit')) {
                return;
            }
            e.preventDefault();
            this.submitForm(form);
        });
    }

    scheduleSubmit(form) {
        const formId = form.id || form.action;

        // Annuler le timer précédent
        if (this.debounceTimers.has(formId)) {
            clearTimeout(this.debounceTimers.get(formId));
        }

        // Afficher l'indicateur "en cours"
        this.showSavingIndicator(form);

        // Planifier la sauvegarde après 1s d'inactivité
        this.debounceTimers.set(formId, setTimeout(() => {
            this.submitForm(form);
        }, 1000));
    }

    async submitForm(form) {
        const formData = new FormData(form);

        // Ajouter les dégradations non cochées comme tableau vide si aucune n'est cochée
        const hasDegradations = form.querySelector('input[name="degradations[]"]');
        if (hasDegradations) {
            const checkedDegradations = form.querySelectorAll('input[name="degradations[]"]:checked');
            if (checkedDegradations.length === 0) {
                // Supprimer les anciennes valeurs et ajouter un tableau vide
                formData.delete('degradations[]');
            }
        }

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            if (response.ok) {
                const data = await response.json();
                this.showSavedIndicator(form);

                // Toast discret
                if (window.toast) {
                    window.toast.show('Modifications enregistrées', 'success', 2000);
                }
            } else {
                const errorData = await response.json().catch(() => ({}));
                this.showErrorIndicator(form);

                if (window.toast) {
                    window.toast.show(errorData.message || 'Erreur lors de l\'enregistrement', 'error');
                }
            }
        } catch (error) {
            console.error('Auto-save error:', error);
            this.showErrorIndicator(form);

            if (window.toast) {
                window.toast.show('Erreur réseau', 'error');
            }
        }
    }

    showSavingIndicator(form) {
        const indicator = form.querySelector('.save-indicator');
        if (indicator) {
            indicator.innerHTML = `
                <span class="flex items-center gap-1.5 text-amber-600">
                    <svg class="w-3 h-3 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Enregistrement...
                </span>
            `;
        }
    }

    showSavedIndicator(form) {
        const indicator = form.querySelector('.save-indicator');
        if (indicator) {
            indicator.innerHTML = `
                <span class="flex items-center gap-1 text-green-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Enregistré
                </span>
            `;
            setTimeout(() => {
                indicator.innerHTML = '';
            }, 3000);
        }
    }

    showErrorIndicator(form) {
        const indicator = form.querySelector('.save-indicator');
        if (indicator) {
            indicator.innerHTML = `
                <span class="flex items-center gap-1 text-red-600">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Erreur
                </span>
            `;
        }
    }
}

// Initialiser
document.addEventListener('DOMContentLoaded', () => {
    new AutoSave();
});
