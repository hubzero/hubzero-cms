{{--
  Software Downloads — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo   = \Components\Cart\Admin\Helpers\Permissions::getActions('download');
  $sort    = $filters['sort'] ?? 'dDownloaded';
  $sortDir = $filters['sort_Dir'] ?? 'DESC';
@endphp

{{-- Toolbar --}}
@php
  Toolbar::title(Lang::txt('COM_CART') . ': ' . Lang::txt('COM_CART_SOFTWARE_DOWNLOADS'), 'cart');

  if ($canDo->get('core.admin')) {
      Toolbar::preferences($option, '550');
      Toolbar::spacer();
  }
  if ($canDo->get('core.edit.state')) {
      Toolbar::publishList();
      Toolbar::unpublishList();
      Toolbar::spacer();
  }
  Toolbar::custom('download', 'download.png', '', 'Download CSV', false);
  Toolbar::spacer();
  Toolbar::help('downloads');
@endphp

@include('com_cart::admin.views.downloads.tmpl._submenu')

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

      @if(!empty($filters['skuRequested']))
        <select name="skuRequested" id="skuRequested"
                class="select select-bordered select-sm"
                data-submit-on-change>
          <option value="0">{{ Lang::txt('COM_CART_ALL_SKUS') }}</option>
          <option value="{{ $filters['skuRequested'] }}" selected>
            {{ $skuRequestedName }}
          </option>
        </select>
      @endif

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
        @if($filters['uidNumber'] || $filters['pId'] || $filters['sId'])
          <tr>
            <th colspan="8" class="bg-base-200">
              @if($filters['uidNumber'])
                @php
                  $fUser = User::getInstance($filters['uidNumber']);
                  $fUserLabel = $fUser->get('id')
                      ? e($fUser->get('name')) . ' (' . e($fUser->get('username')) . ')'
                      : Lang::txt('COM_CART_USER_ID') . ': ' . $filters['uidNumber'];
                @endphp
                {{ Lang::txt('COM_CART_ORDERS_FOR') }}: {{ $fUserLabel }}
                <button type="button" id="filter_uidNumber-clear"
                        class="btn btn-xs btn-ghost">{{ Lang::txt('JSEARCH_FILTER_CLEAR') }}</button>
              @endif
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
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{!! Html::grid('sort', 'COM_CART_PRODUCT', 'product', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_CART_DOWNLOADED_BY', 'dName', $sortDir, $sort) !!}</th>
          <th class="priority-4">{{ Lang::txt('COM_CART_USER_INFO') }}</th>
          <th class="priority-4">{{ Lang::txt('COM_CART_EULA') }}</th>
          <th>{!! Html::grid('sort', 'COM_CART_DOWNLOADED', 'dDownloaded', $sortDir, $sort) !!}</th>
          <th class="priority-3">IP</th>
          <th>{!! Html::grid('sort', 'COM_CART_STATUS', 'dStatus', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $isActive = (int)$row->dStatus === 1;
            $stateTask  = $isActive ? 'inactive' : 'active';
            $stateClass = $isActive ? 'badge-success' : 'badge-ghost';
            $stateText  = $isActive ? Lang::txt('COM_CART_ACTIVE') : Lang::txt('COM_CART_INACTIVE');

            $pFilterUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&pId=' . $row->pId,
                false, false
            );
            $sFilterUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&sId=' . $row->sId,
                false, false
            );
            $uidFilterUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&uidNumber=' . $row->uidNumber,
                false, false
            );
            $stateUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=' . $stateTask
                . '&id=' . $row->dId,
                false, false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->dId }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $row->pName ?? 'Download' }} {{ $row->dId }}"
                     data-check-item />
            </td>
            <td>
              @if($row->pName)
                <a href="{{ $pFilterUrl }}" class="link link-hover text-primary font-medium">
                  {{ $row->pName }}
                </a>
              @else
                <span class="text-error italic">Product n/a</span>
              @endif
              <br />
              SKU:
              @if($row->sSku)
                <a href="{{ $sFilterUrl }}" class="link link-hover text-sm">
                  {{ $row->sSku }}
                </a>
              @else
                <span class="text-error italic">n/a</span>
              @endif
            </td>
            <td>
              @if($row->uidNumber)
                <a href="{{ $uidFilterUrl }}" class="link link-hover">
                  {{ $row->dName }} ({{ $row->username }})
                </a>
              @else
                {{ Lang::txt('COM_CART_UNKNOWN') }}
              @endif
            </td>
            <td class="priority-4">
              @if(!empty($row->meta) && !empty($row->meta['userInfo']))
                @php
                  $meta = unserialize($row->meta['userInfo']['mtValue']);
                  $data = [];
                  if (is_array($meta)) {
                      foreach ($meta as $mtV) {
                          $data[] = is_array($mtV) ? implode('; ', $mtV) : $mtV;
                      }
                  }
                @endphp
                {{ implode(', ', $data) }}
              @endif
            </td>
            <td class="priority-4">
              @if(!empty($row->meta) && !empty($row->meta['eulaAccepted']) && $row->meta['eulaAccepted']['mtValue'])
                <span class="badge badge-sm badge-success whitespace-nowrap">EULA accepted</span>
              @endif
            </td>
            <td>
              <time datetime="{{ $row->dDownloaded }}">{{ $row->dDownloaded }}</time>
            </td>
            <td class="priority-3">{{ $row->dIp }}</td>
            <td>
              @if($canDo->get('core.edit.state'))
                <a href="{{ $stateUrl }}"
                   title="{{ Lang::txt('COM_CART_SET_TASK', $stateTask) }}">
                  <span class="badge badge-sm {{ $stateClass }}">{{ $stateText }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $stateClass }}">{{ $stateText }}</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Extra hidden fields for filter persistence --}}
  <input type="hidden" name="uidNumber" id="filter_uidNumber"
         value="{{ $filters['uidNumber'] }}" />
  <input type="hidden" name="pId" id="filter_pId"
         value="{{ $filters['pId'] }}" />
  <input type="hidden" name="sId" id="filter_sId"
         value="{{ $filters['sId'] }}" />
</x-admin-form>
