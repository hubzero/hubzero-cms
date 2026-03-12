{{--
  Publications Categories — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo   = \Components\Publications\Helpers\Permissions::getActions('category');
  $sort    = $filters['sort'] ?? 'id';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(
      Lang::txt('COM_PUBLICATIONS_PUBLICATIONS') . ': ' . Lang::txt('COM_PUBLICATIONS_CATEGORIES'),
      'category'
  );
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit.state')) {
      Toolbar::editList();
      Toolbar::publishList('changestatus', Lang::txt('COM_PUBLICATIONS_CHANGE_STATUS'));
  }
  if ($canDo->get('core.delete')) {
      Toolbar::spacer();
      Toolbar::deleteList();
  }

  $__view->css();
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
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_PUBLICATIONS_FIELD_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_PUBLICATIONS_FIELD_NAME', 'name', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_PUBLICATIONS_FIELD_CONTRIBUTABLE', 'contributable', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">
            {!! Html::grid('sort', 'COM_PUBLICATIONS_FIELD_STATUS', 'state', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @php $i = 0; @endphp
        @forelse($rows as $row)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->id, false
            );
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
            <td class="priority-4">{{ $row->id }}</td>
            <td>
              <a href="{!! $editUrl !!}" class="link link-hover text-primary font-medium">
                {{ $row->name }}
              </a>
              <div class="text-xs text-muted-foreground mt-0.5">
                {{ Lang::txt('COM_PUBLICATIONS_FIELD_ALIAS') }}: {{ $row->alias }} |
                {{ Lang::txt('COM_PUBLICATIONS_FIELD_URL_ALIAS') }}: {{ $row->url_alias }} |
                {{ Lang::txt('COM_PUBLICATIONS_FIELD_DC_TYPE') }}: {{ $row->dc_type }}
              </div>
            </td>
            <td class="priority-3 text-center">
              @if($row->contributable == 1)
                <span class="badge badge-success">{{ Lang::txt('JYES') }}</span>
              @else
                <span class="badge badge-ghost">{{ Lang::txt('JNO') }}</span>
              @endif
            </td>
            <td class="priority-2 text-center">
              @if($row->state == 1)
                <span class="badge badge-success">{{ Lang::txt('COM_PUBLICATIONS_ON') }}</span>
              @else
                <span class="badge badge-ghost">{{ Lang::txt('COM_PUBLICATIONS_OFF') }}</span>
              @endif
            </td>
          </tr>
          @php $i++; @endphp
        @empty
          <tr>
            <td colspan="5" class="text-center text-muted-foreground py-4">
              {{ Lang::txt('COM_PUBLICATIONS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
