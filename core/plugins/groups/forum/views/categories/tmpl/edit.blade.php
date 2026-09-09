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

$base = 'index.php?option=' . $option . '&cn=' . $group->get('cn') . '&active=forum';

if ($category->get('section_id') == 0) {
    $category->set('section_id', Request::getInt('section_id'));
}
@endphp

<ul id="page_options">
    <li>
        <a class="btn btn-neutral gap-2" href="{{ Route::url($base) }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
            {{ Lang::txt('PLG_GROUPS_FORUM_ALL_CATEGORIES') }}
        </a>
    </li>
</ul>

<section class="main section">
    <form action="{{ Route::url($base) }}" method="post" id="hubForm" class="full">
        <fieldset>
            <legend class="text-lg font-bold">
                @if ($category->get('id'))
                    {{ Lang::txt('PLG_GROUPS_FORUM_EDIT_CATEGORY') }}
                @else
                    {{ Lang::txt('PLG_GROUPS_FORUM_NEW_CATEGORY') }}
                @endif
            </legend>

            <div class="form-group">
                <label for="field-section_id" class="label">
                    <span class="label-text">
                        {{ Lang::txt('PLG_GROUPS_FORUM_FIELD_SECTION') }}
                        <span class="badge badge-error badge-sm">{{ Lang::txt('PLG_GROUPS_FORUM_REQUIRED') }}</span>
                    </span>
                </label>
                <select name="fields[section_id]" id="field-section_id" class="select select-bordered w-full">
                    <option value="0">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_SECTION_SELECT') }}</option>
                    @foreach ($forum->sections(['state' => 1])->rows() as $section)
                        @php
                        $sectionId = $section->get('id');
                        $sectionTitle = e(stripslashes($section->get('title')));
                        $isSelected = ($category->get('section_id') == $sectionId);
                        @endphp
                        <option value="{{ $sectionId }}" @if ($isSelected) selected="selected" @endif>{{ $sectionTitle }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="field-title" class="label">
                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_TITLE') }}</span>
                </label>
                <input type="text"
                    name="fields[title]"
                    id="field-title"
                    class="input input-bordered w-full"
                    value="{{ e(stripslashes($category->get('title', ''))) }}" />
            </div>

            <div class="form-group">
                <label for="field-description" class="label">
                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_FIELD_DESCRIPTION') }}</span>
                </label>
                <textarea name="fields[description]"
                    id="field-description"
                    class="textarea textarea-bordered w-full"
                    cols="35"
                    rows="5">{{ e(stripslashes($category->get('description', ''))) }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label for="field-closed" class="label cursor-pointer justify-start gap-2">
                        <input class="checkbox"
                            type="checkbox"
                            name="fields[closed]"
                            id="field-closed"
                            value="3"
                            @if ($category->get('closed')) checked="checked" @endif />
                        <span class="label-text">
                            {{ Lang::txt('PLG_GROUPS_FORUM_FIELD_LOCKED') }} &mdash;
                            {{ Lang::txt('PLG_GROUPS_FORUM_FIELD_CLOSED') }}
                        </span>
                    </label>
                </div>
                <div class="form-group">
                    <label for="field-access" class="label">
                        <span class="label-text">{{ Lang::txt('PLG_GROUPS_FORUM_ACCESS_DESCRIPTION') }}:</span>
                    </label>
                    @php
                    $access = $category->get('access');
                    @endphp
                    <select name="fields[access]" id="field-access" class="select select-bordered w-full">
                        <option value="1" @if ($access == 1) selected="selected" @endif>
                            {{ Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_PUBLIC') }}
                        </option>
                        <option value="2" @if ($access == 2) selected="selected" @endif>
                            {{ Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_REGISTERED') }}
                        </option>
                        <option value="5" @if ($access == 5) selected="selected" @endif>
                            {{ Lang::txt('PLG_GROUPS_FORUM_FIELD_READ_ACCESS_OPTION_PRIVATE') }}
                        </option>
                    </select>
                </div>
            </div>
        </fieldset>

        <div class="flex gap-2 mt-4">
            <input class="btn btn-success" type="submit" value="{{ Lang::txt('PLG_GROUPS_FORUM_SAVE') }}" />
            <a class="btn btn-ghost" href="{{ Route::url($base) }}">
                {{ Lang::txt('JCANCEL') }}
            </a>
        </div>

        <input type="hidden" name="fields[alias]" value="{{ e($category->get('alias')) }}" />
        <input type="hidden" name="fields[id]" value="{{ e($category->get('id')) }}" />
        <input type="hidden" name="fields[state]" value="1" />
        <input type="hidden" name="fields[scope]" value="{{ e($forum->get('scope')) }}" />
        <input type="hidden" name="fields[scope_id]" value="{{ e($forum->get('scope_id')) }}" />

        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
        <input type="hidden" name="active" value="forum" />
        <input type="hidden" name="action" value="savecategory" />

        {!! Html::input('token') !!}
    </form>
</section>
