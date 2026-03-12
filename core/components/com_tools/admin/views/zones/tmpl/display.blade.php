{{--
  Tool Zones — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $sort    = $filters['sort'] ?? 'zone';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_ZONES'), 'tools');
  Toolbar::spacer();
  Toolbar::addNew();
  Toolbar::makeDefault('default', 'COM_TOOLS_MAKE_DEFAULT');
  Toolbar::deleteList();
  Toolbar::spacer();
  Toolbar::help('zones');
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
            {!! Html::grid('sort', 'COM_TOOLS_COL_ZONE', 'zone', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">
            {!! Html::grid('sort', 'COM_TOOLS_COL_TYPE', 'type', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_TOOLS_COL_STATE', 'state', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_TOOLS_COL_DEFAULT', 'is_default', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_TOOLS_COL_MASTER', 'master', $sortDir, $sort) !!}
          </th>
          <th class="priority-5">
            {!! Html::grid('sort', 'COM_TOOLS_COL_SSH_KEY', 'ssh_key_path', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">{{ Lang::txt('COM_TOOLS_COL_LOCATIONS') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8">
            {!! $__view->pagination($total, $filters['start'] ?? 0, $filters['limit'] ?? 25) !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($rows as $i => $row)
          @php
            $editUrl     = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&id=' . $row->get('id'), false);
            $stateToggle = ($row->get('state') == 'up') ? 'down' : 'up';
            $stateUrl    = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=state&id=' . $row->get('id') . '&state=' . $stateToggle . '&' . Session::getFormToken() . '=1', false);
            $stateClass  = ($row->get('state') == 'up') ? 'badge-success' : 'badge-error';
            $defClass    = $row->get('is_default') ? 'badge-accent' : 'badge-ghost';
            $locUrl      = Route::url('index.php?option=' . $option . '&controller=locations&zone=' . $row->get('id') . '&tmpl=index', false);
          @endphp
          <tr>
            <td>
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     class="checkbox checkbox-sm"
                     data-check-item
                     aria-label="{{ $row->get('id') }}" />
            </td>
            <td>
              <a href="{{ $editUrl }}" class="link link-hover font-medium">
                {{ $row->get('zone') }}
              </a>
            </td>
            <td class="priority-2">{{ $row->get('type') }}</td>
            <td>
              <a href="{{ $stateUrl }}" class="badge badge-sm {{ $stateClass }}">
                {{ $row->get('state') }}
              </a>
            </td>
            <td>
              <span class="badge badge-sm {{ $defClass }}">
                {{ $row->get('is_default') ? Lang::txt('JYES') : Lang::txt('JNO') }}
              </span>
            </td>
            <td class="priority-4 font-mono text-sm">{{ $row->get('master') }}</td>
            <td class="priority-5 font-mono text-sm">{{ $row->get('ssh_key_path') }}</td>
            <td class="priority-3">
              <a href="{{ $locUrl }}" class="badge badge-sm badge-outline">
                {{ $row->locations('count') }}
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted-foreground py-6">
              {{ Lang::txt('COM_TOOLS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
