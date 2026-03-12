{{--
  Cron Jobs — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Cron\Helpers\Permissions::getActions('component');

  $sort    = $filters['sort'] ?? 'id';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_CRON'), 'cron');
  if ($canDo->get('core.admin')) {
      Toolbar::preferences($option, '550');
      Toolbar::spacer();
  }
  Toolbar::custom('run', 'purge', '', 'COM_CRON_RUN', false);
  Toolbar::spacer();
  if ($canDo->get('core.edit.state')) {
      Toolbar::publishList();
      Toolbar::unpublishList();
      Toolbar::custom('deactivate', 'deactivate', '', 'COM_CRON_DEACTIVATE', false);
      Toolbar::spacer();
  }
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList('COM_CRON_CONFIRM_DELETE');
  }
  Toolbar::spacer();
  Toolbar::help('jobs');
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
          <th>{!! Html::grid('sort', 'COM_CRON_COL_ID', 'id', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_CRON_COL_TITLE', 'title', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_CRON_COL_STATE', 'state', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_CRON_COL_STARTS', 'publish_up', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_CRON_COL_ENDS', 'publish_down', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_CRON_COL_ACTIVE', 'active', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_CRON_COL_LAST_RUN', 'last_run', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_CRON_COL_NEXT_RUN', 'next_run', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="9">
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
                'index.php?option=' . $option . '&controller=' . $controller
                . '&task=edit&id=' . $row->get('id'),
                false, false
            );

            // State
            if ($row->get('state') == 1) {
                $stateText = Lang::txt('JPUBLISHED');
                $stateCls  = 'badge-success';
                $stateTask = 'unpublish';
            } elseif ($row->get('state') == 2) {
                $stateText = Lang::txt('JTRASHED');
                $stateCls  = 'badge-warning';
                $stateTask = 'publish';
            } else {
                $stateText = Lang::txt('JUNPUBLISHED');
                $stateCls  = 'badge-ghost';
                $stateTask = 'publish';
            }

            // Active
            $activeText = $row->get('active') ? Lang::txt('COM_CRON_ACTIVE') : Lang::txt('COM_CRON_INACTIVE');
            $activeCls  = $row->get('active') ? 'badge-success' : 'badge-ghost';

            // Publish Up
            $pubUp = $row->get('publish_up');
            $hasPubUp = $pubUp && $pubUp != '0000-00-00 00:00:00';

            // Publish Down
            $pubDown = $row->get('publish_down');
            $hasPubDown = $pubDown && $pubDown != '0000-00-00 00:00:00';

            // Last Run
            $lastRun = $row->get('last_run');
            $hasLastRun = $lastRun && $lastRun != '0000-00-00 00:00:00';

            // Next Run
            $nextRun = $row->started() ? $row->get('next_run') : $row->get('publish_up');
            $hasNextRun = $nextRun && $nextRun != '0000-00-00 00:00:00';
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
                     data-check-item />
            </td>
            <td>{{ $row->get('id') }}</td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}"
                   class="link link-hover text-primary font-medium">
                  {{ $row->get('title') }}
                </a>
              @else
                {{ $row->get('title') }}
              @endif
            </td>
            <td class="column-status">
              @if($canDo->get('core.edit.state'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=' . $stateTask . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1', false) }}">
                  <span class="badge badge-sm {{ $stateCls }}">{{ $stateText }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $stateCls }}">{{ $stateText }}</span>
              @endif
            </td>
            <td>
              @if($hasPubUp)
                <time datetime="{{ $pubUp }}">
                  {{ Date::of($pubUp)->format(Lang::txt('DATE_FORMAT_HZ1')) }}
                </time>
              @else
                {{ Lang::txt('COM_CRON_NO_DATE_SET') }}
              @endif
            </td>
            <td>
              @if($hasPubDown)
                <time datetime="{{ $pubDown }}">
                  {{ Date::of($pubDown)->format(Lang::txt('DATE_FORMAT_HZ1')) }}
                </time>
              @else
                {{ Lang::txt('COM_CRON_NONE') }}
              @endif
            </td>
            <td>
              <span class="badge badge-sm {{ $activeCls }}">{{ $activeText }}</span>
            </td>
            <td>
              @if($hasLastRun)
                <time datetime="{{ $lastRun }}">{{ $lastRun }}</time>
              @else
                {{ $lastRun }}
              @endif
            </td>
            <td>
              @if($hasNextRun)
                <time datetime="{{ $nextRun }}">{{ $nextRun }}</time>
              @else
                {{ $nextRun }}
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
