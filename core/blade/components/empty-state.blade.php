@props([
    'title' => 'Nothing here yet',
    'message' => '',
    'icon' => '',
])

<div class="empty-state">
    <div class="empty-state-icon">
        @if($icon)
            {!! $icon !!}
        @else
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.75 7.5h16.5" />
            </svg>
        @endif
    </div>
    <h3 class="empty-state-title">{{ $title }}</h3>
    @if($message)
        <p class="empty-state-message">{{ $message }}</p>
    @endif
    @if($slot->isNotEmpty())
        <div class="empty-state-action">
            {{ $slot }}
        </div>
    @endif
</div>
