/**
 * Mobile Drawer Component
 * Provides a slide-out navigation drawer for mobile devices
 */

class MobileDrawer {
    constructor() {
        this.drawer = document.getElementById('mobile-drawer');
        this.overlay = document.getElementById('mobile-drawer-overlay');
        this.toggleBtn = document.getElementById('mobile-nav-toggle');
        this.closeBtn = document.getElementById('mobile-drawer-close');

        if (!this.drawer || !this.overlay || !this.toggleBtn) return;

        this.isOpen = false;
        this.touchStartX = 0;
        this.touchCurrentX = 0;
        this.isDragging = false;

        this.init();
    }

    init() {
        // Toggle button
        this.toggleBtn.addEventListener('click', () => this.open());

        // Close button
        if (this.closeBtn) {
            this.closeBtn.addEventListener('click', () => this.close());
        }

        // Overlay click
        this.overlay.addEventListener('click', () => this.close());

        // Navigation links - close drawer when clicked
        const navLinks = this.drawer.querySelectorAll('.mobile-nav-link');
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                this.close();
            });
        });

        // Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isOpen) {
                this.close();
            }
        });

        // Touch gestures for swipe-to-close
        this.drawer.addEventListener('touchstart', (e) => this.handleTouchStart(e), { passive: true });
        this.drawer.addEventListener('touchmove', (e) => this.handleTouchMove(e), { passive: false });
        this.drawer.addEventListener('touchend', (e) => this.handleTouchEnd(e), { passive: true });

        // Also allow swipe from left edge to open
        document.addEventListener('touchstart', (e) => this.handleEdgeSwipeStart(e), { passive: true });
        document.addEventListener('touchmove', (e) => this.handleEdgeSwipeMove(e), { passive: false });
        document.addEventListener('touchend', (e) => this.handleEdgeSwipeEnd(e), { passive: true });

        // Update active nav item on scroll
        this.setupScrollSpy();
    }

    open() {
        this.isOpen = true;
        this.drawer.classList.remove('-translate-x-full');
        this.overlay.classList.remove('opacity-0', 'pointer-events-none');
        this.toggleBtn.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';

        // Focus management
        this.closeBtn?.focus();
    }

    close() {
        this.isOpen = false;
        this.drawer.classList.add('-translate-x-full');
        this.overlay.classList.add('opacity-0', 'pointer-events-none');
        this.toggleBtn.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';

        // Return focus to toggle button
        this.toggleBtn.focus();
    }

    handleTouchStart(e) {
        this.touchStartX = e.touches[0].clientX;
        this.isDragging = true;
    }

    handleTouchMove(e) {
        if (!this.isDragging || !this.isOpen) return;

        this.touchCurrentX = e.touches[0].clientX;
        const diff = this.touchStartX - this.touchCurrentX;

        // Only allow dragging to the left (closing)
        if (diff > 0) {
            const translateX = Math.min(diff, this.drawer.offsetWidth);
            this.drawer.style.transform = `translateX(-${translateX}px)`;
            this.overlay.style.opacity = 1 - (translateX / this.drawer.offsetWidth) * 0.5;
            e.preventDefault();
        }
    }

    handleTouchEnd() {
        if (!this.isDragging) return;

        const diff = this.touchStartX - this.touchCurrentX;
        const threshold = this.drawer.offsetWidth * 0.3;

        // Reset transform
        this.drawer.style.transform = '';
        this.overlay.style.opacity = '';

        if (diff > threshold) {
            this.close();
        }

        this.isDragging = false;
        this.touchStartX = 0;
        this.touchCurrentX = 0;
    }

    // Edge swipe to open
    handleEdgeSwipeStart(e) {
        if (this.isOpen) return;
        const touch = e.touches[0];
        // Only trigger if starting from left edge (first 20px)
        if (touch.clientX < 20) {
            this.edgeSwipeStartX = touch.clientX;
            this.edgeSwipeActive = true;
        }
    }

    handleEdgeSwipeMove(e) {
        if (!this.edgeSwipeActive || this.isOpen) return;

        const touch = e.touches[0];
        const diff = touch.clientX - this.edgeSwipeStartX;

        if (diff > 50) {
            // Enough swipe to open
            this.edgeSwipeActive = false;
            this.open();
            e.preventDefault();
        }
    }

    handleEdgeSwipeEnd() {
        this.edgeSwipeActive = false;
        this.edgeSwipeStartX = 0;
    }

    setupScrollSpy() {
        const sections = document.querySelectorAll('[id^="piece-"], #infos-generales, #compteurs, #cles');
        const navLinks = this.drawer.querySelectorAll('.mobile-nav-link');

        if (sections.length === 0) return;

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const id = entry.target.id;
                    navLinks.forEach(link => {
                        const href = link.getAttribute('href');
                        if (href === `#${id}`) {
                            link.classList.add('bg-primary-50', 'text-primary-700');
                        } else {
                            link.classList.remove('bg-primary-50', 'text-primary-700');
                        }
                    });
                }
            });
        }, {
            rootMargin: '-20% 0px -70% 0px',
            threshold: 0
        });

        sections.forEach(section => observer.observe(section));
    }
}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    new MobileDrawer();
});
