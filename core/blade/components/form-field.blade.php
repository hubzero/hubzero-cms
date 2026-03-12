@props([
    'name' => '',
    'inputId' => '',
    'label' => '',
    'type' => 'text',
    'hint' => '',
    'required' => false,
    'error' => '',
])

<div class="form-field">
    @if($label)
        <label class="form-field-label" for="{{ $inputId ?: $name }}">
            {{ $label }}
            @if($required)
                <span class="text-error">*</span>
            @endif
        </label>
    @endif

    {{ $slot }}

    @if($hint && !$error)
        <p class="form-field-hint">{{ $hint }}</p>
    @endif

    @if($error)
        <p class="form-field-hint text-error">{{ $error }}</p>
    @endif
</div>
