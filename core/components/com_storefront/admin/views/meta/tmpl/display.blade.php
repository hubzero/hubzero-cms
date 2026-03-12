{{--
  Storefront Meta — Admin product list for meta editing

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
  $sort    = $filters['sort'] ?? 'title';

  Toolbar::title(Lang::txt('COM_STOREFRONT') . ': ' . Lang::txt('COM_STOREFRONT_PRODUCT_META'), 'storefront');
  if ($canDo->get('core.edit.state')) {
      Toolbar::publishList();
      Toolbar::unpublishList();
      Toolbar::spacer();
  }
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList();
  }
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
            {!! Html::grid('sort', 'COM_STOREFRONT_PRODUCT_TYPE', 'ptName', $sortDir, $sort) !!}
          </th>
          <th scope="col">
            {!! Html::grid('sort', 'COM_STOREFRONT_PUBLISHED', 'state', $sortDir, $sort) !!}
          </th>
          <th scope="col">
            {!! Html::grid('sort', 'COM_STOREFRONT_ACCESS', 'access', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5">
            {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach ($rows as $i => $row)
          @php
            switch ($row->pActive) {
                case 1:
                    $stateClass = 'badge-success';
                    $stateTask  = 'unpublish';
                    $stateAlt   = Lang::txt('COM_STOREFRONT_PUBLISHED');
                    break;
                case 2:
                    $stateClass = 'badge-warning';
                    $stateTask  = 'publish';
                    $stateAlt   = Lang::txt('COM_STOREFRONT_TRASHED');
                    break;
                default:
                    $stateClass = 'badge-ghost';
                    $stateTask  = 'publish';
                    $stateAlt   = Lang::txt('COM_STOREFRONT_UNPUBLISHED');
                    break;
            }

            if (!$row->access) {
                $accessLabel = 'public';
            } elseif ($row->access == 1) {
                $accessLabel = 'registered';
            } else {
                $accessLabel = 'special';
            }
          @endphp
          <tr>
            <td>
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->pId }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $row->pName }}"
                     data-check-item />
            </td>
            <td>
              @if ($canDo->get('core.edit'))
                @php
                  $editUrl = Route::url(
                      'index.php?option=' . $option
                      . '&controller=' . $controller
                      . '&task=edit&id=' . $row->pId,
                      false, false
                  );
                @endphp
                <a href="{{ $editUrl }}"
                   title="{{ Lang::txt('COM_STOREFRONT_EDIT_PRODUCT') }}">
                  {{ $row->pName }}
                </a>
              @else
                {{ $row->pName }}
              @endif
            </td>
            <td>
              {{ $row->ptName }}
            </td>
            <td>
              <span class="badge badge-sm {{ $stateClass }}">{{ $stateAlt }}</span>
            </td>
            <td>
              <span class="badge badge-sm badge-outline">{{ $accessLabel }}</span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="cid" value="{{ $filters['section'] ?? '' }}" />
</x-admin-form>
