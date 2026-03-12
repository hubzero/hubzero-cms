@props([
    'title' => '',
    'bodyClass' => '',
])

<header class="page-header">
    <div class="page-header-content">
        <h1>{{ $title }}</h1>
    </div>
    @if(isset($actions) && $actions->isNotEmpty())
        <div class="page-header-actions">
            {{ $actions }}
        </div>
    @endif
    @if(isset($tabs) && $tabs->isNotEmpty())
        <nav>
            {{ $tabs }}
        </nav>
    @endif
</header>

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
