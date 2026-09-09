{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
switch ($which) {
    case 'group':
        $title = Lang::txt('PLG_GROUPS_PROJECTS_SHOW_GROUP');
        break;
    case 'owned':
        $title = Lang::txt('PLG_GROUPS_PROJECTS_SHOW_OWNED');
        break;
    case 'other':
        $title = Lang::txt('PLG_GROUPS_PROJECTS_SHOW_OTHER');
        break;
    default:
    case 'all':
        $title = Lang::txt('PLG_GROUPS_PROJECTS_SHOW_ALL');
        break;
}
@endphp

<table class="table table-zebra w-full">
    <caption class="text-left font-semibold p-2">{{ $title . ' (' . count($rows) . ')' }}</caption>
@if (count($rows) > 0)
    <thead>
        <tr>
            <th class="th_image" colspan="2"></th>
            <th>{{ Lang::txt('PLG_GROUPS_PROJECTS_TITLE') }}</th>
            <th>{{ Lang::txt('PLG_GROUPS_PROJECTS_STATUS') }}</th>
            <th>{{ Lang::txt('PLG_GROUPS_PROJECTS_MY_ROLE') }}</th>
            <th>{{ Lang::txt('PLG_GROUPS_PROJECTS_MEMBERSHIP') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            @php
            $role = $row->access('member')
                ? ($row->access('manager')
                    ? Lang::txt('PLG_GROUPS_PROJECTS_STATUS_MANAGER')
                    : Lang::txt('PLG_GROUPS_PROJECTS_STATUS_COLLABORATOR'))
                : Lang::txt('PLG_GROUPS_PROJECTS_STATUS_NOTMEMBER');

            $role = ($row->access('readonly') && !$row->isArchived())
                ? Lang::txt('PLG_GROUPS_PROJECTS_STATUS_REVIEWER')
                : $role;

            $setup = $row->inSetup() ? Lang::txt('PLG_GROUPS_PROJECTS_STATUS_SETUP') : '';

            $rowUrl = Route::url($row->link());
            $rowThumb = Route::url($row->link('thumb'));
            $rowTitle = e($row->get('title'));
            $rowAlias = $row->get('alias');
            $titleWithAlias = $rowTitle . ' (' . $rowAlias . ')';

            $ownerDisplay = $row->groupOwner()
                ? $row->groupOwner('description')
                : $row->owner('name');
            @endphp
            <tr class="mline">
                <td class="th_image">
                    @if ($row->access('member') || $row->access('readonly'))
                        <a href="{{ $rowUrl }}"
                            title="{{ $titleWithAlias }}">
                            <img src="{{ $rowThumb }}"
                                alt="{{ $rowTitle }}"
                                class="project-image" />
                        </a>
                    @else
                        <img src="{{ $rowThumb }}"
                            alt="{{ $rowTitle }}"
                            class="project-image" />
                    @endif
                    @if ($row->get('newactivity') && $row->isActive() && !$setup)
                        <span class="badge badge-info badge-sm">{{ $row->get('newactivity') }}</span>
                    @endif
                </td>
                <td class="th_privacy">
                    @if (!$row->isPublic())
                        <span class="privacy-icon">&nbsp;</span>
                    @endif
                </td>
                <td class="th_title">
                    @if ($row->access('member') || $row->access('readonly'))
                        <a class="link link-hover link-primary"
                            href="{{ $rowUrl }}"
                            title="{{ $titleWithAlias }}">
                            {{ $rowTitle }}
                        </a>
                    @else
                        {{ $rowTitle }}
                    @endif
                    @if ($which != 'owned')
                        <span class="block text-sm opacity-70">{{ $ownerDisplay }}</span>
                    @endif
                </td>
                <td class="th_status">
                    @if ($row->access('owner'))
                        @if ($row->isActive())
                            <span class="badge badge-success">
                                <a href="{{ Route::url($row->link()) }}"
                                    title="{{ Lang::txt('PLG_GROUPS_PROJECTS_GO_TO_PROJECT') }}">
                                    &raquo; {{ Lang::txt('PLG_GROUPS_PROJECTS_STATUS_ACTIVE') }}
                                </a>
                            </span>
                        @elseif ($row->inSetup())
                            <span class="badge badge-warning">
                                <a href="{{ Route::url($row->link('setup')) }}"
                                    title="{{ Lang::txt('PLG_GROUPS_PROJECTS_CONTINUE_SETUP') }}">
                                    &raquo; {{ Lang::txt('PLG_GROUPS_PROJECTS_STATUS_SETUP') }}
                                </a>
                            </span>
                        @endif
                    @endif
                    @if ($row->isInactive())
                        <span class="badge badge-error">
                            {{ Lang::txt('PLG_GROUPS_PROJECTS_STATUS_SUSPENDED') }}
                        </span>
                    @elseif ($row->isPending())
                        <span class="badge badge-warning">
                            {{ Lang::txt('PLG_GROUPS_PROJECTS_STATUS_PENDING') }}
                        </span>
                    @elseif ($row->isArchived())
                        <span class="badge badge-neutral">
                            {{ Lang::txt('PLG_GROUPS_PROJECTS_STATUS_ARCHIVED') }}
                        </span>
                    @endif
                </td>
                <td class="th_role">
                    {{ $role }}
                </td>
                <td class="th_membership">
                    @if ($row->get('sync_group'))
                        <span class="badge badge-accent">
                            {{ Lang::txt('PLG_GROUPS_PROJECTS_GROUP_SYNCED') }}
                        </span>
                    @else
                        <span class="badge badge-ghost">
                            {{ Lang::txt('PLG_GROUPS_PROJECTS_GROUP_SELECTED') }}
                        </span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
@else
    <tbody>
        <tr>
            <td>
                <p class="alert alert-info">{{ Lang::txt('PLG_GROUPS_PROJECTS_NO_PROJECTS') }}</p>
            </td>
        </tr>
    </tbody>
@endif
</table>
