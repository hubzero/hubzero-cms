{{--
  Publications Master Types — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Plugin;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo      = \Components\Publications\Helpers\Permissions::getActions('type');
  $sort       = $filters['sort'] ?? 'ordering';
  $sortDir    = $filters['sort_Dir'] ?? 'asc';
  $pagination = $__view->pagination($total, $filters['start'] ?? 0, $filters['limit'] ?? 20);

  Toolbar::title(
      Lang::txt('COM_PUBLICATIONS_PUBLICATIONS') . ': ' . Lang::txt('COM_PUBLICATIONS_MASTER_TYPES'),
      'publications'
  );
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::spacer();
      Toolbar::deleteList();
  }
  Toolbar::divider();
  Toolbar::help('types');
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
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_PUBLICATIONS_FIELD_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_PUBLICATIONS_FIELD_NAME', 'type', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">{{ Lang::txt('COM_PUBLICATIONS_FIELD_ALIAS') }}</th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_PUBLICATIONS_FIELD_CONTRIBUTABLE', 'contributable', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_PUBLICATIONS_FIELD_ORDER', 'ordering', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            <div class="admin-pagination">
              {!! $pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($rows as $i => $row)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->id, false
            );
            $cClass = $row->contributable == 1 ? 'badge-success' : 'badge-ghost';
            $cLabel = $row->contributable == 1 ? Lang::txt('jon') : Lang::txt('joff');
            $n      = count($rows);
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->id }}"
                     class="checkbox checkbox-sm"
                     data-check-item />
              <label for="cb{{ $i }}" class="sr-only">{{ $row->id }}</label>
            </td>
            <td class="priority-3">{{ $row->id }}</td>
            <td>
              <a href="{!! $editUrl !!}" class="link link-hover text-primary font-medium">
                {{ $row->type }}
              </a>
            </td>
            <td class="priority-4 text-muted-foreground text-sm">
              {{ $row->alias }}
            </td>
            <td class="priority-3 text-center">
              <span class="badge {{ $cClass }}">{{ $cLabel }}</span>
            </td>
            <td class="order">
              <span>
                @if($i > 0)
                  {!! Html::grid('orderUp', $i, 'orderup', '', 'JLIB_HTML_MOVE_UP', true, 'cb') !!}
                @else
                  &#160;
                @endif
              </span>
              <span>
                @if($i < ($n - 1))
                  {!! Html::grid('orderDown', $i, 'orderdown', '', 'JLIB_HTML_MOVE_DOWN', true, 'cb') !!}
                @else
                  &#160;
                @endif
              </span>
              <input type="hidden" name="order[]" value="{{ $row->ordering }}" />
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted-foreground py-4">
              {{ Lang::txt('COM_PUBLICATIONS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
