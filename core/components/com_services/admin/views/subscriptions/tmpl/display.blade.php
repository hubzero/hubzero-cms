{{--
  Subscriptions — Admin list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $canDo   = \Components\Services\Helpers\Permissions::getActions('service');
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $sort    = $filters['sort'] ?? 'id';
  $now     = Date::toSql();
  $dateFmt = Lang::txt('DATE_FORMAT_HZ1');
  $na      = Lang::txt('COM_SERVICES_NOT_APPLICABLE');

  $__view->css();
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_SERVICES') }}: {{ Lang::txt('COM_SERVICES_SUBSCRIPTIONS') }}"
    icon="services"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  @slot('filters')
    <x-admin-filters>
      @slot('search')
        <label for="filter-status" class="sr-only">{{ Lang::txt('COM_SERVICES_COL_STATUS') }}</label>
        <select name="filter_status"
                id="filter-status"
                class="select select-bordered select-sm"
                data-submit-on-change>
          <option value="pending"
                  @selected($filters['status'] == 'pending')>
            {{ Lang::txt('COM_SERVICES_FILTER_BY_PENDING') }}
          </option>
          <option value="active"
                  @selected($filters['status'] == 'processed')>
            {{ Lang::txt('COM_SERVICES_FILTER_BY_ACTIVE') }}
          </option>
          <option value="cancelled"
                  @selected($filters['status'] == 'cancelled')>
            {{ Lang::txt('COM_SERVICES_FILTER_BY_CANCELLED') }}
          </option>
          <option value="all"
                  @selected($filters['status'] == 'all')>
            {{ Lang::txt('COM_SERVICES_FILTER_BY_ALL') }}
          </option>
        </select>
      @endslot
    </x-admin-filters>
  @endslot

  <table class="admin-table">
    <thead>
      <tr>
        <th scope="col">
          {!! Html::grid('sort', 'COM_SERVICES_COL_ID_CODE', 'id', $sortDir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_SERVICES_COL_STATUS', 'status', $sortDir, $sort) !!}
        </th>
        <th scope="col">{{ Lang::txt('COM_SERVICES_COL_SERVICE') }}</th>
        <th scope="col" class="priority-3">{{ Lang::txt('COM_SERVICES_COL_PENDING') }}</th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_SERVICES_COL_USER', 'uid', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-3">
          {!! Html::grid('sort', 'COM_SERVICES_COL_ADDED', 'added', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-4">
          {!! Html::grid('sort', 'COM_SERVICES_COL_LAST_UPDATED', 'updated', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-2">
          {!! Html::grid('sort', 'COM_SERVICES_COL_EXPIRES', 'expires', $sortDir, $sort) !!}
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $row)
        @php
          $ruser = User::getInstance($row->uid);
          $name  = $ruser->get('id') ? $ruser->get('name') : Lang::txt('COM_SERVICES_UNKNOWN');
          $login = $ruser->get('id') ? $ruser->get('username') : Lang::txt('COM_SERVICES_UNKNOWN');

          $editUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=edit&id=' . $row->id,
              false, false
          );

          $pendingAmount = $row->currency . ' ' . $row->pendingpayment;
          $pending = Lang::txt('COM_SERVICES_FOR_UNITS', $pendingAmount, $row->pendingunits);

          $expires = (intval($row->expires) <> 0)
              ? Date::of($row->expires)->toLocal($dateFmt) : $na;
        @endphp
        <tr>
          <td>
            <a href="{{ $editUrl }}">{{ $row->id }} &mdash; {{ $row->code }}</a>
          </td>
          <td>
            @switch($row->status)
              @case(1)
                @if ($row->expires > $now)
                  <span class="badge badge-success">{{ Lang::txt('COM_SERVICES_STATE_ACTIVE') }}</span>
                @else
                  <span class="badge badge-warning">{{ Lang::txt('COM_SERVICES_EXPIRED') }}</span>
                @endif
                @break
              @case(0)
                <span class="badge badge-info">{{ Lang::txt('COM_SERVICES_STATE_PENDING') }}</span>
                @break
              @case(2)
                <span class="badge badge-ghost">{{ Lang::txt('COM_SERVICES_STATE_CANCELED') }}</span>
                @php
                  if ($row->pendingpayment) {
                      $pending .= ' (' . Lang::txt('COM_SERVICES_REFUND') . ')';
                  }
                @endphp
                @break
            @endswitch
          </td>
          <td>
            <a href="{{ $editUrl }}">
              {{ $row->category }} &mdash; {{ $row->title }}
            </a>
          </td>
          <td class="priority-3">
            @php
              $hasPending = $row->pendingpayment > 0 || $row->pendingunits > 0;
            @endphp
            @if ($hasPending)
              <span class="font-semibold text-warning-content bg-warning/20 px-1 rounded">{{ $pending }}</span>
            @else
              {{ $pending }}
            @endif
          </td>
          <td>{{ $name }} ({{ $login }})</td>
          <td class="priority-3">
            {{ Date::of($row->added)->toLocal($dateFmt) }}
          </td>
          <td class="priority-4">
            {{ $row->updated ? Date::of($row->updated)->toLocal($dateFmt) : Lang::txt('COM_SERVICES_NEVER') }}
          </td>
          <td class="priority-2">{{ $expires }}</td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="8">{!! $rows->pagination !!}</td>
      </tr>
    </tfoot>
  </table>

  <input type="hidden" name="boxchecked" value="0" />
</x-admin-form>
