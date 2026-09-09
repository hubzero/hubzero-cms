@props([
    'title' => '',
    'count' => 0,
])

<h2 class="text-lg font-semibold mb-4">
    {{ $title }}
    <span class="text-base-content/50 font-normal text-sm">({{ $count }})</span>
</h2>
