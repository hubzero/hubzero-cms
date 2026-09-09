{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->css();
$__view->js();

$base = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=forum';
@endphp

@if ($__view->getError())
    <div class="alert alert-error">
        <p>{{ $__view->getError() }}</p>
    </div>
@endif

<form action="{{ Route::url($base . '&action=savesettings') }}" method="post" id="hubForm" class="full">
    <fieldset>
        <legend class="text-lg font-bold">{{ Lang::txt('PLG_GROUPS_FORUM_SETTINGS_THREADS') }}</legend>

        <div class="form-group">
            <label for="param-threading" class="label">
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_SETTINGS_THREADING') }}</span>
            </label>
            <select name="params[threading]" id="param-threading" class="select select-bordered w-full">
                <option value="list" @if ($config->get('threading', 'list') == 'list') selected="selected" @endif>
                    {{ Lang::txt('PLG_GROUPS_FORUM_SETTINGS_LIST') }}
                </option>
                <option value="tree" @if ($config->get('threading', 'list') == 'tree') selected="selected" @endif>
                    {{ Lang::txt('PLG_GROUPS_FORUM_SETTINGS_TREE') }}
                </option>
            </select>
            <label class="label">
                <span class="label-text-alt text-base-content/70">{{ Lang::txt('PLG_GROUPS_FORUM_SETTINGS_THREADING_HINT') }}</span>
            </label>
        </div>

        <div class="form-group">
            <label for="param-sorting" class="label">
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_SETTINGS_SORTING') }}</span>
            </label>
            @php
            $sorting = $config->get('sorting', 'activity');
            @endphp
            <select name="params[sorting]" id="param-sorting" class="select select-bordered w-full">
                <option value="activity" @if ($sorting == 'activity') selected="selected" @endif>
                    {{ Lang::txt('PLG_GROUPS_FORUM_SETTINGS_ACTIVITY') }}
                </option>
                <option value="created" @if ($sorting == 'created') selected="selected" @endif>
                    {{ Lang::txt('PLG_GROUPS_FORUM_SETTINGS_CREATED') }}
                </option>
                <option value="replies" @if ($sorting == 'replies') selected="selected" @endif>
                    {{ Lang::txt('PLG_GROUPS_FORUM_SETTINGS_REPLIES') }}
                </option>
                <option value="title" @if ($sorting == 'title') selected="selected" @endif>
                    {{ Lang::txt('PLG_GROUPS_FORUM_SETTINGS_TITLE') }}
                </option>
            </select>
            <label class="label">
                <span class="label-text-alt text-base-content/70">{{ Lang::txt('PLG_GROUPS_FORUM_SETTINGS_SORTING_HINT') }}</span>
            </label>
        </div>

        <div class="form-group">
            <label for="param-threading_depth" class="label">
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_SETTINGS_THREADING_DEPTH') }}</span>
            </label>
            <input type="text"
                class="input input-bordered w-full"
                name="params[threading_depth]"
                id="param-threading_depth"
                value="{{ $config->get('threading_depth', 3) }}" />
            <label class="label">
                <span class="label-text-alt text-base-content/70">{{ Lang::txt('PLG_GROUPS_FORUM_SETTINGS_THREADING_DEPTH_HINT') }}</span>
            </label>
        </div>

        <fieldset class="form-group">
            <legend class="text-base font-semibold">{{ Lang::txt('PLG_GROUPS_FORUM_SETTINGS_ALLOW_ANONYMOUS') }}</legend>

            <div class="flex gap-4">
                <label for="param-allow_anonymous-no" class="label cursor-pointer justify-start gap-2">
                    <input type="radio"
                        class="radio"
                        name="params[allow_anonymous]"
                        id="param-allow_anonymous-no"
                        value="0"
                        @if (!$config->get('allow_anonymous')) checked="checked" @endif />
                    <span class="label-text">{{ Lang::txt('JNO') }}</span>
                </label>
                <label for="param-allow_anonymous-yes" class="label cursor-pointer justify-start gap-2">
                    <input type="radio"
                        class="radio"
                        name="params[allow_anonymous]"
                        id="param-allow_anonymous-yes"
                        value="1"
                        @if ($config->get('allow_anonymous')) checked="checked" @endif />
                    <span class="label-text">{{ Lang::txt('JYES') }}</span>
                </label>
            </div>
        </fieldset>
    </fieldset>

    <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
    <input type="hidden" name="process" value="1" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="active" value="forum" />
    <input type="hidden" name="action" value="savesettings" />

    {!! Html::input('token') !!}

    <div class="flex gap-2 mt-4">
        <input class="btn btn-success" type="submit" value="{{ Lang::txt('PLG_GROUPS_FORUM_SAVE') }}" />
        <a class="btn btn-ghost" href="{{ Route::url($base) }}">
            {{ Lang::txt('JCANCEL') }}
        </a>
    </div>
</form>
