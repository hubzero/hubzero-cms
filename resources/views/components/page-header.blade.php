@props(['title', 'subtitle' => null])

<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-900">{{ $title }}</h1>
    @if ($subtitle)
        <p class="mt-1 text-gray-500">{{ $subtitle }}</p>
    @endif
    {{ $slot }}
</div>
