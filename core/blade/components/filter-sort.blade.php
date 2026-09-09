@props([
    'action' => '',
    'selects' => [],
])

<form method="get"
      action="{{ $action }}"
      class="flex items-center gap-2 ml-auto"
      data-submit-on-change>
    {{-- Hidden inputs to preserve filter state --}}
    {{ $slot }}

    @foreach($selects as $select)
        <label for="{{ $select['id'] }}" class="text-sm text-muted-foreground">
            {{ $select['label'] }}:
        </label>
        <select id="{{ $select['id'] }}"
                name="{{ $select['name'] }}"
                class="select select-bordered select-sm">
            @foreach($select['options'] as $val => $label)
                <option value="{{ $val }}"
                    {{ ($select['value'] ?? '') == $val ? 'selected' : '' }}>
                    {{ $label }}
                </option>
            @endforeach
        </select>
    @endforeach

    <noscript><button class="btn btn-sm" type="submit">Go</button></noscript>
</form>
