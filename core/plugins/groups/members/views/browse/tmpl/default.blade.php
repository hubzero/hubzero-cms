{{--
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\App;
use Hubzero\Facades\Component;
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

$filters = [
    'members'  => Lang::txt('PLG_GROUPS_MEMBERS'),
    'managers' => Lang::txt('PLG_GROUPS_MEMBERS_MANAGERS'),
    'pending'  => Lang::txt('PLG_GROUPS_MEMBERS_PENDING'),
    'invitees' => Lang::txt('PLG_GROUPS_MEMBERS_INVITEES'),
];

if ($filter == '') {
    $filter = 'members';
}

$role_id   = '';
$role_name = '';

if ($role_filter) {
    foreach ($member_roles as $role) {
        if ($role['id'] == $role_filter) {
            $role_id   = $role['id'];
            $role_name = $role['name'];
            break;
        }
    }
}

$optionVal = 'com_groups';
@endphp

<h3 class="text-xl font-bold mb-4">
    {{ Lang::txt('PLG_GROUPS_MEMBERS') }}
</h3>

@if ($membership_control == 1)
    <div class="flex flex-wrap gap-2 mb-4">
        @if ($group->get('join_policy') < 3)
            @if (
                $authorized == 'manager'
                || $authorized == 'admin'
                || \Components\Groups\Helpers\Permissions::userHasPermissionForGroupAction(
                    $group,
                    'group.invite'
                )
            )
                <a class="btn btn-primary btn-sm"
                    href="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&task=invite') }}">
                    {{ Lang::txt('PLG_GROUPS_MEMBERS_INVITE_MEMBERS') }}
                </a>
            @endif
        @endif
        @if ($membership_control == 1 && $authorized == 'manager')
            <a class="btn btn-primary btn-sm"
                href="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&active=members&action=addrole') }}">
                {{ Lang::txt('PLG_GROUPS_MEMBERS_ADD_ROLE') }}
            </a>
        @endif
    </div>
@endif

<div class="flex flex-col lg:flex-row gap-6">
    {{-- Main content --}}
    <div class="flex-1 min-w-0">
        <form action="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&active=members&filter=' . $filter) }}"
            method="post">

            {{-- Search --}}
            <div class="join w-full mb-4">
                <label for="entry-search-field" class="sr-only">
                    {{ Lang::txt('PLG_GROUPS_MEMBERS_SEARCH_LABEL') }}
                </label>
                <input type="text"
                    name="q"
                    id="entry-search-field"
                    value="{{ e($q) }}"
                    placeholder="{{ Lang::txt('PLG_GROUPS_MEMBERS_SEARCH_PLACEHOLDER') }}"
                    class="input input-bordered join-item flex-1" />
                <button type="submit" class="btn btn-primary join-item">
                    {{ Lang::txt('PLG_GROUPS_MEMBERS_SEARCH') }}
                </button>
            </div>

            <div class="card bg-base-100 shadow">
                <div class="card-body p-0">
                    {{-- Filter tabs and message option --}}
                    <div class="flex flex-wrap items-center justify-between gap-2 p-4 border-b border-base-300">
                        @php
                            $isManagerOrAdmin = ($authorized == 'manager' || $authorized == 'admin');
                        @endphp

                        <div role="tablist" class="tabs tabs-border">
                            @foreach ($filters as $filterKey => $filterName)
                                @php
                                    if (($filterKey == 'pending' || $filterKey == 'invitees') && $membership_control == 0) {
                                        continue;
                                    }
                                    $showFilter = ($filterKey != 'pending' && $filterKey != 'invitees')
                                        || ($authorized == 'admin' || $authorized == 'manager');
                                @endphp
                                @if ($showFilter)
                                    @php
                                        $isActive = ($filter == $filterKey);
                                        $filterUrl = Route::url(
                                            'index.php?option=' . $optionVal
                                            . '&cn=' . $group->get('cn')
                                            . '&active=members&filter=' . $filterKey
                                        );
                                        if ($filterKey == 'pending') {
                                            $count = count($group->get('applicants'));
                                        } elseif ($filterKey == 'invitees') {
                                            $count = count($group->get('invitees')) + count($current_inviteemails);
                                        } else {
                                            $count = count($group->get($filterKey));
                                        }
                                    @endphp
                                    <a role="tab"
                                        class="tab {{ $isActive ? 'tab-active' : '' }}"
                                        href="{{ $filterUrl }}">
                                        {{ $filterName }} ({{ $count }})
                                    </a>
                                @endif
                            @endforeach
                        </div>

                        @if ($isManagerOrAdmin && count($groupusers) > 0 && $messages_acl != 'nobody')
                            @php
                                if ($role_id) {
                                    $msgAppend = '&users[]=role&role_id=' . $role_id;
                                    $msgTitle = Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE_ALL_ROLE', $role_name);
                                } else {
                                    switch ($filter) {
                                        case 'pending':
                                            $msgAppend = '&users[]=applicants';
                                            $msgTitle = Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE_ALL_APPLICANTS');
                                            break;
                                        case 'invitees':
                                            $msgAppend = '&users[]=invitees';
                                            $msgTitle = Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE_ALL_INVITEES');
                                            break;
                                        case 'managers':
                                            $msgAppend = '&users[]=managers';
                                            $msgTitle = Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE_ALL_MANAGERS');
                                            break;
                                        case 'members':
                                        default:
                                            $msgAppend = '&users[]=all';
                                            $msgTitle = Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE_ALL_MEMBERS');
                                            break;
                                    }
                                }
                                $messageUrl = Route::url(
                                    'index.php?option=' . $optionVal
                                    . '&cn=' . $group->get('cn')
                                    . '&active=messages&action=new'
                                    . $msgAppend
                                );
                            @endphp
                            <a class="btn btn-ghost btn-sm tooltip"
                                href="{{ $messageUrl }}"
                                data-tip="{{ $msgTitle }}">
                                {{ Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE_ALL') }}
                            </a>
                        @endif
                    </div>

                    {{-- Members table --}}
                    <div class="overflow-x-auto">
                        <table class="table">
                            <tbody>
                                @if ($groupusers)
                                    @php
                                        $sessions = Hubzero\Session\Helper::getAllSessions([
                                            'guest'    => 0,
                                            'distinct' => 1,
                                        ]);
                                        if ($limit == 0) {
                                            $limit = 500;
                                        }
                                        $db = App::get('db');
                                    @endphp

                                    @for ($i = 0; $i < $limit; $i++)
                                        @php
                                            if (($i + $start) >= count($groupusers)) {
                                                break;
                                            }
                                            $guser = $groupusers[($i + $start)];
                                            $u = User::getInstance($guser);
                                            $inviteemail = false;

                                            if (\Components\Members\Helpers\Utility::validemail($guser)) {
                                                $inviteemail = true;
                                                $pic = rtrim(Request::base(true), '/')
                                                    . '/core/components/com_groups/site/assets/img/emailthumb.png';
                                            } elseif (!is_object($u)) {
                                                continue;
                                            } else {
                                                $pic = $u->picture(0);
                                            }

                                            $invited = null;
                                            $cls = '';

                                            switch ($filter) {
                                                case 'invitees':
                                                    $status = Lang::txt('PLG_GROUPS_MEMBERS_STATUS_INVITEE');
                                                    if ($inviteemail) {
                                                        $query = "SELECT `timestamp`
                                                            FROM `#__xgroups_log`
                                                            WHERE `action`=" . $db->quote('membership_invites_sent') . "
                                                            AND `comments` LIKE " . $db->quote('%"' . $u->get($guser) . '"%') . "
                                                            AND `gidNumber`=" . $db->quote($group->get('gidNumber')) . "
                                                            ORDER BY `timestamp` DESC
                                                            LIMIT 1";
                                                    } else {
                                                        $query = "SELECT `timestamp`
                                                            FROM `#__xgroups_log`
                                                            WHERE `action`=" . $db->quote('membership_invites_sent') . "
                                                            AND `comments` LIKE " . $db->quote('%"' . $u->get('id') . '"%') . "
                                                            AND `gidNumber`=" . $db->quote($group->get('gidNumber')) . "
                                                            ORDER BY `timestamp` DESC
                                                            LIMIT 1";
                                                    }
                                                    $db->setQuery($query);
                                                    $invited = $db->loadResult();
                                                    break;
                                                case 'pending':
                                                    $status = Lang::txt('PLG_GROUPS_MEMBERS_STATUS_PENDING');
                                                    break;
                                                case 'managers':
                                                    $status = Lang::txt('PLG_GROUPS_MEMBERS_STATUS_MANAGER');
                                                    $cls .= ' manager';
                                                    break;
                                                case 'members':
                                                default:
                                                    $status = 'Member';
                                                    if (in_array($guser, $managers)) {
                                                        $status = Lang::txt('PLG_GROUPS_MEMBERS_STATUS_MANAGER');
                                                        $cls .= 'manager';
                                                    }
                                                    break;
                                            }

                                            if (is_object($u) && User::get('id') == $u->get('id')) {
                                                $cls .= ' me';
                                            }

                                            $online = 0;
                                            if ($sessions) {
                                                foreach ($sessions as $session) {
                                                    if ($session->userid == $u->get('id')) {
                                                        $online = 1;
                                                    }
                                                }
                                            }

                                            $url = $group->isSuperGroup()
                                                ? 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=members&scope=' . $u->get('id')
                                                : $u->link();
                                        @endphp

                                        <tr class="{{ trim($cls) }}">
                                            {{-- Avatar --}}
                                            <td class="w-16">
                                                <div class="avatar {{ $online ? 'online' : 'offline' }}">
                                                    <div class="w-12 rounded-full">
                                                        <img src="{{ $pic }}" alt=""
                                                            width="50" height="50" />
                                                    </div>
                                                </div>
                                                @if ($online)
                                                    <span class="badge badge-success badge-xs">
                                                        {{ Lang::txt('PLG_GROUPS_MEMBERS_ONLINE') }}
                                                    </span>
                                                @endif
                                            </td>

                                            {{-- Name / Status / Organization --}}
                                            <td>
                                                @if ($inviteemail)
                                                    <span class="font-semibold">
                                                        <a href="mailto:{{ $guser }}" class="link link-primary">
                                                            {{ $guser }}
                                                        </a>
                                                    </span>
                                                    <br />
                                                    <span class="badge badge-ghost badge-sm">
                                                        {{ Lang::txt('PLG_GROUPS_MEMBERS_INVITE_SENT_TO_EMAIL') }}
                                                    </span>
                                                    @if ($invited)
                                                        <br />
                                                        <span class="text-xs text-base-content/60">
                                                            <time datetime="{{ $invited }}">
                                                                {{ Lang::txt('Invited on %s', Date::of($invited)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))) }}
                                                            </time>
                                                        </span>
                                                    @endif
                                                @else
                                                    @php
                                                        $surname = $u->get('surname');
                                                        $givenName = $u->get('givenName');
                                                        $displayName = !empty($surname) ? $surname : '';
                                                        $displayName .= !empty($givenName) ? (!empty($displayName) ? ', ' . $givenName : $givenName) : '';
                                                        $isAccessible = in_array($u->get('access'), User::getAuthorisedViewLevels())
                                                            && ($u->get('activation') > 0);
                                                    @endphp
                                                    <span class="font-semibold">
                                                        @if ($isAccessible)
                                                            <a href="{{ Route::url($url) }}" class="link link-primary">
                                                                {{ $displayName }}
                                                            </a>
                                                        @else
                                                            {{ $displayName }}
                                                        @endif
                                                    </span>
                                                    <br />
                                                    <span class="badge badge-ghost badge-sm">{{ $status }}</span>
                                                    @if ($invited)
                                                        <br />
                                                        <span class="text-xs text-base-content/60">
                                                            <time datetime="{{ $invited }}">
                                                                {{ Lang::txt('Invited on %s', Date::of($invited)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))) }}
                                                            </time>
                                                        </span>
                                                    @endif
                                                    @if ($u->get('organization'))
                                                        <br />
                                                        <span class="text-sm text-base-content/70">
                                                            {{ e(stripslashes($u->get('organization'))) }}
                                                        </span>
                                                    @endif
                                                @endif

                                                {{-- Roles --}}
                                                @if ($filter == 'members' || $filter == 'managers')
                                                    @php
                                                        $db2 = App::get('db');
                                                        $db2->setQuery(
                                                            "SELECT r.id, r.name, r.permissions
                                                            FROM `#__xgroups_roles` as r
                                                            LEFT JOIN `#__xgroups_member_roles` as m ON m.roleid=r.id
                                                            WHERE m.uidNumber=" . $db2->quote($u->get('id'))
                                                            . " AND r.gidNumber=" . $db2->quote($group->get('gidNumber'))
                                                        );
                                                        $roles = $db2->loadAssocList();
                                                    @endphp
                                                    @if ($roles)
                                                        <div class="mt-1">
                                                            <strong class="text-xs">{{ Lang::txt('PLG_GROUPS_MEMBERS_MEMBER_ROLES') }}:</strong>
                                                            @foreach ($roles as $roleIdx => $memberRole)
                                                                @if ($roleIdx > 0), @endif
                                                                <a href="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&active=members&filter=' . $filter . '&role_filter=' . $memberRole['id']) }}"
                                                                    class="link link-secondary text-xs">
                                                                    {{ $memberRole['name'] }}
                                                                </a>
                                                                @if ($authorized == 'manager' && $membership_control == 1)
                                                                    <a href="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&active=members&action=deleterole&uid=' . $u->get('id') . '&role=' . $memberRole['id']) }}"
                                                                        class="text-error text-xs">&times;</a>
                                                                @endif
                                                            @endforeach
                                                            @if ($authorized == 'manager' && $membership_control == 1)
                                                                , <a href="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&active=members&action=assignrole&uid=' . $u->get('id')) }}"
                                                                    class="link text-xs">
                                                                    {{ Lang::txt('PLG_GROUPS_MEMBERS_ASSIGN_ROLE') }}
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @elseif ($membership_control == 1 && $isManagerOrAdmin)
                                                        <div class="mt-1">
                                                            <strong class="text-xs">{{ Lang::txt('PLG_GROUPS_MEMBERS_MEMBER_ROLES') }}:</strong>
                                                            <a href="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&active=members&action=assignrole&uid=' . $u->get('id')) }}"
                                                                class="link text-xs">
                                                                {{ Lang::txt('PLG_GROUPS_MEMBERS_ASSIGN_ROLE') }}
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endif

                                                {{-- Pending reason --}}
                                                @if ($filter == 'pending')
                                                    @php
                                                        $database = App::get('db');
                                                        $row = new \Components\Groups\Tables\Reason($database);
                                                        $row->loadReason($u->get('id'), $group->get('gidNumber'));
                                                    @endphp
                                                    @if ($row)
                                                        <div class="mt-1 text-xs">
                                                            <span class="font-semibold">{{ Lang::txt('PLG_GROUPS_MEMBERS_REASON_FOR_REQUEST') }}</span>
                                                            <p>{{ stripslashes($row->reason) }}</p>
                                                            <span class="text-base-content/60">
                                                                {{ Date::of($row->date)->toLocal('F d, Y @ g:ia') }}
                                                            </span>
                                                        </div>
                                                    @endif
                                                @endif
                                            </td>

                                            {{-- Action buttons --}}
                                            <td class="text-right">
                                                <div class="flex flex-wrap gap-1 justify-end">
                                                    @if ($authorized == 'manager' || $authorized == 'admin')
                                                        @switch($filter)
                                                            @case('invitees')
                                                                @if ($membership_control == 1)
                                                                    @php
                                                                        $cancelUser = $inviteemail ? urlencode(urlencode($guser)) : $guser;
                                                                        $cancelName = $inviteemail ? e($guser) : e($u->get('name'));
                                                                    @endphp
                                                                    <a class="btn btn-error btn-xs tooltip"
                                                                        href="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&active=members&action=cancel&users[]=' . $cancelUser . '&filter=' . $filter) }}"
                                                                        data-tip="{{ Lang::txt('PLG_GROUPS_MEMBERS_CANCEL_MEMBER', $cancelName) }}">
                                                                        {{ Lang::txt('PLG_GROUPS_MEMBERS_CANCEL') }}
                                                                    </a>
                                                                @endif
                                                                @break

                                                            @case('pending')
                                                                @if ($membership_control == 1)
                                                                    <a class="btn btn-error btn-xs tooltip"
                                                                        href="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&active=members&action=deny&users[]=' . $guser . '&filter=' . $filter) }}"
                                                                        data-tip="{{ Lang::txt('PLG_GROUPS_MEMBERS_DECLINE_MEMBER', e($u->get('name'))) }}">
                                                                        {{ Lang::txt('PLG_GROUPS_MEMBERS_DENY') }}
                                                                    </a>
                                                                    <a class="btn btn-success btn-xs tooltip"
                                                                        href="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&active=members&action=approve&users[]=' . $guser . '&filter=' . $filter) }}"
                                                                        data-tip="{{ Lang::txt('PLG_GROUPS_MEMBERS_APPROVE_MEMBER', e($u->get('name'))) }}">
                                                                        {{ Lang::txt('PLG_GROUPS_MEMBERS_APPROVE') }}
                                                                    </a>
                                                                @endif
                                                                @break

                                                            @default
                                                                @if ($membership_control == 1)
                                                                    @if (!in_array($guser, $managers) || (in_array($guser, $managers) && count($managers) > 1))
                                                                        <a class="btn btn-error btn-xs tooltip"
                                                                            href="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&active=members&action=remove&users[]=' . $guser . '&filter=' . $filter) }}"
                                                                            data-tip="{{ Lang::txt('PLG_GROUPS_MEMBERS_REMOVE_MEMBER', e($u->get('name'))) }}">
                                                                            {{ Lang::txt('PLG_GROUPS_MEMBERS_REMOVE') }}
                                                                        </a>
                                                                    @endif

                                                                    @if (in_array($guser, $managers))
                                                                        @if (count($managers) > 1)
                                                                            <a class="btn btn-warning btn-xs tooltip"
                                                                                href="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&active=members&action=demote&users[]=' . $guser . '&filter=' . $filter . '&limit=' . $limit . '&limitstart=' . $start) }}"
                                                                                data-tip="{{ Lang::txt('PLG_GROUPS_MEMBERS_DEMOTE_MEMBER', e($u->get('name'))) }}">
                                                                                {{ Lang::txt('PLG_GROUPS_MEMBERS_DEMOTE') }}
                                                                            </a>
                                                                        @endif
                                                                    @else
                                                                        <a class="btn btn-info btn-xs tooltip"
                                                                            href="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&active=members&action=promote&users[]=' . $guser . '&filter=' . $filter . '&limit=' . $limit . '&limitstart=' . $start) }}"
                                                                            data-tip="{{ Lang::txt('PLG_GROUPS_MEMBERS_PROMOTE_MEMBER', e($u->get('name'))) }}">
                                                                            {{ Lang::txt('PLG_GROUPS_MEMBERS_PROMOTE') }}
                                                                        </a>
                                                                    @endif
                                                                @endif
                                                                @break
                                                        @endswitch
                                                    @endif

                                                    {{-- Message button --}}
                                                    @if (!(is_object($u) && User::get('id') == $u->get('uidNumber')) && $filter != 'invitees' && $filter != 'pending')
                                                        @php
                                                            $membersParams = Component::params('com_members');
                                                            $userMessaging = $membersParams->get('user_messaging', 1);
                                                        @endphp
                                                        @if (!$inviteemail && $messages_acl != 'nobody')
                                                            @if ($u->get('activation') > 0)
                                                                @if (in_array(User::get('id'), $group->get('managers')) && $u->get('activation') > 0)
                                                                    <a class="btn btn-ghost btn-xs tooltip"
                                                                        href="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&active=messages&action=new&users[]=' . $guser) }}"
                                                                        data-tip="{{ Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE') }} :: Send a message to {{ e($u->get('name')) }}">
                                                                        {{ Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE') }}
                                                                    </a>
                                                                @elseif (($userMessaging == 2 || ($userMessaging == 1 && in_array(User::get('id'), $group->get('members')))) && $u->get('activation') > 0)
                                                                    <a class="btn btn-ghost btn-xs tooltip"
                                                                        href="{{ Route::url('index.php?option=com_members&id=' . User::get('id') . '&active=messages&task=new&to[]=' . $guser) }}"
                                                                        data-tip="{{ Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE') }} :: Send a message to {{ e($u->get('name')) }}">
                                                                        {{ Lang::txt('PLG_GROUPS_MEMBERS_MESSAGE') }}
                                                                    </a>
                                                                @endif
                                                            @else
                                                                <span class="badge badge-warning badge-sm">
                                                                    {{ Lang::txt('PLG_GROUPS_MEMBERS_EMAIL_NOT_ACTIVATED') }}
                                                                </span>
                                                            @endif
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endfor
                                @else
                                    <tr>
                                        <td class="text-center py-8 text-base-content/60" colspan="3">
                                            {{ Lang::txt('PLG_GROUPS_MEMBERS_NO_RESULTS') }}
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination --}}
                    @php
                        $pageNav = $__view->pagination(
                            count($groupusers),
                            $start,
                            $limit
                        );
                        $pageNav->setAdditionalUrlParam('cn', $group->get('cn'));
                        $pageNav->setAdditionalUrlParam('active', 'members');
                        $pageNav->setAdditionalUrlParam('filter', $filter);
                        $pageNav->setAdditionalUrlParam('q', $q);
                    @endphp
                    <div class="p-4">
                        {!! $pageNav->render() !!}
                    </div>
                </div>
            </div>

            <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
            <input type="hidden" name="active" value="members" />
            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="filter" value="{{ $filter }}" />
        </form>
    </div>

    {{-- Sidebar: Roles --}}
    <aside class="w-full lg:w-72 shrink-0">
        <div class="card bg-base-100 shadow">
            <div class="card-body">
                <h4 class="card-title text-base">
                    {{ Lang::txt('PLG_GROUPS_MEMBERS_MEMBER_ROLES') }}
                </h4>
                @if (count($member_roles) > 0)
                    <ul class="menu menu-sm bg-base-100 rounded-box p-0">
                        @foreach ($member_roles as $role)
                            @php
                                $isActive = ($role['id'] == $role_filter);
                            @endphp
                            <li>
                                <div class="flex items-center gap-1 {{ $isActive ? 'active' : '' }}">
                                    <a href="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&active=members&role_filter=' . $role['id']) }}"
                                        class="flex-1">
                                        {{ e($role['name']) }}
                                    </a>
                                    @if ($authorized == 'manager' && $membership_control == 1)
                                        <a href="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&active=members&action=editrole&role=' . $role['id']) }}"
                                            class="btn btn-ghost btn-xs"
                                            title="{{ Lang::txt('PLG_GROUPS_MEMBERS_ROLE_EDIT') }}">
                                            {{ Lang::txt('PLG_GROUPS_MEMBERS_ROLE_EDIT') }}
                                        </a>
                                        <a href="{{ Route::url('index.php?option=' . $optionVal . '&cn=' . $group->get('cn') . '&active=members&action=removerole&role=' . $role['id']) }}"
                                            class="btn btn-ghost btn-xs text-error"
                                            title="{{ Lang::txt('PLG_GROUPS_MEMBERS_ROLE_REMOVE') }}">
                                            {{ Lang::txt('PLG_GROUPS_MEMBERS_ROLE_REMOVE') }}
                                        </a>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-base-content/60 text-sm">
                        {{ Lang::txt('PLG_GROUPS_MEMBERS_NO_ROLES_FOUND') }}
                    </p>
                @endif
            </div>
        </div>
    </aside>
</div>
