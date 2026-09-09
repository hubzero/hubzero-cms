@props([
    'options' => [],
    'active' => '',
])

<div role="tablist" {{ $attributes->merge(['class' => 'tabs tabs-border']) }}>
    @foreach($options as $url => $label)
        <a role="tab"
           class="tab {{ $url === $active ? 'tab-active' : '' }}"
           href="{{ $url }}"
           @if($url === $active) aria-selected="true" @endif>
            {!! $label !!}
        </a>
    @endforeach
</div>
