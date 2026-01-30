@props(['etatDesLieux'])

{{-- Bouton flottant pour ouvrir le drawer (visible uniquement sur mobile/tablet) --}}
<button type="button"
    id="mobile-nav-toggle"
    class="lg:hidden fixed bottom-6 right-6 z-40 bg-primary-600 text-white w-14 h-14 rounded-full shadow-lg flex items-center justify-center hover:bg-primary-700 transition-all active:scale-95"
    aria-label="Ouvrir la navigation"
    aria-expanded="false"
    aria-controls="mobile-drawer">
    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>

{{-- Overlay --}}
<div id="mobile-drawer-overlay"
    class="lg:hidden fixed inset-0 bg-black/50 z-40 opacity-0 pointer-events-none transition-opacity duration-300"
    aria-hidden="true">
</div>

{{-- Drawer --}}
<div id="mobile-drawer"
    class="lg:hidden fixed inset-y-0 left-0 z-50 w-80 max-w-[85vw] bg-white shadow-xl transform -translate-x-full transition-transform duration-300 ease-out flex flex-col"
    role="dialog"
    aria-modal="true"
    aria-label="Navigation de l'état des lieux">

    {{-- Header du drawer --}}
    <div class="flex items-center justify-between p-4 border-b border-slate-200 bg-slate-50">
        <h2 class="font-semibold text-slate-800">Navigation</h2>
        <button type="button"
            id="mobile-drawer-close"
            class="p-2 -mr-2 text-slate-500 hover:text-slate-700 hover:bg-slate-100 rounded-lg transition-colors"
            aria-label="Fermer la navigation">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- Contenu du drawer --}}
    <div class="flex-1 overflow-y-auto overscroll-contain">
        {{-- Indicateur de progression --}}
        <div class="p-4 border-b border-slate-100">
            <x-progress-indicator :etatDesLieux="$etatDesLieux" />
        </div>

        {{-- Navigation rapide --}}
        <nav class="p-4" aria-label="Navigation des sections">
            <p class="text-xs text-slate-500 mb-3 font-medium uppercase tracking-wide">Sections</p>
            <div class="flex flex-col gap-1" id="mobile-pieces-nav">
                <a href="#infos-generales"
                   class="mobile-nav-link text-sm px-4 py-3 rounded-lg hover:bg-slate-100 transition-colors flex items-center gap-3 min-h-[44px]">
                    <span class="text-lg">📋</span>
                    <span>Infos générales</span>
                </a>
                <a href="#compteurs"
                   class="mobile-nav-link text-sm px-4 py-3 rounded-lg hover:bg-slate-100 transition-colors flex items-center gap-3 min-h-[44px]">
                    <span class="text-lg">⚡</span>
                    <span>Compteurs</span>
                    @if($etatDesLieux->compteurs->count() === 4)
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500 ml-auto"></span>
                    @elseif($etatDesLieux->compteurs->count() > 0)
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 ml-auto"></span>
                    @endif
                </a>
                <a href="#cles"
                   class="mobile-nav-link text-sm px-4 py-3 rounded-lg hover:bg-slate-100 transition-colors flex items-center gap-3 min-h-[44px]">
                    <span class="text-lg">🔑</span>
                    <span>Clés</span>
                    @if($etatDesLieux->cles->isNotEmpty())
                        <span class="w-2.5 h-2.5 rounded-full bg-green-500 ml-auto"></span>
                    @endif
                </a>

                @if($etatDesLieux->pieces->count() > 0)
                    <div class="border-t border-slate-200 my-3 -mx-4"></div>
                    <p class="text-xs text-slate-500 mb-2 font-medium uppercase tracking-wide">Pièces</p>

                    @foreach ($etatDesLieux->pieces as $piece)
                        @php
                            $piecePhotos = $piece->elements->flatMap(fn($e) => $e->photos);
                            $hasElements = $piece->elements->isNotEmpty();
                            $hasPhotos = $piecePhotos->isNotEmpty();
                            $navPieceStatus = match(true) {
                                $hasElements && $hasPhotos => 'bg-green-500',
                                $hasElements => 'bg-amber-500',
                                default => 'bg-slate-300',
                            };
                        @endphp
                        <a href="#piece-{{ $piece->id }}"
                           class="mobile-nav-link text-sm px-4 py-3 rounded-lg hover:bg-slate-100 transition-colors flex items-center gap-3 min-h-[44px]"
                           data-piece-nav="{{ $piece->id }}">
                            <span class="w-2.5 h-2.5 rounded-full {{ $navPieceStatus }} flex-shrink-0"></span>
                            <span class="truncate">{{ $piece->nom }}</span>
                        </a>
                    @endforeach
                @endif
            </div>
        </nav>
    </div>

    {{-- Footer du drawer avec actions rapides --}}
    <div class="p-4 border-t border-slate-200 bg-slate-50 safe-area-bottom">
        <a href="{{ route('etats-des-lieux.show', $etatDesLieux) }}"
           class="flex items-center justify-center gap-2 w-full px-4 py-3 text-sm text-slate-600 hover:bg-slate-100 rounded-lg transition-colors min-h-[44px]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour à l'état des lieux
        </a>
    </div>
</div>
