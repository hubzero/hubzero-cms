{{--
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

$formUrl = Route::url(
    'index.php?option=' . $option
    . '&cn=' . $group->get('cn')
    . '&active=members'
);
$formId = 'hubForm' . ($no_html ? '-ajax' : '');

$u = User::getInstance($uid);

$current_roles = [];
$memberRoles = \Components\Groups\Helpers\Permissions::getGroupMemberRoles(
    $u->get('id'),
    $group->get('gidNumber')
);
if ($memberRoles) {
    foreach ($memberRoles as $memberRole) {
        $current_roles[] = $memberRole['name'];
    }
}
@endphp

@if ($__view->getError())
    <div class="alert alert-error mb-4">
        <span>{{ $__view->getError() }}</span>
    </div>
@endif

<form action="{{ $formUrl }}"
    method="post"
    id="{{ $formId }}">

    <fieldset class="card bg-base-100 shadow">
        <div class="card-body">
            <legend class="card-title text-lg">
                {{ Lang::txt('PLG_GROUPS_MEMBERS_ASSIGN_ROLE') }}
            </legend>

            <input type="hidden" name="uid" value="{{ e($uid) }}" id="uid" />

            <div class="form-control mb-4">
                <label class="label">
                    <span class="label-text">
                        <strong>{{ Lang::txt('PLG_GROUPS_MEMBERS_MEMBER') }}:</strong>
                        {{ e($u->get('name')) }}
                    </span>
                </label>
            </div>

            <div class="form-control">
                <label class="label" for="roles">
                    <span class="label-text font-semibold">
                        {{ Lang::txt('PLG_GROUPS_MEMBERS_SELECT_ROLE') }}
                    </span>
                </label>
                <select name="role" id="roles" class="select select-bordered w-full">
                    <option value="">
                        {{ Lang::txt('PLG_GROUPS_MEMBERS_OPT_SELECT_ROLE') }}
                    </option>
                    @foreach ($roles as $role)
                        @if (!in_array($role['name'], $current_roles))
                            <option value="{{ $role['id'] }}">
                                {{ e($role['name']) }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
        </div>
    </fieldset>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
    <input type="hidden" name="active" value="members" />
    <input type="hidden" name="action" value="submitrole" />
    <input type="hidden" name="no_html" value="{{ $no_html }}" />

    <div class="mt-4">
        <button type="submit" name="submit" class="btn btn-primary">
            {{ Lang::txt('PLG_GROUPS_MEMBERS_ASSIGN_ROLE') }}
        </button>
    </div>
</form>
