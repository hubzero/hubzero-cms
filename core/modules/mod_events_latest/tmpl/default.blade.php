{{--
  Latest Events module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if ($__module->getError())
  <div role="alert" class="alert alert-error">
    <span>{{ $__module->getError() }}</span>
  </div>
@elseif (count($events) > 0)
  <ul class="list bg-base-100 rounded-box shadow-sm">
    @foreach ($events as $event)
      @php
        $eventUrl = Route::url('index.php?option=com_events&task=details&id=' . $event->id);
      @endphp
      <li class="list-row items-center">
        <div class="text-center leading-tight">
          <span class="block text-xs font-semibold uppercase text-primary">
            {{ Date::of($event->publish_up)->toLocal('M') }}
          </span>
          <span class="block text-2xl font-bold">
            {{ Date::of($event->publish_up)->toLocal('d') }}
          </span>
        </div>
        <div>
          <a href="{{ $eventUrl }}" class="link link-hover">
            {{ html_entity_decode(stripslashes($event->title)) }}
          </a>
        </div>
      </li>
    @endforeach
  </ul>
  @php
    $eventsUrl = Route::url(
      'index.php?option=com_events&year=' . Date::of('now')->format('Y')
      . '&month=' . Date::of('now')->format('m')
    );
  @endphp
  <p class="mt-3 text-end">
    <a href="{{ $eventsUrl }}" class="link link-hover text-sm">
      {!! Lang::txt('MOD_EVENTS_LATEST_MORE') !!}
    </a>
  </p>
@else
  <p class="text-base-content/60">{{ Lang::txt('MOD_EVENTS_LATEST_NONE_FOUND') }}</p>
@endif
