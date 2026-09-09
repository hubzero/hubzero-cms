@props([
    'cols' => 3,
])

<div {{ $attributes->merge(['class' => "card-grid card-grid-{$cols}"]) }}>
    {{ $slot }}
</div>
