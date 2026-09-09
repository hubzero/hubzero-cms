@props([
    'title' => '',
    'bodyClass' => '',
])

@php
$hasActions = isset($actions) && $actions->isNotEmpty();
$hasTabs = isset($tabs) && $tabs->isNotEmpty();
$showHeader = $title !== '' || $hasActions || $hasTabs;
@endphp

@if ($showHeader)
<header class="page-header">
    <div class="page-header-content">
        <h1>{{ $title }}</h1>
    </div>
    @if($hasActions)
        <div class="page-header-actions">
            {{ $actions }}
        </div>
    @endif
    @if($hasTabs)
        <nav>
            {{ $tabs }}
        </nav>
    @endif
</header>
@endif

<section class="{{ trim('page-body ' . $bodyClass) }}">
    @if(isset($sidebar) && $sidebar->isNotEmpty())
        <div class="page-layout">
            <div class="page-main">
                {{ $slot }}
            </div>
            <aside class="page-sidebar">
                {{ $sidebar }}
            </aside>
        </div>
    @else
        {{ $slot }}
    @endif
</section>
