{{--
  Tool Sessions — Admin list view (active sessions)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $sort    = $filters['sort'] ?? 'sessnum';
  $sortDir = $filters['sort_Dir'] ?? 'desc';

  Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_SESSIONS'), 'tools');
  Toolbar::deleteList();
  Toolbar::spacer();
  Toolbar::help('sessions');

  $clearUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&username=&appname=&exechost=&start=0', false);
@endphp

@include('com_tools::admin.views.sessions.tmpl._submenu')

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  @slot('filters')
    <x-admin-filters>
      @slot('search')
        <label for="filter_appname" class="sr-only">{{ Lang::txt('COM_TOOLS_APPNAME_SELECT') }}</label>
        <select name="appname" id="filter_appname" class="select select-bordered select-sm" data-submit-on-change>
          <option value="">{{ Lang::txt('COM_TOOLS_APPNAME_SELECT') }}</option>
          @foreach($appnames as $record)
            <option value="{{ $record->appname }}" @selected(($filters['appname'] ?? '') == $record->appname)>
              {{ $record->appname }}
            </option>
          @endforeach
        </select>

        <label for="filter_exechost" class="sr-only">{{ Lang::txt('COM_TOOLS_EXECHOST_SELECT') }}</label>
        <select name="exechost" id="filter_exechost" class="select select-bordered select-sm" data-submit-on-change>
          <option value="">{{ Lang::txt('COM_TOOLS_EXECHOST_SELECT') }}</option>
          @foreach($exechosts as $record)
            <option value="{{ $record->exechost }}" @selected(($filters['exechost'] ?? '') == $record->exechost)>
              {{ $record->exechost }}
            </option>
          @endforeach
        </select>

        <label for="filter_username" class="sr-only">{{ Lang::txt('COM_TOOLS_USERNAME_SELECT') }}</label>
        <select name="username" id="filter_username" class="select select-bordered select-sm" data-submit-on-change>
          <option value="">{{ Lang::txt('COM_TOOLS_USERNAME_SELECT') }}</option>
          @foreach($usernames as $record)
            <option value="{{ $record->viewuser }}" @selected(($filters['username'] ?? '') == $record->viewuser)>
              {{ $record->viewuser }}
            </option>
          @endforeach
        </select>

        <a href="{{ $clearUrl }}" class="btn btn-sm btn-ghost">{{ Lang::txt('JSEARCH_FILTER_CLEAR') }}</a>
      @endslot
    </x-admin-filters>
  @endslot

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox" class="checkbox checkbox-sm" data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{!! Html::grid('sort', 'COM_TOOLS_COL_SESSION', 'sessnum', $sortDir, $sort) !!}</th>
          <th class="priority-2">{!! Html::grid('sort', 'COM_TOOLS_COL_OWNER', 'username', $sortDir, $sort) !!}</th>
          <th class="priority-2">{!! Html::grid('sort', 'COM_TOOLS_COL_VIEWER', 'viewuser', $sortDir, $sort) !!}</th>
          <th class="priority-4">{!! Html::grid('sort', 'COM_TOOLS_COL_STARTED', 'start', $sortDir, $sort) !!}</th>
          <th class="priority-3">{!! Html::grid('sort', 'COM_TOOLS_COL_LAST_ACCESSED', 'accesstime', $sortDir, $sort) !!}</th>
          <th class="priority-3">{!! Html::grid('sort', 'COM_TOOLS_COL_TOOL', 'appname', $sortDir, $sort) !!}</th>
          <th class="priority-4">{!! Html::grid('sort', 'COM_TOOLS_COL_EXEC_HOST', 'exechost', $sortDir, $sort) !!}</th>
          <th>{{ Lang::txt('COM_TOOLS_COL_STOP') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="9">
            {!! $__view->pagination($total, $filters['start'] ?? 0, $filters['limit'] ?? 25) !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($rows as $i => $row)
          @php
            $usernameUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&username=' . $row->username, false);
            $appnameUrl  = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&appname=' . $row->appname, false);
            $exchostUrl  = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&exechost=' . $row->exechost, false);
            $removeUrl   = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=remove&id=' . $row->sessnum . '&' . Session::getFormToken() . '=1', false);
          @endphp
          <tr>
            <td>
              <input type="checkbox" name="id[]" id="cb{{ $i }}" value="{{ $row->sessnum }}"
                     class="checkbox checkbox-sm" data-check-item aria-label="{{ $row->sessnum }}" />
            </td>
            <td>
              <span class="tooltip" data-tip="{{ $row->sessname }}: {{ $row->exechost }} / {{ $row->remoteip }}">
                {{ $row->sessnum }}
              </span>
            </td>
            <td class="priority-2">
              <a href="{{ $usernameUrl }}" class="link link-hover">{{ $row->username }}</a>
            </td>
            <td class="priority-2">{{ $row->viewuser }}</td>
            <td class="priority-4">
              <time datetime="{{ $row->start }}">{{ $row->start }}</time>
            </td>
            <td class="priority-3">
              <time datetime="{{ $row->accesstime }}">{{ $row->accesstime }}</time>
            </td>
            <td class="priority-3">
              <a href="{{ $appnameUrl }}" class="link link-hover font-mono text-sm">{{ $row->appname }}</a>
            </td>
            <td class="priority-4">
              <a href="{{ $exchostUrl }}" class="link link-hover font-mono text-sm">{{ $row->exechost }}</a>
            </td>
            <td>
              <a href="{{ $removeUrl }}"
                 class="btn btn-xs btn-error btn-outline"
                 title="{{ Lang::txt('COM_TOOLS_TERMINATE') }}">
                {{ Lang::txt('COM_TOOLS_TERMINATE') }}
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center text-muted-foreground py-6">
              {{ Lang::txt('COM_TOOLS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
