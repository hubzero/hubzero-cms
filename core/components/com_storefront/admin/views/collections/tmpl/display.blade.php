{{--
  Storefront Collections — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Storefront\Admin\Helpers\Permissions::getActions('collection');

  $sort    = $filters['sort'] ?? 'title';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_STOREFRONT') . ': Collections', 'storefront');
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
          <th>{!! Html::grid('sort', 'COM_STOREFRONT_TITLE', 'title', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'Alias', 'cAlias', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'Type', 'cType', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_STOREFRONT_PUBLISHED', 'state', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option . '&controller=' . $controller
                . '&task=edit&id=' . $row->cId,
                false, false
            );

            if ($row->cActive == 1) {
                $stateText = Lang::txt('COM_STOREFRONT_PUBLISHED');
                $stateCls  = 'badge-success';
                $stateTask = 'unpublish';
            } elseif ($row->cActive == 2) {
                $stateText = Lang::txt('COM_STOREFRONT_TRASHED');
                $stateCls  = 'badge-error';
                $stateTask = 'publish';
            } else {
                $stateText = Lang::txt('COM_STOREFRONT_UNPUBLISHED');
                $stateCls  = 'badge-ghost';
                $stateTask = 'publish';
            }
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->cId }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $row->cName }}"
                     data-check-item />
            </td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}"
                   class="link link-hover text-primary font-medium">
                  {{ $row->cName }}
                </a>
              @else
                {{ $row->cName }}
              @endif
            </td>
            <td>{{ $row->cAlias }}</td>
            <td>{{ $row->cType }}</td>
            <td class="column-status">
              @if($canDo->get('core.edit.state'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=' . $stateTask . '&id=' . $row->cId . '&' . Session::getFormToken() . '=1', false) }}">
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
