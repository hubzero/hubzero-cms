{{--
  Storefront Whitelist — Admin list of whitelisted users for a SKU

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo   = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $sort    = $filters['sort'] ?? 'uId';

  $skuEditUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=skus&task=edit&id=' . $sku->getId(),
      false, false
  );

  $newPopupUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&tmpl=component&task=new&id=' . $sku->getId(),
      false, false
  );

  Toolbar::title(
      Lang::txt('COM_STOREFRONT') . ': '
      . Lang::txt('COM_STOREFRONT_SKU_WHITELIST'),
      'storefront'
  );
  Toolbar::appendButton('Popup', 'new', 'Add Users', $newPopupUrl, 570, 170);
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList();
  }
  Toolbar::spacer();
  Toolbar::cancel();
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th colspan="4">
            Whitelisted users for:
            <a href="{{ $skuEditUrl }}"
               title="{{ Lang::txt('COM_STOREFRONT_EDIT_SKU') }}">
              {{ $sku->getName() }}
            </a>
          </th>
        </tr>
        <tr>
          <th scope="col" class="w-4">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th scope="col">
            {!! Html::grid('sort', 'ID', 'uId', $sortDir, $sort) !!}
          </th>
          <th scope="col">
            {!! Html::grid('sort', 'Name', 'name', $sortDir, $sort) !!}
          </th>
          <th scope="col">Email</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="4">
            {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach ($rows as $i => $row)
          @php
            if (!$row->uId) {
                $row->uId      = '--';
                $row->name     = '[UNREGISTERED]';
                $row->email    = '--';
                $row->username = $row->uName;
            }
          @endphp
          <tr>
            <td>
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->id }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $row->name }} ({{ $row->username }})"
                     data-check-item />
            </td>
            <td>
              {{ $row->uId }}
            </td>
            <td>
              {{ $row->name }}
              ({{ $row->username }})
            </td>
            <td>
              {{ $row->email }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="sId" value="{{ $sId }}" />
  <input type="hidden" name="pId" value="{{ $sku->getProductId() }}" />
</x-admin-form>
