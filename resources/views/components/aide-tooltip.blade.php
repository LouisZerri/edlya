@props(['texte', 'position' => 'top'])

<div class="relative inline-flex items-center group">
    <button type="button" class="ml-1 text-slate-400 hover:text-primary-600 transition-colors cursor-help" tabindex="-1">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
    </button>
    <div class="absolute {{ $position === 'top' ? 'bottom-full mb-2' : ($position === 'bottom' ? 'top-full mt-2' : ($position === 'left' ? 'right-full mr-2' : 'left-full ml-2')) }} left-1/2 -translate-x-1/2 z-50 hidden group-hover:block w-64">
        <div class="bg-slate-800 text-white text-xs rounded-lg px-3 py-2 shadow-lg">
            {{ $texte }}
            <div class="absolute {{ $position === 'top' ? 'top-full' : ($position === 'bottom' ? 'bottom-full' : '') }} left-1/2 -translate-x-1/2 border-4 border-transparent {{ $position === 'top' ? 'border-t-slate-800' : ($position === 'bottom' ? 'border-b-slate-800' : '') }}"></div>
        </div>
    </div>
</div>