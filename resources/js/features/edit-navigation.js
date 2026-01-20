/**
 * Edit Navigation - Sticky navigation with active highlighting
 */
document.addEventListener('DOMContentLoaded', function() {
    const navLinks = document.querySelectorAll('.nav-link');
    if (navLinks.length === 0) return;

    // Get all main sections (not the accordion content divs)
    const sections = document.querySelectorAll('#infos-generales, #compteurs, #cles, [id^="piece-"]:not([id^="piece-content-"])');
    if (sections.length === 0) return;

    // Get absolute top position of an element
    function getAbsoluteTop(element) {
        const rect = element.getBoundingClientRect();
        return rect.top + window.scrollY;
    }

    // Track which section is currently active based on scroll position
    function updateActiveNav() {
        const scrollPosition = window.scrollY + 120; // Offset for header

        // Build array of sections with their positions
        const sectionPositions = [];
        sections.forEach(section => {
            const top = getAbsoluteTop(section);
            sectionPositions.push({
                section: section,
                top: top,
                bottom: top + section.offsetHeight
            });
        });

        // Sort by top position
        sectionPositions.sort((a, b) => a.top - b.top);

        // Find current section
        let currentSection = null;

        // First, check if we're inside any section
        for (const item of sectionPositions) {
            if (scrollPosition >= item.top && scrollPosition < item.bottom) {
                currentSection = item.section;
                break;
            }
        }

        // If not inside any section, find the last section we've scrolled past
        if (!currentSection) {
            for (let i = sectionPositions.length - 1; i >= 0; i--) {
                if (scrollPosition >= sectionPositions[i].top) {
                    currentSection = sectionPositions[i].section;
                    break;
                }
            }
        }

        // Update nav links
        navLinks.forEach(link => {
            link.classList.remove('bg-primary-50', 'text-primary-600', 'font-medium');
        });

        if (currentSection) {
            const activeLink = document.querySelector(`.nav-link[href="#${currentSection.id}"]`);
            if (activeLink) {
                activeLink.classList.add('bg-primary-50', 'text-primary-600', 'font-medium');
            }
        }
    }

    // Throttle scroll events for performance
    let scrollTimeout;
    window.addEventListener('scroll', function() {
        if (scrollTimeout) {
            window.cancelAnimationFrame(scrollTimeout);
        }
        scrollTimeout = window.requestAnimationFrame(updateActiveNav);
    });

    // Initial update
    updateActiveNav();

    // Helper function to open an accordion
    function openAccordion(contentId) {
        const content = document.getElementById(contentId);
        if (!content) return false;

        const isHidden = content.classList.contains('hidden');
        if (isHidden) {
            // Find the toggle button and update its state
            const toggleBtn = document.querySelector(`[data-accordion-toggle="${contentId}"]`);
            if (toggleBtn) {
                const icon = toggleBtn.querySelector('[data-accordion-icon]');
                content.classList.remove('hidden');
                icon?.classList.add('rotate-180');
                toggleBtn.setAttribute('aria-expanded', 'true');
            }
            return true; // Accordion was opened
        }
        return false; // Accordion was already open
    }

    // Smooth scroll behavior for nav links
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            if (href && href.startsWith('#')) {
                e.preventDefault();
                const targetId = href.substring(1);
                const target = document.getElementById(targetId);

                if (target) {
                    // Determine which accordion content to open
                    let accordionContentId = null;
                    let accordionWasOpened = false;

                    if (targetId === 'compteurs') {
                        accordionContentId = 'compteurs-content';
                    } else if (targetId === 'cles') {
                        accordionContentId = 'cles-content';
                    } else if (targetId.startsWith('piece-') && !targetId.startsWith('piece-content-')) {
                        // Extract the piece ID from "piece-123" format
                        const pieceId = targetId.substring(6); // Remove "piece-" prefix
                        accordionContentId = 'piece-content-' + pieceId;
                    }

                    // Open the accordion if needed
                    if (accordionContentId) {
                        accordionWasOpened = openAccordion(accordionContentId);
                    }

                    // Scroll to target - wait longer if accordion was just opened
                    const scrollDelay = accordionWasOpened ? 100 : 0;
                    setTimeout(() => {
                        const targetRect = target.getBoundingClientRect();
                        const offsetTop = window.pageYOffset + targetRect.top - 24;

                        window.scrollTo({
                            top: offsetTop,
                            behavior: 'smooth'
                        });
                    }, scrollDelay);
                }
            }
        });
    });
});
