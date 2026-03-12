{{--
  Support — Abuse Reports list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  Toolbar::title(
      Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_ABUSE_REPORTS'),
      'support'
  );
  Toolbar::help('abusereports');

  $formAction = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller, false
  );
@endphp

@include('com_support::admin/views/abusereports/tmpl/_submenu')

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Filters --}}
  <div class="flex items-center gap-3 mb-4">
    <label for="filter-state" class="label-text font-medium whitespace-nowrap">
      {{ Lang::txt('COM_SUPPORT_SHOW') }}:
    </label>
    <select name="state" id="filter-state"
            class="select select-bordered select-sm"
            data-submit-on-change>
      <option value="0" @selected($filters['state'] == 0)>
        {{ Lang::txt('COM_SUPPORT_OUTSTANDING') }}
      </option>
      <option value="1" @selected($filters['state'] == 1)>
        {{ Lang::txt('COM_SUPPORT_RELEASED') }}
      </option>
      <option value="2" @selected($filters['state'] == 2)>
        {{ Lang::txt('COM_SUPPORT_DELETED') }}
      </option>
    </select>
  </div>

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="priority-4">{{ Lang::txt('COM_SUPPORT_COL_ID') }}</th>
          <th class="priority-3">{{ Lang::txt('COM_SUPPORT_COL_STATUS') }}</th>
          <th>{{ Lang::txt('COM_SUPPORT_COL_REPORTED_ITEM') }}</th>
          <th class="priority-2">{{ Lang::txt('COM_SUPPORT_COL_REASON') }}</th>
          <th class="priority-3">{{ Lang::txt('COM_SUPPORT_COL_BY') }}</th>
          <th class="priority-4">{{ Lang::txt('COM_SUPPORT_COL_DATE') }}</th>
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
        @forelse($rows as $row)
          @php
            switch ($row->state) {
                case '1':
                    $status      = Lang::txt('COM_SUPPORT_REPORT_RELEASED');
                    $badgeClass  = 'badge-success';
                    break;
                case '0':
                    $status      = Lang::txt('COM_SUPPORT_REPORT_NEW');
                    $badgeClass  = 'badge-warning';
                    break;
                default:
                    $status      = '';
                    $badgeClass  = 'badge-ghost';
            }
            $rowUser = User::getInstance($row->created_by);
            $viewUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=view&id=' . $row->id
                . '&cat=' . $row->category,
                false, false
            );
          @endphp
          <tr>
            <td class="priority-4 text-sm text-muted-foreground">{{ $row->id }}</td>
            <td class="priority-3">
              @if($status)
                <span class="badge {{ $badgeClass }}">{{ $status }}</span>
              @endif
            </td>
            <td>
              <a href="{{ $viewUrl }}"
                 class="link link-hover text-primary font-medium">
                {{ $row->category . ' #' . $row->referenceid }}
              </a>
            </td>
            <td class="priority-2 text-sm">{{ $row->subject }}</td>
            <td class="priority-3 text-sm">{{ $rowUser->get('username') }}</td>
            <td class="priority-4 text-sm text-muted-foreground">
              {{ Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted-foreground py-8">
              {{ Lang::txt('JNONE') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <input type="hidden" name="task" value="display" />
</x-admin-form>
