{{--
  Storefront Option Groups — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo   = \Components\Storefront\Admin\Helpers\Permissions::getActions('category');
  $sort    = $filters['sort'] ?? '';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_STOREFRONT') . ': Option Groups', 'storefront');
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
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>
            {!! Html::grid('sort', 'COM_STOREFRONT_TITLE', 'title', $sortDir, $sort) !!}
          </th>
          <th>{{ Lang::txt('Options (published)') }}</th>
          <th>
            {!! Html::grid('sort', 'COM_STOREFRONT_PUBLISHED', 'state', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="4">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach ($rows as $i => $row)
          @php
            switch ($row->ogActive) {
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

            $key       = $row->ogId;
            $countInfo = $options->$key;
            $optTotal  = $countInfo->active + $countInfo->inactive;
            $optDisplay = $optTotal;
            if ($optTotal > 0) {
                $optDisplay .= ' (' . $countInfo->active . ')';
            }
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->ogId }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $row->ogName }}"
                     data-check-item />
            </td>
            <td>
              @if ($canDo->get('core.edit'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&ogId=' . $row->ogId, false) }}"
                   class="link link-hover text-primary font-medium"
                   title="{{ Lang::txt('COM_STOREFRONT_EDIT_CATEGORY') }}">
                  {{ $row->ogName }}
                </a>
              @else
                {{ $row->ogName }}
              @endif
            </td>
            <td>
              <a href="{{ Route::url('index.php?option=' . $option . '&controller=options&task=display&ogId=' . $row->ogId, false) }}"
                 class="link link-hover text-primary"
                 title="{{ Lang::txt('View Options') }}">
                {{ $optDisplay }}
              </a>
              @if ($canDo->get('core.create'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=options&task=add&ogId=' . $row->ogId, false) }}"
                   class="link link-hover text-primary ml-1"
                   title="{{ Lang::txt('Add Option') }}">
                  [+]
                </a>
              @endif
            </td>
            <td class="column-status">
              @if ($canDo->get('core.edit.state'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=' . $stateTask . '&id=' . $row->ogId, false) }}"
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
</x-admin-form>
