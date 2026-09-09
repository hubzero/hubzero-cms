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

$base = 'index.php?option=' . $option . '&cn=' . $group->get('cn') . '&active=blog';
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

<form action="{{ Route::url($base . '&action=delete&entry=' . $entry->get('id')) }}"
    method="post"
    id="hubForm">
    <div class="explaination">
        @if ($authorized)
            <p>
                <a class="btn btn-primary gap-2" href="{{ Route::url($base . '&action=new') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    {{ Lang::txt('PLG_GROUPS_BLOG_NEW_ENTRY') }}
                </a>
            </p>
        @endif
    </div>

    <fieldset>
        <legend>{{ Lang::txt('PLG_GROUPS_BLOG_DELETE_HEADER') }}</legend>

        <div class="alert alert-warning">
            <p>{{ Lang::txt('PLG_GROUPS_BLOG_DELETE_WARNING', e(stripslashes($entry->get('title')))) }}</p>
        </div>

        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-2">
                <input type="checkbox"
                    class="checkbox"
                    name="confirmdel"
                    id="confirmdel"
                    value="1" />
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_BLOG_DELETE_CONFIRM') }}</span>
            </label>
        </div>
    </fieldset>

    <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
    <input type="hidden" name="process" value="1" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="active" value="blog" />
    <input type="hidden" name="task" value="view" />
    <input type="hidden" name="action" value="delete" />
    <input type="hidden" name="entry" value="{{ $entry->get('id') }}" />

    {!! Html::input('token') !!}

    <div class="flex gap-2 mt-4">
        <button class="btn btn-error" type="submit">
            {{ Lang::txt('PLG_GROUPS_BLOG_DELETE') }}
        </button>
        <a class="btn btn-ghost" href="{{ Route::url($entry->link()) }}">
            {{ Lang::txt('JCANCEL') }}
        </a>
    </div>
</form>
