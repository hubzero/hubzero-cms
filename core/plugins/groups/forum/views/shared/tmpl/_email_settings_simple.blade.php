{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

<form method="post" action="{{ Route::url($base) }}" id="forum-options">
    <fieldset>
        <legend class="text-base font-semibold">{{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_SETTINGS') }}</legend>

        <input type="hidden" name="action" value="savememberoptions" />
        <input type="hidden" name="memberoptionid" value="{{ $recvEmailOptionID }}" />
        <input type="hidden" name="postsaveredirect" value="{{ Route::url($base) }}" />
        {!! Html::input('token') !!}

        <label class="label cursor-pointer justify-start gap-2" for="recvpostemail">
            <input type="checkbox"
                class="checkbox checkbox-sm"
                id="recvpostemail"
                value="1"
                name="recvpostemail"
                @if ($recvEmailOptionValue == 1) checked="checked" @endif />
            <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS') }}</span>
        </label>
        <input class="btn btn-sm btn-primary mt-2" type="submit" value="{{ Lang::txt('PLG_GROUPS_FORUM_SAVE') }}" />
    </fieldset>
</form>
