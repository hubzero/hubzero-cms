{{--
  com_installer manage — Extension list

  Variables: $rows, $filters, $pagination, $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $canDo    = \Components\Installer\Admin\Helpers\Installer::getActions();
  $helpers  = '\Components\Installer\Admin\Helpers\Installer';
  $sort     = $filters['sort'] ?? 'name';
  $sortDir  = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_INSTALLER_HEADER_MANAGE'), 'install');
  if ($canDo->get('core.edit.state')) {
      Toolbar::publish('manage.publish', 'JTOOLBAR_ENABLE', true);
      Toolbar::unpublish('manage.unpublish', 'JTOOLBAR_DISABLE', true);
      Toolbar::divider();
  }
  Toolbar::custom('manage.refresh', 'refresh', 'refresh', 'JTOOLBAR_REFRESH_CACHE', true);
  Toolbar::divider();
  if ($canDo->get('core.admin')) {
      Toolbar::preferences('com_installer');
      Toolbar::divider();
  }
  Toolbar::help('manage');
@endphp

<nav role="navigation" class="sub sub-navigation">
  <ul>
    <li>
      <a class="active" href="{!! Route::url('index.php?option=' . $option . '&controller=manage', false) !!}">
        {{ Lang::txt('COM_INSTALLER_SUBMENU_CORE_EXTENSIONS') }}
      </a>
    </li>
    <li>
      <a href="{!! Route::url('index.php?option=' . $option . '&controller=migrations', false) !!}">
        {{ Lang::txt('COM_INSTALLER_SUBMENU_MIGRATIONS') }}
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
  @slot('filters')
    <x-admin-filters>
      @slot('search')
        <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
        <input type="text"
               name="filter_search"
               id="filter_search"
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] }}"
               placeholder="{{ Lang::txt('JSEARCH_FILTER_LABEL') }}" />
        <button type="submit" class="btn btn-sm btn-primary">
          {{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}
        </button>
        <button type="button" class="btn btn-sm btn-ghost border border-base-300" data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      <label for="filter_location" class="sr-only">{{ Lang::txt('COM_INSTALLER_VALUE_CLIENT_SELECT') }}</label>
      <select name="filter_location" id="filter_location"
              class="select select-bordered select-sm" data-submit-on-change>
        <option value="">{{ Lang::txt('COM_INSTALLER_VALUE_CLIENT_SELECT') }}</option>
        {!! Html::select('options', $helpers::LocationOptions(), 'value', 'text', $filters['client_id'], true) !!}
      </select>

      <label for="filter_status" class="sr-only">{{ Lang::txt('COM_INSTALLER_VALUE_STATE_SELECT') }}</label>
      <select name="filter_status" id="filter_status"
              class="select select-bordered select-sm" data-submit-on-change>
        <option value="">{{ Lang::txt('COM_INSTALLER_VALUE_STATE_SELECT') }}</option>
        {!! Html::select('options', $helpers::StatusOptions(), 'value', 'text', $filters['status'], true) !!}
      </select>

      <label for="filter_type" class="sr-only">{{ Lang::txt('COM_INSTALLER_VALUE_TYPE_SELECT') }}</label>
      <select name="filter_type" id="filter_type"
              class="select select-bordered select-sm" data-submit-on-change>
        <option value="">{{ Lang::txt('COM_INSTALLER_VALUE_TYPE_SELECT') }}</option>
        {!! Html::select('options', $helpers::TypeOptions(), 'value', 'text', $filters['type']) !!}
      </select>

      <label for="filter_group" class="sr-only">{{ Lang::txt('COM_INSTALLER_VALUE_FOLDER_SELECT') }}</label>
      <select name="filter_group" id="filter_group"
              class="select select-bordered select-sm" data-submit-on-change>
        <option value="">{{ Lang::txt('COM_INSTALLER_VALUE_FOLDER_SELECT') }}</option>
        {!! Html::select('options', $helpers::GroupOptions(), 'value', 'text', $filters['group']) !!}
      </select>
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
          <th>{!! Html::grid('sort', 'COM_INSTALLER_HEADING_NAME', 'name', $sortDir, $sort) !!}</th>
          <th class="priority-2">{!! Html::grid('sort', 'COM_INSTALLER_HEADING_LOCATION', 'client_id', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'JSTATUS', 'status', $sortDir, $sort) !!}</th>
          <th class="priority-3">{!! Html::grid('sort', 'COM_INSTALLER_HEADING_TYPE', 'type', $sortDir, $sort) !!}</th>
          <th class="priority-4">{{ Lang::txt('HVERSION') }}</th>
          <th class="priority-5">{{ Lang::txt('JDATE') }}</th>
          <th class="priority-5">{{ Lang::txt('JAUTHOR') }}</th>
          <th class="priority-4">{!! Html::grid('sort', 'COM_INSTALLER_HEADING_FOLDER', 'folder', $sortDir, $sort) !!}</th>
          <th class="priority-4">{!! Html::grid('sort', 'COM_INSTALLER_HEADING_ID', 'extension_id', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="10">
            <div class="admin-pagination">{!! $pagination !!}</div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse ($rows as $i => $item)
          @php
            $item->translate();
            $isProtected = ($item->get('status') == 2);
            $canChange   = $canDo->get('core.edit.state');
            $token       = Session::getFormToken();

            if ($item->enabled) {
                $stateCls  = 'badge-success';
                $stateTxt  = Lang::txt('JENABLED');
                $stateTask = 'manage.unpublish';
            } else {
                $stateCls  = 'badge-ghost';
                $stateTxt  = Lang::txt('JDISABLED');
                $stateTask = 'manage.publish';
            }
          @endphp
          @php $protectedCls = $isProtected ? ' text-muted-foreground' : ''; @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $item->get('extension_id') }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $item->get('name') }}"
                     data-check-item />
            </td>
            <td>
              <span class="{{ $protectedCls }}" title="{{ $item->get('name') . ' — ' . $item->get('description') }}">
                {{ $item->get('name') }}
              </span>
              @if ($isProtected)
                <br><span class="text-[0.65rem] text-muted-foreground">{{ Lang::txt('COM_INSTALLER_EXTENSION_PROTECTED') }}</span>
              @endif
            </td>
            <td class="priority-2 text-center">
              <span class="text-sm text-muted-foreground">{{ $item->get('client') }}</span>
            </td>
            <td class="text-center">
              @if (!$item->get('element'))
                <span class="badge badge-sm badge-error">✗</span>
              @elseif ($canChange)
                <a href="{{ Route::url('index.php?option=com_installer&controller=manage&task=' . $stateTask . '&id=' . $item->get('extension_id') . '&' . $token . '=1', false) }}">
                  <span class="badge badge-sm {{ $stateCls }}">{{ $stateTxt }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $stateCls }}">{{ $stateTxt }}</span>
              @endif
            </td>
            <td class="priority-3 text-center text-sm{{ $protectedCls }}">
              {{ Lang::txt('COM_INSTALLER_TYPE_' . $item->get('type')) }}
            </td>
            <td class="priority-4 text-center text-sm tabular-nums{{ $protectedCls }}">
              {{ $item->get('version') ?: '—' }}
            </td>
            <td class="priority-5 text-center text-sm{{ $protectedCls }}">
              {{ $item->get('creationDate') ?: '—' }}
            </td>
            <td class="priority-5 text-sm{{ $protectedCls }}">
              {{ $item->get('author') ?: '—' }}
            </td>
            <td class="priority-4 text-center text-sm{{ $protectedCls }}">
              {{ $item->get('folder') ?: Lang::txt('COM_INSTALLER_TYPE_NONAPPLICABLE') }}
            </td>
            <td class="priority-4 tabular-nums text-sm{{ $protectedCls }}">
              {{ $item->get('extension_id') }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="10" class="text-center py-8 text-muted-foreground">
              {{ Lang::txt('COM_INSTALLER_MSG_MANAGE_NOEXTENSION') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
