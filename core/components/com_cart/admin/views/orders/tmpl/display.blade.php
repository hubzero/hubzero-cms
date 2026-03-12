{{--
  Orders — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo   = \Components\Cart\Admin\Helpers\Permissions::getActions('orders');
  $sort    = $filters['sort'] ?? 'tLastUpdated';
  $sortDir = $filters['sort_Dir'] ?? 'DESC';
@endphp

{{-- Toolbar --}}
@php
  Toolbar::title(Lang::txt('COM_CART') . ': Orders', 'cart');

  if ($canDo->get('core.admin')) {
      Toolbar::preferences($option, '550');
      Toolbar::spacer();
  }
  Toolbar::custom('download', 'download.png', '', 'Download CSV', false);
  Toolbar::spacer();
  Toolbar::help('downloads');
@endphp

@include('com_cart::admin.views.orders.tmpl._submenu')

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  @slot('filters')
    <x-admin-filters>
      @slot('search')
        <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
        <input type="text"
               name="search"
               id="filter_search"
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] }}"
               placeholder="{{ Lang::txt('JSEARCH_FILTER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_CART_GO') }}</button>
      @endslot

      <label for="filter-report-notes" class="text-sm">{{ Lang::txt('COM_CART_SHOW_NOTES') }}:</label>
      <select name="report-notes" id="filter-report-notes"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="0" @selected($filters['report-notes'] === 0)>
          {{ Lang::txt('COM_CART_SHOW_NOTES_ALL') }}
        </option>
        <option value="1" @selected($filters['report-notes'] === 1)>
          {{ Lang::txt('COM_CART_SHOW_NOTES_ONLY') }}
        </option>
      </select>

      <label for="filter-report-from" class="text-sm">{{ Lang::txt('From') }}:</label>
      <input type="text"
             name="report-from"
             id="filter-report-from"
             class="input input-bordered input-sm w-28"
             value="{{ $filters['report-from'] }}"
             placeholder="{{ Lang::txt('From') }}" />
      <span>&mdash;</span>
      <label for="filter-report-to" class="text-sm">{{ Lang::txt('To') }}:</label>
      <input type="text"
             name="report-to"
             id="filter-report-to"
             class="input input-bordered input-sm w-28"
             value="{{ $filters['report-to'] }}"
             placeholder="{{ Lang::txt('To') }}" />
      <button type="submit" class="btn btn-sm btn-ghost">{{ Lang::txt('Update') }}</button>
    </x-admin-filters>
  @endslot

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        @if($filters['uidNumber'])
          <tr>
            <th colspan="6" class="bg-base-200">
              @php
                $fUser = User::getInstance($filters['uidNumber']);
                $fUserLabel = $fUser->get('id')
                    ? e($fUser->get('name')) . ' (' . e($fUser->get('username')) . ')'
                    : Lang::txt('COM_CART_USER_ID') . ': ' . $filters['uidNumber'];
              @endphp
              {{ Lang::txt('COM_CART_ORDERS_FOR') }}: {{ $fUserLabel }}
              <button type="button" id="filter_uidNumber-clear"
                      class="btn btn-xs btn-ghost">{{ Lang::txt('JSEARCH_FILTER_CLEAR') }}</button>
            </th>
          </tr>
        @endif
        <tr>
          <th>{!! Html::grid('sort', 'COM_CART_ORDER_ID', 'tId', $sortDir, $sort) !!}</th>
          <th>{{ Lang::txt('COM_CART_ORDER_TOTAL') }}</th>
          <th>{{ Lang::txt('COM_CART_ORDER_NUM_ITEMS') }}</th>
          <th>{!! Html::grid('sort', 'COM_CART_ORDER_PLACED', 'tLastUpdated', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_CART_ORDERED_BY', 'Name', $sortDir, $sort) !!}</th>
          <th class="priority-3">{!! Html::grid('sort', 'Payment method', 'tiPayment', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $row)
          @php
            $viewUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=view&id=' . $row->tId,
                false, false
            );
            $itemsUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=items&order=' . $row->tId,
                false, false
            );
            $uidUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&uidNumber=' . $row->uidNumber,
                false, false
            );
          @endphp
          <tr>
            <td>
              <a href="{{ $viewUrl }}" class="link link-hover text-primary font-medium">
                {{ $row->tId }}
              </a>
            </td>
            <td>${{ number_format($row->tiTotal, 2) }}</td>
            <td>
              <a href="{{ $itemsUrl }}" class="link link-hover">
                {{ $row->tiItemsQty }}
              </a>
            </td>
            <td>
              <time datetime="{{ $row->tLastUpdated }}">{{ $row->tLastUpdated }}</time>
            </td>
            <td>
              @if($row->uidNumber)
                <a href="{{ $uidUrl }}" class="link link-hover">
                  {{ $row->name ? $row->name : Lang::txt('COM_CART_UNKNOWN') }}
                </a>
              @else
                {{ Lang::txt('COM_CART_UNKNOWN') }}
              @endif
            </td>
            <td class="priority-3">{{ $row->tiPayment }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="uidNumber" id="filter_uidNumber"
         value="{{ $filters['uidNumber'] }}" />
</x-admin-form>
