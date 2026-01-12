document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('[data-accordion-toggle]').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.accordionToggle;
            const content = document.getElementById(targetId);
            const icon = this.querySelector('[data-accordion-icon]');

            if (content.classList.contains('hidden')) {
                content.classList.remove('hidden');
                icon?.classList.add('rotate-180');
                this.setAttribute('aria-expanded', 'true');
            } else {
                content.classList.add('hidden');
                icon?.classList.remove('rotate-180');
                this.setAttribute('aria-expanded', 'false');
            }
        });
    });
});