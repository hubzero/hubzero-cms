{{--
  Storefront SKUs — Admin list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $canDo   = \Components\Storefront\Admin\Helpers\Permissions::getActions('product');
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $sort    = $filters['sort'] ?? 'title';

  $productEditUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=products&task=edit&id=' . $product->getId(), false
  );
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_STOREFRONT') }}: {{ Lang::txt('COM_STOREFRONT_SKUS') }}"
    icon="storefront"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  <table class="admin-table">
    <thead>
      <tr>
        <th colspan="4">
          {{ Lang::txt('COM_STOREFRONT_SKUS_FOR') }}:
          <a href="{{ $productEditUrl }}"
             title="{{ Lang::txt('COM_STOREFRONT_EDIT_PRODUCT') }}">
            {{ $product->getName() }}
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
          {!! Html::grid('sort', 'COM_STOREFRONT_TITLE', 'title', $sortDir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_STOREFRONT_STATE', 'state', $sortDir, $sort) !!}
        </th>
        <th scope="col">{{ Lang::txt('COM_STOREFRONT_RESTRICTIONS') }}</th>
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
          $editUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=edit&id=' . $row->sId, false
          );
          $restrictUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=restrictions&id=' . $row->sId, false
          );
        @endphp
        <tr>
          <td>
            <input type="checkbox"
                   name="id[]"
                   id="cb{{ $i }}"
                   value="{{ $row->sId }}"
                   class="checkbox checkbox-sm"
                   aria-label="{{ $row->sSku }}"
                   data-check-item />
          </td>
          <td>
            @if ($canDo->get('core.edit'))
              <a href="{{ $editUrl }}"
                 title="{{ Lang::txt('COM_STOREFRONT_EDIT_SKU') }}">
                {{ $row->sSku }}
              </a>
            @else
              {{ $row->sSku }}
            @endif
          </td>
          <td>
            @if ($row->sActive == 1)
              <span class="badge badge-success">{{ Lang::txt('COM_STOREFRONT_PUBLISHED') }}</span>
            @elseif ($row->sActive == 2)
              <span class="badge badge-warning">{{ Lang::txt('COM_STOREFRONT_TRASHED') }}</span>
            @else
              <span class="badge badge-ghost">{{ Lang::txt('COM_STOREFRONT_UNPUBLISHED') }}</span>
            @endif
          </td>
          <td>
            @if ($row->sRestricted)
              @if ($canDo->get('core.edit'))
                <a href="{{ $restrictUrl }}"
                   title="{{ Lang::txt('COM_STOREFRONT_VIEW_RESTRICTIONS') }}">
                  {{ Lang::txt('COM_STOREFRONT_RESTRICTED') }}
                </a>
              @else
                {{ Lang::txt('COM_STOREFRONT_RESTRICTED') }}
              @endif
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>

  <input type="hidden" name="pId" value="{{ $pId }}" />
</x-admin-form>
