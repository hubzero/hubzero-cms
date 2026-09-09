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
    @if($type === 'checkbox')
        <label class="label cursor-pointer justify-start gap-2"
               for="{{ $inputId ?: $name }}">
            {{ $slot }}
            @if($label)
                <span class="label-text">
                    {{ $label }}
                    @if($required)
                        <span class="text-error">*</span>
                    @endif
                </span>
            @endif
        </label>
    @else
        @if($label)
            <label class="form-field-label" for="{{ $inputId ?: $name }}">
                {{ $label }}
                @if($required)
                    <span class="text-error">*</span>
                @endif
            </label>
        @endif

        {{ $slot }}
    @endif

    @if($hint && !$error)
        <p class="form-field-hint">{{ $hint }}</p>
    @endif

    @if($error)
        <p class="form-field-hint text-error">{{ $error }}</p>
    @endif
</div>
