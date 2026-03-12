{{--
  Downloads by SKU — Admin summary view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo   = \Components\Cart\Admin\Helpers\Permissions::getActions('download');
  $sort    = $filters['sort'] ?? 'downloaded';
  $sortDir = $filters['sort_Dir'] ?? 'DESC';
@endphp

{{-- Toolbar --}}
@php
  Toolbar::title(Lang::txt('COM_CART') . ': ' . Lang::txt('COM_CART_SOFTWARE_DOWNLOADS') . ' by SKU', 'cart');

  if ($canDo->get('core.admin')) {
      Toolbar::preferences($option, '550');
      Toolbar::spacer();
  }
  Toolbar::custom('downloadSku', 'download.png', '', 'Download CSV', false);
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
        <tr>
          <th>{!! Html::grid('sort', 'COM_CART_PRODUCT', 'product', $sortDir, $sort) !!}</th>
          <th>{{ Lang::txt('COM_CART_SKU') }}</th>
          <th>{!! Html::grid('sort', 'COM_CART_DOWNLOADED', 'downloaded', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="3">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $row)
          @php
            $pUrl = Route::url(
                'index.php?option=com_storefront&controller=products&task=edit&id=' . $row->pId,
                false, false
            );
            $sUrl = Route::url(
                'index.php?option=com_storefront&controller=skus&task=edit&id=' . $row->sId,
                false, false
            );
            $dlUrl = Route::url(
                'index.php?option=com_cart&controller=downloads&task=display&skuRequested=' . $row->sId,
                false, false
            );
          @endphp
          <tr>
            <td>
              @if($row->pName)
                <a href="{{ $pUrl }}" class="link link-hover text-primary font-medium">
                  {{ $row->pName }}
                </a>
              @else
                <span class="text-error italic">Product n/a</span>
              @endif
            </td>
            <td>
              @if($row->sSku)
                <a href="{{ $sUrl }}" class="link link-hover">{{ $row->sSku }}</a>
              @else
                <span class="text-error italic">SKU n/a</span>
              @endif
            </td>
            <td>
              <a href="{{ $dlUrl }}" class="link link-hover">{{ $row->downloaded }}</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
