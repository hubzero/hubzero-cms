{{--
  Member Points — transaction history table.

  Variables from plugin (onMembers):
    $funds — available points balance
    $sum   — total points earned
    $hist  — array of transaction history objects

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;

  $__view->css();

  $pointsLabel    = strtolower(Lang::txt('PLG_MEMBERS_POINTS'));
  $availableLabel = number_format($funds) . ' ' . strtolower(Lang::txt('PLG_MEMBERS_POINTS_AVAILABLE'));
@endphp

<h3 class="text-lg font-semibold mb-4">
  {{ Lang::txt('PLG_MEMBERS_POINTS') }}
</h3>

<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
  <div class="card bg-base-100 shadow-sm">
    <div class="card-body">
      <h4 class="card-title text-sm">{{ Lang::txt('PLG_MEMBERS_POINTS_BALANCE') }}</h4>
      <p class="text-3xl font-bold">
        {{ number_format($sum) }}
        <span class="text-base font-normal text-base-content/60">{{ $pointsLabel }}</span>
      </p>
      <p class="text-base-content/60">( {{ $availableLabel }} )</p>
    </div>
  </div>
  <div>
    <p class="text-base-content/70">
      <strong>{{ Lang::txt('PLG_MEMBERS_POINTS_HOW_ARE_POINTS_AWARDED') }}</strong><br />
      {{ Lang::txt('PLG_MEMBERS_POINTS_AWARDED_EXPLANATION') }}
    </p>
  </div>
</div>

<div class="overflow-x-auto">
  <table class="table table-zebra w-full">
    <caption class="text-left text-sm text-base-content/60 mb-2">
      {{ Lang::txt('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_CAPTION') }}
    </caption>
    <thead>
      <tr>
        <th>{{ Lang::txt('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_TH_DATE') }}</th>
        <th>{{ Lang::txt('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_TH_DESCRIPTION') }}</th>
        <th>{{ Lang::txt('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_TH_TYPE') }}</th>
        <th class="text-right">{{ Lang::txt('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_TH_AMOUNT') }}</th>
        <th class="text-right">{{ Lang::txt('PLG_MEMBERS_POINTS_TRANSACTIONS_TBL_TH_BALANCE') }}</th>
      </tr>
    </thead>
    <tbody>
      @if ($hist)
        @foreach ($hist as $item)
          <tr>
            <td>{{ Date::of($item->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}</td>
            <td>{{ $item->description }}</td>
            <td>{{ $item->type }}</td>
            <td class="text-right">
              @if ($item->type === 'withdraw')
                <span class="text-error font-medium">-{{ $item->amount }}</span>
              @elseif ($item->type === 'hold')
                <span class="text-warning font-medium">({{ $item->amount }})</span>
              @else
                <span class="text-success font-medium">+{{ $item->amount }}</span>
              @endif
            </td>
            <td class="text-right">{{ $item->balance }}</td>
          </tr>
        @endforeach
      @else
        <tr>
          <td colspan="5" class="text-center text-base-content/60">
            {{ Lang::txt('PLG_MEMBERS_POINTS_NO_TRANSACTIONS') }}
          </td>
        </tr>
      @endif
    </tbody>
  </table>
</div>
