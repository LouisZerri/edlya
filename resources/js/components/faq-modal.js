document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('faq-modal');
    const searchInput = document.getElementById('faq-search');
    const faqContent = document.getElementById('faq-content');
    const noResults = document.getElementById('faq-no-results');
    
    if (!modal) return;
    
    // Ouvrir le modal
    document.querySelectorAll('[data-faq-open]').forEach(btn => {
        btn.addEventListener('click', () => {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            searchInput?.focus();
        });
    });
    
    // Fermer le modal
    document.querySelectorAll('[data-faq-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        });
    });
    
    // Fermer avec Escape
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            modal.classList.add('hidden');
            document.body.style.overflow = '';
        }
    });
    
    // Toggle questions
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const answer = question.nextElementSibling;
            const isOpen = !answer.classList.contains('hidden');
            
            // Fermer toutes les autres
            document.querySelectorAll('.faq-answer').forEach(a => a.classList.add('hidden'));
            document.querySelectorAll('.faq-question').forEach(q => q.classList.remove('bg-primary-50', 'text-primary-700'));
            
            // Toggle celle-ci
            if (!isOpen) {
                answer.classList.remove('hidden');
                question.classList.add('bg-primary-50', 'text-primary-700');
            }
        });
    });
    
    // Recherche
    searchInput?.addEventListener('input', (e) => {
        const query = e.target.value.toLowerCase().trim();
        let hasResults = false;
        
        document.querySelectorAll('.faq-item').forEach(item => {
            const keywords = item.dataset.keywords || '';
            const questionText = item.querySelector('.faq-question')?.textContent.toLowerCase() || '';
            const answerText = item.querySelector('.faq-answer')?.textContent.toLowerCase() || '';
            
            const matches = query === '' || 
                keywords.includes(query) || 
                questionText.includes(query) || 
                answerText.includes(query);
            
            item.style.display = matches ? '' : 'none';
            if (matches) hasResults = true;
        });
        
        // Afficher/masquer catégories vides
        document.querySelectorAll('.faq-category').forEach(cat => {
            const visibleItems = cat.querySelectorAll('.faq-item[style=""], .faq-item:not([style])');
            cat.style.display = visibleItems.length > 0 ? '' : 'none';
        });
        
        // Aucun résultat
        if (faqContent && noResults) {
            faqContent.style.display = hasResults ? '' : 'none';
            noResults.classList.toggle('hidden', hasResults);
        }
    });
});