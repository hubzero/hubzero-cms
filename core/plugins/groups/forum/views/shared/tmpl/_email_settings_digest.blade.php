{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

<p class="text-sm">
    {{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_CURRENT_SETTINGS') }}
    {{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_CURRENT_SETTINGS_' . $recvEmailOptionValue) }}
    <br />
    <a href="#" class="edit-forum-options link link-primary text-sm">
        {{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_CHANGE_SETTINGS') }}
    </a>
</p>
<div class="edit-forum-options-panel">
    <form method="post" action="{{ Route::url($base) }}" id="forum-options-extended">
        <div class="mb-2">
            <label class="label cursor-pointer justify-start gap-2">
                <input type="checkbox"
                    class="checkbox checkbox-sm edit-forum-options-receive-emails"
                    value="1"
                    name="recvpostemail"
                    @if ($recvEmailOptionValue >= 1) checked="checked" @endif />
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_TOGGLE') }}</span>
            </label>
        </div>
        <div class="edit-forum-options-as text-sm mb-2">
            {{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_INTERVAL') }}
        </div>
        <div class="mb-2">
            <label class="label cursor-pointer justify-start gap-2">
                <input type="radio"
                    name="recvpostemail"
                    class="radio radio-sm edit-forum-options-immediate"
                    value="1"
                    @if ($recvEmailOptionValue == 1) checked="checked" @endif
                    @if ($recvEmailOptionValue == 0) disabled="disabled" @endif />
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_IMMEDIATELY') }}</span>
            </label>
        </div>
        <div class="mb-2 flex items-center gap-2">
            <label class="label cursor-pointer justify-start gap-2">
                <input type="radio"
                    name="recvpostemail"
                    class="radio radio-sm edit-forum-options-digest"
                    value="2"
                    @if ($recvEmailOptionValue >= 2) checked="checked" @endif
                    @if ($recvEmailOptionValue == 0) disabled="disabled" @endif />
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_AS_A') }}</span>
            </label>
            <select name="recvpostemail"
                class="select select-bordered select-sm edit-forum-options-frequency"
                @if ($recvEmailOptionValue < 2) disabled="disabled" @endif>
                <option value="2" @if ($recvEmailOptionValue == 2) selected="selected" @endif>
                    {{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_DAILY') }}
                </option>
                <option value="3" @if ($recvEmailOptionValue == 3) selected="selected" @endif>
                    {{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_WEEKLY') }}
                </option>
                <option value="4" @if ($recvEmailOptionValue == 4) selected="selected" @endif>
                    {{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_MONTHLY') }}
                </option>
            </select>
            <span class="text-sm">{{ Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_DIGEST') }}</span>
        </div>

        <input type="hidden" name="action" value="savememberoptions" />
        <input type="hidden" name="memberoptionid" value="{{ $recvEmailOptionID }}" />
        {!! Html::input('token') !!}

        <div class="edit-forum-options-actions flex gap-2 mt-2">
            <input type="submit" class="btn btn-sm btn-success" value="{{ Lang::txt('PLG_GROUPS_FORUM_SAVE') }}" />
            <input type="button" class="btn btn-sm btn-ghost edit-forum-options-cancel" value="{{ Lang::txt('JCANCEL') }}" />
        </div>
    </form>
</div>
