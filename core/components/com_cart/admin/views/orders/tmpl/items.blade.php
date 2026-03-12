{{--
  Items Ordered — Admin report view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo   = \Components\Cart\Admin\Helpers\Permissions::getActions('orders');
  $sort    = $filters['sort'] ?? 'tLastUpdated';
  $sortDir = $filters['sort_Dir'] ?? 'DESC';
@endphp

{{-- Toolbar --}}
@php
  Toolbar::title(Lang::txt('COM_CART') . ': Items Ordered', 'cart');

  if ($canDo->get('core.admin')) {
      Toolbar::preferences($option, '550');
      Toolbar::spacer();
  }
  Toolbar::custom('downloadOrders', 'download.png', '', 'Download CSV', false);
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
        @if(!empty($filters['order']))
          <tr>
            <th colspan="7" class="bg-base-200">
              {{ Lang::txt('COM_CART_ORDER') }}: #{{ $filters['order'] }}
              <button type="button" class="filter-clear btn btn-xs btn-ghost">
                {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
              </button>
            </th>
          </tr>
        @endif
        @if(!empty($filters['pId']) || !empty($filters['sId']))
          <tr>
            <th colspan="7" class="bg-base-200">
              @if($filters['pId'])
                @php
                  $fProduct = \Components\Storefront\Models\Product::getInstance($filters['pId']);
                @endphp
                {{ Lang::txt('COM_CART_ORDERS_OF') }}: {{ $fProduct->getName() }}
                <button type="button" id="filter_pId-clear"
                        class="btn btn-xs btn-ghost">{{ Lang::txt('JSEARCH_FILTER_CLEAR') }}</button>
              @endif
              @if($filters['sId'])
                @php
                  $fSku = \Components\Storefront\Models\Sku::getInstance($filters['sId']);
                @endphp
                {{ Lang::txt('COM_CART_ORDERS_OF') }}: {{ $fSku->getName() }}
                <button type="button" id="filter_sId-clear"
                        class="btn btn-xs btn-ghost">{{ Lang::txt('JSEARCH_FILTER_CLEAR') }}</button>
              @endif
            </th>
          </tr>
        @endif
        <tr>
          <th>{!! Html::grid('sort', 'COM_CART_SKU_ID', 'sId', $sortDir, $sort) !!}</th>
          <th>{{ Lang::txt('COM_CART_PRODUCT') }}</th>
          <th>{{ Lang::txt('COM_CART_QUANTITY') }}</th>
          <th>{{ Lang::txt('COM_CART_PRICE') }}</th>
          <th>{!! Html::grid('sort', 'COM_CART_ORDER_ID', 'tId', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_CART_ORDER_PLACED', 'tLastUpdated', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_CART_ORDERED_BY', 'Name', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="7">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $row)
          @php
            $itemInfo = is_array($row->itemInfo['info'])
                ? (object)$row->itemInfo['info']
                : $row->itemInfo['info'];

            $skuEditUrl = Route::url(
                'index.php?option=com_storefront&controller=skus&task=edit&id=' . $itemInfo->sId,
                false, false
            );
            $pFilterUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=' . $task
                . '&pId=' . $itemInfo->pId,
                false, false
            );
            $sFilterUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=' . $task
                . '&sId=' . $row->sId,
                false, false
            );
            $orderUrl = Route::url(
                'index.php?option=com_cart&controller=orders&task=view&id=' . $row->tId,
                false, false
            );
            $memberUrl = Route::url(
                'index.php?option=com_members&task=edit&id=' . $row->uidNumber,
                false, false
            );
          @endphp
          <tr>
            <td>
              <a href="{{ $skuEditUrl }}" class="link link-hover">{{ $row->sId }}</a>
            </td>
            <td>
              @if($itemInfo->pName)
                <a href="{{ $pFilterUrl }}" class="link link-hover text-primary font-medium">
                  {{ $itemInfo->pName }}
                </a>
              @else
                <span class="text-error italic">Product n/a</span>
              @endif
              <br />
              SKU:
              @if($itemInfo->sSku)
                <a href="{{ $sFilterUrl }}" class="link link-hover text-sm">
                  {{ $itemInfo->sSku }}
                </a>
              @else
                <span class="text-error italic">n/a</span>
              @endif
            </td>
            <td>{{ $row->tiQty }}</td>
            <td>${{ number_format($row->tiPrice, 2) }}</td>
            <td>
              <a href="{{ $orderUrl }}" class="link link-hover text-primary">{{ $row->tId }}</a>
            </td>
            <td>
              <time datetime="{{ $row->tLastUpdated }}">{{ $row->tLastUpdated }}</time>
            </td>
            <td>
              @if($row->uidNumber)
                <a href="{{ $memberUrl }}" class="link link-hover">
                  {{ $row->name ? $row->name : Lang::txt('COM_CART_UNKNOWN') }}
                </a>
              @else
                {{ Lang::txt('COM_CART_UNKNOWN') }}
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="order" id="filter_ordernum"
         value="{{ $filters['order'] ?? '' }}" />
  <input type="hidden" name="pId" id="filter_pId"
         value="{{ $filters['pId'] ?? '' }}" />
  <input type="hidden" name="sId" id="filter_sId"
         value="{{ $filters['sId'] ?? '' }}" />
</x-admin-form>
