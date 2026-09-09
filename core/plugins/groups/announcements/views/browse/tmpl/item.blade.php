{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
// Default to unpublished
$class = 'unpublished';

// Is the announcement available?
if ($announcement->get('state') == 1 && $announcement->inPublishWindow()) {
    $class = 'published';
}

// Is it high priority?
if ($announcement->get('priority')) {
    $class .= ' high';
}

// Is it sticky?
if ($announcement->get('sticky')) {
    $class .= ' sticky';
}

// Did the user already close this?
$closed = Request::getWord('group_announcement_' . $announcement->get('id'), '', 'cookie');
if ($closed == 'closed' && $showClose == true) {
    return;
}

// Determine daisyUI alert class based on state
$alertClass = 'alert';
if (strstr($class, 'unpublished')) {
    $alertClass .= ' alert-warning';
} elseif ($announcement->get('priority')) {
    $alertClass .= ' alert-error';
} else {
    $alertClass .= ' alert-info';
}
@endphp

<div class="announcement-container {{ $class }} card card-bordered bg-base-100 shadow-sm mb-4">
    <div class="card-body">
        @if (strstr($class, 'unpublished'))
            <div class="badge badge-warning mb-2">{{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_NOT_ACTIVE') }}</div>
        @endif

        @if ($announcement->get('priority'))
            <div class="badge badge-error mb-2">High Priority</div>
        @endif

        @if ($announcement->get('sticky'))
            <div class="badge badge-accent mb-2">Sticky</div>
        @endif

        <div class="announcement prose max-w-none">
            @php
            $content = $announcement->content;
            if (strlen(strip_tags($content)) > 500 && $showClose) {
                $content = Hubzero\Utility\Str::truncate($announcement->get('content'), 500, ['html' => true]);
                $announcementsUrl = Route::url(
                    'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=announcements'
                );
                $content .= '<p><a href="' . $announcementsUrl . '" class="link link-primary"'
                    . ' title="' . Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_MORE_TITLE') . '">'
                    . Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_MORE')
                    . '</a></p>';
            }
            @endphp
            {!! $content !!}
        </div>

        <div class="divider my-2"></div>

        <div class="flex flex-wrap items-center gap-4 text-sm text-base-content/70">
            <span class="entry-id">#{{ $announcement->get('id') }}</span>

            @if ($authorized == 'manager')
                @php
                $profile = $announcement->creator;
                @endphp
                <span class="entry-author">
                    {{ e($profile->get('name', Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_UNKNOWN'))) }}
                </span>
            @endif

            <time datetime="{{ $announcement->published() }}">
                {{ $announcement->published('time') }}
            </time>
            <time datetime="{{ $announcement->published() }}">
                {{ $announcement->published('date') }}
            </time>

            @if ($group->published == 1 && $authorized == 'manager' && !$showClose)
                @php
                $isCreatorOrManager = User::get('id') == $announcement->get('created_by')
                    || $authorized == 'manager';
                $announcementBase = 'index.php?option=com_groups&cn='
                    . $group->get('cn')
                    . '&active=announcements';
                $announcementId = $announcement->get('id');
                @endphp

                @if ($isCreatorOrManager)
                    <div class="ml-auto flex gap-2">
                        @php
                        $editUrl = Route::url($announcementBase . '&action=edit&id=' . $announcementId);
                        $deleteUrl = Route::url($announcementBase . '&action=delete&id=' . $announcementId);
                        @endphp
                        <a class="btn btn-sm btn-ghost"
                            href="{{ $editUrl }}"
                            title="{{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_EDIT') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                            {{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_EDIT') }}
                        </a>
                        <a class="btn btn-sm btn-ghost text-error"
                            href="{{ $deleteUrl }}"
                            data-confirm="{{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_CONFIRM_DELETE') }}"
                            title="{{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_DELETE') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            {{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_DELETE') }}
                        </a>
                    </div>
                @endif
            @endif
        </div>

        @if ($showClose)
            @php
            $closeUrl = Route::url(
                'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=announcements'
            );
            @endphp
            <button class="btn btn-sm btn-ghost btn-circle absolute right-2 top-2 close"
                data-id="{{ $announcement->get('id') }}"
                data-duration="30"
                title="{{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_CLOSE_TITLE') }}">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                <span class="sr-only">{{ Lang::txt('PLG_GROUPS_ANNOUNCEMENTS_CLOSE') }}</span>
            </button>
        @endif
    </div>
</div>
