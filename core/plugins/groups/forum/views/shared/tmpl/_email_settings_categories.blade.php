{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->js('notify');
$__view->js('emailSettings');

$preexistingSubscriptionIds = [];
$currentUserId = User::get('id');
@endphp

<form method="post" id="email-settings">
    <fieldset>
        <legend class="text-base font-semibold">{{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_SETTINGS') }}</legend>

        <span class="block mb-2 text-sm text-base-content/70">
            {{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_CATEGORIES') }}
        </span>

        @foreach ($categories as $cat)
            @php
            $usersSubscription = $cat->usersCategories()
                ->whereEquals('user_id', $currentUserId)
                ->row();

            $checked = '';

            if (!$usersSubscription->isNew()) {
                $checked = 'checked';
                $preexistingSubscriptionIds[] = $cat->get('id');
            }
            @endphp
            <label class="label cursor-pointer justify-start gap-2 mb-2">
                <input type="checkbox"
                    class="checkbox checkbox-sm"
                    name="{{ $cat->get('id') }}"
                    {{ $checked }} />
                <span class="label-text">{{ $cat->get('title') }}</span>
            </label>
        @endforeach

        {!! Html::input('token') !!}
        <input type="hidden" id="user-id" value="{{ $currentUserId }}" />
        <input type="hidden"
            id="preexisting-subscriptions"
            value="{{ implode(',', $preexistingSubscriptionIds) }}" />

        <input class="btn btn-sm btn-primary mt-2" type="submit" value="{{ Lang::txt('PLG_GROUPS_FORUM_SAVE') }}" />
    </fieldset>
</form>
