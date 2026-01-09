document.addEventListener('DOMContentLoaded', function() {
    // Créer la modale lightbox
    const lightbox = document.createElement('div');
    lightbox.id = 'lightbox';
    lightbox.className = 'fixed inset-0 z-50 hidden bg-slate-900/90 flex items-center justify-center p-4';
    lightbox.innerHTML = `
        <button type="button" id="lightbox-close" class="absolute top-4 right-4 text-white hover:text-slate-300 cursor-pointer">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <button type="button" id="lightbox-prev" class="absolute left-4 text-white hover:text-slate-300 cursor-pointer hidden">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button type="button" id="lightbox-next" class="absolute right-16 text-white hover:text-slate-300 cursor-pointer hidden">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        <div class="max-w-5xl max-h-full">
            <img id="lightbox-image" src="" alt="" class="max-w-full max-h-[85vh] object-contain rounded-lg">
            <p id="lightbox-caption" class="text-white text-center mt-3 text-sm"></p>
        </div>
    `;
    document.body.appendChild(lightbox);

    const lightboxImage = document.getElementById('lightbox-image');
    const lightboxCaption = document.getElementById('lightbox-caption');
    const lightboxClose = document.getElementById('lightbox-close');
    const lightboxPrev = document.getElementById('lightbox-prev');
    const lightboxNext = document.getElementById('lightbox-next');

    let currentGallery = [];
    let currentIndex = 0;

    // Ouvrir la lightbox
    document.addEventListener('click', function(e) {
        const trigger = e.target.closest('[data-lightbox]');
        if (!trigger) return;

        e.preventDefault();

        const galleryId = trigger.dataset.lightbox;
        const allImages = document.querySelectorAll(`[data-lightbox="${galleryId}"]`);
        
        currentGallery = Array.from(allImages).map(img => ({
            src: img.dataset.src || img.href,
            caption: img.dataset.caption || ''
        }));

        currentIndex = Array.from(allImages).indexOf(trigger);
        
        showImage();
        lightbox.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Afficher/masquer les boutons de navigation
        if (currentGallery.length > 1) {
            lightboxPrev.classList.remove('hidden');
            lightboxNext.classList.remove('hidden');
        } else {
            lightboxPrev.classList.add('hidden');
            lightboxNext.classList.add('hidden');
        }
    });

    // Fermer la lightbox
    lightboxClose.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            closeLightbox();
        }
    });

    // Navigation
    lightboxPrev.addEventListener('click', function(e) {
        e.stopPropagation();
        currentIndex = (currentIndex - 1 + currentGallery.length) % currentGallery.length;
        showImage();
    });

    lightboxNext.addEventListener('click', function(e) {
        e.stopPropagation();
        currentIndex = (currentIndex + 1) % currentGallery.length;
        showImage();
    });

    // Navigation clavier
    document.addEventListener('keydown', function(e) {
        if (lightbox.classList.contains('hidden')) return;

        if (e.key === 'Escape') {
            closeLightbox();
        } else if (e.key === 'ArrowLeft' && currentGallery.length > 1) {
            currentIndex = (currentIndex - 1 + currentGallery.length) % currentGallery.length;
            showImage();
        } else if (e.key === 'ArrowRight' && currentGallery.length > 1) {
            currentIndex = (currentIndex + 1) % currentGallery.length;
            showImage();
        }
    });

    function showImage() {
        const item = currentGallery[currentIndex];
        lightboxImage.src = item.src;
        lightboxCaption.textContent = item.caption;
        
        if (currentGallery.length > 1) {
            lightboxCaption.textContent += ` (${currentIndex + 1}/${currentGallery.length})`;
        }
    }

    function closeLightbox() {
        lightbox.classList.add('hidden');
        document.body.style.overflow = '';
    }
});