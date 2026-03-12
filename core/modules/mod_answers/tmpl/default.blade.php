{{--
  mod_answers — admin dashboard Blade template

  Shows closed/open question counts with a dual-tone progress bar
  matching the original nanohub cpanel design.

  Variables: $module, $params, $closed, $open, $pastDay,
             $myclosed, $myopen

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $total     = $closed + $open;
  $closedPct = $total ? round(($closed / $total) * 100) : 0;
  $openPct   = 100 - $closedPct;
  $showMine  = $params->get('showMine', 0);
@endphp

@if ($total)
  {{-- Dual-tone progress bar: closed (primary/teal) + open (accent/amber) --}}
  <div class="rounded-full overflow-hidden flex mb-4 bg-base-200 h-3">
    <div class="bg-primary transition-all" data-style-width="{{ $closedPct }}%"></div>
    <div class="bg-accent opacity-70 transition-all" data-style-width="{{ $openPct }}%"></div>
  </div>
@endif

{{-- Large hero numbers --}}
<div class="flex">
  <div class="flex-1 text-center py-2">
    <div class="text-4xl font-bold leading-none text-primary">
      <a href="{{ Route::url('index.php?option=com_answers&state=1', false) }}"
         class="link link-hover text-inherit">{{ number_format($closed) }}</a>
    </div>
    <div class="text-xs opacity-70 mt-1">{{ Lang::txt('MOD_ANSWERS_CLOSED') }}</div>
  </div>
  <div class="flex-1 text-center py-2">
    <div class="text-4xl font-bold leading-none text-accent-dark">
      <a href="{{ Route::url('index.php?option=com_answers&state=0', false) }}"
         class="link link-hover text-inherit">{{ number_format($open) }}</a>
    </div>
    <div class="text-xs opacity-70 mt-1">{{ Lang::txt('MOD_ANSWERS_ASKED') }}</div>
  </div>
</div>

@if ($showMine && ($myclosed !== null || $myopen !== null))
  <div class="border-t border-base-200 mt-3 pt-3">
    <div class="text-xs text-muted-foreground mb-1">{{ Lang::txt('MOD_ANSWERS_MINE') }}</div>
    <div class="flex gap-4 text-sm">
      <span class="font-medium">{{ $myclosed ?? 0 }}
        <span class="text-xs text-muted-foreground">{{ Lang::txt('MOD_ANSWERS_CLOSED') }}</span></span>
      <span class="font-medium text-accent">{{ $myopen ?? 0 }}
        <span class="text-xs text-muted-foreground">{{ Lang::txt('MOD_ANSWERS_OPEN') }}</span></span>
    </div>
  </div>
@endif
