{{--
  Groups Roles — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Groups\Helpers\Permissions::getActions('group');
  $tmpl  = Request::getCmd('tmpl', '');

  $sort    = $filters['sort'] ?? 'id';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  if ($tmpl != 'component') {
      Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . Lang::txt('COM_GROUPS_ROLES'), 'groups');
      if ($canDo->get('core.create')) {
          Toolbar::addNew();
      }
      if ($canDo->get('core.edit')) {
          Toolbar::editList();
      }
      if ($canDo->get('core.delete')) {
          Toolbar::deleteList('COM_GROUPS_DELETE_CONFIRM', 'delete');
      }
      Toolbar::spacer();
      Toolbar::help('groups');
  }
@endphp

@php
  $formUrl = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller, false
  );
@endphp

<form action="{{ $formUrl }}" method="post" name="adminForm" id="adminForm">

  @if($tmpl == 'component')
    <fieldset>
      <div class="configuration">
        <div class="flex gap-2">
          <button type="button" class="btn btn-sm btn-primary" data-submit-task="add">
            {{ Lang::txt('JACTION_CREATE') }}
          </button>
          <button type="button" class="btn btn-sm btn-error" data-submit-task="remove">
            {{ Lang::txt('JACTION_DELETE') }}
          </button>
        </div>
        {{ Lang::txt('COM_GROUPS_ROLES') }}
      </div>
    </fieldset>
  @endif

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th colspan="3">
            @php
              $groupCn   = e($group->get('cn'));
              $groupDesc = $group->get('description');
            @endphp
            @if($tmpl != 'component')
              <a href="{!! Route::url('index.php?option=' . $option, false) !!}">
                {{ Lang::txt('COM_GROUPS') }}
              </a>
            @else
              {{ Lang::txt('COM_GROUPS') }}
            @endif
            &gt; ({{ $groupCn }}) {{ $groupDesc }}
          </th>
        </tr>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_GROUPS_FIELD_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_GROUPS_NAME', 'name', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="3">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->get('id')
                . '&gid=' . $filters['gid']
                . ($tmpl ? '&tmpl=' . $tmpl : ''), false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $row->get('name')) }}"
                     data-check-item />
            </td>
            <td class="priority-4">{{ $row->get('id') }}</td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{!! $editUrl !!}" class="link link-hover font-medium">
                  {{ $row->get('name') }}
                </a>
              @else
                <span>{{ $row->get('name') }}</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="gid" value="{{ $filters['gid'] }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" autocomplete="off" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="tmpl" value="{{ $tmpl }}" />
  <input type="hidden" name="filter_order" value="{{ $sort }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $sortDir }}" />
  {!! Html::input('token') !!}
</form>
