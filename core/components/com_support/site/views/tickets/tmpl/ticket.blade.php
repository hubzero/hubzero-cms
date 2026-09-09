{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Config;
    use Hubzero\Facades\Date;
    use Hubzero\Facades\Event;
    use Hubzero\Facades\Filesystem;
    use Hubzero\Facades\Html;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;

    $__view->css()
        ->css('jquery.ui.css', 'system')
        ->js('jquery.timepicker.js', 'system')
        ->js('ticket.js')
        ->js();

    $status = $row->status->get('title');

    $unknown  = 1;
    $usertype = Lang::txt('COM_SUPPORT_UNKNOWN');

    $protocol = (($_SERVER['HTTPS'] ?? '') === '') ? 'http://' : 'https://';

    if ($row->get('login')) {
        $submitter = $row->submitter;
        if ($submitter->get('id')) {
            $gids = [];
            foreach (User::getInstance($row->submitter->get('id'))->accessgroups() as $g) {
                $gids[] = $g->group_id;
            }
            $usertype = implode(', ', $gids);

            $memberUrl    = Route::url('index.php?option=com_members&id=' . $submitter->get('id'));
            $escapedName  = $__view->escape(stripslashes($row->get('name')));
            $escapedLogin = $__view->escape(stripslashes($row->get('login')));
            $name = '<a rel="profile" href="' . $memberUrl . '">'
                . $escapedName . ' (' . $escapedLogin . ')</a>';
            $unknown = 0;
        } else {
            $name  = '<a rel="email" href="mailto:' . $row->get('email') . '">';
            $escapedName  = $__view->escape(stripslashes($row->get('name')));
            $escapedLogin = $__view->escape(stripslashes($row->get('login')));
            $name .= ($row->get('login'))
                ? $escapedName . ' (' . $escapedLogin . ')'
                : $escapedName;
            $name .= '</a>';
        }
    } else {
        $escapedName = $__view->escape(stripslashes($row->get('name')));
        $name = '<a rel="email" href="mailto:' . $row->get('email') . '">' . $escapedName . '</a>';
    }

    $prev = null;
    $next = null;

    $cc = [];

    $filterStr = '&show=' . $filters['show']
        . '&search=' . $filters['search']
        . '&limit=' . $filters['limit']
        . '&limitstart=' . $filters['start'];
@endphp

<x-page-container :title="$title">
    {{-- Header navigation --}}
    <div class="mb-4 flex items-center gap-2">
        @if ($prev)
            @php($prevUrl = Route::url('index.php?option=' . $option . '&task=ticket&id=' . $prev->id . $filterStr))
            <a class="btn btn-sm btn-outline" href="{{ $prevUrl }}">
                {{ Lang::txt('COM_SUPPORT_PREV') }}
            </a>
        @endif

        @php($browseUrl = Route::url('index.php?option=' . $option . '&task=tickets' . $filterStr))
        <a class="btn btn-sm btn-outline" href="{{ $browseUrl }}">
            {{ Lang::txt('COM_SUPPORT_TICKETS') }}
        </a>

        @if ($next)
            @php($nextUrl = Route::url('index.php?option=' . $option . '&task=ticket&id=' . $next->id . $filterStr))
            <a class="btn btn-sm btn-outline" href="{{ $nextUrl }}">
                {{ Lang::txt('COM_SUPPORT_NEXT') }}
            </a>
        @endif
    </div>

    @if ($__view->getError())
        <div class="alert alert-error" role="alert">
            {!! implode('<br />', $__view->getErrors()) !!}
        </div>
    @endif

    {{-- Section 1: Ticket entry --}}
    <section class="main section">
        <div class="section-inner hz-layout-with-aside">
            <div class="subject">
                <div class="ticket entry" id="t{{ $row->get('id') }}">
                    <p class="entry-member-photo">
                        <span class="entry-anchor"></span>
                        <img src="{{ $row->submitter->picture($unknown) }}" alt="" />
                    </p>
                    <div class="entry-content">
                        <p class="entry-title">
                            <strong>{!! $name !!}</strong>
                            @php
                                $permalinkUrl   = Route::url($row->link());
                                $permalinkTitle = Lang::txt('COM_SUPPORT_PERMALINK');
                            @endphp
                            <a
                                class="permalink"
                                href="{{ $permalinkUrl }}"
                                title="{{ $permalinkTitle }}"
                            >
                                <span class="entry-date-at">{{ Lang::txt('COM_SUPPORT_AT') }}</span>
                                <span class="time">
                                    <time datetime="{{ $row->created() }}">
                                        {{ $row->created('time') }}
                                    </time>
                                </span>
                                <span class="entry-date-on">{{ Lang::txt('COM_SUPPORT_ON') }}</span>
                                <span class="date">
                                    <time datetime="{{ $row->created() }}">
                                        {{ $row->created('date') }}
                                    </time>
                                </span>
                            </a>
                        </p>
                        <div class="entry-body">
                            <p>{!! $row->content !!}</p>
                            @if ($row->attachments->count())
                                <div class="comment-attachments">
                                    @foreach ($row->attachments as $attachment)
                                        @php
                                            if (!trim($attachment->get('description'))) {
                                                $attachment->set('description', $attachment->get('filename'));
                                            }
                                        @endphp

                                        @if ($attachment->isImage())
                                            @php
                                                $attLink = Route::url($attachment->link());
                                                $attDesc = $__view->escape($attachment->get('description'));
                                            @endphp
                                            @if ($attachment->width() > 400)
                                                <p>
                                                    <a href="{{ $attLink }}" rel="lightbox">
                                                        <img
                                                            src="{{ $attLink }}"
                                                            alt="{{ $attDesc }}"
                                                            width="400"
                                                        />
                                                    </a>
                                                </p>
                                            @else
                                                <p>
                                                    <img src="{{ $attLink }}" alt="{{ $attDesc }}" />
                                                </p>
                                            @endif
                                        @else
                                            @php
                                                $attLink = Route::url($attachment->link());
                                                $attExt  = Filesystem::extension($attachment->get('filename'));
                                                $attDesc = $__view->escape($attachment->get('description'));
                                            @endphp
                                            <a
                                                class="attachment {{ $attExt }}"
                                                href="{{ $attLink }}"
                                                title="{{ $attDesc }}"
                                            >
                                                <p class="attachment-description">
                                                    {{ $attachment->get('description') }}
                                                </p>
                                                <p class="attachment-meta">
                                                    <span class="attachment-size">
                                                        {{ Hubzero\Utility\Number::formatBytes($attachment->size()) }}
                                                    </span>
                                                    <span class="attachment-action">
                                                        {{ Lang::txt('JLIB_HTML_CLICK_TO_DOWNLOAD') }}
                                                    </span>
                                                </p>
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Admin details table --}}
                    @if ($row->access('update', 'tickets') > 0)
                        <div class="entry-details">
                            <table>
                                <tbody>
                                    <tr>
                                        <th scope="row">
                                            {{ Lang::txt('COM_SUPPORT_TICKET_DETAILS_EMAIL') }}:
                                        </th>
                                        <td>
                                            <a href="mailto:{{ $row->get('email') }}">
                                                {{ $__view->escape($row->get('email')) }}
                                            </a>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            {{ Lang::txt('COM_SUPPORT_TICKET_DETAILS_USERTYPE') }}:
                                        </th>
                                        <td>{{ $__view->escape($usertype) }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            {{ Lang::txt('COM_SUPPORT_TICKET_DETAILS_OS') }}:
                                        </th>
                                        <td>
                                            {{ $__view->escape($row->get('os')) }}
                                            /
                                            {{ $__view->escape($row->get('browser')) }}
                                            ({{ $row->get('cookies')
                                                ? Lang::txt('COM_SUPPORT_COOKIES_ENABLED')
                                                : Lang::txt('COM_SUPPORT_COOKIES_DISABLED') }})
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            {{ Lang::txt('COM_SUPPORT_TICKET_DETAILS_IP') }}:
                                        </th>
                                        <td>
                                            {{ $__view->escape($row->get('ip')) }}
                                            ({{ $__view->escape($row->get('hostname')) }})
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            {{ Lang::txt('COM_SUPPORT_TICKET_DETAILS_REFERRER') }}:
                                        </th>
                                        <td>{{ $__view->escape($row->get('referrer', ' ')) }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">
                                            {{ Lang::txt('COM_SUPPORT_TICKET_DETAILS_INSTANCES') }}:
                                        </th>
                                        <td>{{ $__view->escape($row->get('instances')) }}</td>
                                    </tr>
                                    @if ($uas = $row->get('uas'))
                                        <tr>
                                            <td colspan="2">{{ $__view->escape($uas) }}</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            <aside class="aside">
                <div class="ticket-status">
                    @php
                        $isOpenClass = (!$row->isOpen()) ? 'closed' : 'open';
                        $isOpenLabel = (!$row->isOpen())
                            ? Lang::txt('COM_SUPPORT_TICKET_STATUS_CLOSED_TICKET')
                            : Lang::txt('COM_SUPPORT_TICKET_STATUS_OPEN_TICKET');
                    @endphp
                    <p class="{{ $isOpenClass }}">
                        <strong>{{ $isOpenLabel }}</strong>
                    </p>
                    @if (!$row->isOpen())
                        <p>{!! Lang::txt('COM_SUPPORT_NOTE_TO_REOPEN') !!}</p>
                    @endif
                </div>

                <div class="ticket-watch">
                    @if ($row->isWatching())
                        <div id="watching">
                            <p>{{ Lang::txt('COM_SUPPORT_CURRENTLY_WATCHING') }}</p>
                            @php($stopUrl = Route::url($row->link('stopWatching') . $filterStr))
                            <p>
                                <a class="stop-watching btn" href="{{ $stopUrl }}">
                                    {{ Lang::txt('COM_SUPPORT_STOP_WATCHING') }}
                                </a>
                            </p>
                        </div>
                    @else
                        @php($startUrl = Route::url($row->link('startWatching') . $filterStr))
                        <p>
                            <a class="start-watching btn" href="{{ $startUrl }}">
                                {{ Lang::txt('COM_SUPPORT_WATCHING_TICKET') }}
                            </a>
                        </p>
                    @endif
                    <p>{{ Lang::txt('COM_SUPPORT_WATCHING_EXPLANATION') }}</p>
                </div>
            </aside>
        </div>
    </section>

    {{-- Section 2: Comments --}}
    @if ($row->access('read', 'comments'))
        <section class="below section">
            <div class="section-inner hz-layout-with-aside">
                <div class="subject">
                    <h3>{{ Lang::txt('COM_SUPPORT_TICKET_COMMENTS') }}</h3>

                    @if ($row->comments->count() > 0)
                        <ol class="comments">
                            @php
                                $o = 'even';
                                $i = 0;
                            @endphp
                            @foreach ($row->comments as $cmt)
                                @php
                                    if ($cmt->changelog()->format() != 'html') {
                                        $cc = $cmt->changelog()->get('cc');
                                    }

                                    if (!$row->access('read', 'private_comments') && $cmt->isPrivate()) {
                                        continue;
                                    }
                                    $i++;

                                    if ($cmt->isPrivate()) {
                                        $access = 'private';
                                    } else {
                                        $access = 'public';
                                    }
                                    $submitterUser = $row->submitter->get('username');
                                    if ($cmt->get('created_by') == $submitterUser && !$cmt->isPrivate()) {
                                        $access = 'submitter';
                                    }

                                    $cmtName = Lang::txt('COM_SUPPORT_UNKNOWN');
                                    $cite = $cmtName;

                                    if ($cmt->creator->get('id')) {
                                        $cite = $__view->escape(stripslashes($cmt->creator->get('name')));
                                        $creatorUrl = Route::url(
                                            'index.php?option=com_members&id=' . $cmt->creator->get('id')
                                        );
                                        $creatorLogin = $__view->escape(
                                            stripslashes($cmt->creator->get('username'))
                                        );
                                        $cmtName = '<a href="' . $creatorUrl . '">'
                                            . $cite . ' (' . $creatorLogin . ')</a>';
                                    }

                                    $o = ($o == 'odd') ? 'even' : 'odd';
                                    $commentCreated  = $__view->escape($cmt->created());
                                    $commentPermaUrl = Route::url($cmt->link());
                                    $commentPermalink = $protocol . $_SERVER['HTTP_HOST'] . $commentPermaUrl;
                                @endphp

                                <li
                                    class="comment {{ $access }} {{ $o }}"
                                    id="c{{ $cmt->get('id') }}"
                                >
                                    <p class="comment-member-photo">
                                        <img src="{{ $cmt->creator->picture() }}" alt="" />
                                    </p>
                                    <div class="comment-content">
                                        <p class="comment-head">
                                            <strong>{!! $cmtName !!}</strong>
                                            <div class="comment-meta">
                                                <a
                                                    class="permalink"
                                                    href="{{ $commentPermaUrl }}"
                                                    title="{{ Lang::txt('COM_SUPPORT_PERMALINK') }}"
                                                >
                                                    <span class="comment-date-at">
                                                        {{ Lang::txt('COM_SUPPORT_AT') }}
                                                    </span>
                                                    <span class="time">
                                                        <time datetime="{{ $commentCreated }}">
                                                            {{ $cmt->created('time') }}
                                                        </time>
                                                    </span>
                                                    <span class="comment-date-on">
                                                        {{ Lang::txt('COM_SUPPORT_ON') }}
                                                    </span>
                                                    <span class="date">
                                                        <time datetime="{{ $commentCreated }}">
                                                            {{ $cmt->created('date') }}
                                                        </time>
                                                    </span>
                                                </a>
                                                <a
                                                    class="copy-link show-hover-target"
                                                    href="{{ $commentPermalink }}"
                                                >
                                                    <span class="lbl show-hover-child">Copy link</span>
                                                    {!! Html::asset('icon', 'link') !!}
                                                </a>
                                            </div>
                                        </p>

                                        @if ($content = $cmt->comment)
                                            <div class="comment-body">
                                                <p>{!! $content !!}</p>
                                            </div>
                                        @endif

                                        @if ($cmt->attachments->count())
                                            <div class="comment-attachments">
                                                @foreach ($cmt->attachments as $attachment)
                                                    @php
                                                        if (!trim($attachment->get('description'))) {
                                                            $attachment->set(
                                                                'description',
                                                                $attachment->get('filename')
                                                            );
                                                        }
                                                    @endphp

                                                    @if ($attachment->hasFile())
                                                        @if ($attachment->isImage())
                                                            @php
                                                                $attLink = Route::url($attachment->link());
                                                                $attDesc = $__view->escape(
                                                                    $attachment->get('description')
                                                                );
                                                            @endphp
                                                            @if ($attachment->width() > 400)
                                                                <p>
                                                                    <a
                                                                        href="{{ $attLink }}"
                                                                        rel="lightbox"
                                                                    >
                                                                        <img
                                                                            src="{{ $attLink }}"
                                                                            alt="{{ $attDesc }}"
                                                                            width="400"
                                                                        />
                                                                    </a>
                                                                </p>
                                                            @else
                                                                <p>
                                                                    <img
                                                                        src="{{ $attLink }}"
                                                                        alt="{{ $attDesc }}"
                                                                    />
                                                                </p>
                                                            @endif
                                                        @else
                                                            @php
                                                                $attLink = Route::url($attachment->link());
                                                                $attExt  = Filesystem::extension(
                                                                    $attachment->get('filename')
                                                                );
                                                                $attDesc = $__view->escape(
                                                                    $attachment->get('description')
                                                                );
                                                                $attSize = Hubzero\Utility\Number::formatBytes(
                                                                    $attachment->size()
                                                                );
                                                            @endphp
                                                            <a
                                                                class="attachment {{ $attExt }}"
                                                                href="{{ $attLink }}"
                                                                title="{{ $attDesc }}"
                                                            >
                                                                <p class="attachment-description">
                                                                    {{ $attDesc }}
                                                                </p>
                                                                <p class="attachment-meta">
                                                                    <span class="attachment-size">
                                                                        {{ $attSize }}
                                                                    </span>
                                                                    <span class="attachment-action">
                                                                        {{ Lang::txt('JLIB_HTML_CLICK_TO_DOWNLOAD') }}
                                                                    </span>
                                                                </p>
                                                            </a>
                                                        @endif
                                                    @else
                                                        @php
                                                            $attExt  = Filesystem::extension(
                                                                $attachment->get('filename')
                                                            );
                                                            $attDesc = $__view->escape(
                                                                $attachment->get('description')
                                                            );
                                                            $attFile = $__view->escape(
                                                                $attachment->get('filename')
                                                            );
                                                        @endphp
                                                        <div
                                                            class="attachment {{ $attExt }}"
                                                            title="{{ $attDesc }}"
                                                        >
                                                            <p class="attachment-description">
                                                                {{ $attDesc }}
                                                            </p>
                                                            <p class="attachment-meta">
                                                                <span class="attachment-size">
                                                                    {{ $attFile }}
                                                                </span>
                                                                <span class="attachment-action">
                                                                    {{ Lang::txt('JLIB_HTML_ERROR_FILE_NOT_FOUND') }}
                                                                </span>
                                                            </p>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    @if ($row->access('update', 'tickets') > 0)
                                        <div class="comment-changelog">
                                            {!! $cmt->changelog()->render() !!}
                                        </div>
                                    @endif
                                </li>
                            @endforeach
                        </ol>
                    @else
                        <p class="no-comments">
                            {{ Lang::txt('COM_SUPPORT_NO_COMMENTS_FOUND') }}
                        </p>
                    @endif
                </div>

                <aside class="aside">
                    @if ($row->access('create', 'comments'))
                        <p>
                            <a class="icon-add add btn" href="#commentform">
                                {{ Lang::txt('COM_SUPPORT_ADD_COMMENT') }}
                            </a>
                        </p>
                    @endif
                </aside>
            </div>
        </section>
    @endif

    {{-- Section 3: Comment form --}}
    @if ($row->access('create', 'comments') || $row->access('update', 'tickets'))
        <section class="below section">
            <div class="section-inner hz-layout-with-aside">
                <div class="subject">
                    <h3>{{ Lang::txt('COM_SUPPORT_COMMENT_FORM') }}</h3>
                    <form
                        action="{{ Route::url($row->link('update')) }}"
                        method="post"
                        id="commentform"
                        enctype="multipart/form-data"
                    >
                        <p class="comment-member-photo">
                            <span class="comment-anchor"></span>
                            @php($anon = User::isGuest() ? 1 : 0)
                            <img src="{{ User::picture($anon) }}" alt="" />
                        </p>

                        <fieldset>
                            <input type="hidden" name="id" value="{{ $row->get('id') }}" />
                            <input
                                type="hidden"
                                name="ticket[id]"
                                id="ticketid"
                                value="{{ $row->get('id') }}"
                            />
                            <input
                                type="hidden"
                                name="username"
                                value="{{ User::get('username') }}"
                            />
                            <input type="hidden" name="option" value="{{ $option }}" />
                            <input type="hidden" name="controller" value="{{ $controller }}" />
                            <input type="hidden" name="task" value="update" />
                            <input type="hidden" name="started" value="{{ Date::toSql() }}" />
                            <input
                                type="hidden"
                                name="search"
                                value="{{ $__view->escape($filters['search']) }}"
                            />
                            <input
                                type="hidden"
                                name="show"
                                value="{{ $__view->escape($filters['show']) }}"
                            />
                            <input
                                type="hidden"
                                name="limit"
                                value="{{ $__view->escape($filters['limit']) }}"
                            />
                            <input
                                type="hidden"
                                name="limistart"
                                value="{{ $__view->escape($filters['start']) }}"
                            />
                            @if (!$row->access('create', 'private_comments'))
                                <input type="hidden" name="access" value="0" />
                            @endif

                            {{-- Admin ticket details fieldset --}}
                            @if ($row->access('update', 'tickets'))
                                <fieldset>
                                    @if ($row->access('update', 'tickets') > 0)
                                        <legend>
                                            <span>{{ Lang::txt('COM_SUPPORT_TICKET_DETAILS') }}</span>
                                        </legend>

                                        {{-- Tags --}}
                                        <div class="form-group">
                                            <label for="tags">
                                                {{ Lang::txt('COM_SUPPORT_COMMENT_TAGS') }}:<br />
                                                @php
                                                    $tf = Event::trigger(
                                                        'hubzero.onGetMultiEntry',
                                                        [['tags', 'tags', 'actags', '', $row->tags('string', null)]]
                                                    );
                                                @endphp
                                                @if (count($tf) > 0)
                                                    {!! $tf[0] !!}
                                                @else
                                                    <input
                                                        type="text"
                                                        name="tags"
                                                        id="tags"
                                                        class="form-control"
                                                        value="{{ $__view->escape($row->tags('string')) }}"
                                                    />
                                                @endif
                                            </label>
                                        </div>

                                        {{-- Group + Owner --}}
                                        <div class="grid">
                                            <div class="col span6">
                                                <div class="form-group">
                                                    <label for="acgroup">
                                                        {{ Lang::txt('COM_SUPPORT_COMMENT_GROUP') }}:
                                                        @php
                                                            $group = '';
                                                            if ($row->get('group_id')) {
                                                                $g = \Hubzero\User\Group::getInstance(
                                                                    $row->get('group_id')
                                                                );
                                                                if ($g) {
                                                                    $group = $g->get('cn');
                                                                }
                                                            }

                                                            $gc = Event::trigger(
                                                                'hubzero.onGetSingleEntryWithSelect',
                                                                [[
                                                                    'groups', 'ticket[group_id]', 'acgroup',
                                                                    '', $group, '', 'ticketowner'
                                                                ]]
                                                            );
                                                        @endphp
                                                        @if (count($gc) > 0)
                                                            {!! $gc[0] !!}
                                                        @else
                                                            <input
                                                                type="text"
                                                                name="ticket[group_id]"
                                                                value="{{ $row->get('group_id') }}"
                                                                class="form-control"
                                                                id="acgroup"
                                                                autocomplete="off"
                                                            />
                                                        @endif
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col span6 omega">
                                                <div class="form-group">
                                                    <label>
                                                        {{ Lang::txt('COM_SUPPORT_COMMENT_OWNER') }}:
                                                        {!! $lists['owner'] !!}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Severity + Status --}}
                                        <div class="grid">
                                            <div class="col span6">
                                                @php
                                                    $sevHelpUrl = Route::url(
                                                        'index.php?option=com_help&component=support&page=ticket#severity'
                                                    );
                                                    $sevHelpTitle = Lang::txt('COM_SUPPORT_TICKET_SEVERITY_HELP');
                                                @endphp
                                                <label for="ticket-field-severity">
                                                    {{ Lang::txt('COM_SUPPORT_COMMENT_SEVERITY') }}:
                                                    <a
                                                        class="icon-help tooltips popup"
                                                        href="{{ $sevHelpUrl }}"
                                                        title="{{ $sevHelpTitle }}"
                                                    >{{ $sevHelpTitle }}</a>
                                                    <select
                                                        name="ticket[severity]"
                                                        id="ticket-field-severity"
                                                        class="form-control"
                                                    >
                                                        @foreach ($lists['severities'] as $severity)
                                                            @php
                                                                $sevSel = ($severity == $row->get('severity'))
                                                                    ? ' selected="selected"' : '';
                                                                $sevLabel = Lang::txt(
                                                                    'COM_SUPPORT_TICKET_SEVERITY_'
                                                                    . strtoupper($severity)
                                                                );
                                                            @endphp
                                                            <option
                                                                value="{{ $severity }}"
                                                                {!! $sevSel !!}
                                                            >{{ $sevLabel }}</option>
                                                        @endforeach
                                                    </select>
                                                </label>
                                            </div>
                                            <div class="col span6 omega">
                                                <div class="form-group">
                                                    <label for="status">
                                                        {{ Lang::txt('COM_SUPPORT_COMMENT_STATUS') }}:
                                                        <select
                                                            name="ticket[status]"
                                                            id="status"
                                                            class="form-control"
                                                        >
                                                            <optgroup
                                                                label="{{ Lang::txt('COM_SUPPORT_COMMENT_OPT_OPEN') }}"
                                                            >
                                                                @foreach (
                                                                    \Components\Support\Models\Status::allOpen()->rows()
                                                                    as $st
                                                                )
                                                                    @php
                                                                        $stId  = $st->get('id');
                                                                        $stSel = ($row->isOpen()
                                                                            && $row->get('status') == $stId)
                                                                            ? ' selected="selected"' : '';
                                                                        $stTitle = $__view->escape(
                                                                            $st->get('title')
                                                                        );
                                                                    @endphp
                                                                    <option
                                                                        value="{{ $stId }}"
                                                                        {!! $stSel !!}
                                                                    >{{ $stTitle }}</option>
                                                                @endforeach
                                                            </optgroup>
                                                            <optgroup
                                                                label="{{ Lang::txt('COM_SUPPORT_CLOSED') }}"
                                                            >
                                                                @php
                                                                    $closedSel = (!$row->isOpen()
                                                                        && $row->get('status') == 0)
                                                                        ? ' selected="selected"' : '';
                                                                @endphp
                                                                <option
                                                                    value="0"
                                                                    {!! $closedSel !!}
                                                                >{{ Lang::txt('COM_SUPPORT_COMMENT_OPT_CLOSED') }}</option>
                                                                @foreach (
                                                                    \Components\Support\Models\Status::allClosed()->rows()
                                                                    as $st
                                                                )
                                                                    @php
                                                                        $stId  = $st->get('id');
                                                                        $stSel = (!$row->isOpen()
                                                                            && $row->get('status') == $stId)
                                                                            ? ' selected="selected"' : '';
                                                                        $stTitle = $__view->escape(
                                                                            $st->get('title')
                                                                        );
                                                                    @endphp
                                                                    <option
                                                                        value="{{ $stId }}"
                                                                        {!! $stSel !!}
                                                                    >{{ $stTitle }}</option>
                                                                @endforeach
                                                            </optgroup>
                                                        </select>
                                                    </label>
                                                </div>
                                    @else
                                        <input
                                            type="hidden"
                                            name="tags"
                                            value="{{ $__view->escape($row->tags('string')) }}"
                                        />

                                        @if ($row->isSubmitter())
                                            @if (!$row->isOpen())
                                                <div class="form-group form-check">
                                                    <label
                                                        class="option form-check-label"
                                                        for="field-status"
                                                    >
                                                        <input
                                                            class="option form-check-input"
                                                            type="checkbox"
                                                            name="ticket[status]"
                                                            id="field-status"
                                                            value="1"
                                                        />
                                                        {{ Lang::txt('COM_SUPPORT_COMMENT_REOPEN') }}
                                                    </label>
                                                </div>
                                            @else
                                                <div class="form-group form-check">
                                                    <label
                                                        class="option form-check-label"
                                                        for="field-status"
                                                    >
                                                        <input
                                                            class="option form-check-input"
                                                            type="checkbox"
                                                            name="ticket[status]"
                                                            id="field-status"
                                                            value="0"
                                                        />
                                                        {{ Lang::txt('COM_SUPPORT_COMMENT_CLOSE') }}
                                                    </label>
                                                </div>
                                            @endif
                                        @endif
                                    @endif {{-- ACL can update ticket (admin) --}}

                                    @if ($row->access('update', 'tickets') > 0)
                                            </div>
                                        </div>

                                        {{-- Target date --}}
                                        <div class="form-group">
                                            <label for="field-target_date">
                                                {{ Lang::txt('COM_SUPPORT_COMMENT_TARGET_DATE') }}:
                                                @php
                                                    $tzOffset = timezone_offset_get(
                                                        new DateTimeZone(Config::get('offset')),
                                                        Date::getRoot()
                                                    ) / 60;
                                                    $targetDate = $row->get('target_date');
                                                    $hasTarget  = (
                                                        $targetDate && $targetDate != '0000-00-00 00:00:00'
                                                    );
                                                    $targetVal = $hasTarget
                                                        ? $__view->escape(
                                                            Date::of($targetDate)->toLocal('Y-m-d H:i:s')
                                                        )
                                                        : '';
                                                @endphp
                                                <input
                                                    type="text"
                                                    name="ticket[target_date]"
                                                    class="datetime-field form-control"
                                                    id="field-target_date"
                                                    data-timezone="{{ $tzOffset }}"
                                                    placeholder="YYYY-MM-DD hh:mm:ss"
                                                    value="{{ $targetVal }}"
                                                />
                                            </label>
                                        </div>

                                        {{-- Category --}}
                                        @if (isset($lists['categories']) && $lists['categories'])
                                            <div class="form-group">
                                                <label for="ticket-field-category">
                                                    {{ Lang::txt('COM_SUPPORT_COMMENT_CATEGORY') }}:
                                                    <select
                                                        name="ticket[category]"
                                                        id="ticket-field-category"
                                                        class="form-control"
                                                    >
                                                        <option value="">
                                                            {{ Lang::txt('COM_SUPPORT_NONE') }}
                                                        </option>
                                                        @foreach ($lists['categories'] as $category)
                                                            @php
                                                                $catAlias = $__view->escape($category->alias);
                                                                $catTitle = $__view->escape(
                                                                    stripslashes($category->title)
                                                                );
                                                                $catSel = (
                                                                    $row->get('category') == $category->alias
                                                                ) ? ' selected="selected"' : '';
                                                            @endphp
                                                            <option
                                                                value="{{ $catAlias }}"
                                                                {!! $catSel !!}
                                                            >{{ $catTitle }}</option>
                                                        @endforeach
                                                    </select>
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                </fieldset>
                            @else
                                <input
                                    type="hidden"
                                    name="tags"
                                    value="{{ $__view->escape($row->tags('string')) }}"
                                />
                            @endif {{-- ACL can update tickets --}}

                            {{-- Comment area --}}
                            @php
                                $canCreateComments = $row->access('create', 'comments');
                                $canCreatePrivate  = $row->access('create', 'private_comments');
                            @endphp
                            @if ($canCreateComments || $canCreatePrivate)
                                @php
                                    $results = Event::trigger('support.onTicketComment', [$row]);
                                    echo implode("\n", $results);
                                @endphp

                                <fieldset>
                                    <legend>
                                        {{ Lang::txt('COM_SUPPORT_COMMENT_LEGEND_COMMENTS') }}:
                                    </legend>

                                    @if ($canCreateComments > 0 || $canCreatePrivate)
                                        <div class="top grouping">
                                    @endif

                                    {{-- Message template select (admin) --}}
                                    @if ($row->access('create', 'comments') > 0)
                                        <div class="form-group">
                                            <label for="messages">
                                                <select
                                                    name="messages"
                                                    id="messages"
                                                    class="form-control"
                                                >
                                                    <option value="mc">
                                                        {{ Lang::txt('COM_SUPPORT_COMMENT_CUSTOM') }}
                                                    </option>
                                                    @php($hi = [])
                                                    @foreach ($lists['messages'] as $message)
                                                        @php
                                                            $msgTitle = $__view->escape(
                                                                stripslashes($message->title)
                                                            );
                                                        @endphp
                                                        <option value="m{{ $message->id }}">
                                                            {{ $msgTitle }}
                                                        </option>
                                                        @php
                                                            $msgVal = $__view->escape(
                                                                $message->transformMessage($row->get('id'))
                                                            );
                                                            $hi[] = '<input type="hidden"'
                                                                . ' name="m' . $message->id . '"'
                                                                . ' id="m' . $message->id . '"'
                                                                . ' value="' . $msgVal . '" />';
                                                        @endphp
                                                    @endforeach
                                                </select>
                                            </label>
                                            {!! implode("\n", $hi) !!}
                                        </div>
                                    @endif

                                    {{-- Private comment checkbox --}}
                                    @if ($row->access('create', 'private_comments'))
                                        <div class="form-group form-check">
                                            <label for="make-private" class="form-check-label">
                                                <input
                                                    class="option form-check-input"
                                                    type="checkbox"
                                                    name="access"
                                                    id="make-private"
                                                    value="1"
                                                />
                                                {{ Lang::txt('COM_SUPPORT_COMMENT_PRIVATE') }}
                                            </label>
                                        </div>
                                    @endif

                                    @if ($canCreateComments > 0 || $canCreatePrivate)
                                        </div>
                                        <div class="clear"></div>
                                    @endif

                                    {{-- Comment textarea --}}
                                    <div class="form-group">
                                        <textarea
                                            name="comment"
                                            id="comment"
                                            class="form-control"
                                            rows="13"
                                            cols="35"
                                        >{{ $comment->get('comment') }}</textarea>
                                    </div>

                                    {{-- Terse email checkbox (admin, config-gated) --}}
                                    @if ($row->access('create', 'comments') > 0)
                                        @if ($config->get('email_terse'))
                                            <div class="form-group form-check">
                                                <label for="email_terse" class="form-check-label">
                                                    <input
                                                        class="option form-check-input"
                                                        type="checkbox"
                                                        name="email_terse"
                                                        id="email_terse"
                                                        value="1"
                                                        checked="checked"
                                                    />
                                                    {{ Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_TERSE') }}
                                                </label>
                                            </div>
                                        @endif
                                    @endif
                                </fieldset>

                                {{-- File attachments --}}
                                <fieldset>
                                    <legend>
                                        {{ Lang::txt('COM_SUPPORT_COMMENT_LEGEND_ATTACHMENTS') }}
                                    </legend>
                                    @php
                                        $tmp = Request::getString('tmp_dir', ('-' . time()), 'post');
                                        $__view->js('jquery.fileuploader.js', 'system');
                                        $jbase = rtrim(Request::base(true), '/');
                                        $uploadAction = $jbase
                                            . '/index.php?option=com_support&amp;no_html=1'
                                            . '&amp;controller=media&amp;task=upload&amp;ticket='
                                            . $row->get('id') . '&amp;comment=' . $tmp;
                                        $listAction = $jbase
                                            . '/index.php?option=com_support&amp;no_html=1'
                                            . '&amp;controller=media&amp;task=list&amp;ticket='
                                            . $row->get('id') . '&amp;comment=' . $tmp;
                                    @endphp
                                    <div
                                        id="ajax-uploader"
                                        data-action="{{ $uploadAction }}"
                                        data-list="{{ $listAction }}"
                                    >
                                        <noscript>
                                            <div class="form-group">
                                                <label for="upload">
                                                    {{ Lang::txt('COM_SUPPORT_COMMENT_FILE') }}:
                                                    <input
                                                        type="file"
                                                        name="upload[]"
                                                        id="upload"
                                                        class="form-control-file"
                                                        multiple="multiple"
                                                    />
                                                </label>
                                            </div>
                                            <div class="form-group">
                                                <label for="field-description">
                                                    {{ Lang::txt('COM_SUPPORT_COMMENT_FILE_DESCRIPTION') }}:
                                                    <input
                                                        type="text"
                                                        name="description"
                                                        id="field-description"
                                                        class="form-control"
                                                        value=""
                                                    />
                                                </label>
                                            </div>
                                        </noscript>
                                    </div>
                                    <div class="field-wrap file-list" id="ajax-uploader-list">
                                        {!!
                                            $__view->view('list', 'media')
                                                ->set('model', $comment)
                                                ->set('comment', $tmp)
                                                ->set('ticket', $row->get('id'))
                                                ->display()
                                        !!}
                                    </div>
                                    <input
                                        type="hidden"
                                        name="tmp_dir"
                                        id="comment-tmp_dir"
                                        value="{{ $tmp }}"
                                    />
                                </fieldset>
                            @endif {{-- canCreateComments || canCreatePrivate --}}

                            {{-- Email notifications (admin) --}}
                            @if ($row->access('create', 'comments') > 0)
                                <fieldset>
                                    <legend>
                                        {{ Lang::txt('COM_SUPPORT_COMMENT_LEGEND_EMAIL') }}:
                                    </legend>
                                    <div class="grid">
                                        <div class="col span6">
                                            <div class="form-group">
                                                <label
                                                    for="email_submitter"
                                                    class="form-check-label"
                                                >
                                                    <input
                                                        class="option form-check-input"
                                                        type="checkbox"
                                                        name="email_submitter"
                                                        id="email_submitter"
                                                        value="1"
                                                        checked="checked"
                                                    />
                                                    {{ Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_SUBMITTER') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col span6 omega">
                                            <div class="form-group">
                                                <label
                                                    for="email_owner"
                                                    class="form-check-label"
                                                >
                                                    <input
                                                        class="option form-check-input"
                                                        type="checkbox"
                                                        name="email_owner"
                                                        id="email_owner"
                                                        value="1"
                                                        checked="checked"
                                                    />
                                                    {{ Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_OWNER') }}
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="acmembers">
                                            {{ Lang::txt('COM_SUPPORT_COMMENT_SEND_EMAIL_CC') }}:
                                            @php
                                                $mc = Event::trigger(
                                                    'hubzero.onGetMultiEntry',
                                                    [['members', 'cc', 'acmembers', '', implode(', ', $cc)]]
                                                );
                                            @endphp
                                            @if (count($mc) > 0)
                                                @php
                                                    $ccInstr = Lang::txt(
                                                        'COM_SUPPORT_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS_AUTOCOMPLETE'
                                                    );
                                                @endphp
                                                <span class="hint">{{ $ccInstr }}</span>
                                                {!! $mc[0] !!}
                                            @else
                                                @php
                                                    $ccInstr = Lang::txt(
                                                        'COM_SUPPORT_COMMENT_SEND_EMAIL_CC_INSTRUCTIONS'
                                                    );
                                                    $ccVal = implode(', ', $cc);
                                                @endphp
                                                <span class="hint">{{ $ccInstr }}</span>
                                                <input
                                                    type="text"
                                                    name="cc"
                                                    id="acmembers"
                                                    class="form-control"
                                                    value="{{ $ccVal }}"
                                                    size="35"
                                                />
                                            @endif
                                        </label>
                                    </div>
                                </fieldset>
                            @else
                                <input
                                    type="hidden"
                                    name="email_submitter"
                                    id="email_submitter"
                                    value="1"
                                />
                                <input
                                    type="hidden"
                                    name="email_owner"
                                    id="email_owner"
                                    value="1"
                                />
                                <input
                                    type="hidden"
                                    name="cc"
                                    id="acmembers"
                                    value="{{ implode(', ', $cc) }}"
                                />
                            @endif

                            {!! Html::input('token') !!}

                            <p class="submit">
                                <input
                                    type="submit"
                                    class="btn btn-success"
                                    value="{{ Lang::txt('COM_SUPPORT_SUBMIT_COMMENT') }}"
                                />
                            </p>
                        </fieldset>
                    </form>
                </div>

                <aside class="aside">
                    <p>{{ Lang::txt('COM_SUPPORT_COMMENT_FORM_EXPLANATION') }}</p>
                </aside>
            </div>
        </section>
    @endif
</x-page-container>
