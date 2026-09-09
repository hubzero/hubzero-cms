{{--
  mod_courses — admin dashboard course statistics

  Shows course enrollment as an SVG $area chart with status counts.

  Variables: $module, $params, $published, $draft,
             $unpublished, $archived, $totals

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  // Build flat data points for the SVG chart
  $points = [];
  $maxVal = 1;
  if ($totals) {
      foreach ($totals as $year => $months) {
          foreach ($months as $m => $val) {
              $points[] = ['year' => $year, 'month' => $m, 'value' => (int) $val];
              if ($val > $maxVal) $maxVal = (int) $val;
          }
      }
  }

  $total = $published + $draft + $unpublished + $archived;

  $stats = [
    ['label' => Lang::txt('MOD_COURSES_PUBLISHED'),   'count' => $published,   'color' => 'var(--color-success)', 'state' => 1],
    ['label' => Lang::txt('MOD_COURSES_DRAFT'),        'count' => $draft,       'color' => 'var(--color-accent)',  'state' => 3],
    ['label' => Lang::txt('MOD_COURSES_UNPUBLISHED'),  'count' => $unpublished, 'color' => 'var(--color-neutral)', 'state' => 0],
    ['label' => Lang::txt('MOD_COURSES_ARCHIVED'),     'count' => $archived,    'color' => 'var(--color-base-300)','state' => 2],
  ];

  // Build SVG $area chart path (200×60 viewbox)
  $chartW   = 200;
  $chartH   = 60;
  $padTop   = 4;
  $padBot   = 2;
  $usableH  = $chartH - $padTop - $padBot;
  $numPts   = count($points);

  $pathData = '';
  $areaData = '';
  if ($numPts > 1) {
      $coords = [];
      foreach ($points as $i => $pt) {
          $x = round(($i / ($numPts - 1)) * $chartW, 2);
          $y = round($chartH - $padBot - ($pt['value'] / $maxVal) * $usableH, 2);
          $coords[] = "$x,$y";
      }
      $pathData = 'M' . implode(' L', $coords);
      $areaData = $pathData . " L{$chartW},{$chartH} L0,{$chartH} Z";
  }
@endphp

{{-- Enrollment $area chart --}}
@if ($numPts > 1)
  <div class="mb-3">
    <svg viewBox="0 0 {{ $chartW }} {{ $chartH }}" class="w-full h-16 block" preserveAspectRatio="none">
      <path d="{{ $areaData }}" fill="var(--color-primary)" opacity="0.15"/>
      <path d="{{ $pathData }}" fill="none" stroke="var(--color-primary)" stroke-width="1.5"/>
    </svg>
    <div class="flex justify-between text-[0.6rem] opacity-50 mt-0.5 px-0.5">
      <span>{{ $points[0]['year'] }}</span>
      <span>{{ Lang::txt('MOD_COURSES_ENROLLED') }}</span>
      <span>{{ $points[$numPts - 1]['year'] }}</span>
    </div>
  </div>
@endif

{{-- Status counts --}}
<div class="grid grid-cols-2 gap-2">
  @foreach ($stats as $stat)
    <a href="{{ Route::url('index.php?option=com_courses&state=' . $stat['state'], false) }}"
       class="flex items-center gap-2 px-2 py-1.5 rounded bg-base-200 hover:bg-base-300 transition-colors">
      <span class="shrink-0 rounded-sm inline-block size-2.5"
            data-$style-bg="{{ $stat['color'] }}"></span>
      <span class="text-xs truncate flex-1">{{ $stat['label'] }}</span>
      <span class="text-sm font-bold tabular-nums">{{ number_format($stat['count']) }}</span>
    </a>
  @endforeach
</div>
