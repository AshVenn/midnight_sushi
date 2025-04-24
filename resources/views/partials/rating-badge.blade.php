@props(['rating' => 0, 'count' => 0])

<div class="flex items-center gap-2">
    {{-- étoiles (taille 4) --}}
    <x-rating :value="$rating" size="4"/>
    <span class="text-sm text-gray-600">({{ $count }} avis)</span>
</div>
