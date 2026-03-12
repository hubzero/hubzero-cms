{{--
  com_installer customexts — Custom extension list

  Variables: $rows, $filters, $pagination, $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo   = \Components\Installer\Admin\Helpers\Installer::getActions();
  $helpers = '\Components\Installer\Admin\Helpers\Installer';
  $sort    = $filters['sort'] ?? 'name';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADER_CUSTOMEXTS'), 'customexts');
  if ($canDo->get('core.create')) {
      Toolbar::addNew('customexts.edit');
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList('COM_INSTALLER_CUSTOMEXTS_DELETE_CONFIRM', 'remove');
  }
  Toolbar::divider();
  if ($canDo->get('core.edit.state')) {
      Toolbar::publish('customexts.publish', 'JTOOLBAR_ENABLE', true);
      Toolbar::unpublish('customexts.unpublish', 'JTOOLBAR_DISABLE', true);
      Toolbar::divider();
  }
  Toolbar::custom('customexts.update', 'refresh', '', 'COM_INSTALLER_CUSTOMEXTS_UPDATE_CODE');
  Toolbar::divider();
  Toolbar::help('customexts');
@endphp

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

      <label for="filter_location" class="sr-only">{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_CLIENT_SELECT') }}</label>
      <select name="filter_location" id="filter_location"
              class="select select-bordered select-sm" data-submit-on-change>
        <option value="">{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_CLIENT_SELECT') }}</option>
        {!! Html::select('options', $helpers::LocationOptions(), 'value', 'text', $filters['client_id'], true) !!}
      </select>

      <label for="filter_status" class="sr-only">{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_STATE_SELECT') }}</label>
      <select name="filter_status" id="filter_status"
              class="select select-bordered select-sm" data-submit-on-change>
        <option value="">{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_STATE_SELECT') }}</option>
        {!! Html::select('options', $helpers::StatusOptions(), 'value', 'text', $filters['status'], true) !!}
      </select>

      <label for="filter_type" class="sr-only">{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_TYPE_SELECT') }}</label>
      <select name="filter_type" id="filter_type"
              class="select select-bordered select-sm" data-submit-on-change>
        <option value="">{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_TYPE_SELECT') }}</option>
        {!! Html::select('options', $helpers::TypeOptions(), 'value', 'text', $filters['type']) !!}
      </select>

      <label for="filter_group" class="sr-only">{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_FOLDER_SELECT') }}</label>
      <select name="filter_group" id="filter_group"
              class="select select-bordered select-sm" data-submit-on-change>
        <option value="">{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_FOLDER_SELECT') }}</option>
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
          <th>{!! Html::grid('sort', 'COM_INSTALLER_CUSTOMEXTS_HEADING_NAME', 'name', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_INSTALLER_CUSTOMEXTS_HEADING_STATUS', 'status', $sortDir, $sort) !!}</th>
          <th class="priority-2">{!! Html::grid('sort', 'COM_INSTALLER_CUSTOMEXTS_HEADING_LOCATION', 'client_id', $sortDir, $sort) !!}</th>
          <th class="priority-3">{!! Html::grid('sort', 'COM_INSTALLER_CUSTOMEXTS_HEADING_TYPE', 'type', $sortDir, $sort) !!}</th>
          <th class="priority-4">{!! Html::grid('sort', 'COM_INSTALLER_CUSTOMEXTS_HEADING_FOLDER', 'folder', $sortDir, $sort) !!}</th>
          <th class="priority-5">{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADING_MODIFIED_ON') }}</th>
          <th class="priority-5">{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADING_MODIFIED_BY') }}</th>
          <th class="priority-4">{!! Html::grid('sort', 'COM_INSTALLER_CUSTOMEXTS_HEADING_ID', 'extension_id', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="9">
            <div class="admin-pagination">{!! $pagination !!}</div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse ($rows as $i => $item)
          @php
            $canChange = $canDo->get('core.edit.state');
            $token     = Session::getFormToken();
            $modifier  = User::getInstance($item->get('modified_by'));
            $modName   = $modifier->get('name', Lang::txt('COM_INSTALLER_CUSTOMEXTS_UNKNOWN')) . ' (' . $item->get('modified_by') . ')';

            if ($item->enabled) {
                $stateCls  = 'badge-success';
                $stateTxt  = Lang::txt('JENABLED');
                $stateTask = 'customexts.unpublish';
            } else {
                $stateCls  = 'badge-ghost';
                $stateTxt  = Lang::txt('JDISABLED');
                $stateTask = 'customexts.publish';
            }
          @endphp
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
              @if ($canDo->get('core.edit'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&id=' . $item->get('extension_id'), false) }}"
                   class="link link-hover text-primary font-medium">
                  {{ $item->get('name') }}
                </a>
              @else
                {{ $item->get('name') }}
              @endif
            </td>
            <td class="text-center">
              @if (!$item->get('alias'))
                <span class="badge badge-sm badge-error">✗</span>
              @elseif ($canChange)
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=' . $stateTask . '&id=' . $item->get('extension_id') . '&' . $token . '=1', false) }}">
                  <span class="badge badge-sm {{ $stateCls }}">{{ $stateTxt }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $stateCls }}">{{ $stateTxt }}</span>
              @endif
            </td>
            <td class="priority-2 text-center text-sm">
              {{ $item->get('client_id') == 1 ? Lang::txt('JADMINISTRATOR') : Lang::txt('JSITE') }}
            </td>
            <td class="priority-3 text-center text-sm">
              {{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_TYPE_' . $item->get('type')) }}
            </td>
            <td class="priority-4 text-center text-sm">
              {{ $item->get('folder') ?: '—' }}
            </td>
            <td class="priority-5 text-sm">
              {{ $item->get('modified') ?: '—' }}
            </td>
            <td class="priority-5 text-sm">
              {{ $modName }}
            </td>
            <td class="priority-4 tabular-nums text-sm">
              {{ $item->get('extension_id') }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center py-8 text-muted-foreground">
              {{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_MSG_MANAGE_NO_EXTENSIONS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
