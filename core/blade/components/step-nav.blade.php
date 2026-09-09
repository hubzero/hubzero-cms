@props([
    'steps' => [],
    'current' => 0,
])

<ul class="steps steps-horizontal w-full mb-8">
    @foreach($steps as $i => $step)
        @php
            $label = is_array($step) ? ($step['label'] ?? '') : $step;
            $url = is_array($step) ? ($step['url'] ?? null) : null;
            $isCompleted = $i < $current;
            $isCurrent = $i === $current;
            $stepClass = ($isCompleted || $isCurrent) ? 'step-primary' : '';
        @endphp
        <li class="step {{ $stepClass }}"
            @if($isCurrent) aria-current="step" @endif>
            @if($url && $isCompleted)
                <a href="{{ $url }}">{{ $label }}</a>
            @else
                {{ $label }}
            @endif
        </li>
    @endforeach
</ul>
