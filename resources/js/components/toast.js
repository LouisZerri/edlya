class Toast {
    constructor() {
        this.container = document.getElementById('toast-container');
        this.init();
    }

    init() {
        const toastData = document.getElementById('toast-data');
        if (toastData) {
            const type = toastData.dataset.type;
            const message = toastData.dataset.message;
            this.show(message, type);
        }
    }

    show(message, type = 'info', duration = 5000) {
        if (!this.container) return;

        const toast = document.createElement('div');
        toast.className = this.getClasses(type);
        toast.innerHTML = `
            <span class="flex-1">${message}</span>
            <button type="button" class="ml-3 flex-shrink-0 text-current opacity-70 hover:opacity-100 p-1" data-dismiss="toast">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        `;

        toast.querySelector('[data-dismiss="toast"]').addEventListener('click', () => {
            this.dismiss(toast);
        });

        this.container.appendChild(toast);

        setTimeout(() => {
            this.dismiss(toast);
        }, duration);
    }

    getClasses(type) {
        const base = 'flex items-center px-3 py-2 sm:px-4 sm:py-2.5 rounded-lg shadow-lg text-sm transform transition-all duration-300 translate-x-0';

        const variants = {
            success: 'bg-green-600 text-white',
            error: 'bg-red-600 text-white',
            info: 'bg-primary-600 text-white',
        };

        return `${base} ${variants[type] || variants.info}`;
    }

    dismiss(toast) {
        toast.classList.add('opacity-0', 'translate-x-full');
        setTimeout(() => toast.remove(), 300);
    }
}

document.addEventListener('DOMContentLoaded', () => {
    window.toast = new Toast();
});