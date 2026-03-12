{{-- /**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */ --}}
@php
use Hubzero\Facades\App;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Session;
use Hubzero\Facades\Toolbar;

$canDo = \Components\Groups\Helpers\Permissions::getActions('group');

Toolbar::title(Lang::txt('COM_GROUPS'), 'groups');

Toolbar::appendButton(
    'Popup',
    'new',
    'COM_GROUPS_NEW',
    Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller
        . '&tmpl=component&task=new&gid=' . $filters['gid'], false
    ),
    570,
    170
);

Toolbar::appendButton(
    'Link',
    'unblock',
    'COM_GROUPS_ROLE_ASSIGN',
    'index.php?option=' . $option . '&controller=roles&tmpl=component&task=assign&gid=' . $filters['gid'],
    400,
    400
);

Toolbar::spacer();

switch ($filters['status']) {
    case 'invitee':
        if ($canDo->get('core.delete')) {
            Toolbar::custom(
                'uninvite',
                'unpublish',
                'COM_GROUPS_MEMBER_UNINVITE',
                'COM_GROUPS_MEMBER_UNINVITE',
                false,
                false
            );
        }
        break;
    case 'applicant':
        if ($canDo->get('core.edit')) {
            Toolbar::custom(
                'approve',
                'publish',
                'COM_GROUPS_MEMBER_APPROVE',
                'COM_GROUPS_MEMBER_APPROVE',
                false,
                false
            );
        }
        if ($canDo->get('core.delete')) {
            Toolbar::custom(
                'deny',
                'unpublish',
                'COM_GROUPS_MEMBER_DENY',
                'COM_GROUPS_MEMBER_DENY',
                false,
                false
            );
        }
        break;
    default:
        if ($canDo->get('core.edit')) {
            Toolbar::custom(
                'promote',
                'promote',
                'COM_GROUPS_MEMBER_PROMOTE',
                'COM_GROUPS_MEMBER_PROMOTE',
                false,
                false
            );
            Toolbar::custom(
                'demote',
                'demote',
                'COM_GROUPS_MEMBER_DEMOTE',
                'COM_GROUPS_MEMBER_DEMOTE',
                false,
                false
            );
        }
        if ($canDo->get('core.delete')) {
            Toolbar::deleteList('COM_GROUPS_MEMBER_DELETE', 'delete');
        }
        break;
}

Toolbar::spacer();
Toolbar::help('membership');

$__view->css();
$__view->js();

$database = App::get('db');

$formUrl  = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);
$rolesUrl = Route::url(
    'index.php?option=com_groups&controller=roles&tmpl=component&gid=' . $filters['gid'], false
);
$rolesRel = '{size: {width: 570, height: 170}, onClose: function() {}}';
@endphp

<x-admin-form option="{{ $option }}" controller="{{ $controller }}" sort="{{ $filters['sort'] ?? 'name' }}" sortDir="{{ $filters['sort_Dir'] ?? 'asc' }}">
    @slot('filters')
        <x-admin-filters>
            @slot('search')
                <input
                    type="text"
                    name="search"
                    id="filter_search"
                    class="input input-bordered input-sm"
                    value="{{ $filters['search'] }}"
                    placeholder="{{ Lang::txt('COM_GROUPS_SEARCH') }}" />
            @endslot

            <label for="filter-status" class="sr-only">{{ Lang::txt('COM_GROUPS_MEMBER_STATUS') }}</label>
            <select name="status" id="filter-status" class="select select-bordered select-sm" data-submit-on-change>
                <option value=""{{ $filters['status'] == '' ? ' selected="selected"' : '' }}>
                    {{ Lang::txt('COM_GROUPS_MEMBER_STATUS') }}
                </option>
                <option value="manager"{{ $filters['status'] == 'manager' ? ' selected="selected"' : '' }}>
                    {{ Lang::txt('Manager') }}
                </option>
                <option value="applicant"{{ $filters['status'] == 'applicant' ? ' selected="selected"' : '' }}>
                    {{ Lang::txt('Applicant') }}
                </option>
                <option value="invitee"{{ $filters['status'] == 'invitee' ? ' selected="selected"' : '' }}>
                    {{ Lang::txt('Invitee') }}
                </option>
            </select>

            <a class="btn btn-sm" href="{{ $rolesUrl }}" rel="{{ $rolesRel }}">
                {{ Lang::txt('Roles') }}
            </a>
        </x-admin-filters>
    @endslot

    <input type="hidden" name="gid" value="{{ $filters['gid'] }}" />

    <table class="admin-table">
        <thead>
            <tr>
                <th colspan="8">
                    <a href="{{ Route::url('index.php?option=' . $option, false) }}">{{ Lang::txt('COM_GROUPS') }}</a> &gt;
                    ({{ $group->get('cn') }}) {{ $group->get('description') }}
                </th>
            </tr>
            <tr>
                <th scope="col">
                    <input
                        type="checkbox"
                        class="checkbox checkbox-sm"
                        data-check-all
                        aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
                </th>
                <th scope="col" class="priority-4">
                    {!! Html::grid('sort', 'COM_GROUPS_USERID', 'uidNumber', @$filters['sort_Dir'], @$filters['sort']) !!}
                </th>
                <th scope="col">
                    {!! Html::grid('sort', 'COM_GROUPS_NAME', 'name', @$filters['sort_Dir'], @$filters['sort']) !!}
                </th>
                <th scope="col" class="priority-3">
                    {!! Html::grid('sort', 'COM_GROUPS_USERNAME', 'username', @$filters['sort_Dir'], @$filters['sort']) !!}
                </th>
                <th scope="col" class="priority-5">
                    {!! Html::grid('sort', 'COM_GROUPS_EMAIL', 'email', @$filters['sort_Dir'], @$filters['sort']) !!}
                </th>
                <th scope="col">{{ Lang::txt('COM_GROUPS_MEMBER_STATUS') }}</th>
                <th scope="col" colspan="2">{{ Lang::txt('COM_GROUPS_MEMBER_ACTION') }}</th>
            </tr>
        </thead>
        <tfoot>
            <tr>
                <td colspan="8">
                    {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
                </td>
            </tr>
        </tfoot>
        <tbody>
            @foreach ($rows as $i => $row)
                @php
                $reasonforjoin = '';
                if (isset($row->username)) {
                    $reason = new \Components\Groups\Tables\Reason($database);
                    $reason->loadReason($row->username, $filters['gidNumber']);
                    if ($reason) {
                        $reasonforjoin = $reason->reason ?? '';
                    }
                }

                $status = $row->role;
                if (in_array($row->uidNumber, $group->get('managers'))) {
                    $status = 'manager';
                }

                $roles = \Components\Groups\Helpers\Permissions::getGroupMemberRoles(
                    $row->uidNumber,
                    $group->get('gidNumber')
                );

                $memberId = isset($row->uidNumber) ? $row->uidNumber : ($row->email ?? '');
                @endphp
                <tr>
                    <td>
                        <input
                            type="checkbox"
                            name="id[]"
                            id="cb{{ $i }}"
                            value="{{ $memberId }}"
                            class="checkbox checkbox-sm"
                            aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $row->name ?? $memberId) }}"
                            data-check-item />
                    </td>
                    <td class="priority-4">
                        {{ $row->uidNumber ?? '' }}
                    </td>
                    <td>
                        @if ($canDo->get('core.edit') && isset($row->username))
                            @php
                            $memberEditUrl = Route::url(
                                'index.php?option=com_members&controller=members&task=edit&id='
                                . $row->uidNumber, false
                            );
                            @endphp
                            <a href="{{ $memberEditUrl }}">{{ $row->name }}</a>
                        @else
                            <span>{{ $row->name }}</span>
                        @endif
                        @if ($roles)
                            <br />
                            <span class="roles">
                                @php
                                $r = [];
                                foreach ($roles as $role) {
                                    $unassignUrl = Route::url(
                                        'index.php?option=com_groups&controller=roles&task=unassign'
                                        . '&gid=' . $filters['gid']
                                        . '&id=' . $row->uidNumber
                                        . '&roleid=' . $role['id']
                                        . '&return=' . $controller, false
                                    );
                                    $r[] = '<span class="role">'
                                        . $role['name']
                                        . ' <a href="' . $unassignUrl . '"'
                                        . ' title="' . Lang::txt('COM_GROUPS_UNASSIGN_ROLE') . '">'
                                        . 'x</a></span>';
                                }
                                echo implode(', ', $r);
                                @endphp
                            </span>
                        @endif
                    </td>
                    <td class="priority-3">
                        <span>{{ $row->username ?? '' }}</span>
                    </td>
                    <td class="priority-5">
                        <span>{{ $row->email ?? '' }}</span>
                    </td>
                    <td>
                        @php
                        $badgeClass = match($status) {
                            'manager'                   => 'badge-success',
                            'member'                    => 'badge-ghost',
                            'applicant'                 => 'badge-warning',
                            'invitee', 'inviteemail'    => 'badge-info',
                            default                     => 'badge-ghost',
                        };
                        @endphp
                        <span class="badge badge-sm {{ $badgeClass }}">{{ $status }}</span>
                    </td>
                    @if ($canDo->get('core.edit'))
                        @php
                        switch ($status) {
                            case 'invitee':
                            case 'inviteemail':
                                $actionMemberId = isset($row->uidNumber) ? $row->uidNumber : ($row->email ?? '');
                                $uninviteUrl = Route::url(
                                    'index.php?option=' . $option
                                    . '&controller=' . $controller
                                    . '&task=uninvite&gid=' . $filters['gid']
                                    . '&id=' . $actionMemberId
                                    . '&' . Session::getFormToken() . '=1', false
                                );
                                break;
                            case 'applicant':
                                $approveUrl = Route::url(
                                    'index.php?option=' . $option
                                    . '&controller=' . $controller
                                    . '&task=approve&gid=' . $filters['gid']
                                    . '&id=' . $row->uidNumber
                                    . '&' . Session::getFormToken() . '=1', false
                                );
                                $denyUrl = Route::url(
                                    'index.php?option=' . $option
                                    . '&controller=' . $controller
                                    . '&task=deny&gid=' . $filters['gid']
                                    . '&id=' . $row->uidNumber
                                    . '&' . Session::getFormToken() . '=1', false
                                );
                                break;
                            case 'manager':
                                $demoteUrl = Route::url(
                                    'index.php?option=' . $option
                                    . '&controller=' . $controller
                                    . '&task=demote&gid=' . $filters['gid']
                                    . '&id=' . $row->uidNumber
                                    . '&' . Session::getFormToken() . '=1', false
                                );
                                break;
                            default:
                            case 'member':
                                $promoteUrl = Route::url(
                                    'index.php?option=' . $option
                                    . '&controller=' . $controller
                                    . '&task=promote&gid=' . $filters['gid']
                                    . '&id=' . $row->uidNumber
                                    . '&' . Session::getFormToken() . '=1', false
                                );
                                $deleteUrl = Route::url(
                                    'index.php?option=' . $option
                                    . '&controller=' . $controller
                                    . '&task=delete&gid=' . $filters['gid']
                                    . '&id=' . $row->uidNumber
                                    . '&' . Session::getFormToken() . '=1', false
                                );
                                break;
                        }
                        @endphp
                        @if ($status == 'invitee' || $status == 'inviteemail')
                            <td>
                                <a class="btn btn-xs btn-ghost"
                                   href="{{ $uninviteUrl }}"
                                   data-confirm="Cancel invitation?">
                                    {{ Lang::txt('COM_GROUPS_MEMBER_UNINVITE') }}
                                </a>
                            </td>
                            <td></td>
                        @elseif ($status == 'applicant')
                            <td>
                                <a class="btn btn-xs btn-success" href="{{ $approveUrl }}">
                                    {{ Lang::txt('COM_GROUPS_MEMBER_APPROVE') }}
                                </a>
                            </td>
                            <td>
                                <a class="btn btn-xs btn-error"
                                   href="{{ $denyUrl }}"
                                   data-confirm="Deny membership?">
                                    {{ Lang::txt('COM_GROUPS_MEMBER_DENY') }}
                                </a>
                            </td>
                        @elseif ($status == 'manager')
                            <td>
                                <a class="btn btn-xs btn-ghost" href="{{ $demoteUrl }}">
                                    ↓ {{ Lang::txt('COM_GROUPS_MEMBER_DEMOTE') }}
                                </a>
                            </td>
                            <td></td>
                        @else
                            <td>
                                <a class="btn btn-xs btn-ghost" href="{{ $promoteUrl }}">
                                    ↑ {{ Lang::txt('COM_GROUPS_MEMBER_PROMOTE') }}
                                </a>
                            </td>
                            <td>
                                <a class="btn btn-xs btn-ghost text-error"
                                   href="{{ $deleteUrl }}"
                                   data-confirm="Cancel membership?">
                                    {{ Lang::txt('COM_GROUPS_MEMBER_REMOVE') }}
                                </a>
                            </td>
                        @endif
                    @else
                        <td></td>
                        <td></td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

</x-admin-form>
