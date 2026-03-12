{{--
  mod_wishlist — admin dashboard Blade template

  Shows wishlist item status as an SVG donut chart with legend.

  Variables: $module, $params, $wishlist, $granted, $accepted,
             $pending, $rejected, $withdrawn, $removed

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $total = $granted + $accepted + $pending + $rejected + $withdrawn + $removed;
  $base  = 'index.php?option=com_wishlist&controller=wishes&wishlist=' . $wishlist;

  $stats = [
    ['label' => Lang::txt('MOD_WISHLIST_PENDING'),   'count' => $pending,   'color' => 'var(--color-neutral)',   'url' => $base . '&filterby=pending'],
    ['label' => Lang::txt('MOD_WISHLIST_ACCEPTED'),  'count' => $accepted,  'color' => 'var(--color-accent)',    'url' => $base . '&filterby=accepted'],
    ['label' => Lang::txt('MOD_WISHLIST_GRANTED'),   'count' => $granted,   'color' => 'var(--color-success)',   'url' => $base . '&filterby=granted'],
    ['label' => Lang::txt('MOD_WISHLIST_REJECTED'),  'count' => $rejected,  'color' => 'var(--color-error)',     'url' => $base . '&filterby=rejected'],
    ['label' => Lang::txt('MOD_WISHLIST_WITHDRAWN'), 'count' => $withdrawn, 'color' => 'var(--color-secondary)', 'url' => $base . '&filterby=withdrawn'],
    ['label' => Lang::txt('MOD_WISHLIST_REMOVED'),   'count' => $removed,   'color' => 'var(--color-base-300)',  'url' => $base . '&filterby=deleted'],
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

@if ($total)
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
              font-size="2.5" fill="currentColor" opacity="0.5">{{ Lang::txt('MOD_WISHLIST_TOTAL') }}</text>
      </svg>
    </div>

    {{-- Legend --}}
    <div class="flex-1 min-w-0 space-y-1.5 text-xs">
      @foreach ($stats as $stat)
        <div class="flex items-center gap-2">
          <span class="shrink-0 rounded-sm inline-block size-2.5"
                data-style-bg="{{ $stat['color'] }}"></span>
          <a href="{{ Route::url($stat['url'], false) }}"
             class="link link-hover flex-1 truncate">{{ $stat['label'] }}</a>
          <span class="font-medium tabular-nums shrink-0">{{ number_format($stat['count']) }}</span>
        </div>
      @endforeach
    </div>

  </div>
@endif
