{{--
  mod_supportactivity — support ticket activity feed

  Shows recent support ticket activity (new tickets, comments, changes).

  Variables: $results, $feed, $params, $module

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__module->css()
           ->js();

  $categoryIcons = [
    'ticket'  => '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>',
    'comment' => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>',
    'change'  => '<polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 2.13-9.36L1 10"/>',
  ];

  $categoryColors = [
    'ticket'  => 'text-info',
    'comment' => 'text-primary',
    'change'  => 'text-muted-foreground',
  ];
@endphp

@php
  $feedUrl = Request::base(true)
      . '?task=module&no_html=1&module='
      . $module->name . '&feedactivity=1&start=';
@endphp

@if ($results && count($results))
  <div class="space-y-0.5 max-h-[28rem] overflow-y-auto overflow-x-hidden"
       data-feed-url="{{ $feedUrl }}">
    @foreach ($results as $result)
      @php
        $cat       = $result->category ?? 'change';
        $icon      = $categoryIcons[$cat] ?? $categoryIcons['change'];
        $colorCls  = $categoryColors[$cat] ?? 'text-muted-foreground';
        $langKey   = 'MOD_SUPPORTACTIVITY_' . strtoupper($cat);
        $ticketUrl = Route::url(
            'index.php?option=com_support&controller=tickets&task=edit&id='
            . $result->ticket . ($result->id ? '#c' . $result->id : ''),
            false
        );
        $timeStr = Date::of($result->created)->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
        $dateStr = Date::of($result->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
      @endphp
      <a href="{{ $ticketUrl }}"
         class="flex items-start gap-2 px-2 py-1.5 rounded hover:bg-base-200 transition-colors group">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
             stroke-linejoin="round" class="shrink-0 mt-0.5 {{ $colorCls }}">
          {!! $icon !!}
        </svg>
        <div class="flex-1 min-w-0">
          <div class="text-xs truncate">
            {{ Lang::txt($langKey, $result->ticket) }}
          </div>
          <div class="text-[0.65rem] opacity-50">
            <time datetime="{{ $result->created }}">{{ $timeStr }} &middot; {{ $dateStr }}</time>
          </div>
        </div>
      </a>
    @endforeach
  </div>
@else
  <div class="text-center opacity-70 py-4 text-xs">
    {{ Lang::txt('MOD_SUPPORTACTIVITY_NO_RESULTS') }}
  </div>
@endif
