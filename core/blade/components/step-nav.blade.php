@props([
    'steps' => [],
    'current' => 0,
])

<ul class="step-nav">
    @foreach($steps as $i => $step)
        @php
            $stepClass = $i < $current ? 'step-completed' : ($i === $current ? 'step-current' : '');
        @endphp
        <li class="step-nav-item {{ $stepClass }}">
            @if(is_array($step) && isset($step['url']) && $i < $current)
                <a href="{{ $step['url'] }}">{{ $step['label'] ?? $step }}</a>
            @else
                {{ is_array($step) ? ($step['label'] ?? '') : $step }}
            @endif
        </li>
    @endforeach
</ul>
