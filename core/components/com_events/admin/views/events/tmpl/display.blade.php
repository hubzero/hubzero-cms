{{--
  Events — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  Toolbar::title(Lang::txt('COM_EVENTS_MANAGER'), 'event');
  Toolbar::preferences('com_events', '550');
  Toolbar::spacer();
  Toolbar::custom('addpage', 'new', 'COM_EVENTS_PAGES_ADD', 'COM_EVENTS_PAGES_ADD', true, false);
  Toolbar::custom('respondents', 'user', 'COM_EVENTS_VIEW_RESPONDENTS', 'COM_EVENTS_VIEW_RESPONDENTS', true, false);
  Toolbar::spacer();
  Toolbar::publishList();
  Toolbar::unpublishList();
  Toolbar::spacer();
  Toolbar::addNew();
  Toolbar::editList();
  Toolbar::deleteList();
  Toolbar::spacer();
  Toolbar::help('events');

  $database = App::get('db');
  $p = new \Components\Events\Tables\Page($database);
  $now = Date::toSql();
@endphp

<form action="{{ Route::url('index.php?option=' . $option, false) }}"
      method="post" name="adminForm" id="adminForm">

  <fieldset class="admin-filters-bar flex flex-wrap items-end gap-3 mb-4">
    <input type="text"
           name="search"
           id="filter_search"
           class="input input-sm input-bordered w-64"
           placeholder="{{ Lang::txt('COM_EVENTS_SEARCH_PLACEHOLDER') }}"
           value="{{ $filters['search'] ?? '' }}" />

    <label for="catid" class="sr-only">{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CATEGORY') }}</label>
    {!! $clist !!}
    <label for="group_id" class="sr-only">{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ACCESS') }}</label>
    {!! $glist !!}

    <button type="submit" class="btn btn-sm btn-primary">
      {{ Lang::txt('COM_EVENTS_SEARCH_GO') }}
    </button>
  </fieldset>

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th>{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ID') }}</th>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_TITLE') }}</th>
          <th>{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_CATEGORY') }}</th>
          <th>{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_STATE') }}</th>
          <th>{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_TIMESHEET') }}</th>
          <th>{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_ACCESS') }}</th>
          <th>{{ Lang::txt('COM_EVENTS_CAL_LANG_EVENT_PAGES') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit'])->render() !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $checkedOut = $row->checked_out && $row->checked_out != User::get('id');

            $editUrl = Route::url(
                'index.php?option=' . $option . '&controller=' . $controller
                . '&task=edit&id=' . $row->id,
                false, false
            );

            // State logic
            $nullDate = '0000-00-00 00:00:00';
            if ($now <= $row->publish_up && $row->state == '1') {
                $stateText = Lang::txt('COM_EVENTS_EVENT_PENDING');
                $stateCls  = 'badge-warning';
            } elseif (
                ($now <= $row->publish_down || !$row->publish_down || $row->publish_down == $nullDate)
                && $row->state == '1'
            ) {
                $stateText = Lang::txt('COM_EVENTS_EVENT_PUBLISHED');
                $stateCls  = 'badge-success';
            } elseif ($now > $row->publish_down && $row->state == '1') {
                $stateText = Lang::txt('COM_EVENTS_EVENT_EXPIRED');
                $stateCls  = 'badge-error';
            } else {
                $stateText = Lang::txt('COM_EVENTS_EVENT_UNPUBLISHED');
                $stateCls  = 'badge-ghost';
            }

            $stateTask = $row->state ? 'unpublish' : 'publish';

            // Dates
            $pubUp = (!$row->publish_up || $row->publish_up == $nullDate)
                ? Lang::txt('COM_EVENTS_CAL_LANG_ALWAYS')
                : date('Y-m-d H:i', strtotime($row->publish_up));
            $pubDown = (!$row->publish_down || $row->publish_down == $nullDate)
                ? Lang::txt('COM_EVENTS_CAL_LANG_NEVER')
                : date('Y-m-d H:i', strtotime($row->publish_down));

            $pages = $p->getCount(['event_id' => $row->id]);

            $pagesUrl = Route::url(
                'index.php?option=' . $option . '&controller=pages&event_id=' . $row->id,
                false, false
            );
          @endphp
          <tr>
            <td>{{ $row->id }}</td>
            <td class="column-check">
              @if(!$checkedOut)
                <input type="checkbox"
                       name="id[]"
                       id="cb{{ $i }}"
                       value="{{ $row->id }}"
                       class="checkbox checkbox-sm"
                       aria-label="{{ $row->title }}"
                       data-check-item />
              @endif
            </td>
            <td>
              @if($checkedOut)
                <span class="text-muted-foreground">
                  {{ $row->title }}
                </span>
              @else
                <a href="{{ $editUrl }}"
                   class="link link-hover text-primary font-medium">
                  {{ $row->title }}
                </a>
              @endif
            </td>
            <td>{{ $row->category ?? '' }}</td>
            <td class="column-status">
              <a href="#"
                 data-list-item-task
                 data-cb="cb{{ $i }}"
                 data-task="{{ $stateTask }}"
                 aria-label="{{ $stateText }}">
                <span class="badge badge-sm {{ $stateCls }}">{{ $stateText }}</span>
              </a>
            </td>
            <td class="text-xs">
              <div>{{ Lang::txt('COM_EVENTS_CAL_LANG_FROM') }}: {{ $pubUp }}</div>
              <div>{{ Lang::txt('COM_EVENTS_CAL_LANG_TO') }}: {{ $pubDown }}</div>
            </td>
            <td>
              @if($row->scope == 'group')
                @php
                  $group = \Hubzero\User\Group::getInstance($row->scope_id);
                @endphp
                @if(is_object($group))
                  <span class="badge badge-sm badge-info">
                    {{ $group->get('description') }}
                  </span>
                @else
                  <span class="text-muted-foreground text-xs">
                    {{ Lang::txt('COM_EVENTS_EVENT_GROUP_NOT_FOUND', $row->scope_id) }}
                  </span>
                @endif
              @else
                {{ $row->groupname ?? '' }}
              @endif
            </td>
            <td>
              <a href="{{ $pagesUrl }}" class="link link-hover text-primary">
                {{ Lang::txt('COM_EVENTS_EVENT_NUMBER_OF_PAGES', $pages) }}
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" autocomplete="" />
  <input type="hidden" name="boxchecked" value="0" />
  {!! Html::input('token') !!}
</form>
