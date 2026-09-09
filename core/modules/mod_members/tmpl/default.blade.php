{{--
  mod_members — admin dashboard Blade template

  Left: SVG donut of domain breakdown.
  Right: dual progress bars with large stat numbers.
  Bottom: collapsible domain table.

  Variables: $module, $params, $domains, $confirmed, $unconfirmed,
             $approved, $unapproved, $pastDay

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $total        = $confirmed + $unconfirmed;
  $confirmedPct = $total     ? round(($confirmed  / $total)    * 100, 2) : 0;
  $unconfPct    = 100 - $confirmedPct;
  $approvedPct  = $confirmed ? round(($approved   / $confirmed) * 100, 2) : 0;
  $unapprPct    = 100 - $approvedPct;

  // Colors cycling for donut segments and domain table squares
  $domainColors = [
    '#0a7b72','#f59e0b','#6366f1','#ec4899','#06b6d4',
    '#84cc16','#f97316','#8b5cf6','#14b8a6','#ef4444',
    '#a855f7','#3b82f6','#22c55e','#eab308','#64748b',
    '#0ea5e9','#d946ef','#10b981','#fb923c','#6d28d9',
    '#0284c7','#be185d','#059669','#b45309','#7c3aed',
  ];

  // Build donut segments from domain data
  $domainTotal = array_sum(array_column((array)$domains, 'email_count'));
  $cumulative  = 0;
  $domainSegs  = [];
  foreach ($domains as $i => $d) {
      $pct          = $domainTotal > 0 ? round(($d->email_count / $domainTotal) * 100, 2) : 0;
      $domainSegs[] = [
          'pct'    => $pct,
          'offset' => round(25 - $cumulative, 2),
          'color'  => $domainColors[$i % count($domainColors)],
          'domain' => $d->domain,
          'count'  => $d->email_count,
      ];
      $cumulative += $pct;
  }
@endphp

{{-- Top section: donut + stats --}}
<div class="flex gap-4 items-start mb-4">

  {{-- SVG donut (domain breakdown) --}}
  <div class="shrink-0">
    <svg viewBox="0 0 36 36" class="w-32 h-32 block">
      <g transform="rotate(-90 18 18)">
        <circle cx="18" cy="18" r="15.9155" fill="none"
                stroke="var(--color-base-200)" stroke-width="3.5"/>
        @foreach ($domainSegs as $seg)
          @if ($seg['pct'] > 0)
            <circle cx="18" cy="18" r="15.9155" fill="none"
                    stroke="{{ $seg['color'] }}" stroke-width="3.5"
                    stroke-dasharray="{{ $seg['pct'] }} {{ 100 - $seg['pct'] }}"
                    stroke-dashoffset="{{ $seg['offset'] }}"/>
          @endif
        @endforeach
      </g>
      <text x="18" y="15.5" text-anchor="middle" dominant-baseline="middle"
            font-size="4.5" font-weight="700" fill="currentColor">{{ number_format($total) }}</text>
      <text x="18" y="21.5" text-anchor="middle" dominant-baseline="middle"
            font-size="2.5" fill="currentColor" opacity="0.7">{{ Lang::txt('MOD_MEMBERS_TOTAL') }}</text>
    </svg>
  </div>

  {{-- Stats column --}}
  <div class="flex-1 min-w-0">

    {{-- Confirmed progress bar --}}
    <div class="rounded-full overflow-hidden flex bg-base-200 mb-1.5 h-[0.375rem]">
      <div class="bg-primary transition-all" data-$style-width="{{ $confirmedPct }}%"></div>
      <div class="bg-accent opacity-60" data-$style-width="{{ $unconfPct }}%"></div>
    </div>

    {{-- Confirmed / Unconfirmed / ~24hrs --}}
    <div class="flex mb-3">
      <div class="flex-1 text-center">
        <div class="text-2xl font-bold leading-none text-primary">
          <a href="{{ Route::url('index.php?option=com_members&controller=manage&confirmed=1', false) }}"
             class="link link-hover text-inherit">{{ number_format($confirmed) }}</a>
        </div>
        <div class="text-[0.65rem] opacity-70 mt-0.5">{{ Lang::txt('MOD_MEMBERS_CONFIRMED') }}</div>
      </div>
      <div class="flex-1 text-center">
        <div class="text-2xl font-bold leading-none text-accent-dark">
          <a href="{{ Route::url('index.php?option=com_members&controller=manage&confirmed=0', false) }}"
             class="link link-hover text-inherit">{{ number_format($unconfirmed) }}</a>
        </div>
        <div class="text-[0.65rem] opacity-70 mt-0.5">{{ Lang::txt('MOD_MEMBERS_UNCONFIRMED') }}</div>
      </div>
      <div class="flex-1 text-center">
        <div class="text-2xl font-bold leading-none">
          <a href="{{ Route::url('index.php?option=com_members&controller=manage', false) }}"
             class="link link-hover">{{ number_format($pastDay) }}</a>
        </div>
        <div class="text-[0.65rem] opacity-70 mt-0.5">{{ Lang::txt('MOD_MEMBERS_NEW') }}</div>
      </div>
    </div>

    {{-- Approved progress bar --}}
    <div class="rounded-full overflow-hidden flex bg-base-200 mb-1.5 h-[0.375rem]">
      <div class="bg-success transition-all" data-$style-width="{{ $approvedPct }}%"></div>
      <div class="bg-error opacity-70" data-$style-width="{{ $unapprPct }}%"></div>
    </div>

    {{-- Approved / Unapproved --}}
    <div class="flex">
      <div class="flex-1 text-center">
        <div class="text-2xl font-bold leading-none text-success">
          <a href="{{ Route::url('index.php?option=com_members&controller=manage&approved=1', false) }}"
             class="link link-hover text-inherit">{{ number_format($approved) }}</a>
        </div>
        <div class="text-[0.65rem] opacity-70 mt-0.5">{{ Lang::txt('MOD_MEMBERS_APPROVED') }}</div>
      </div>
      <div class="flex-1 text-center">
        <div class="text-2xl font-bold leading-none text-error">
          <a href="{{ Route::url('index.php?option=com_members&controller=manage&approved=0', false) }}"
             class="link link-hover text-inherit">{{ number_format($unapproved) }}</a>
        </div>
        <div class="text-[0.65rem] opacity-70 mt-0.5">{{ Lang::txt('MOD_MEMBERS_UNAPPROVED') }}</div>
      </div>
    </div>

  </div>
</div>

{{-- Domain breakdown (collapsible) --}}
@if (!empty($domains))
  <details>
    <summary class="text-xs font-medium py-1 cursor-pointer select-none opacity-70">
      {{ Lang::txt('MOD_MEMBERS_BY_DOMAIN') }}
    </summary>
    <table class="w-full text-xs mt-1 border-collapse">
      <thead>
        <tr>
          <th class="text-left font-medium opacity-70 py-0.5">{{ Lang::txt('MOD_MEMBERS_DOMAIN') }}</th>
          <th class="text-right font-medium opacity-70 py-0.5">{{ Lang::txt('MOD_MEMBERS_REGISTERED') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($domains as $i => $domain)
          <tr>
            <td class="py-0.5">
              <span class="inline-block rounded-sm align-middle mr-1.5 size-2.5"
                    data-$style-bg="{{ $domainColors[$i % count($domainColors)] }}"></span>{{ $domain->domain }}
            </td>
            <td class="text-right font-medium">{{ number_format($domain->email_count) }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </details>
@endif
