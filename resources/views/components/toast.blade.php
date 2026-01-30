<div
    id="toast-container"
    class="fixed top-4 left-4 right-4 sm:left-auto sm:right-4 sm:max-w-sm z-50 flex flex-col gap-2"
    aria-live="polite"
></div>

@if(session('success') || session('error') || session('info'))
    <div 
        id="toast-data" 
        data-type="{{ session('success') ? 'success' : (session('error') ? 'error' : 'info') }}"
        data-message="{{ session('success') ?? session('error') ?? session('info') }}"
        class="hidden"
    ></div>
@endif