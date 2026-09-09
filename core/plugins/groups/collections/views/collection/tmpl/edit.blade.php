{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$base = 'index.php?option=' . $option . '&cn=' . $group->get('cn') . '&active=' . $name;

$legend = !$entry->exists()
    ? 'PLG_GROUPS_COLLECTIONS_NEW_COLLECTION'
    : 'PLG_GROUPS_COLLECTIONS_EDIT_COLLECTION';

$default = $params->get('access-plugin');
$accessValue = $entry->get('access', $default);
@endphp

@if ($__view->getError())
    <div class="alert alert-error">
        <p>{{ $__view->getError() }}</p>
    </div>
@endif

<form action="{{ Route::url($base . '&scope=save') }}"
    method="post"
    id="hubForm"
    class="full"
    enctype="multipart/form-data">

    <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
        <legend class="fieldset-legend text-lg font-semibold">
            {{ Lang::txt($legend) }}
        </legend>

        <div class="form-group mb-4">
            <label for="field-access" class="label">
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_PRIVACY') }}</span>
            </label>
            <select name="fields[access]" id="field-access" class="select select-bordered w-full">
                <option value="0" @if ($accessValue == 0) selected @endif>
                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_PRIVACY_PUBLIC') }}
                </option>
                <option value="1" @if ($accessValue == 1) selected @endif>
                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_PRIVACY_REGISTERED') }}
                </option>
                <option value="4" @if ($accessValue == 4) selected @endif>
                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_PRIVACY_PRIVATE') }}
                </option>
            </select>
        </div>

        <div class="form-group mb-4">
            <label for="field-title" class="label">
                <span class="label-text">
                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_TITLE') }}
                    <span class="badge badge-error badge-sm">{{ Lang::txt('JREQUIRED') }}</span>
                </span>
            </label>
            <input type="text"
                name="fields[title]"
                id="field-title"
                class="input input-bordered w-full @if ($task == 'save' && !$entry->get('title')) input-error @endif"
                value="{{ e(stripslashes($entry->get('title', ''))) }}" />
        </div>

        <div class="form-group mb-4">
            <label for="field-description" class="label">
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_DESCRIPTION') }}</span>
            </label>
            @php
            echo $__view->editor(
                'fields[description]',
                e(stripslashes($entry->description('raw'))),
                35,
                5,
                'field-description',
                ['class' => 'form-control minimal no-footer']
            );
            @endphp
        </div>

        <div class="form-group mb-4">
            <label for="actags" class="label">
                <span class="label-text">{{ Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_TAGS') }}</span>
            </label>
            @php
            $tags = ($entry->get('id') ? $entry->item()->tags('string') : '');
            $tf = Event::trigger('hubzero.onGetMultiEntry', [['tags', 'tags', 'actags', '', $tags]]);
            $tf = implode('', $tf);
            @endphp
            @if ($tf)
                {!! $tf !!}
            @else
                <input type="text"
                    name="tags"
                    id="actags"
                    class="input input-bordered w-full"
                    value="{{ e($tags) }}" />
            @endif
            <div class="label">
                <span class="label-text-alt">{{ Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_TAGS_HINT') }}</span>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="form-group">
                <label for="field-layout" class="label">
                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_LAYOUT') }}</span>
                </label>
                <select name="fields[layout]" id="field-layout" class="select select-bordered w-full">
                    <option value="grid" @if ($entry->get('layout') == 'grid') selected @endif>
                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_LAYOUT_GRID') }}
                    </option>
                    <option value="list" @if ($entry->get('layout') == 'list') selected @endif>
                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_LAYOUT_LIST') }}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="field-sort" class="label">
                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_SORT') }}</span>
                </label>
                <select name="fields[sort]" id="field-sort" class="select select-bordered w-full">
                    <option value="created" @if ($entry->get('sort') == 'created') selected @endif>
                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_SORT_CREATED') }}
                    </option>
                    <option value="ordering" @if ($entry->get('sort') == 'ordering') selected @endif>
                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_SORT_ORDERING') }}
                    </option>
                </select>
            </div>
        </div>
        <div class="label">
            <span class="label-text-alt">{{ Lang::txt('PLG_GROUPS_COLLECTIONS_FIELD_SORT_DETAILS') }}</span>
        </div>
    </fieldset>

    <input type="hidden" name="fields[id]" value="{{ e($entry->get('id')) }}" />
    <input type="hidden" name="fields[object_id]" value="{{ e($group->get('gidNumber')) }}" />
    <input type="hidden" name="fields[object_type]" value="group" />
    <input type="hidden" name="fields[created]" value="{{ e($entry->get('created')) }}" />
    <input type="hidden" name="fields[created_by]" value="{{ e($entry->get('created_by')) }}" />
    <input type="hidden" name="fields[state]" value="{{ e($entry->get('state')) }}" />

    <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="active" value="{{ $name }}" />

    {!! Html::input('token') !!}
    <input type="hidden" name="action" value="savecollection" />

    <div class="mt-4">
        <button type="submit" class="btn btn-success">
            {{ Lang::txt('PLG_GROUPS_COLLECTIONS_SAVE') }}
        </button>
    </div>
</form>
