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
@endphp

<form action="{{ $formUrl }}" method="post" id="hubForm">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Sidebar explanation --}}
        <div class="lg:col-span-1 order-first lg:order-last">
            <div class="alert alert-info">
                <span>{{ Lang::txt('PLG_GROUPS_MEMBERS_DENY_EXPLANATION') }}</span>
            </div>
        </div>

        {{-- Main form --}}
        <div class="lg:col-span-2">
            <fieldset class="card bg-base-100 shadow">
                <div class="card-body">
                    <legend class="card-title text-lg">
                        {{ Lang::txt('PLG_GROUPS_MEMBERS_DENY_MEMBERSHIP') }}
                    </legend>

                    @php
                        $names = [];
                        foreach ($users as $user) {
                            $u = User::getInstance($user);
                            $names[] = e($u->get('name'));
                        }
                    @endphp

                    @foreach ($users as $user)
                        <input type="hidden" name="users[]" value="{{ e($user) }}" />
                    @endforeach

                    <div class="form-control mb-4">
                        <label class="label">
                            <span class="label-text">
                                {{ Lang::txt('PLG_GROUPS_MEMBERS_DENY_USERS') }}
                            </span>
                        </label>
                        <p class="font-bold">{{ implode(', ', $names) }}</p>
                    </div>

                    <div class="form-control">
                        <label class="label" for="reason">
                            <span class="label-text">
                                {{ Lang::txt('PLG_GROUPS_MEMBERS_DENY_REASON') }}
                            </span>
                        </label>
                        <textarea name="reason"
                            id="reason"
                            rows="12"
                            class="textarea textarea-bordered w-full"></textarea>
                    </div>
                </div>
            </fieldset>
        </div>
    </div>

    <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
    <input type="hidden" name="active" value="members" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="action" value="confirmdeny" />

    <div class="mt-4">
        <button type="submit" class="btn btn-primary">
            {{ Lang::txt('PLG_GROUPS_MEMBERS_SUBMIT') }}
        </button>
    </div>
</form>
