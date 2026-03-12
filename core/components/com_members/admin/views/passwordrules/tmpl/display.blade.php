{{--
  Password Rules — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo   = \Components\Members\Helpers\Admin::getActions('component');
  $sort    = $filters['sort'] ?? 'ordering';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  if ($canDo->get('core.manage')) {
      Toolbar::custom('restore_default_content', 'refresh', 'refresh', 'COM_MEMBERS_PASSWORD_RESTORE_DEFAULTS', false, false);
      Toolbar::spacer();
  }
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS') }}: {{ Lang::txt('COM_MEMBERS_PASSWORD_RULES') }}"
    icon="user"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<nav role="navigation" class="sub sub-navigation">
  <ul>
    <li>
      <a class="active"
         href="{!! Route::url('index.php?option=' . $option . '&controller=passwordrules', false) !!}">
        {{ Lang::txt('COM_MEMBERS_PASSWORD_RULES') }}
      </a>
    </li>
    <li>
      <a href="{!! Route::url('index.php?option=' . $option . '&controller=passwordblacklist', false) !!}">
        {{ Lang::txt('COM_MEMBERS_PASSWORD_BLACKLIST') }}
      </a>
    </li>
  </ul>
</nav>

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
            {!! Html::grid('sort', 'COM_MEMBERS_PASSWORD_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_MEMBERS_PASSWORD_RULE', 'rule', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_MEMBERS_PASSWORD_DESCRIPTION', 'description', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_MEMBERS_PASSWORD_ORDERING', 'ordering', $sortDir, $sort) !!}
            {!! Html::grid('order', $rows) !!}
          </th>
          <th class="priority-2">
            {!! Html::grid('sort', 'COM_MEMBERS_PASSWORD_ENABLED', 'enabled', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @php $n = $rows->count(); @endphp
        @foreach($rows as $i => $row)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->get('id'), false
            );
            $toggleUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=toggle_enabled&id=' . $row->get('id')
                . '&' . Session::getFormToken() . '=1', false
            );
          @endphp
          <tr>
            <td class="column-check">
              @if($canDo->get('core.edit'))
                <input type="checkbox"
                       name="id[]"
                       id="cb{{ $i }}"
                       value="{{ $row->get('id') }}"
                       class="checkbox checkbox-sm"
                       data-check-item
                       aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $row->get('rule')) }}" />
              @endif
            </td>
            <td class="priority-4">{{ $row->get('id') }}</td>
            <td class="priority-3">{{ $row->get('rule') }}</td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{!! $editUrl !!}" class="link link-hover text-primary font-medium">
                  {{ $row->description }}
                </a>
              @else
                {{ $row->description }}
              @endif
            </td>
            <td>
              @if($canDo->get('core.edit'))
                @if($i > 0)
                  {!! Html::grid('orderUp', $i, 'orderup', '', 'JLIB_HTML_MOVE_UP', true, 'cb') !!}
                @endif
                @if($i < ($n - 1))
                  {!! Html::grid('orderDown', $i, 'orderdown', '', 'JLIB_HTML_MOVE_DOWN', true, 'cb') !!}
                @endif
                <input type="text"
                       name="order[]"
                       size="5"
                       class="input input-bordered input-xs w-16 text-center"
                       value="{{ $row->get('ordering') }}"
                       aria-label="{{ Lang::txt('JFIELD_ORDERING_LABEL') }}"
                       @if(!$row->get('ordering')) disabled @endif />
              @else
                {{ $row->get('ordering') }}
              @endif
            </td>
            <td class="priority-2">
              @if($canDo->get('core.edit'))
                <a href="{!! $toggleUrl !!}"
                   class="badge badge-sm whitespace-nowrap {{ $row->get('enabled') ? 'badge-success' : 'badge-ghost' }}">
                  {{ Lang::txt($row->get('enabled') ? 'JYES' : 'JNO') }}
                </a>
              @else
                <span class="badge badge-sm whitespace-nowrap {{ $row->get('enabled') ? 'badge-success' : 'badge-ghost' }}">
                  {{ Lang::txt($row->get('enabled') ? 'JYES' : 'JNO') }}
                </span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
