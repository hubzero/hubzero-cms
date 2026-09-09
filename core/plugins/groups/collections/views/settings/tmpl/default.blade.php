{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@if ($__view->getError())
    <div class="alert alert-error">
        <p>{{ $__view->getError() }}</p>
    </div>
@endif

@php
$formAction = Route::url(
    'index.php?option=' . $option
    . '&cn=' . $group->get('cn')
    . '&active=collections&action=savesettings'
);
@endphp
<form action="{{ $formAction }}"
    method="post"
    id="hubForm"
    class="full">

    <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box mb-4">
        <legend class="fieldset-legend text-lg font-semibold">
            {{ Lang::txt('PLG_GROUPS_COLLECTIONS') }}
        </legend>

        <div class="form-group mb-4">
            <label for="param-create_collection" class="label">
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_COLLECTIONS') }}</span>
            </label>
            <select name="params[create_collection]" id="param-create_collection" class="select select-bordered w-full">
                <option value="0" @if (!$params->get('create_collection', 1)) selected @endif>
                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_COLLECTIONS_ALL') }}
                </option>
                <option value="1" @if ($params->get('create_collection', 1) == 1) selected @endif>
                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_COLLECTIONS_MANAGERS') }}
                </option>
            </select>
        </div>

        <div class="alert alert-info">
            {{ Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_COLLECTIONS_INFO') }}
        </div>
    </fieldset>

    <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box mb-4">
        <legend class="fieldset-legend text-lg font-semibold">
            {{ Lang::txt('PLG_GROUPS_COLLECTIONS_POSTS') }}
        </legend>

        <div class="form-group mb-4">
            <label for="param-create_post" class="label">
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_POSTS') }}</span>
            </label>
            <select name="params[create_post]" id="param-create_post" class="select select-bordered w-full">
                <option value="0" @if (!$params->get('create_post', 0)) selected @endif>
                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_POSTS_ALL') }}
                </option>
                <option value="1" @if ($params->get('create_post', 0) == 1) selected @endif>
                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_POSTS_MANAGERS') }}
                </option>
            </select>
        </div>

        <div class="alert alert-info">
            {{ Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_POSTS_INFO') }}
        </div>
    </fieldset>

    <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
    <input type="hidden" name="process" value="1" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="active" value="collections" />
    <input type="hidden" name="action" value="savesettings" />

    {!! Html::input('token') !!}

    <div class="mt-4 flex gap-2">
        <button type="submit" class="btn btn-success">
            {{ Lang::txt('PLG_GROUPS_COLLECTIONS_SAVE') }}
        </button>
        <a class="btn btn-ghost"
            href="{{ Route::url('index.php?option=' . $option . '&cn=' . $group->get('cn') . '&active=collections') }}">
            {{ Lang::txt('JCANCEL') }}
        </a>
    </div>
</form>
