{{--
  Storefront Options — Admin list view (scoped to option group)

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
  $sort    = $filters['sort'] ?? '';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_STOREFRONT') . ': Options', 'storefront');
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

  $ogEditUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=optiongroups&task=edit&ogId='
      . $ogId,
      false, false
  );
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
          <th colspan="3" class="text-left">
            Options for:
            <a href="{{ $ogEditUrl }}"
               class="link link-hover text-primary"
               title="{{ Lang::txt('Edit option group') }}">
              {{ $optionGroup->getName() }}
            </a>
          </th>
        </tr>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>
            {!! Html::grid('sort', 'COM_STOREFRONT_TITLE', 'title', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_STOREFRONT_PUBLISHED', 'state', $sortDir, $sort) !!}
          </th>
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
        @foreach ($rows as $i => $row)
          @php
            switch ($row->oActive) {
                case 1:
                    $stateCls  = 'badge-success';
                    $stateTask = 'unpublish';
                    $stateText = Lang::txt('COM_STOREFRONT_PUBLISHED');
                    break;
                case 2:
                    $stateCls  = 'badge-error';
                    $stateTask = 'publish';
                    $stateText = Lang::txt('COM_STOREFRONT_TRASHED');
                    break;
                case 0:
                default:
                    $stateCls  = 'badge-ghost';
                    $stateTask = 'publish';
                    $stateText = Lang::txt('COM_STOREFRONT_UNPUBLISHED');
                    break;
            }
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->oId }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $row->oName }}"
                     data-check-item />
            </td>
            <td>
              @if ($canDo->get('core.edit'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&id=' . $row->oId, false) }}"
                   class="link link-hover text-primary font-medium"
                   title="{{ Lang::txt('COM_STOREFRONT_EDIT_OPTION') }}">
                  {{ $row->oName }}
                </a>
              @else
                {{ $row->oName }}
              @endif
            </td>
            <td class="column-status">
              @if ($canDo->get('core.edit.state'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=' . $stateTask . '&id=' . $row->oId, false) }}&ogId={{ $row->ogId }}"
                   title="{{ Lang::txt('COM_STOREFRONT_SET_TASK', $stateTask) }}">
                  <span class="badge badge-sm {{ $stateCls }}">{{ $stateText }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $stateCls }}">{{ $stateText }}</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="ogId" value="{{ $ogId }}" />
</x-admin-form>
