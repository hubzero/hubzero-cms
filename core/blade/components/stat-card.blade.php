@props([
    'label' => '',
    'value' => '',
    'description' => '',
    'trend' => '',
])

<div class="stat-card">
    <div class="stat-card-label">{{ $label }}</div>
    <div class="stat-card-value {{ $trend === 'up' ? 'stat-trend-up' : ($trend === 'down' ? 'stat-trend-down' : '') }}">
        {{ $value }}
    </div>
    @if($description)
        <div class="stat-card-desc">{{ $description }}</div>
    @endif
</div>
