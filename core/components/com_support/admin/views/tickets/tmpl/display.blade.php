{{--
  Support — Tickets list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $__view->css()->css('support.blade')->js('tickets.blade.js');

  // -- Toolbar setup --
  Toolbar::title(Lang::txt('COM_SUPPORT') . ': ' . Lang::txt('COM_SUPPORT_TICKETS'), 'support');
  Toolbar::preferences('com_support', '550');
  Toolbar::spacer();
  Toolbar::addNew();
  Toolbar::deleteList();
  Toolbar::spacer();
  Toolbar::help('tickets');

  // -- Pre-compute URLs --
  $saveOrderingUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=queries&task=saveordering&' . Session::getFormToken() . '=1', false
  );

  $watchOpenUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&show=-1&limitstart=0'
      . (intval($filters['show']) != -1 ? '&search=' : ''), false
  );

  $watchClosedUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&show=-2&limitstart=0'
      . (intval($filters['show']) != -2 ? '&search=' : ''), false
  );

  $newQueryUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=queries&task=add&tmpl=component&' . Session::getFormToken() . '=1', false
  );

  $addFolderUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=queries&task=addfolder&' . Session::getFormToken() . '=1', false
  );

  $saveFolderRootUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=queries&task=savefolder&' . Session::getFormToken() . '=1', false
  );

  $ticketFormAction = Route::url(
      'index.php?option=' . $option . '&controller=' . $controller, false
  );

  // -- Sort direction helpers --
  $direction   = strtolower($filters['sortdir']) == 'desc' ? 'asc' : 'desc';
  $sortDir     = strtolower($filters['sortdir']);
  $sortClickLabel = Lang::txt('COM_SUPPORT_COL_CLICK_TO_SORT');

  // -- Collect unique status colors up front --
  $statusColors = [];
  foreach ($rows as $row) {
      $sid = $row->status->get('id');
      if (!isset($statusColors[$sid])) {
          $statusColors[$sid] = $row->status->get('color');
      }
  }
@endphp

<div class="panel" id="panes">
  <div class="panel-row">

    {{-- ===== LEFT PANE: Query sidebar ===== --}}
    <div class="pane pane-queries"
         id="queries"
         data-update="{{ $saveOrderingUrl }}">
      <div class="pane-inner">

        {{-- Watch list --}}
        <ul id="watch-list">
          <li id="folder_watching" class="open">
            <span class="icon-watch folder">
              {{ Lang::txt('COM_SUPPORT_QUERIES_WATCHING') }}
            </span>
            <ul id="queries_watching" class="wqueries">
              <li class="{{ intval($filters['show']) == -1 ? 'active' : '' }}">
                <a class="query" href="{{ $watchOpenUrl }}">
                  {{ Lang::txt('COM_SUPPORT_QUERIES_WATCHING_OPEN') }}
                  <span>{{ $watch['open'] }}</span>
                </a>
              </li>
              <li class="{{ intval($filters['show']) == -2 ? 'active' : '' }}">
                <a class="query" href="{{ $watchClosedUrl }}">
                  {{ Lang::txt('COM_SUPPORT_QUERIES_WATCHING_CLOSED') }}
                  <span>{{ $watch['closed'] }}</span>
                </a>
              </li>
            </ul>
          </li>
        </ul>

        {{-- Saved query folders --}}
        <ul id="query-list">
          @foreach ($folders as $folder)
            @php
              $deleteFolderUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=queries&task=removefolder&id=' . $folder->id
                  . '&' . Session::getFormToken() . '=1', false
              );
              $editFolderUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=queries&task=editfolder&id=' . $folder->id
                  . '&tmpl=component&' . Session::getFormToken() . '=1', false
              );
              $saveFolderUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=queries&task=savefolder&'
                  . Session::getFormToken() . '=1&fields[id]=' . $folder->id, false
              );
            @endphp
            <li id="folder_{{ $folder->id }}" class="open">
              <span class="icon-folder folder"
                    id="{{ $folder->id }}-title"
                    data-id="{{ $folder->id }}">{{ $folder->title }}</span>
              <span class="folder-options">
                <a class="delete"
                   href="{{ $deleteFolderUrl }}"
                   title="{{ Lang::txt('JACTION_DELETE') }}">
                  {{ Lang::txt('JACTION_DELETE') }}
                </a>
                <a class="edit editfolder"
                   data-id="{{ $folder->id }}"
                   href="{{ $editFolderUrl }}"
                   data-href="{{ $saveFolderUrl }}"
                   title="{{ Lang::txt('JACTION_EDIT') }}">
                  {{ Lang::txt('JACTION_EDIT') }}
                </a>
              </span>
              <ul id="queries_{{ $folder->id }}" class="queries">
                @foreach ($folder->queries as $query)
                  @php
                    $querySearchParam = (intval($filters['show']) != $query->id)
                        ? '&search=&limitstart=0' : '';
                    $queryUrl = Route::url(
                        'index.php?option=' . $option
                        . '&controller=' . $controller
                        . '&show=' . $query->id . $querySearchParam, false
                    );
                    $deleteQueryUrl = Route::url(
                        'index.php?option=' . $option
                        . '&controller=queries&task=remove&id=' . $query->id
                        . '&' . Session::getFormToken() . '=1', false
                    );
                    $editQueryUrl = Route::url(
                        'index.php?option=' . $option
                        . '&controller=queries&task=edit&id=' . $query->id
                        . '&tmpl=component&' . Session::getFormToken() . '=1', false
                    );
                    $deleteConfirm = Lang::txt('COM_SUPPORT_QUERIES_CONFIRM_DELETE');
                  @endphp
                  <li id="query_{{ $query->id }}"
                      class="{{ intval($filters['show']) == $query->id ? 'active' : '' }}">
                    <a class="query" href="{{ $queryUrl }}">
                      {{ $query->title }}
                      <span>{{ $query->count }}</span>
                    </a>
                    <span class="query-options">
                      <a class="delete"
                         href="{{ $deleteQueryUrl }}"
                         title="{{ Lang::txt('JACTION_DELETE') }}"
                         data-confirm="{{ $deleteConfirm }}">
                        {{ Lang::txt('JACTION_DELETE') }}
                      </a>
                      <a class="modal edit"
                         href="{{ $editQueryUrl }}"
                         title="{{ Lang::txt('JACTION_EDIT') }}"
                         rel="{handler: 'iframe', size: {x: 570, y: 550}}">
                        {{ Lang::txt('JACTION_EDIT') }}
                      </a>
                    </span>
                  </li>
                @endforeach
              </ul>
            </li>
          @endforeach
        </ul>

        {{-- Controls: new query (modal), new folder --}}
        <ul class="controls">
          <li>
            <a class="icon-list modal"
               id="new-query"
               href="{{ $newQueryUrl }}"
               rel="{handler: 'iframe', size: {x: 570, y: 550}}"
               title="{{ Lang::txt('COM_SUPPORT_ADD_CUSTOM_QUERY') }}">
              {{ Lang::txt('COM_SUPPORT_ADD_CUSTOM_QUERY') }}
            </a>
          </li>
          <li>
            <a class="icon-folder"
               id="new-folder"
               href="{{ $addFolderUrl }}"
               data-href="{{ $saveFolderRootUrl }}"
               data-prompt="{{ Lang::txt('COM_SUPPORT_FOLDER_NAME') }}"
               title="{{ Lang::txt('COM_SUPPORT_ADD_FOLDER') }}">
              {{ Lang::txt('COM_SUPPORT_ADD_FOLDER') }}
            </a>
          </li>
        </ul>

      </div>
    </div>{{-- / .pane-queries --}}

    {{-- ===== CENTER PANE: Ticket list ===== --}}
    <div class="pane pane-list">
      <div class="pane-inner" id="tickets">

        <form action="{{ $ticketFormAction }}"
              method="post"
              name="adminForm"
              id="ticketForm">

          <div class="list-options">

            {{-- Sort bar --}}
            <ul class="sort-options">
              <li>
                <span class="sort-header">{{ Lang::txt('COM_SUPPORT_SORT_RESULTS') }}</span>
                <ul>
                  <li>
                    <a{{ $filters['sort'] == 'created' ? ' class="active ' . $sortDir . '"' : '' }}
                       href="#"
                       data-sort="created"
                       data-direction="{{ $direction }}"
                       title="{{ $sortClickLabel }}">
                      {{ Lang::txt('COM_SUPPORT_COL_AGE') }}
                    </a>
                  </li>
                  <li>
                    <a{{ $filters['sort'] == 'status' ? ' class="active ' . $sortDir . '"' : '' }}
                       href="#"
                       data-sort="status"
                       data-direction="{{ $direction }}"
                       title="{{ $sortClickLabel }}">
                      {{ Lang::txt('COM_SUPPORT_COL_STATUS') }}
                    </a>
                  </li>
                  <li>
                    <a{{ $filters['sort'] == 'severity' ? ' class="active ' . $sortDir . '"' : '' }}
                       href="#"
                       data-sort="severity"
                       data-direction="{{ $direction }}"
                       title="{{ $sortClickLabel }}">
                      {{ Lang::txt('COM_SUPPORT_COL_SEVERITY') }}
                    </a>
                  </li>
                  <li>
                    <a{{ $filters['sort'] == 'summary' ? ' class="active ' . $sortDir . '"' : '' }}
                       href="#"
                       data-sort="summary"
                       data-direction="{{ $direction }}"
                       title="{{ $sortClickLabel }}">
                      {{ Lang::txt('COM_SUPPORT_COL_SUMMARY') }}
                    </a>
                  </li>
                  <li>
                    <a{{ $filters['sort'] == 'group' ? ' class="active ' . $sortDir . '"' : '' }}
                       href="#"
                       data-sort="group"
                       data-direction="{{ $direction }}"
                       title="{{ $sortClickLabel }}">
                      {{ Lang::txt('COM_SUPPORT_COL_GROUP') }}
                    </a>
                  </li>
                  <li>
                    <a{{ $filters['sort'] == 'owner' ? ' class="active ' . $sortDir . '"' : '' }}
                       href="#"
                       data-sort="owner"
                       data-direction="{{ $direction }}"
                       title="{{ $sortClickLabel }}">
                      {{ Lang::txt('COM_SUPPORT_COL_OWNER') }}
                    </a>
                  </li>
                </ul>
              </li>
            </ul>

            {{-- Search fieldset --}}
            <fieldset id="filter-bar">
              <label for="filter_search">{{ Lang::txt('COM_SUPPORT_FIND') }}:</label>
              <input type="text"
                     name="search"
                     id="filter_search"
                     class="filter"
                     value="{{ $filters['search'] }}"
                     placeholder="{{ Lang::txt('COM_SUPPORT_FIND_IN_QUERY_PLACEHOLDER') }}" />

              <input type="hidden" name="filter_order"
                     value="{{ $filters['sort'] }}" />
              <input type="hidden" name="filter_order_Dir"
                     value="{{ $filters['sortdir'] }}" />
              <input type="hidden" name="show"
                     value="{{ $filters['show'] }}" />

              <button type="submit">{{ Lang::txt('COM_SUPPORT_GO') }}</button>
            </fieldset>

          </div>{{-- / .list-options --}}

          <ul id="tktlist">
            @forelse ($rows as $i => $row)
              @php
                $lastcomment = $row->comments()
                    ->order('created', 'desc')
                    ->row()
                    ->get('created');

                $tags = $row->tags('linkedlist');

                $ticketEditUrl = Route::url(
                    'index.php?option=' . $option
                    . '&controller=' . $controller
                    . '&task=edit&id=' . $row->get('id'), false
                );

                $ticketSummary = (
                    $row->get('summary', Lang::txt('COM_SUPPORT_TICKET_NO_CONTENT'))
                );

                $statusTitle = Lang::txt('COM_SUPPORT_COL_STATUS') . ': '
                    . $row->status->get('title');
                if (!$row->isOpen()) {
                    $statusTitle .= ' (' . $row->get('resolved') . ')';
                }

                $hasTargetDate = $row->get('target_date')
                    && $row->get('target_date') != '0000-00-00 00:00:00';

                $severity = $row->get('severity', 'normal');
                $severityTip = Lang::txt('COM_SUPPORT_TICKET_PRIORITY')
                    . ': ' . $severity;
              @endphp
              <li class="{{ $row->isOpen() ? 'open' : 'closed' }}"
                  data-id="{{ $row->get('id') }}"
                  id="ticket-{{ $row->get('id') }}">
                <div class="ticket-wrap status-{{ $row->status->get('id') }}"
                     @if(!empty($statusColors[$row->status->get('id')]))
                       data-style-border-left-color="#{{ $statusColors[$row->status->get('id')] }}"
                     @endif>

                  {{-- Row 1: checkbox, ticket ID, status badge, last activity, target date --}}
                  <p>
                    <input type="checkbox"
                           name="id[]"
                           id="cb{{ $i }}"
                           value="{{ $row->get('id') }}"
                           aria-label="{{ Lang::txt('COM_SUPPORT_TICKET') }} #{{ $row->get('id') }}"
                           class="checkbox-toggle" />
                    <span class="ticket-id"># {{ $row->get('id') }}</span>
                    <span class="{{ $row->status->get('alias') }} status hasTip"
                          title="{{ $statusTitle }}">
                      {{ $row->status->get('title') }}
                      @if (!$row->isOpen())
                        ({{ $row->get('resolved') }})
                      @endif
                    </span>
                    @if ($lastcomment && $lastcomment != '0000-00-00 00:00:00')
                      <span class="ticket-activity">
                        <time datetime="{{ $lastcomment }}">
                          {{ Date::of($lastcomment)->relative() }}
                        </time>
                      </span>
                    @endif
                    @if ($hasTargetDate)
                      @php
                        $targetDateFormatted = Date::of($row->get('target_date'))
                            ->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                        $targetDateTitle = Lang::txt(
                            'Target date: %s',
                            $targetDateFormatted
                        );
                        $targetDatetime = Date::of($row->get('target_date'))
                            ->format('Y-m-d\TH:i:s\Z');
                      @endphp
                      <span class="ticket-target_date hasTip"
                            title="{{ $targetDateTitle }}">
                        <time datetime="{{ $targetDatetime }}">
                          {{ $targetDateFormatted }}
                        </time>
                      </span>
                    @endif
                  </p>

                  {{-- Row 2: author name + created datetime --}}
                  <p>
                    <span class="ticket-author">
                      {{ $row->get('name') }}
                      @if ($row->get('login'))
                        @php
                          $memberUrl = Route::url(
                              'index.php?option=com_members&task=edit&id='
                              . e($row->get('login')), false
                          );
                        @endphp
                        (<a href="{{ $memberUrl }}">{{ $row->get('login') }}</a>)
                      @endif
                    </span>
                    <span class="ticket-datetime">
                      @ <time datetime="{{ $row->get('created') }}">
                        {{ $row->get('created') }}
                      </time>
                    </span>
                  </p>

                  {{-- Row 3: summary edit link --}}
                  <p>
                    <a class="ticket-content" href="{{ $ticketEditUrl }}">
                      {{ $ticketSummary }}
                    </a>
                  </p>

                  {{-- Row 4: tags, group, assignee (if any) --}}
                  @if ($tags || $row->isOwned() || $row->get('group_id'))
                    <p class="ticket-details">
                      @if ($tags)
                        <span class="ticket-tags">{!! $tags !!}</span>
                      @endif
                      @if ($row->get('group_id'))
                        @php
                          $group = \Hubzero\User\Group::getInstance($row->get('group_id'));
                          $gname = $group
                              ? $group->get('cn')
                              : Lang::txt('COM_SUPPORT_UNKNOWN');
                        @endphp
                        <span class="ticket-group">{{ $gname }}</span>
                      @endif
                      @if ($row->isOwned())
                        @php
                          $assigneeUsername = $row->assignee->get('username');
                          $assigneeOrg      = $row->assignee->get(
                              'organization',
                              Lang::txt('COM_SUPPORT_USER_ORG_UNKNOWN')
                          );
                          $ownerTip = Lang::txt('COM_SUPPORT_TICKET_ASSIGNED_TO')
                              . ': ' . $assigneeUsername . ' — ' . $assigneeOrg;
                        @endphp
                        <span class="ticket-owner"
                              title="{{ $ownerTip }}">
                          {{ $row->assignee->get('name') }}
                        </span>
                      @endif
                    </p>
                  @endif

                  {{-- Row 5: severity badge --}}
                  <p class="tkt-severity">
                    <span class="ticket-severity {{ $severity }} hasTip"
                          title="{{ $severityTip }}">
                      <span>{{ $severity }}</span>
                    </span>
                  </p>

                </div>
              </li>
            @empty
              <li>
                <p class="no-records">{{ Lang::txt('No tickets found.') }}</p>
              </li>
            @endforelse
          </ul>

          <div class="admin-pagination">
            {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
          </div>

          <input type="hidden" name="option" value="{{ $option }}" />
          <input type="hidden" name="controller" value="{{ $controller }}" />
          <input type="hidden" name="task" value="" />
          <input type="hidden" name="boxchecked" value="0" />
          {!! Html::input('token') !!}

        </form>

      </div>{{-- / .pane-inner --}}
    </div>{{-- / .pane-list --}}

    {{-- ===== RIGHT PANE: Detail placeholder ===== --}}
    <div class="pane pane-item">
      <div class="pane-inner" id="ticket">
        <p class="instructions">
          {{ Lang::txt('Select a ticket from the list to view details.') }}
        </p>
      </div>{{-- / .pane-inner --}}
    </div>{{-- / .pane-item --}}

  </div>{{-- / .panel-row --}}
</div>{{-- / .panel --}}
