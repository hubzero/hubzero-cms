@props([
    'heading' => '',
])

<fieldset class="form-section">
    @if($heading)
        <legend class="form-section-heading">{{ $heading }}</legend>
    @endif

    <div class="form-section-body">
        {{ $slot }}
    </div>
</fieldset>
