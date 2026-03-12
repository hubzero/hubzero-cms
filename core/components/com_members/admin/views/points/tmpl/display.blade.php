{{--
  Points Overview — Admin display

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_MENU_POINTS'), 'user');
  Toolbar::preferences('com_members', '550');
  $__view->css();
@endphp

@include('com_members::admin.views.points.tmpl._submenu')

<form action="{!! Route::url('index.php?option=' . $option, false) !!}"
      method="post"
      name="adminForm"
      id="adminForm">

  @if($rows)
    <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto mb-6">
      <table class="admin-table">
        <caption class="p-3 text-left font-semibold">Top Earners</caption>
        <thead>
          <tr>
            <th>Name</th>
            <th>UID</th>
            <th>Lifetime Earnings</th>
            <th>Current Balance</th>
            <th>Transaction History</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $row)
            @php
              $wuser = User::getInstance($row->uid);
              $name  = $wuser->get('id') ? $wuser->get('name') : Lang::txt('COM_MEMBERS_UNKNOWN');
              $historyUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=' . $controller
                  . '&task=edit&uid=' . $row->uid, false
              );
            @endphp
            <tr>
              <td class="font-medium">{{ $name }}</td>
              <td>{{ $row->uid }}</td>
              <td>{{ $row->earnings }}</td>
              <td>{{ $row->balance }}</td>
              <td>
                <a href="{!! $historyUrl !!}" class="btn btn-xs btn-ghost">
                  view
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <div class="alert alert-info mb-6">No user information found.</div>
  @endif

  @if(count($stats) > 0)
    <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
      @php $asOf = Date::of(Date::toSql())->toLocal(Lang::txt('DATE_FORMAT_HZ1')); @endphp
      <table class="admin-table">
        <caption class="p-3 text-left font-semibold">
          Economy Activity Stats as of {{ $asOf }}
        </caption>
        <thead>
          <tr>
            <th rowspan="2">Activity</th>
            <th colspan="3" class="text-center">All time</th>
            <th colspan="2" class="text-center">Current month</th>
            <th colspan="2" class="text-center">Previous month</th>
          </tr>
          <tr>
            <th>Points</th>
            <th>Transactions</th>
            <th>Avg Pnt/Trans</th>
            <th>Points</th>
            <th>Transactions</th>
            <th>Points</th>
            <th>Transactions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($stats as $stat)
            <tr>
              <th class="font-medium">{{ $stat['memo'] }}</th>
              <td>{{ $stat['alltimepts'] }}</td>
              <td>{{ $stat['alltimetran'] }}</td>
              <td>{{ $stat['avg'] ?? '' }}</td>
              <td>{{ $stat['thismonthpts'] ?? '' }}</td>
              <td>{{ $stat['thismonthtran'] ?? '' }}</td>
              <td>{{ $stat['lastmonthpts'] }}</td>
              <td>{{ $stat['lastmonthtran'] }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @else
    <div class="alert alert-info">No summary information found.</div>
  @endif

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" />
  {!! Html::input('token') !!}
</form>
