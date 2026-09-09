{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
use Hubzero\Facades\Config;
use Hubzero\Facades\Date;

$__view->css();
$__view->css('flatpickr.min.css', 'system');
$__view->js('flatpickr.min.js', 'system');
$__view->js();

if ($entry->get('publish_down') && $entry->get('publish_down') == '0000-00-00 00:00:00') {
    $entry->set('publish_down', '');
}

$base = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=blog';

$tz = new DateTimeZone(Config::get('offset'));
$tzOffset = timezone_offset_get($tz, Date::of('now')) / 60;
$publishUpVal = e(
    Date::of($entry->get('publish_up'))->toLocal('Y-m-d H:i:s')
);
$down = '';
if ($entry->get('publish_down') != '') {
    $down = e(Date::of($entry->get('publish_down'))->toLocal('Y-m-d H:i:s'));
}
@endphp

<ul id="page_options">
    <li>
        <a class="btn btn-ghost gap-2" href="{{ Route::url($base) }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" /></svg>
            {{ Lang::txt('PLG_GROUPS_BLOG_ARCHIVE') }}
        </a>
    </li>
</ul>

@foreach ($__view->getErrors() as $error)
    <div class="alert alert-error">{{ $error }}</div>
@endforeach

<form action="{{ Route::url($base) }}" method="post" id="hubForm" class="full">
    <fieldset>
        <legend>{{ Lang::txt('PLG_GROUPS_BLOG_EDIT_DETAILS') }}</legend>

        <div class="form-control w-full mb-4">
            <label class="label" for="field-title">
                <span class="label-text">
                    {{ Lang::txt('PLG_GROUPS_BLOG_TITLE') }}
                    <span class="text-error">{{ Lang::txt('JREQUIRED') }}</span>
                </span>
            </label>
            <input type="text"
                class="input input-bordered w-full @if($task == 'save' && !$entry->get('title')) input-error @endif"
                name="entry[title]"
                id="field-title"
                value="{{ e(stripslashes($entry->get('title', ''))) }}" />
            @if ($task == 'save' && !$entry->get('title'))
                <label class="label">
                    <span class="label-text-alt text-error">{{ Lang::txt('PLG_GROUPS_BLOG_ERROR_PROVIDE_TITLE') }}</span>
                </label>
            @endif
        </div>

        <div class="form-control w-full mb-4">
            <label class="label" for="entry_content">
                <span class="label-text">
                    {{ Lang::txt('PLG_GROUPS_BLOG_FIELD_CONTENT') }}
                    <span class="text-error">{{ Lang::txt('JREQUIRED') }}</span>
                </span>
            </label>
            {!! $__view->editor(
                'entry[content]',
                e($entry->get('content')),
                50,
                30,
                'entry_content',
                ['class' => 'form-control']
            ) !!}
            @if ($task == 'save' && !$entry->get('content'))
                <label class="label">
                    <span class="label-text-alt text-error">{{ Lang::txt('PLG_GROUPS_BLOG_ERROR_PROVIDE_CONTENT') }}</span>
                </label>
            @endif
        </div>

        <fieldset class="mb-4">
            <legend>{{ Lang::txt('PLG_GROUPS_BLOG_UPLOADED_FILES') }}</legend>
            <div class="field-wrap">
                @php
                $filerSrc = 'index.php?option=com_blog&controller=media&id='
                    . $group->get('gidNumber')
                    . '&scope=group&tmpl=component';
                @endphp
                <iframe width="100%"
                    height="260"
                    name="filer"
                    id="filer"
                    src="{{ $filerSrc }}">
                </iframe>
            </div>
        </fieldset>

        <div class="form-control w-full mb-4">
            <label class="label" for="actags">
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_BLOG_FIELD_TAGS') }}</span>
            </label>
            @php
            $tagsValue = e($entry->tags('string'));
            echo $__view->autocompleter('tags', 'tags', $tagsValue, 'actags');
            @endphp
            <label class="label">
                <span class="label-text-alt">{{ Lang::txt('PLG_GROUPS_BLOG_FIELD_TAGS_HINT') }}</span>
            </label>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="form-control">
                <label class="label cursor-pointer justify-start gap-2">
                    <input type="checkbox"
                        class="checkbox"
                        name="entry[allow_comments]"
                        id="field-allow_comments"
                        value="1"
                        @checked($entry->get('allow_comments') == 1) />
                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_BLOG_FIELD_ALLOW_COMMENTS') }}</span>
                </label>
            </div>

            <div class="form-control w-full">
                <label class="label" for="field-access">
                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_BLOG_FIELD_PRIVACY') }}</span>
                </label>
                @php
                $access = $entry->get('access');
                @endphp
                <select class="select select-bordered w-full" name="entry[access]" id="field-access">
                    <option value="1" @selected($access == 1)>
                        {{ Lang::txt('PLG_GROUPS_BLOG_FIELD_STATE_PUBLIC') }}
                    </option>
                    <option value="2" @selected($access == 2)>
                        {{ Lang::txt('PLG_GROUPS_BLOG_FIELD_STATE_REGISTERED') }}
                    </option>
                    <option value="5" @selected($access > 2)>
                        {{ Lang::txt('PLG_GROUPS_BLOG_FIELD_STATE_PRIVATE') }}
                    </option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div class="form-control w-full">
                <label class="label" for="field-publish_up">
                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_BLOG_PUBLISH_UP') }}</span>
                </label>
                <input type="text"
                    class="input input-bordered w-full"
                    name="entry[publish_up]"
                    id="field-publish_up"
                    data-flatpickr="datetime"
                    data-timezone="{{ $tzOffset }}"
                    value="{{ $publishUpVal }}" />
                <label class="label">
                    <span class="label-text-alt">{{ Lang::txt('PLG_GROUPS_BLOG_FIELD_PUBLISH_HINT') }}</span>
                </label>
            </div>

            <div class="form-control w-full">
                <label class="label" for="field-publish_down">
                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_BLOG_PUBLISH_DOWN') }}</span>
                </label>
                <input type="text"
                    class="input input-bordered w-full"
                    name="entry[publish_down]"
                    id="field-publish_down"
                    data-flatpickr="datetime"
                    data-timezone="{{ $tzOffset }}"
                    value="{{ $down }}" />
                <label class="label">
                    <span class="label-text-alt">{{ Lang::txt('PLG_GROUPS_BLOG_FIELD_PUBLISH_HINT') }}</span>
                </label>
            </div>
        </div>
    </fieldset>

    <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
    <input type="hidden" name="entry[id]" value="{{ e($entry->get('id')) }}" />
    <input type="hidden" name="entry[created]" value="{{ e($entry->get('created')) }}" />
    <input type="hidden" name="entry[created_by]" value="{{ e($entry->get('created_by')) }}" />
    <input type="hidden" name="entry[scope]" value="group" />
    <input type="hidden" name="entry[scope_id]" value="{{ e($group->get('gidNumber')) }}" />
    <input type="hidden" name="entry[state]" value="{{ $entry->get('state', 1) }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="active" value="blog" />
    <input type="hidden" name="action" value="save" />

    {!! Html::input('token') !!}

    <div class="flex gap-2 mt-4">
        <button class="btn btn-primary" type="submit">
            {{ Lang::txt('PLG_GROUPS_BLOG_SAVE') }}
        </button>
        @if ($entry->get('id'))
            <a class="btn btn-ghost" href="{{ Route::url($entry->link()) }}">
                {{ Lang::txt('JCANCEL') }}
            </a>
        @endif
    </div>
</form>
