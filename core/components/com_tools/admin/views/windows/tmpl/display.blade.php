{{--
  Windows (AppStream apps) — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $sort    = $filters['sort'] ?? 'toolname';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_WINDOWS'), 'tools');
  Toolbar::addNew();
  Toolbar::deleteList();
  Toolbar::spacer();
  Toolbar::help('windows');
@endphp

@include('com_tools::admin.views.windows.tmpl._submenu')

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
            <input type="checkbox" class="checkbox checkbox-sm" data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th class="priority-5">{!! Html::grid('sort', 'COM_TOOLS_COL_ID', 'id', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_TOOLS_COL_NAME', 'toolname', $sortDir, $sort) !!}</th>
          <th class="priority-4">{!! Html::grid('sort', 'COM_TOOLS_COL_TITLE', 'title', $sortDir, $sort) !!}</th>
          <th class="priority-3">{!! Html::grid('sort', 'COM_TOOLS_COL_UUID', 'path', $sortDir, $sort) !!}</th>
          <th class="priority-2">{{ Lang::txt('COM_TOOLS_WINDOWS_SESSIONS_INUSE') }}</th>
          <th class="priority-1">{{ Lang::txt('COM_TOOLS_WINDOWS_SESSIONS_AVAILABLE') }}</th>
          <th>{!! Html::grid('sort', 'COM_TOOLS_COL_STATE', 'state', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8">
            {!! $rows->pagination !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($rows as $i => $row)
          @php
            $appid       = e($row->get('path'));
            $appinfo     = @exec('/usr/bin/hz-aws-appstream getapp --appid ' . escapeshellarg($appid));
            $appArr      = explode('|', $appinfo ?? '');
            $sessInUse   = $appArr[2] ?? '';
            $sessAvail   = $appArr[3] ?? '';
            $editUrl     = Route::url(
              'index.php?option=' . $option . '&controller=' . $controller
              . '&task=edit&id=' . $row->get('id'), false
            );
          @endphp
          <tr>
            <td>
              <input type="checkbox" name="id[]" id="cb{{ $i }}" value="{{ $row->get('id') }}"
                     class="checkbox checkbox-sm" data-check-item aria-label="{{ $row->get('id') }}" />
            </td>
            <td class="priority-5">{{ $row->get('id') }}</td>
            <td>
              <a href="{{ $editUrl }}" class="link link-hover font-medium">
                {{ $row->get('alias', '') }}
              </a>
            </td>
            <td class="priority-4">
              <a href="{{ $editUrl }}" class="link link-hover">
                {{ $row->get('title', '') }}
              </a>
            </td>
            <td class="priority-3 font-mono text-sm">
              <a href="{{ $editUrl }}" class="link link-hover">{{ $appid }}</a>
            </td>
            <td class="priority-2">
              @if($sessInUse !== '')
                <span class="badge badge-sm badge-warning">{{ $sessInUse }}</span>
              @else
                <span class="text-muted-foreground">—</span>
              @endif
            </td>
            <td class="priority-1">
              @if($sessAvail !== '')
                <span class="badge badge-sm badge-success">{{ $sessAvail }}</span>
              @else
                <span class="text-muted-foreground">—</span>
              @endif
            </td>
            <td>
              @php
                $stateClass = $row->get('published') ? 'badge-success' : 'badge-ghost';
                $stateLabel = $row->get('published') ? Lang::txt('JPUBLISHED') : Lang::txt('JUNPUBLISHED');
              @endphp
              <span class="badge badge-sm {{ $stateClass }}">{{ $stateLabel }}</span>
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
