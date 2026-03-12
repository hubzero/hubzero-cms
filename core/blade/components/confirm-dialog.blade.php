@props([
    'title' => 'Are you sure?',
    'description' => 'This action cannot be undone.',
    'action' => '',
    'method' => 'post',
    'confirmLabel' => 'Delete',
    'cancelUrl' => '',
    'cancelLabel' => 'Cancel',
    'error' => '',
])

<section class="confirm-delete" role="alertdialog"
         aria-labelledby="confirm-title" aria-describedby="confirm-desc">
    <div class="confirm-card">
        <div class="confirm-icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-12 h-12">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.814-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
            </svg>
        </div>

        <h2 class="confirm-title" id="confirm-title">{{ $title }}</h2>
        <p class="confirm-desc" id="confirm-desc">{{ $description }}</p>

        @if($slot->isNotEmpty())
            <div class="confirm-impact">
                {{ $slot }}
            </div>
        @endif

        @if($error)
            <div class="alert alert-error" role="alert">{{ $error }}</div>
        @endif

        <form method="{{ $method }}" action="{{ $action }}">
            <div class="confirm-actions">
                <button class="btn btn-danger" type="submit">{{ $confirmLabel }}</button>
                @if($cancelUrl)
                    <a class="btn btn-ghost" href="{{ $cancelUrl }}">{{ $cancelLabel }}</a>
                @endif
            </div>
            {{ $hiddenFields ?? '' }}
        </form>
    </div>
</section>
