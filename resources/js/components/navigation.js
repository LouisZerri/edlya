document.addEventListener('DOMContentLoaded', function() {
    const menuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');

    if (menuButton && mobileMenu) {
        menuButton.addEventListener('click', function() {
            const isOpen = mobileMenu.classList.contains('hidden');
            
            mobileMenu.classList.toggle('hidden');
            menuButton.setAttribute('aria-expanded', isOpen);
        });

        // Fermer le menu si on clique en dehors
        document.addEventListener('click', function(event) {
            if (!menuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
                mobileMenu.classList.add('hidden');
                menuButton.setAttribute('aria-expanded', 'false');
            }
        });
    }
});