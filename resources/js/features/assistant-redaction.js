document.addEventListener('DOMContentLoaded', function() {
    // Gérer les boutons d'assistant IA
    document.querySelectorAll('.btn-assistant-ia').forEach(btn => {
        btn.addEventListener('click', async function() {
            const form = this.closest('form');
            const input = form?.querySelector('input[name="observations"]');
            
            if (!input) return;
            
            const elementNom = form.querySelector('input[name="nom"]')?.value || 'Élément';
            const elementEtat = form.querySelector('select[name="etat"]')?.value || 'bon';
            const currentObs = input.value;
            
            // Récupérer les dégradations cochées
            const degradations = Array.from(form.querySelectorAll('input[name="degradations[]"]:checked') || [])
                .map(cb => cb.value);
            
            // État loading
            const originalHtml = this.innerHTML;
            this.disabled = true;
            this.innerHTML = `
                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            `;
            
            try {
                const response = await fetch('/aide/ameliorer-observation', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    },
                    body: JSON.stringify({
                        element: elementNom,
                        etat: elementEtat,
                        observation: currentObs,
                        degradations: degradations,
                    }),
                });
                
                const data = await response.json();
                
                if (data.success && data.observation) {
                    input.value = data.observation;
                    input.classList.add('ring-2', 'ring-green-300', 'bg-green-50');
                    setTimeout(() => {
                        input.classList.remove('ring-2', 'ring-green-300', 'bg-green-50');
                    }, 2000);
                }
            } catch (error) {
                console.error('Erreur assistant:', error);
            } finally {
                this.disabled = false;
                this.innerHTML = originalHtml;
            }
        });
    });

    // Boutons +/- pour les clés
    document.querySelectorAll('.cle-minus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const input = document.getElementById(`cle-nombre-${id}`);
            if (input && parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        });
    });

    document.querySelectorAll('.cle-plus').forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            const input = document.getElementById(`cle-nombre-${id}`);
            if (input && parseInt(input.value) < 99) {
                input.value = parseInt(input.value) + 1;
            }
        });
    });

    // Ajout dégradation personnalisée
    document.querySelectorAll('.add-degradation-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const text = prompt('Ajouter une dégradation :');
            if (text && text.trim()) {
                const container = this.parentElement;
                const label = document.createElement('label');
                label.className = 'degradation-badge cursor-pointer inline-flex items-center px-2.5 py-1 text-xs rounded-full border bg-red-100 text-red-700 border-red-300';
                label.innerHTML = `
                    <input type="checkbox" name="degradations[]" value="${text.trim()}" class="hidden" checked>
                    <span>${text.trim()}</span>
                `;
                container.insertBefore(label, this);
            }
        });
    });

    // Changement couleur select état
    document.querySelectorAll('select[name="etat"]').forEach(select => {
        select.addEventListener('change', function() {
            const colors = {
                'neuf': 'border-emerald-400 bg-emerald-50 text-emerald-700',
                'tres_bon': 'border-green-400 bg-green-50 text-green-700',
                'bon': 'border-blue-400 bg-blue-50 text-blue-700',
                'usage': 'border-amber-400 bg-amber-50 text-amber-700',
                'mauvais': 'border-orange-400 bg-orange-50 text-orange-700',
                'hors_service': 'border-red-400 bg-red-50 text-red-700',
            };
            
            // Reset classes
            this.className = this.className.replace(/border-\w+-\d+|bg-\w+-\d+|text-\w+-\d+/g, '');
            this.classList.add('w-36', 'px-3', 'py-2', 'border-2', 'rounded-lg', 'text-sm', 'font-medium', 'focus:ring-2', 'focus:ring-primary-100', 'outline-none');
            
            // Add new colors
            const newColors = colors[this.value] || 'border-slate-300 bg-white';
            newColors.split(' ').forEach(c => this.classList.add(c));
        });
    });
});