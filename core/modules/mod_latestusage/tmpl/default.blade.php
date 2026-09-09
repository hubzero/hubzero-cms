{{--
  Latest Usage statistics module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div class="{{ $cls }}">
  <ul class="list bg-base-100 rounded-box">
    <li class="list-row p-3 flex justify-between">
      <span class="font-medium">{{ Lang::txt('MOD_LATESTUSAGE_USERS') }}</span>
      <span class="tabular-nums">{{ $users }}</span>
    </li>
    <li class="list-row p-3 flex justify-between">
      <span class="font-medium">{{ Lang::txt('MOD_LATESTUSAGE_RESOURCES') }}</span>
      <span class="tabular-nums">{{ $resources }}</span>
    </li>
    <li class="list-row p-3 flex justify-between">
      <span class="font-medium">{{ Lang::txt('MOD_LATESTUSAGE_TOOLS') }}</span>
      <span class="tabular-nums">{{ $tools }}</span>
    </li>
    <li class="list-row p-3 flex justify-between">
      <span class="font-medium">{{ Lang::txt('MOD_LATESTUSAGE_SIMULATIONS') }}</span>
      <span class="tabular-nums">{{ $sims }}</span>
    </li>
  </ul>
  <div class="flex justify-between mt-2">
    @php
      $onlineUrl = Route::url('index.php?option=com_usage&task=maps&type=online');
      $moreUrl = Route::url('index.php?option=com_usage');
    @endphp
    <a href="{{ $onlineUrl }}" class="btn btn-sm btn-outline">
      {{ Lang::txt('MOD_LATESTUSAGE_WHOSONLONE') }}
    </a>
    <a href="{{ $moreUrl }}" class="btn btn-sm btn-outline">
      {{ Lang::txt('MOD_LATESTUSAGE_MORE') }}
    </a>
  </div>
</div>
