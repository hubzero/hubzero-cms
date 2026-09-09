{{--
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
@endphp

@if ($__view->getError())
    <div class="alert alert-error mb-4">
        <span>{{ $__view->getError() }}</span>
    </div>
@endif

<div class="flex flex-wrap gap-2 mb-4">
    <a class="btn btn-ghost btn-sm"
        href="{{ Route::url('index.php?option=' . $option . '&cn=' . $group->get('cn') . '&active=members') }}">
        {{ Lang::txt('PLG_GROUPS_MEMBERS') }}
    </a>
</div>

<form action="{{ Route::url('index.php?option=' . $option . '&cn=' . $group->get('cn') . '&active=members') }}"
    method="post"
    id="hubForm">

    <fieldset class="card bg-base-100 shadow">
        <div class="card-body">
            <legend class="card-title text-lg">
                {{ Lang::txt('PLG_GROUPS_MEMBERS_ROLE_DETAILS') }}
            </legend>

            <div class="form-control mb-4">
                <label class="label" for="role-name">
                    <span class="label-text">
                        {{ Lang::txt('PLG_GROUPS_MEMBERS_ROLE_NAME') }}:
                        <span class="text-error">{{ Lang::txt('JREQUIRED') }}</span>
                    </span>
                </label>
                <input type="text"
                    name="role[name]"
                    id="role-name"
                    value="{{ $role->name }}"
                    class="input input-bordered w-full" />
            </div>

            <fieldset class="mt-4">
                <legend class="font-semibold text-base mb-2">
                    {{ Lang::txt('PLG_GROUPS_MEMBERS_ROLE_PERMISSIONS') }}
                </legend>
                @foreach ($available_permissions as $perm => $label)
                    <div class="form-control">
                        <label class="label cursor-pointer justify-start gap-3">
                            <input type="hidden"
                                name="role[permissions][{{ $perm }}]"
                                value="0" />
                            <input type="checkbox"
                                class="checkbox"
                                name="role[permissions][{{ $perm }}]"
                                value="1"
                                {{ $role->permissions->get($perm) ? 'checked' : '' }} />
                            <span class="label-text">{{ $label }}</span>
                        </label>
                    </div>
                @endforeach
            </fieldset>
        </div>
    </fieldset>

    <input type="hidden" name="role[id]" value="{{ $role->id }}" />
    <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
    <input type="hidden" name="active" value="members" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="action" value="saverole" />

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            {{ Lang::txt('PLG_GROUPS_MEMBERS_SUBMIT') }}
        </button>
    </div>
</form>
