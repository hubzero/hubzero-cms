{{--
  mod_wishvoters -- wish list top voters

  Variables: $rows, $params

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div{!! ($params->get('moduleclass')) ? ' class="' . e($params->get('moduleclass')) . '"' : '' !!}>
  <h3 class="text-lg font-semibold mb-2">
    {{ Lang::txt('MOD_WISHVOTERS_GIVING_MOST_INPUT') }}
  </h3>

  @if (count($rows) <= 0)
    <p class="text-sm opacity-70">{{ Lang::txt('MOD_WISHVOTERS_NO_VOTES') }}</p>
  @else
    <ul class="list bg-base-100 rounded-box shadow-sm">
      {{-- Header $row --}}
      <li class="list-row font-semibold text-sm">
        <div class="flex-1">{{ Lang::txt('MOD_WISHVOTERS_COL_NAME') }}</div>
        <div class="text-right">{{ Lang::txt('MOD_WISHVOTERS_COL_RANKED') }}</div>
      </li>

      @php $k = 1; @endphp
      @foreach ($rows as $row)
        @if ($k <= intval($params->get('limit', 10)))
          @php
            $name  = Lang::txt('MOD_WISHVOTERS_UNKNOWN');
            $login = '';
            $auser = User::getInstance($row->userid);
            if (is_object($auser)) {
                $name  = $auser->get('name');
                $login = $auser->get('username');
            }
          @endphp
          <li class="list-row">
            <div class="flex-1">
              <span class="opacity-50 tabular-nums mr-1">{{ $k }}.</span>
              {{ stripslashes($name) }}
              @if ($login)
                <span class="opacity-50">({{ stripslashes($login) }})</span>
              @endif
            </div>
            <div class="text-right font-medium tabular-nums">
              {{ $row->times }}
            </div>
          </li>
          @php $k++; @endphp
        @endif
      @endforeach
    </ul>
  @endif
</div>
