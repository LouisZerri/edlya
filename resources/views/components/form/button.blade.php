@php
    $classes = match($variant) {
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700',
        'secondary' => 'bg-slate-200 text-slate-700 hover:bg-slate-300',
        'danger' => 'bg-red-600 text-white hover:bg-red-700',
        default => 'bg-primary-600 text-white hover:bg-primary-700',
    };
@endphp

<button 
    type="{{ $type }}"
    {{ $attributes->merge(['class' => 'w-full py-2 px-4 rounded-lg font-medium transition-colors cursor-pointer ' . $classes]) }}
>
    {{ $slot }}
</button>