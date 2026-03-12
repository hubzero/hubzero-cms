{{--
  mod_groups — admin dashboard Blade template

  Join-policy donut + visibility bar + footer stat row.

  Variables: $module, $params, $type, $visible, $hidden,
             $closed, $invite, $restricted, $open,
             $approved, $pending, $pastDay

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $base  = 'index.php?option=com_groups&controller=manage&type=' . $type;
  $total = $open + $restricted + $invite + $closed;

  $segments = [
    ['label' => Lang::txt('MOD_GROUPS_OPEN'),       'count' => $open,       'color' => 'var(--color-primary)',      'url' => $base . '&policy=open'],
    ['label' => Lang::txt('MOD_GROUPS_RESTRICTED'),  'count' => $restricted, 'color' => 'var(--color-accent)',       'url' => $base . '&policy=restricted'],
    ['label' => Lang::txt('MOD_GROUPS_INVITE'),      'count' => $invite,     'color' => 'var(--color-secondary)',    'url' => $base . '&policy=invite'],
    ['label' => Lang::txt('MOD_GROUPS_CLOSED'),      'count' => $closed,     'color' => 'var(--color-base-content)', 'url' => $base . '&policy=closed'],
  ];

  $cumulative = 0;
  foreach ($segments as &$seg) {
      $seg['pct']    = $total > 0 ? round(($seg['count'] / $total) * 100, 2) : 0;
      $seg['offset'] = round(25 - $cumulative, 2);
      $cumulative   += $seg['pct'];
  }
  unset($seg);

  $visTotal   = $visible + $hidden;
  $visiblePct = $visTotal > 0 ? round(($visible / $visTotal) * 100, 2) : 0;
  $hiddenPct  = 100 - $visiblePct;
@endphp

{{-- Donut + legend --}}
<div class="flex gap-4 items-center mb-4">

  <div class="shrink-0">
    <svg viewBox="0 0 36 36" class="w-28 h-28 block">
      <g transform="rotate(-90 18 18)">
        <circle cx="18" cy="18" r="15.9155" fill="none"
                stroke="var(--color-base-300)" stroke-width="3"/>
        @foreach ($segments as $seg)
          @if ($seg['pct'] > 0)
            <circle cx="18" cy="18" r="15.9155" fill="none"
                    stroke="{{ $seg['color'] }}" stroke-width="3"
                    stroke-dasharray="{{ $seg['pct'] }} {{ 100 - $seg['pct'] }}"
                    stroke-dashoffset="{{ $seg['offset'] }}"/>
          @endif
        @endforeach
      </g>
      <text x="18" y="16.5" text-anchor="middle" dominant-baseline="middle"
            font-size="5" font-weight="600" fill="currentColor">{{ number_format($total) }}</text>
      <text x="18" y="22" text-anchor="middle" dominant-baseline="middle"
            font-size="2.5" fill="currentColor" opacity="0.7">{{ Lang::txt('MOD_GROUPS_TOTAL') }}</text>
    </svg>
  </div>

  <div class="flex-1 min-w-0 space-y-1.5 text-xs">
    @foreach ($segments as $seg)
      <div class="flex items-center gap-1.5">
        <span class="shrink-0 rounded-sm inline-block size-2.5"
              data-style-bg="{{ $seg['color'] }}"></span>
        <a href="{{ Route::url($seg['url'], false) }}"
           class="link link-hover flex-1 truncate">{{ $seg['label'] }}</a>
        <span class="font-semibold tabular-nums shrink-0">{{ number_format($seg['count']) }}</span>
      </div>
    @endforeach
  </div>

</div>

{{-- Visibility bar --}}
<div class="rounded-full overflow-hidden flex bg-base-200 mb-1 h-[0.35rem]">
  <div class="bg-primary transition-all" data-style-width="{{ $visiblePct }}%"></div>
  <div class="bg-base-300" data-style-width="{{ $hiddenPct }}%"></div>
</div>
<div class="flex mb-4 text-xs">
  <div class="flex-1">
    <a href="{{ Route::url($base . '&discoverability=0', false) }}" class="link link-hover">
      <span class="font-semibold text-primary">{{ number_format($visible) }}</span>
      <span class="opacity-70 ml-0.5">{{ Lang::txt('MOD_GROUPS_VISIBLE') }}</span>
    </a>
  </div>
  <div class="text-right">
    <a href="{{ Route::url($base . '&discoverability=1', false) }}" class="link link-hover">
      <span class="font-semibold opacity-70">{{ number_format($hidden) }}</span>
      <span class="opacity-70 ml-0.5">{{ Lang::txt('MOD_GROUPS_HIDDEN') }}</span>
    </a>
  </div>
</div>

{{-- Footer stat row --}}
<div class="flex border-t border-base-200 pt-2 gap-1">
  <div class="flex-1 text-center">
    <div class="text-xl font-bold leading-none text-success">
      <a href="{{ Route::url($base . '&approved=1', false) }}"
         class="link link-hover text-inherit">{{ number_format($approved) }}</a>
    </div>
    <div class="text-[0.65rem] opacity-70 mt-1">{{ Lang::txt('MOD_GROUPS_PUBLISHED') }}</div>
  </div>
  <div class="w-px bg-base-200"></div>
  <div class="flex-1 text-center">
    @if ($pending > 0)
      <div class="text-xl font-bold leading-none text-accent-dark">
        <a href="{{ Route::url($base . '&approved=0', false) }}"
           class="link link-hover text-inherit">{{ number_format($pending) }}</a>
      </div>
    @else
      <div class="text-xl font-bold leading-none opacity-70">0</div>
    @endif
    <div class="text-[0.65rem] opacity-70 mt-1">{{ Lang::txt('MOD_GROUPS_PENDING') }}</div>
  </div>
  <div class="w-px bg-base-200"></div>
  <div class="flex-1 text-center">
    <div class="text-xl font-bold leading-none">
      <a href="{{ Route::url($base . '&created=pastday', false) }}"
         class="link link-hover">{{ number_format($pastDay) }}</a>
    </div>
    <div class="text-[0.65rem] opacity-70 mt-1">{{ Lang::txt('MOD_GROUPS_NEW') }}</div>
  </div>
</div>
