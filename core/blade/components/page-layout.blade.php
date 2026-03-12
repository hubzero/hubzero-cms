@props([
    'sidebar' => null,
])

<section class="page-body">
    @if($sidebar)
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
