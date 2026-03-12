{{--
  Points — Manage user points

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_POINTS_MANAGE'), 'user');
  $__view->js();
@endphp

@include('com_members::admin.views.points.tmpl._submenu')

<form action="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller, false) !!}"
      method="post"
      name="adminForm"
      id="item-form">

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
    <div class="lg:col-span-5">
      <x-admin-fieldset legend="User Details">
        <div class="admin-field">
          <label for="account-uid" class="label">User ID:</label>
          <input type="text"
                 name="account[uid]"
                 id="account-uid"
                 class="input input-bordered w-full"
                 required
                 value="{{ $row->uid }}" />
          <input type="hidden" name="uid" value="{{ $row->uid }}" />
        </div>

        <div class="admin-field">
          <label for="account-balance" class="label">Point Balance:</label>
          <input type="text"
                 name="account[balance]"
                 id="account-balance"
                 class="input input-bordered w-full"
                 value="{{ $row->balance }}" />
        </div>

        <div class="admin-field">
          <label for="account-earnings" class="label">Total Earnings:</label>
          <input type="text"
                 name="account[earnings]"
                 id="account-earnings"
                 class="input input-bordered w-full"
                 value="{{ $row->earnings }}" />
        </div>
      </x-admin-fieldset>

      <x-admin-fieldset legend="New Transaction">
        <div class="admin-field">
          <label for="type" class="label">Type:</label>
          <select name="transaction[type]" id="type" class="select select-bordered w-full">
            <option>deposit</option>
            <option>withdraw</option>
            <option>creation</option>
          </select>
        </div>

        <div class="admin-field">
          <label for="transaction-amount" class="label">Amount:</label>
          <input type="text"
                 name="transaction[amount]"
                 id="transaction-amount"
                 class="input input-bordered w-full"
                 value="" />
        </div>

        <div class="admin-field">
          <label for="transaction-description" class="label">Description:</label>
          <input type="text"
                 name="transaction[description]"
                 id="transaction-description"
                 class="input input-bordered w-full"
                 value="" />
        </div>

        <div class="admin-field">
          <label for="transaction-category" class="label">Category:</label>
          <input type="text"
                 name="transaction[category]"
                 id="transaction-category"
                 class="input input-bordered w-full"
                 value="" />
          <p class="text-xs text-muted-foreground mt-1">E.g. answers, store, survey, general etc.</p>
        </div>

        <button type="submit" class="btn btn-primary mt-2">Save change</button>
      </x-admin-fieldset>
    </div>

    <div class="lg:col-span-7">
      <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
        <table class="admin-table">
          <caption class="p-3 text-left font-semibold">Transaction History</caption>
          <thead>
            <tr>
              <th>Date</th>
              <th>Description</th>
              <th>Category</th>
              <th>Type</th>
              <th class="text-right">Amount</th>
              <th class="text-right">Balance</th>
            </tr>
          </thead>
          <tbody>
            @forelse($history as $item)
              <tr>
                <td class="text-sm whitespace-nowrap">
                  {{ Date::of($item->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1') . ' ' . Lang::txt('TIME_FORMAT_HZ1')) }}
                </td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->category }}</td>
                <td>{{ $item->type }}</td>
                <td class="text-right">
                  @if($item->type == 'withdraw')
                    <span class="text-error">-{{ $item->amount }}</span>
                  @elseif($item->type == 'hold')
                    <span class="text-warning">{{ $item->amount }}</span>
                  @else
                    <span class="text-success">+{{ $item->amount }}</span>
                  @endif
                </td>
                <td class="text-right">{{ $item->balance }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted-foreground py-8">
                  There is no information available on this user's transactions.
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <input type="hidden" name="account[id]" value="{{ $row->id }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="save" />
  {!! Html::input('token') !!}
</form>
