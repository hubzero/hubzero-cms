@props([
    'cols' => 3,
])

<div class="card-grid card-grid-{{ $cols }}">
    {{ $slot }}
</div>
