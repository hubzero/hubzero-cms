{{--
  mod_supporttickets — admin dashboard Blade template

  Shows open/unassigned/new ticket counts and an SVG area
  chart of opened vs closed tickets by month.

  Variables: $module, $params, $topened, $openedmonths, $closedmonths

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $ticketsUrl      = Route::url('index.php?option=com_support&controller=tickets', false);
  $openCount       = isset($topened[0]) ? (int)$topened[0]->count : 0;
  $unassignedCount = isset($topened[2]) ? (int)$topened[2]->count : 0;
  $newCount        = isset($topened[1]) ? (int)$topened[1]->count : 0;

  // Flatten year/month arrays to sequential point lists
  $openedPts  = [];
  $closedPts  = [];
  $yearLabels = [];  // [pointIndex => year] for January of each year

  foreach ($openedmonths as $year => $months) {
      foreach ($months as $month => $count) {
          if ((int)$month === 1) {
              $yearLabels[count($openedPts)] = (int)$year;
          }
          $openedPts[] = (int)$count;
          $closedPts[] = (int)($closedmonths[$year][$month] ?? 0);
      }
  }

  $n        = count($openedPts);
  $hasChart = $n > 1;

  if ($hasChart) {
      $maxVal    = max(max($openedPts), max($closedPts), 1);
      $svgW      = 600;
      $padT      = 6;
      $padB      = count($yearLabels) > 0 ? 18 : 6;
      $svgH      = 100 + $padB;
      $chartH    = $svgH - $padT - $padB;
      $xStep     = $svgW / max($n - 1, 1);
      $bottom    = $padT + $chartH;
      $openPath  = '';
      $closPath  = '';

      for ($i = 0; $i < $n; $i++) {
          $x  = round($i * $xStep, 1);
          $yO = round($padT + $chartH * (1 - $openedPts[$i] / $maxVal), 1);
          $yC = round($padT + $chartH * (1 - $closedPts[$i] / $maxVal), 1);
          $openPath .= ($i === 0 ? "M $x,$yO" : " L $x,$yO");
          $closPath .= ($i === 0 ? "M $x,$yC" : " L $x,$yC");
      }

      $lastX    = round(($n - 1) * $xStep, 1);
      $openArea = $openPath . " L $lastX,$bottom L 0,$bottom Z";
      $closArea = $closPath . " L $lastX,$bottom L 0,$bottom Z";
      $showEvery = count($yearLabels) > 10 ? 2 : 1;
  }
@endphp

@if ($hasChart)
  <div class="w-full mb-3">
    <svg viewBox="0 0 600 {{ $svgH }}" class="w-full block">
      {{-- Closed area (grey, behind) --}}
      <path d="{{ $closArea }}" fill="var(--color-secondary)" opacity="0.3"/>
      {{-- Opened area (accent, in front) --}}
      <path d="{{ $openArea }}" fill="var(--color-accent)" opacity="0.5"/>
      {{-- Closed line --}}
      <path d="{{ $closPath }}" fill="none" stroke="var(--color-secondary)" stroke-width="1.2" stroke-linejoin="round"/>
      {{-- Opened line --}}
      <path d="{{ $openPath }}" fill="none" stroke="var(--color-accent)" stroke-width="1.5" stroke-linejoin="round"/>
      {{-- Legend top-right --}}
      <rect x="476" y="6"  width="12" height="5" fill="var(--color-accent)"    opacity="0.8" rx="1"/>
      <text x="492" y="11" font-size="9" fill="currentColor" opacity="0.75">{{ Lang::txt('MOD_SUPPORTTICKETS_OPENED') }}</text>
      <rect x="476" y="16" width="12" height="5" fill="var(--color-secondary)" opacity="0.5" rx="1"/>
      <text x="492" y="21" font-size="9" fill="currentColor" opacity="0.75">{{ Lang::txt('MOD_SUPPORTTICKETS_CLOSED') }}</text>
      {{-- Year labels --}}
      @foreach ($yearLabels as $idx => $year)
        @if ($idx % $showEvery === 0)
          @php $lx = round($idx * $xStep, 1); @endphp
          <text x="{{ $lx }}" y="{{ $svgH - 2 }}"
                text-anchor="middle" font-size="9" fill="currentColor" opacity="0.7">{{ $year }}</text>
        @endif
      @endforeach
    </svg>
  </div>
@endif

{{-- Three large stat numbers matching original layout: Open | Unassigned | New --}}
<div class="flex border border-base-200 rounded-lg overflow-hidden">
  <div class="flex-1 py-3 px-2 text-center border-r border-base-200">
    <div class="text-2xl font-bold leading-none text-accent-dark">
      <a href="{{ $ticketsUrl }}" class="link link-hover text-inherit"
         title="{{ Lang::txt('MOD_SUPPORTTICKETS_OPEN_TITLE') }}">{{ number_format($openCount) }}</a>
    </div>
    <div class="text-xs text-muted-foreground mt-1">{{ Lang::txt('MOD_SUPPORTTICKETS_OPEN') }}</div>
  </div>
  <div class="flex-1 py-3 px-2 text-center border-r border-base-200">
    <div class="text-2xl font-bold leading-none text-accent-dark">
      <a href="{{ $ticketsUrl }}" class="link link-hover text-inherit"
         title="{{ Lang::txt('MOD_SUPPORTTICKETS_UNASSIGNED_TITLE') }}">{{ number_format($unassignedCount) }}</a>
    </div>
    <div class="text-xs text-muted-foreground mt-1">{{ Lang::txt('MOD_SUPPORTTICKETS_UNASSIGNED') }}</div>
  </div>
  <div class="flex-1 py-3 px-2 text-center">
    <div class="text-2xl font-bold leading-none text-accent-dark">
      <a href="{{ $ticketsUrl }}" class="link link-hover text-inherit"
         title="{{ Lang::txt('MOD_SUPPORTTICKETS_NEW_TITLE') }}">{{ number_format($newCount) }}</a>
    </div>
    <div class="text-xs text-muted-foreground mt-1">{{ Lang::txt('MOD_SUPPORTTICKETS_NEW') }}</div>
  </div>
</div>
