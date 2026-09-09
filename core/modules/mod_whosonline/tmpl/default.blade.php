{{--
  Who's Online module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div class="{{ $params->get('moduleclass_sfx', '') }}">
  @if ($params->get('showmode', 0) == 0 || $params->get('showmode', 0) == 2)
    <div class="stats flex gap-6 mb-4">
      <div class="text-center">
        <div class="text-2xl font-bold">{{ number_format($loggedInCount) }}</div>
        <div class="text-xs text-base-content/60">{{ Lang::txt('MOD_WHOSONLINE_LOGGEDIN') }}</div>
      </div>
      <div class="text-center">
        <div class="text-2xl font-bold">{{ number_format($guestCount) }}</div>
        <div class="text-xs text-base-content/60">{{ Lang::txt('MOD_WHOSONLINE_GUESTS') }}</div>
      </div>
    </div>
  @endif

  @if ($params->get('showmode', 0) == 1 || $params->get('showmode', 0) == 2)
    <ul class="list bg-base-100 rounded-box">
      @foreach ($loggedInList as $loggedin)
        @php
          $memberUrl = Route::url('index.php?option=com_members&id=' . $loggedin->get('id'));
        @endphp
        <li class="list-row py-1 items-center">
          <span>{{ $loggedin->get('name') }}</span>
          <a href="{{ $memberUrl }}" class="link link-hover text-sm">
            {{ Lang::txt('MOD_WHOSONLINE_LOGGEDIN_VIEW_PROFILE') }}
          </a>
        </li>
      @endforeach
    </ul>
  @endif

  <div class="mt-3">
    <a class="btn btn-sm btn-outline"
       href="{{ Route::url('index.php?option=com_members&task=activity') }}">
      {{ Lang::txt('MOD_WHOSONLINE_VIEW_ALL_ACTIVITIY') }}
    </a>
  </div>
</div>
