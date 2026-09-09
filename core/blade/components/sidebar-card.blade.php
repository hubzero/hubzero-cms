@props([
    'title' => '',
])

<div {{ $attributes->merge(['class' => 'card bg-base-100 shadow-sm']) }}>
    <div class="card-body">
        @if($title)
            <h3 class="card-title text-sm">{{ $title }}</h3>
        @endif
        {{ $slot }}
    </div>
</div>
