@props(['etatDesLieux'])

@php
    $total = 4; // Infos, Compteurs, Clés, Pièces
    $completed = 0;

    // Section 1: Informations générales
    if ($etatDesLieux->locataire_nom) $completed++;

    // Section 2: Compteurs
    if ($etatDesLieux->compteurs->isNotEmpty()) $completed++;

    // Section 3: Clés
    if ($etatDesLieux->cles->isNotEmpty()) $completed++;

    // Section 4: Pièces (toutes les pièces doivent avoir au moins un élément)
    if ($etatDesLieux->pieces->isNotEmpty() &&
        $etatDesLieux->pieces->every(fn($p) => $p->elements->isNotEmpty())) $completed++;

    $percentage = ($completed / $total) * 100;
@endphp

<div class="bg-white p-4 rounded-lg border border-slate-200 mb-6">
    <div class="flex items-center justify-between mb-2">
        <span class="text-sm font-medium text-slate-700">Progression</span>
        <span class="text-sm text-slate-500">{{ $completed }}/{{ $total }} sections</span>
    </div>
    <div class="w-full bg-slate-200 rounded-full h-2">
        <div class="bg-primary-600 h-2 rounded-full transition-all duration-300"
             style="width: {{ $percentage }}%"></div>
    </div>
    <div class="mt-3 grid grid-cols-4 gap-2">
        <div class="flex items-center gap-1.5 text-xs {{ $etatDesLieux->locataire_nom ? 'text-green-600' : 'text-slate-400' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $etatDesLieux->locataire_nom ? 'bg-green-500' : 'bg-slate-300' }}"></span>
            Infos
        </div>
        <div class="flex items-center gap-1.5 text-xs {{ $etatDesLieux->compteurs->isNotEmpty() ? 'text-green-600' : 'text-slate-400' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $etatDesLieux->compteurs->isNotEmpty() ? 'bg-green-500' : 'bg-slate-300' }}"></span>
            Compteurs
        </div>
        <div class="flex items-center gap-1.5 text-xs {{ $etatDesLieux->cles->isNotEmpty() ? 'text-green-600' : 'text-slate-400' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $etatDesLieux->cles->isNotEmpty() ? 'bg-green-500' : 'bg-slate-300' }}"></span>
            Clés
        </div>
        <div class="flex items-center gap-1.5 text-xs {{ $etatDesLieux->pieces->isNotEmpty() && $etatDesLieux->pieces->every(fn($p) => $p->elements->isNotEmpty()) ? 'text-green-600' : 'text-slate-400' }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $etatDesLieux->pieces->isNotEmpty() && $etatDesLieux->pieces->every(fn($p) => $p->elements->isNotEmpty()) ? 'bg-green-500' : 'bg-slate-300' }}"></span>
            Pièces
        </div>
    </div>
</div>
