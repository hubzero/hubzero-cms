{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Date;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\Session;

    $live_site = rtrim(Request::base(), '/');
    $tok = Session::getFormToken();

    $__view->css()
        ->css('conditions.css')
        ->js('jquery.hoverIntent.js', 'system')
        ->js('json2.js')
        ->js('condition.builder.js')
        ->js('tickets.js');

    $showCurrent = intval($filters['show']);
    $curSort     = $filters['sort'];
    $sortDir     = strtolower($filters['sortdir']);
    $direction   = ($sortDir == 'desc') ? 'asc' : 'desc';

    $sortBase = 'index.php?option=' . $option
        . '&controller=' . $controller
        . '&task=display&show=' . $filters['show']
        . '&search=' . $filters['search']
        . '&sortdir=' . $direction
        . '&limit=' . $filters['limit'] . '&limitstart=0';

    $sortUrl = [
        'created'  => Route::url($sortBase . '&sort=created'),
        'status'   => Route::url($sortBase . '&sort=status'),
        'severity' => Route::url($sortBase . '&sort=severity'),
        'summary'  => Route::url($sortBase . '&sort=summary'),
        'group'    => Route::url($sortBase . '&sort=group'),
        'owner'    => Route::url($sortBase . '&sort=owner'),
    ];

    $formAction = Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller . '&task=display'
    );

    $updateUrl = Route::url(
        'index.php?option=' . $option
        . '&controller=queries&task=saveordering&' . $tok . '=1'
    );

    $clickToSort    = Lang::txt('COM_SUPPORT_CLICK_TO_SORT');
    $delConfirmText = Lang::txt('COM_SUPPORT_QUERIES_CONFIRM_DELETE');
    $folderLabel    = Lang::txt('COM_SUPPORT_FOLDER_NAME');

    $statuses = [];
@endphp

<header id="content-header">
    <h2>{{ $title }}</h2>

    <div id="content-header-extra">
        <ul id="useroptions">
            @if ($acl->check('read', 'tickets'))
                @php
                    $statsUrl = Route::url(
                        'index.php?option=' . $option
                        . '&controller=' . $controller . '&task=stats'
                    );
                @endphp
                <li>
                    <a class="icon-stats stats btn" href="{{ $statsUrl }}">
                        {{ Lang::txt('COM_SUPPORT_STATS') }}
                    </a>
                </li>
            @endif
            <li class="last">
                @php
                    $newUrl = Route::url(
                        'index.php?option=' . $option
                        . '&controller=' . $controller . '&task=new'
                    );
                @endphp
                <a class="icon-add add btn" href="{{ $newUrl }}">
                    {{ Lang::txt('COM_SUPPORT_NEW_TICKET') }}
                </a>
            </li>
        </ul>
    </div>
</header>

<section class="panel tickets">
    <div class="panel-row">

        {{-- Left panel: Query folders sidebar --}}
        <div class="pane pane-queries" id="queries" data-update="{{ $updateUrl }}">
            <div class="pane-inner">

                {{-- Watch list (admin only) --}}
                @if ($acl->check('read', 'tickets'))
                    @php
                        $watchOpenUrl = Route::url(
                            'index.php?option=' . $option
                            . '&controller=' . $controller
                            . '&task=display&show=-1&limitstart=0'
                            . ($showCurrent != -1 ? '&search=' : '')
                        );
                        $watchClosedUrl = Route::url(
                            'index.php?option=' . $option
                            . '&controller=' . $controller
                            . '&task=display&show=-2&limitstart=0'
                            . ($showCurrent != -2 ? '&search=' : '')
                        );
                    @endphp
                    <ul id="watch-list">
                        <li id="folder_watching" class="open">
                            <span class="icon-watch folder">
                                {{ Lang::txt('COM_SUPPORT_WATCH_LIST') }}
                            </span>
                            <ul id="queries_watching" class="wqueries">
                                <li @if ($showCurrent == -1) class="active" @endif>
                                    <a class="aquery" href="{{ $watchOpenUrl }}">
                                        {{ $__view->escape(Lang::txt('COM_SUPPORT_WATCH_LIST_OPEN')) }}
                                        <span>{{ $watch['open'] }}</span>
                                    </a>
                                </li>
                                <li @if ($showCurrent == -2) class="active" @endif>
                                    <a class="aquery" href="{{ $watchClosedUrl }}">
                                        {{ $__view->escape(Lang::txt('COM_SUPPORT_WATCH_LIST_CLOSED')) }}
                                        <span>{{ $watch['closed'] }}</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                @endif

                {{-- Query folders --}}
                <ul id="query-list">
                    @foreach ($folders as $folder)
                        @php
                            $folderId    = $__view->escape($folder->id);
                            $folderTitle = $__view->escape($folder->title);
                        @endphp
                        <li id="folder_{{ $folderId }}" class="open">
                            <span
                                class="icon-folder folder"
                                id="{{ $folderId }}-title"
                                data-id="{{ $folderId }}"
                            >{{ $folderTitle }}</span>

                            @if ($acl->check('read', 'tickets'))
                                @php
                                    $delFolderUrl = Route::url(
                                        'index.php?option=' . $option
                                        . '&controller=queries&task=removefolder'
                                        . '&id=' . $folder->id . '&' . $tok . '=1'
                                    );
                                    $editFolderUrl = Route::url(
                                        'index.php?option=' . $option
                                        . '&controller=queries&task=editfolder'
                                        . '&id=' . $folder->id . '&tmpl=component&' . $tok . '=1'
                                    );
                                    $saveFolderUrl = Route::url(
                                        'index.php?option=' . $option
                                        . '&controller=queries&task=savefolder'
                                        . '&' . $tok . '=1&fields[id]=' . $folder->id
                                    );
                                @endphp
                                <span class="folder-options">
                                    <a
                                        class="delete"
                                        href="{{ $delFolderUrl }}"
                                        data-confirm="{{ $delConfirmText }}"
                                        title="{{ Lang::txt('JACTION_DELETE') }}"
                                    >
                                        {{ Lang::txt('JACTION_DELETE') }}
                                    </a>
                                    <a
                                        class="edit editfolder"
                                        data-id="{{ $folderId }}"
                                        href="{{ $editFolderUrl }}"
                                        data-href="{{ $saveFolderUrl }}"
                                        data-name="{{ $folderLabel }}"
                                        title="{{ Lang::txt('JACTION_EDIT') }}"
                                    >
                                        {{ Lang::txt('JACTION_EDIT') }}
                                    </a>
                                </span>
                            @endif

                            <ul
                                id="queries_{{ $__view->escape($folder->id) }}"
                                class="queries"
                            >
                                @foreach ($folder->queries as $query)
                                    @php
                                        $qId       = $__view->escape($query->id);
                                        $isActiveQ = ($showCurrent == $query->id);
                                        $qUrl = Route::url(
                                            'index.php?option=' . $option
                                            . '&controller=' . $controller
                                            . '&task=display&show=' . $query->id
                                            . (!$isActiveQ ? '&search=&limitstart=0' : '')
                                        );
                                    @endphp
                                    <li
                                        id="query_{{ $qId }}"
                                        @if ($isActiveQ) class="active" @endif
                                    >
                                        <a class="aquery" href="{{ $qUrl }}">
                                            {{ $__view->escape(stripslashes($query->title)) }}
                                            <span>{{ $query->get('count') }}</span>
                                        </a>

                                        @if ($acl->check('read', 'tickets'))
                                            @php
                                                $delQueryUrl = Route::url(
                                                    'index.php?option=' . $option
                                                    . '&controller=queries&task=remove'
                                                    . '&id=' . $query->id . '&' . $tok . '=1'
                                                );
                                                $editQueryUrl = Route::url(
                                                    'index.php?option=' . $option
                                                    . '&controller=queries&task=edit'
                                                    . '&id=' . $query->id . '&tmpl=component&' . $tok . '=1'
                                                );
                                            @endphp
                                            <span class="query-options">
                                                <a
                                                    class="delete"
                                                    href="{{ $delQueryUrl }}"
                                                    data-confirm="{{ $delConfirmText }}"
                                                    title="{{ Lang::txt('JACTION_DELETE') }}"
                                                >
                                                    {{ Lang::txt('JACTION_DELETE') }}
                                                </a>
                                                <a
                                                    class="modal edit"
                                                    href="{{ $editQueryUrl }}"
                                                    title="{{ Lang::txt('JACTION_EDIT') }}"
                                                    rel="{handler: 'iframe', size: {x: 570, y: 550}}"
                                                >
                                                    {{ Lang::txt('JACTION_EDIT') }}
                                                </a>
                                            </span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>

                {{-- Add controls (admin) --}}
                @if ($acl->check('read', 'tickets'))
                    @php
                        $addQueryUrl = Route::url(
                            'index.php?option=' . $option
                            . '&controller=queries&task=add&' . $tok . '=1'
                        );
                        $addFolderUrl = Route::url(
                            'index.php?option=' . $option
                            . '&controller=queries&task=addfolder&' . $tok . '=1'
                        );
                        $saveFolderNewUrl = Route::url(
                            'index.php?option=' . $option
                            . '&controller=queries&task=savefolder&' . $tok . '=1'
                        );
                        $addQueryTitle  = Lang::txt('COM_SUPPORT_ADD_QUERY');
                        $addFolderTitle = Lang::txt('COM_SUPPORT_ADD_FOLDER');
                    @endphp
                    <ul class="controls">
                        <li>
                            <a
                                class="icon-list modal"
                                id="new-query"
                                href="{{ $addQueryUrl }}"
                                rel="{handler: 'iframe', size: {x: 570, y: 550}}"
                                title="{{ $addQueryTitle }}"
                            >
                                {{ $addQueryTitle }}
                            </a>
                        </li>
                        <li>
                            <a
                                class="icon-folder"
                                id="new-folder"
                                href="{{ $addFolderUrl }}"
                                data-href="{{ $saveFolderNewUrl }}"
                                data-name="{{ $folderLabel }}"
                                title="{{ $addFolderTitle }}"
                            >
                                {{ $addFolderTitle }}
                            </a>
                        </li>
                    </ul>
                @endif

            </div>
        </div>

        {{-- Right panel: Ticket list --}}
        <div class="pane pane-list">
            <div class="pane-inner" id="tickets">
                <form action="{{ $formAction }}" method="post" id="ticketForm">
                    <div class="list-options">
                        <ul class="sort-options">
                            <li>
                                <span class="sort-header">
                                    {{ Lang::txt('COM_SUPPORT_SORT_RESULTS') }}
                                </span>
                                <ul>
                                    <li>
                                        <a
                                            class="sort-age{{ $curSort == 'created' ? ' active ' . $sortDir : '' }}"
                                            href="{{ $sortUrl['created'] }}"
                                            title="{{ $clickToSort }}"
                                        >
                                            {{ Lang::txt('COM_SUPPORT_COL_AGE') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            class="sort-status{{ $curSort == 'status' ? ' active ' . $sortDir : '' }}"
                                            href="{{ $sortUrl['status'] }}"
                                            title="{{ $clickToSort }}"
                                        >
                                            {{ Lang::txt('COM_SUPPORT_COL_STATUS') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            class="sort-severity{{ $curSort == 'severity' ? ' active ' . $sortDir : '' }}"
                                            href="{{ $sortUrl['severity'] }}"
                                            title="{{ $clickToSort }}"
                                        >
                                            {{ Lang::txt('COM_SUPPORT_COL_SEVERITY') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            class="sort-summary{{ $curSort == 'summary' ? ' active ' . $sortDir : '' }}"
                                            href="{{ $sortUrl['summary'] }}"
                                            title="{{ $clickToSort }}"
                                        >
                                            {{ Lang::txt('COM_SUPPORT_COL_SUMMARY') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            class="sort-group{{ $curSort == 'group' ? ' active ' . $sortDir : '' }}"
                                            href="{{ $sortUrl['group'] }}"
                                            title="{{ $clickToSort }}"
                                        >
                                            {{ Lang::txt('COM_SUPPORT_COL_GROUP') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a
                                            class="sort-owner{{ $curSort == 'owner' ? ' active ' . $sortDir : '' }}"
                                            href="{{ $sortUrl['owner'] }}"
                                            title="{{ $clickToSort }}"
                                        >
                                            {{ Lang::txt('COM_SUPPORT_COL_OWNER') }}
                                        </a>
                                    </li>
                                </ul>
                            </li>
                        </ul>
                        <fieldset id="filter-bar">
                            <label for="filter_search">{{ Lang::txt('COM_SUPPORT_FIND') }}:</label>
                            <input
                                type="text"
                                name="search"
                                id="filter_search"
                                value="{{ $__view->escape($filters['search']) }}"
                                placeholder="{{ Lang::txt('COM_SUPPORT_SEARCH_THIS_QUERY') }}"
                            />

                            <input type="hidden" name="sort" value="{{ $__view->escape($filters['sort']) }}" />
                            <input type="hidden" name="sortdir" value="{{ $__view->escape($filters['sortdir']) }}" />
                            <input type="hidden" name="show" value="{{ $__view->escape($filters['show']) }}" />

                            <input
                                type="submit"
                                class="submit"
                                value="{{ Lang::txt('COM_SUPPORT_GO') }}"
                            />
                        </fieldset>
                    </div>

                    <table id="tktlist">
                        <tfoot>
                            <tr>
                                <td colspan="8">
                                    @php
                                        $pageNav = $__view->pagination(
                                            $total,
                                            $filters['start'],
                                            $filters['limit']
                                        );
                                        $pageNav->setAdditionalUrlParam('show', $filters['show']);
                                        $pageNav->setAdditionalUrlParam('search', $filters['search']);
                                    @endphp
                                    {!! $pageNav->render() !!}
                                </td>
                            </tr>
                        </tfoot>
                        <tbody>
                            @forelse ($rows as $row)
                                @php
                                    // Last comment activity
                                    $lastcomment = $row->comments()
                                        ->order('created', 'desc')
                                        ->row()
                                        ->get('created');

                                    $tags = $row->tags('linkedlist');

                                    // Inject status border color CSS once per status
                                    if (!in_array($row->status->get('id'), $statuses)) {
                                        $statuses[] = $row->status->get('id');
                                        $__view->css(
                                            '#tktlist tbody tr td.status-' . $row->status->get('id')
                                            . ' { border-left-color: #' . $row->status->get('color') . '; }'
                                        );
                                    }

                                    $rowStatusId    = $row->status->get('id');
                                    $rowStatusText  = $row->status->get('text');
                                    $rowStatusClass = $row->status->get('class');
                                    $rowIsOpen      = $row->isOpen();
                                    $rowOpenClass   = ($rowIsOpen ? 'open' : 'closed') . ' ' . $rowStatusClass;
                                    $tipTitle       = Lang::txt('COM_SUPPORT_DETAILS');
                                    $tipStatusLabel = Lang::txt('COM_SUPPORT_COL_STATUS') . ': ' . $rowStatusText;

                                    $rowContent = $__view->escape(
                                        str_replace(
                                            ['<br />', '&amp;'],
                                            ['', '&'],
                                            $row->content
                                        )
                                    );
                                    $rowLink = Route::url(
                                        $row->link() . '&show=' . $filters['show']
                                        . '&search=' . $filters['search']
                                        . '&limit=' . $filters['limit']
                                        . '&limitstart=' . $filters['start']
                                    );
                                    $rowSummary = $row->content
                                        ? \Hubzero\Utility\Str::truncate(strip_tags($row->content), 200)
                                        : Lang::txt('COM_SUPPORT_NO_CONTENT_FOUND');
                                @endphp
                                <tr class="{{ $loop->even ? 'even' : 'odd' }}">
                                    <td class="status-{{ $rowStatusId }}">
                                        <span
                                            class="hasTip"
                                            title="{{ $tipTitle }} :: {{ $tipStatusLabel }}"
                                        >
                                            <span class="ticket-id">
                                                {{ $row->get('id') }}
                                            </span>
                                            <span class="{{ $rowOpenClass }} status">
                                                {{ $rowStatusText }}
                                                @if (!$rowIsOpen)
                                                    ({{ $__view->escape($row->get('resolved')) }})
                                                @endif
                                            </span>
                                            @php
                                                $targetDate = $row->get('target_date');
                                                $hasTarget  = ($targetDate && $targetDate != '0000-00-00 00:00:00');
                                            @endphp
                                            @if ($hasTarget)
                                                @php
                                                    $targetDateObj    = Date::of($targetDate);
                                                    $targetDateLocal  = $targetDateObj->toLocal(
                                                        Lang::txt('DATE_FORMAT_HZ1')
                                                    );
                                                    $targetDateFormat = $targetDateObj->format('Y-m-d\TH:i:s\Z');
                                                    $targetTip = Lang::txt('COM_SUPPORT_TARGET_DATE', $targetDateLocal);
                                                @endphp
                                                <span
                                                    class="ticket-target_date tooltips"
                                                    title="{{ $targetTip }}"
                                                >
                                                    <time datetime="{{ $targetDateFormat }}">
                                                        {{ $targetDateLocal }}
                                                    </time>
                                                </span>
                                            @endif
                                        </span>
                                    </td>
                                    <td colspan="6">
                                        <p>
                                            <span class="ticket-author">
                                                {{ $__view->escape($row->get('name')) }}
                                                @if ($row->submitter->get('id'))
                                                    @php
                                                        $subUrl = Route::url(
                                                            'index.php?option=com_members&id='
                                                            . $row->submitter->get('id')
                                                        );
                                                    @endphp
                                                    (<a href="{{ $subUrl }}">{{ $__view->escape($row->get('login')) }}</a>)
                                                @elseif ($row->get('login'))
                                                    ({{ $__view->escape($row->get('login')) }})
                                                @endif
                                            </span>
                                            @php
                                                $createdDate = Date::of($row->get('created'));
                                                $createdFmt  = $createdDate->format('Y-m-d\TH:i:s\Z');
                                            @endphp
                                            <span class="ticket-datetime">
                                                @ <time datetime="{{ $createdFmt }}">
                                                    {{ $createdDate->toLocal() }}
                                                </time>
                                            </span>
                                            @if ($lastcomment && $lastcomment != '0000-00-00 00:00:00')
                                                @php
                                                    $lastDate = Date::of($lastcomment);
                                                    $lastFmt  = $lastDate->format('Y-m-d\TH:i:s\Z');
                                                @endphp
                                                <span class="ticket-activity">
                                                    <time datetime="{{ $lastFmt }}">
                                                        {{ $lastDate->relative() }}
                                                    </time>
                                                </span>
                                            @endif
                                        </p>
                                        <p>
                                            <a
                                                class="ticket-content"
                                                title="{{ $rowContent }}"
                                                href="{{ $rowLink }}"
                                            >
                                                {!! $rowSummary !!}
                                            </a>
                                        </p>
                                        @if ($tags || $row->isOwned() || $row->get('group_id'))
                                            <p class="ticket-details">
                                                @if ($acl->check('update', 'tickets') && $tags)
                                                    <span class="ticket-tags">
                                                        {!! $tags !!}
                                                    </span>
                                                @endif
                                                @if ($row->get('group_id'))
                                                    <span class="ticket-group">
                                                        @php
                                                            $gname = Lang::txt('COM_SUPPORT_UNKNOWN');
                                                            $group = \Hubzero\User\Group::getInstance(
                                                                $row->get('group_id')
                                                            );
                                                            if ($group) {
                                                                $gname = $group->get('cn');
                                                            }
                                                        @endphp
                                                        {{ $__view->escape($gname) }}
                                                    </span>
                                                @endif
                                                @if ($row->isOwned())
                                                    @php
                                                        $assigneePhoto = $row->assignee->picture();
                                                        $assigneeUser  = $__view->escape(
                                                            stripslashes($row->assignee->get('username', ''))
                                                        );
                                                        $assigneeOrg = $__view->escape(
                                                            stripslashes(
                                                                $row->assignee->get(
                                                                    'organization',
                                                                    Lang::txt('COM_SUPPORT_UNKNOWN')
                                                                )
                                                            )
                                                        );
                                                        $ownerTip = Lang::txt('COM_SUPPORT_ASSIGNED_TO')
                                                            . '::'
                                                            . '<img border=&quot;1&quot; src=&quot;'
                                                            . $assigneePhoto
                                                            . '&quot; name=&quot;imagelib&quot;'
                                                            . ' alt=&quot;User photo&quot;'
                                                            . ' width=&quot;40&quot; height=&quot;40&quot;'
                                                            . ' style=&quot;float: left;'
                                                            . ' margin-right: 0.5em;&quot; />'
                                                            . $assigneeUser . '<br />' . $assigneeOrg;
                                                    @endphp
                                                    <span
                                                        class="ticket-owner hasTip"
                                                        title="{{ $ownerTip }}"
                                                    >
                                                        {{ $__view->escape(stripslashes($row->assignee->get('name', ''))) }}
                                                    </span>
                                                @endif
                                            </p>
                                        @endif
                                    </td>
                                    <td class="tkt-severity">
                                        @php
                                            $sev    = $__view->escape($row->get('severity', 'normal'));
                                            $sevTip = Lang::txt('COM_SUPPORT_PRIORITY') . ':&nbsp;' . $sev;
                                        @endphp
                                        <span
                                            class="ticket-severity {{ $sev }} hasTip"
                                            title="{{ $sevTip }}"
                                        >
                                            <span>{{ $sev }}</span>
                                        </span>
                                        @if ($acl->check('delete', 'tickets'))
                                            @php
                                                $deleteUrl = Route::url($row->link('delete'));
                                            @endphp
                                            <a
                                                class="delete"
                                                href="{{ $deleteUrl }}"
                                                data-confirm="{{ $delConfirmText }}"
                                                title="{{ Lang::txt('JACTION_DELETE') }}"
                                            >
                                                {{ Lang::txt('JACTION_DELETE') }}
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr class="odd noresults">
                                    <td colspan="7">
                                        {{ Lang::txt('COM_SUPPORT_NO_RESULTS_FOUND') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <input type="hidden" name="controller" value="{{ $controller }}" />
                    <input type="hidden" name="option" value="{{ $option }}" />
                    <input type="hidden" name="task" value="display" />
                </form>
            </div>
        </div>

    </div>
</section>
