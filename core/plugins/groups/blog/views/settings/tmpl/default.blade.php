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

$base = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=blog';
@endphp

<ul id="page_options">
    <li>
        <a class="btn btn-ghost gap-2" href="{{ Route::url($base) }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
            {{ Lang::txt('PLG_GROUPS_BLOG_ARCHIVE') }}
        </a>
    </li>
</ul>

@if ($__view->getError())
    <div class="alert alert-error">
        <p>{{ $__view->getError() }}</p>
    </div>
@endif

@if (isset($message) && $message)
    <div class="alert alert-success">
        <p>{{ $message }}</p>
    </div>
@endif

<form action="{{ Route::url($base . '&action=savesettings') }}" method="post" id="hubForm" class="full">
    <fieldset class="mb-6">
        <legend class="text-lg font-bold">{{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS_POSTS') }}</legend>
        <p class="text-base-content/70">{{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS_POSTS_EXPLANATION') }}</p>
    </fieldset>

    <fieldset class="mb-6">
        <legend class="text-lg font-bold">{{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS_ENTRIES') }}</legend>

        <div class="form-control w-full max-w-md mb-4">
            <label class="label" for="param-posting">
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS_ENTRY_POST') }}</span>
            </label>
            <select name="params[posting]" id="param-posting" class="select select-bordered w-full">
                <option value="0" @selected(!$config->get('posting', 0))>
                    {{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS_ENTRY_POST_ALL') }}
                </option>
                <option value="1" @selected($config->get('posting', 0) == 1)>
                    {{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS_ENTRY_POST_MANAGERS') }}
                </option>
            </select>
        </div>

        <p class="text-sm text-base-content/60">
            {{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS_ENTRY_POST_HELP') }}
        </p>
    </fieldset>

    <fieldset class="mb-6">
        <legend class="text-lg font-bold">{{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS_FEEDS') }}</legend>

        <div class="form-control w-full max-w-md mb-4">
            <label class="label" for="param-feeds_enabled">
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS_ENTRY_FEED') }}</span>
            </label>
            <select name="params[feeds_enabled]" id="param-feeds_enabled" class="select select-bordered w-full">
                <option value="0" @selected(!$config->get('feeds_enabled', 1))>
                    {{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS_DISABLED') }}
                </option>
                <option value="1" @selected($config->get('feeds_enabled', 1) == 1)>
                    {{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS_ENABLED') }}
                </option>
            </select>
        </div>

        <div class="form-control w-full max-w-md mb-4">
            <label class="label" for="param-feeds_entries">
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS_FEED_ENTRY_LENGTH') }}</span>
            </label>
            <select name="params[feed_entries]" id="param-feeds_entries" class="select select-bordered w-full">
                <option value="full" @selected($config->get('feed_entries', 'partial') == 'full')>
                    {{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS_FULL') }}
                </option>
                <option value="partial" @selected($config->get('feed_entries', 'partial') == 'partial')>
                    {{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS_PARTIAL') }}
                </option>
            </select>
        </div>

        <p class="text-sm text-base-content/60">
            {{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS_FEED_HELP') }}
        </p>
    </fieldset>

    <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
    <input type="hidden" name="process" value="1" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="active" value="blog" />
    <input type="hidden" name="action" value="savesettings" />

    {!! Html::input('token') !!}

    <div class="flex gap-2 mt-4">
        <button class="btn btn-primary" type="submit">
            {{ Lang::txt('PLG_GROUPS_BLOG_SAVE') }}
        </button>
        <a class="btn btn-ghost" href="{{ Route::url($base) }}">
            {{ Lang::txt('JCANCEL') }}
        </a>
    </div>
</form>
