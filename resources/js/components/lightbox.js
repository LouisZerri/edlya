/**
 * Enhanced Lightbox Component
 * Features: Swipe gestures, pinch-to-zoom, keyboard navigation
 */

document.addEventListener('DOMContentLoaded', function() {
    // Create lightbox modal
    const lightbox = document.createElement('div');
    lightbox.id = 'lightbox';
    lightbox.className = 'fixed inset-0 z-50 hidden bg-slate-900/95 flex items-center justify-center';
    lightbox.innerHTML = `
        <button type="button" id="lightbox-close"
            class="absolute top-4 right-4 z-10 text-white/80 hover:text-white cursor-pointer p-2 min-h-[44px] min-w-[44px] flex items-center justify-center"
            aria-label="Fermer">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
        <button type="button" id="lightbox-prev"
            class="absolute left-2 sm:left-4 z-10 text-white/80 hover:text-white cursor-pointer p-2 min-h-[44px] min-w-[44px] flex items-center justify-center hidden"
            aria-label="Image précédente">
            <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>
        </button>
        <button type="button" id="lightbox-next"
            class="absolute right-2 sm:right-16 z-10 text-white/80 hover:text-white cursor-pointer p-2 min-h-[44px] min-w-[44px] flex items-center justify-center hidden"
            aria-label="Image suivante">
            <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
            </svg>
        </button>
        <div id="lightbox-container" class="w-full h-full flex items-center justify-center overflow-hidden touch-none">
            <div id="lightbox-image-wrapper" class="relative">
                <img id="lightbox-image" src="" alt=""
                    class="max-w-[95vw] max-h-[85vh] object-contain rounded-lg select-none"
                    draggable="false">
            </div>
        </div>
        <div id="lightbox-caption" class="absolute bottom-4 left-0 right-0 text-center">
            <p class="text-white/90 text-sm px-4 py-2 bg-black/30 rounded-full inline-block"></p>
        </div>
        <div id="lightbox-counter" class="absolute top-4 left-4 text-white/70 text-sm hidden"></div>
    `;
    document.body.appendChild(lightbox);

    const container = document.getElementById('lightbox-container');
    const imageWrapper = document.getElementById('lightbox-image-wrapper');
    const lightboxImage = document.getElementById('lightbox-image');
    const lightboxCaption = document.getElementById('lightbox-caption').querySelector('p');
    const lightboxCounter = document.getElementById('lightbox-counter');
    const lightboxClose = document.getElementById('lightbox-close');
    const lightboxPrev = document.getElementById('lightbox-prev');
    const lightboxNext = document.getElementById('lightbox-next');

    let currentGallery = [];
    let currentIndex = 0;

    // Touch/gesture state
    let touchStartX = 0;
    let touchStartY = 0;
    let touchCurrentX = 0;
    let touchCurrentY = 0;
    let isDragging = false;
    let isZoomed = false;
    let currentScale = 1;
    let currentTranslateX = 0;
    let currentTranslateY = 0;
    let pinchStartDistance = 0;
    let pinchStartScale = 1;

    // Open lightbox
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

        resetZoom();
        showImage();
        lightbox.classList.remove('hidden');
        document.body.style.overflow = 'hidden';

        // Show/hide navigation buttons
        if (currentGallery.length > 1) {
            lightboxPrev.classList.remove('hidden');
            lightboxNext.classList.remove('hidden');
            lightboxCounter.classList.remove('hidden');
        } else {
            lightboxPrev.classList.add('hidden');
            lightboxNext.classList.add('hidden');
            lightboxCounter.classList.add('hidden');
        }
    });

    // Close lightbox
    lightboxClose.addEventListener('click', closeLightbox);
    container.addEventListener('click', function(e) {
        // Only close if clicking the background, not the image
        if (e.target === container && !isZoomed) {
            closeLightbox();
        }
    });

    // Navigation buttons
    lightboxPrev.addEventListener('click', function(e) {
        e.stopPropagation();
        navigatePrev();
    });

    lightboxNext.addEventListener('click', function(e) {
        e.stopPropagation();
        navigateNext();
    });

    // Keyboard navigation
    document.addEventListener('keydown', function(e) {
        if (lightbox.classList.contains('hidden')) return;

        switch (e.key) {
            case 'Escape':
                closeLightbox();
                break;
            case 'ArrowLeft':
                if (currentGallery.length > 1 && !isZoomed) navigatePrev();
                break;
            case 'ArrowRight':
                if (currentGallery.length > 1 && !isZoomed) navigateNext();
                break;
            case '+':
            case '=':
                zoomIn();
                break;
            case '-':
                zoomOut();
                break;
            case '0':
                resetZoom();
                break;
        }
    });

    // Touch events for swipe and pinch-to-zoom
    container.addEventListener('touchstart', handleTouchStart, { passive: false });
    container.addEventListener('touchmove', handleTouchMove, { passive: false });
    container.addEventListener('touchend', handleTouchEnd, { passive: true });

    // Double-tap to zoom
    let lastTapTime = 0;
    container.addEventListener('touchend', function(e) {
        const currentTime = new Date().getTime();
        const tapLength = currentTime - lastTapTime;

        if (tapLength < 300 && tapLength > 0 && e.changedTouches.length === 1) {
            e.preventDefault();
            if (isZoomed) {
                resetZoom();
            } else {
                // Zoom to 2x at tap location
                const touch = e.changedTouches[0];
                const rect = lightboxImage.getBoundingClientRect();
                const x = touch.clientX - rect.left;
                const y = touch.clientY - rect.top;
                zoomToPoint(2, x, y);
            }
        }
        lastTapTime = currentTime;
    });

    // Mouse wheel zoom
    container.addEventListener('wheel', function(e) {
        if (lightbox.classList.contains('hidden')) return;
        e.preventDefault();

        const delta = e.deltaY > 0 ? -0.1 : 0.1;
        const newScale = Math.max(1, Math.min(4, currentScale + delta));

        if (newScale === 1) {
            resetZoom();
        } else {
            const rect = lightboxImage.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            zoomToPoint(newScale, x, y);
        }
    }, { passive: false });

    function handleTouchStart(e) {
        if (e.touches.length === 1) {
            // Single touch - start drag/swipe
            touchStartX = e.touches[0].clientX;
            touchStartY = e.touches[0].clientY;
            touchCurrentX = touchStartX;
            touchCurrentY = touchStartY;
            isDragging = true;
        } else if (e.touches.length === 2) {
            // Pinch start
            pinchStartDistance = getTouchDistance(e.touches);
            pinchStartScale = currentScale;
            isDragging = false;
        }
    }

    function handleTouchMove(e) {
        if (e.touches.length === 1 && isDragging) {
            touchCurrentX = e.touches[0].clientX;
            touchCurrentY = e.touches[0].clientY;

            if (isZoomed) {
                // Pan the zoomed image
                e.preventDefault();
                const deltaX = touchCurrentX - touchStartX;
                const deltaY = touchCurrentY - touchStartY;

                updateTransform(
                    currentTranslateX + deltaX,
                    currentTranslateY + deltaY,
                    currentScale
                );

                touchStartX = touchCurrentX;
                touchStartY = touchCurrentY;
            } else {
                // Show swipe preview
                const deltaX = touchCurrentX - touchStartX;
                if (Math.abs(deltaX) > 20 && currentGallery.length > 1) {
                    e.preventDefault();
                    imageWrapper.style.transform = `translateX(${deltaX}px)`;
                    imageWrapper.style.transition = 'none';
                }
            }
        } else if (e.touches.length === 2) {
            // Pinch zoom
            e.preventDefault();
            const distance = getTouchDistance(e.touches);
            const scale = Math.max(1, Math.min(4, pinchStartScale * (distance / pinchStartDistance)));

            const centerX = (e.touches[0].clientX + e.touches[1].clientX) / 2;
            const centerY = (e.touches[0].clientY + e.touches[1].clientY) / 2;
            const rect = lightboxImage.getBoundingClientRect();

            zoomToPoint(scale, centerX - rect.left, centerY - rect.top);
        }
    }

    function handleTouchEnd(e) {
        if (!isDragging) {
            isDragging = false;
            return;
        }

        isDragging = false;

        if (!isZoomed && currentGallery.length > 1) {
            const deltaX = touchCurrentX - touchStartX;
            const threshold = window.innerWidth * 0.2; // 20% of screen width

            imageWrapper.style.transition = 'transform 0.3s ease-out';

            if (deltaX > threshold) {
                // Swipe right - previous
                navigatePrev();
            } else if (deltaX < -threshold) {
                // Swipe left - next
                navigateNext();
            } else {
                // Reset position
                imageWrapper.style.transform = '';
            }

            setTimeout(() => {
                imageWrapper.style.transition = '';
            }, 300);
        }

        touchStartX = 0;
        touchStartY = 0;
        touchCurrentX = 0;
        touchCurrentY = 0;
    }

    function getTouchDistance(touches) {
        const dx = touches[0].clientX - touches[1].clientX;
        const dy = touches[0].clientY - touches[1].clientY;
        return Math.sqrt(dx * dx + dy * dy);
    }

    function zoomToPoint(scale, x, y) {
        const prevScale = currentScale;
        currentScale = scale;
        isZoomed = scale > 1;

        // Calculate new translation to keep the point under cursor/finger
        const scaleChange = scale / prevScale;
        currentTranslateX = x - scaleChange * (x - currentTranslateX);
        currentTranslateY = y - scaleChange * (y - currentTranslateY);

        updateTransform(currentTranslateX, currentTranslateY, currentScale);
    }

    function updateTransform(x, y, scale) {
        currentTranslateX = x;
        currentTranslateY = y;
        currentScale = scale;

        lightboxImage.style.transform = `translate(${x}px, ${y}px) scale(${scale})`;
        lightboxImage.style.transformOrigin = '0 0';
    }

    function zoomIn() {
        const newScale = Math.min(4, currentScale + 0.5);
        if (newScale > 1) {
            const rect = lightboxImage.getBoundingClientRect();
            zoomToPoint(newScale, rect.width / 2, rect.height / 2);
        }
    }

    function zoomOut() {
        const newScale = Math.max(1, currentScale - 0.5);
        if (newScale === 1) {
            resetZoom();
        } else {
            const rect = lightboxImage.getBoundingClientRect();
            zoomToPoint(newScale, rect.width / 2, rect.height / 2);
        }
    }

    function resetZoom() {
        currentScale = 1;
        currentTranslateX = 0;
        currentTranslateY = 0;
        isZoomed = false;
        lightboxImage.style.transform = '';
        lightboxImage.style.transformOrigin = '';
        imageWrapper.style.transform = '';
    }

    function navigatePrev() {
        resetZoom();
        currentIndex = (currentIndex - 1 + currentGallery.length) % currentGallery.length;
        showImage();
    }

    function navigateNext() {
        resetZoom();
        currentIndex = (currentIndex + 1) % currentGallery.length;
        showImage();
    }

    function showImage() {
        const item = currentGallery[currentIndex];

        // Add loading state
        lightboxImage.style.opacity = '0.5';
        lightboxImage.src = item.src;

        lightboxImage.onload = function() {
            lightboxImage.style.opacity = '1';
        };

        // Update caption
        if (item.caption) {
            lightboxCaption.textContent = item.caption;
            lightboxCaption.parentElement.classList.remove('hidden');
        } else {
            lightboxCaption.parentElement.classList.add('hidden');
        }

        // Update counter
        if (currentGallery.length > 1) {
            lightboxCounter.textContent = `${currentIndex + 1} / ${currentGallery.length}`;
        }
    }

    function closeLightbox() {
        lightbox.classList.add('hidden');
        document.body.style.overflow = '';
        resetZoom();
    }
});
