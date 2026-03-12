{{--
  Storefront Serial Numbers — Admin list

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
  $sort    = $filters['sort'] ?? 'srId';

  $skuEditUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=skus&task=edit&id=' . $sku->getId(),
      false, false
  );

  $newPopupUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&tmpl=component&task=new&sId=' . $sku->getId(),
      false, false
  );

  $uploadPopupUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&tmpl=component&task=upload&sId=' . $sku->getId(),
      false, false
  );

  Toolbar::title(
      Lang::txt('COM_STOREFRONT') . ': '
      . Lang::txt('COM_STOREFRONT_SKU_SERIAL_NUMBERS'),
      'storefront'
  );
  Toolbar::appendButton('Popup', 'new', Lang::txt('JTOOLBAR_NEW'), $newPopupUrl, 570, 170);
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList();
  }
  Toolbar::spacer();
  Toolbar::appendButton(
      'Popup', 'upload', Lang::txt('COM_STOREFRONT_UPLOAD_CSV'),
      $uploadPopupUrl, 570, 170
  );
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
            {{ Lang::txt('COM_STOREFRONT_SERIAL_NUMBERS_FOR') }}:
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
            {!! Html::grid('sort', 'ID', 'srId', $sortDir, $sort) !!}
          </th>
          <th scope="col">
            {!! Html::grid('sort', 'COM_STOREFRONT_SERIAL_NUMBER', 'srNumber', $sortDir, $sort) !!}
          </th>
          <th scope="col">
            {!! Html::grid('sort', 'COM_STOREFRONT_STATUS', 'srStatus', $sortDir, $sort) !!}
          </th>
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
          <tr>
            <td>
              @if ($row->srStatus == 'available')
                <input type="checkbox"
                       name="srId[]"
                       id="cb{{ $i }}"
                       value="{{ $row->srId }}"
                       class="checkbox checkbox-sm"
                       aria-label="{{ $row->srNumber }}"
                       data-check-item />
              @endif
            </td>
            <td>
              {{ $row->srId }}
            </td>
            <td>
              {{ $row->srNumber }}
            </td>
            <td>
              @if ($row->srStatus == 'available')
                <span class="badge badge-sm badge-success">{{ $row->srStatus }}</span>
              @else
                <span class="badge badge-sm badge-ghost">{{ $row->srStatus }}</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="sId" value="{{ $sId }}" />
</x-admin-form>
