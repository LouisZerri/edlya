@props(['photos', 'elementId'])

@if($photos->isNotEmpty())
    <div class="flex flex-wrap gap-2 mt-2">
        @foreach($photos as $photo)
            <a href="{{ $photo->url }}" 
               data-lightbox="element-{{ $elementId }}"
               data-src="{{ $photo->url }}"
               data-caption="{{ $photo->legende ?? '' }}"
               class="block cursor-pointer">
                <img src="{{ $photo->url }}" 
                     alt="{{ $photo->legende ?? 'Photo' }}" 
                     class="w-20 h-20 object-cover rounded-lg hover:opacity-90 transition-opacity">
            </a>
        @endforeach
    </div>
@endif