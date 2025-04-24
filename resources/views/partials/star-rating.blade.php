@props(['rating' => 0, 'count' => 0])

<div {{ $attributes->merge(['class' => 'flex items-center gap-2']) }}>
    {{-- étoiles --}}
    <div class="flex">
        @for ($i = 1; $i <= 5; $i++)
            @php $full = $rating >= $i; $half = !$full && $rating >= $i - .5; @endphp
            <svg xmlns="http://www.w3.org/2000/svg"
                 class="w-4 h-4 {{ $full ? 'text-primary' : ($half ? 'text-primary' : 'text-gray-300') }}"
                 viewBox="0 0 20 20" fill="currentColor">
                @if($full)
                    <path d="M9.049 2.927c.3-.921 1.603-.921…"/>
                @elseif($half)
                    <defs>
                        <linearGradient id="half{{ $i }}" x1="0" x2="1" y1="0" y2="0">
                            <stop offset="50%" stop-color="currentColor"/>
                            <stop offset="50%" stop-color="transparent"/>
                        </linearGradient>
                    </defs>
                    <path fill="url(#half{{ $i }})" d="M9.049 2.927c.3-.921 1.603-.921…"/>
                    <path fill="none" stroke="currentColor" stroke-width="1"
                          d="M9.049 2.927c.3-.921 1.603-.921…"/>
                @else
                    <path d="M9.049 2.927c.3-.921 1.603-.921…"/>
                @endif
            </svg>
        @endfor
    </div>
    {{-- compteur avis --}}
    <span class="text-xs text-gray-500">({{ $count }} avis)</span>
</div>
