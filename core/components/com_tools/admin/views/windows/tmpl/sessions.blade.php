{{--
  Windows sessions — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $sort    = $filters['sort'] ?? 'sessionid';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_WINDOWS'), 'tools');
  Toolbar::deleteList('terminate');
  Toolbar::spacer();
  Toolbar::help('sessions');

  $formAction = Route::url(
    'index.php?option=' . $option . '&controller=' . $controller . '&task=sessions', false
  );
@endphp

@include('com_tools::admin.views.windows.tmpl._submenu')

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    action="{{ $formAction }}"
    task="sessions"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  <x-admin-filters>
    <div class="flex items-end gap-2">
      <div>
        <label for="filter_appname" class="label label-text text-base-content">{{ Lang::txt('COM_TOOLS_APPNAME') }}</label>
        <select name="appname" id="filter_appname" class="select select-sm select-bordered" data-submit-on-change>
          <option value="">{{ Lang::txt('COM_TOOLS_APPNAME_SELECT') }}</option>
          @foreach($apps as $app)
            <option value="{{ $app->path }}"
                    @selected(Request::getString('appname', '') === $app->path)>
              {{ $app->title }}
            </option>
          @endforeach
        </select>
      </div>
    </div>
  </x-admin-filters>

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th>{!! Html::grid('sort', 'COM_TOOLS_COL_SESSION', 'sessionid', $sortDir, $sort) !!}</th>
          <th class="priority-2">{!! Html::grid('sort', 'COM_TOOLS_COL_OPAQUE_DATA', 'url', $sortDir, $sort) !!}</th>
          <th class="priority-3">{!! Html::grid('sort', 'COM_TOOLS_COL_STATUS', 'status', $sortDir, $sort) !!}</th>
          <th class="priority-3">{!! Html::grid('sort', 'COM_TOOLS_COL_AVAILABILITY', 'availability', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tbody>
        @forelse($sessions as $s)
          <tr>
            <td class="font-mono text-sm">{{ $s['sessionid'] ?? '' }}</td>
            <td class="priority-2 text-sm break-all">{{ $s['opaquedata'] ?? '' }}</td>
            <td class="priority-3">
              @if(!empty($s['status']))
                <span class="badge badge-sm {{ $s['status'] === 'available' ? 'badge-success' : 'badge-warning' }}">
                  {{ $s['status'] }}
                </span>
              @endif
            </td>
            <td class="priority-3">{{ $s['availability'] ?? '' }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center text-muted-foreground py-6">
              {{ Lang::txt('COM_TOOLS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
