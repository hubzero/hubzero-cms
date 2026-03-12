@props([
    'title' => '',
])

<header class="page-header">
    <div class="page-header-content">
        <h1>{{ $title }}</h1>
    </div>
    @if($slot->isNotEmpty())
        <div class="page-header-actions">
            {{ $slot }}
        </div>
    @endif
</header>
