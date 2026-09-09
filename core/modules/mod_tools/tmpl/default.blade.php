{{--
  mod_tools — admin dashboard tool status widget

  Shows tool pipeline status as an SVG donut chart with legend.

  Variables: $module, $params, $total, $registered, $created,
             $uploaded, $installed, $updated, $approved,
             $published, $retired, $abandoned

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $stats = [
    ['label' => Lang::txt('MOD_TOOLS_COL_PUBLISHED'),  'count' => $published,  'color' => 'var(--color-success)',    'status' => 7],
    ['label' => Lang::txt('MOD_TOOLS_COL_APPROVED'),   'count' => $approved,   'color' => 'var(--color-accent)',     'status' => 6],
    ['label' => Lang::txt('MOD_TOOLS_COL_UPDATED'),    'count' => $updated,    'color' => 'var(--color-info)',       'status' => 5],
    ['label' => Lang::txt('MOD_TOOLS_COL_UPLOADED'),   'count' => $uploaded,   'color' => 'var(--color-secondary)',  'status' => 3],
    ['label' => Lang::txt('MOD_TOOLS_COL_CREATED'),    'count' => $created,    'color' => 'var(--color-neutral)',    'status' => 2],
    ['label' => Lang::txt('MOD_TOOLS_COL_REGISTERED'), 'count' => $registered, 'color' => 'var(--color-base-300)',   'status' => 1],
    ['label' => Lang::txt('MOD_TOOLS_COL_RETIRED'),    'count' => $retired,    'color' => 'var(--color-warning)',    'status' => 8],
    ['label' => Lang::txt('MOD_TOOLS_COL_ABANDONED'),  'count' => $abandoned,  'color' => 'var(--color-error)',      'status' => 9],
  ];

  // SVG donut segments — r=15.9155, circumference≈100
  $cumulative = 0;
  foreach ($stats as &$seg) {
      $seg['pct']    = $total > 0 ? round(($seg['count'] / $total) * 100, 2) : 0;
      $seg['offset'] = round(25 - $cumulative, 2);
      $cumulative   += $seg['pct'];
  }
  unset($seg);
@endphp

<div class="flex gap-4 items-center">

  {{-- SVG donut --}}
  <div class="shrink-0">
    <svg viewBox="0 0 36 36" class="w-28 h-28 block">
      <g transform="rotate(-90 18 18)">
        <circle cx="18" cy="18" r="15.9155" fill="none"
                stroke="var(--color-base-300)" stroke-width="3"/>
        @foreach ($stats as $seg)
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
            font-size="2.5" fill="currentColor" opacity="0.5">{{ Lang::txt('MOD_TOOLS_TOTAL') }}</text>
    </svg>
  </div>

  {{-- Legend --}}
  <div class="flex-1 min-w-0 space-y-1.5 text-xs">
    @foreach ($stats as $stat)
      <div class="flex items-center gap-2">
        <span class="shrink-0 rounded-sm inline-block size-2.5"
              data-$style-bg="{{ $stat['color'] }}"></span>
        <a href="{{ Route::url('index.php?option=com_tools&status=' . $stat['status'], false) }}"
           class="link link-hover flex-1 truncate">{{ $stat['label'] }}</a>
        <span class="font-medium tabular-nums shrink-0">{{ number_format($stat['count']) }}</span>
      </div>
    @endforeach
  </div>

</div>
