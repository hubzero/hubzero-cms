{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->js('fileuploader.blade.js');
$__view->js('fileupload.blade.js');

$item = $entry->item();

if (!$entry->exists()) {
    $entry->set('original', 1);
}

$type = 'file';
if ($type && !in_array($type, ['file', 'image', 'text', 'link'])) {
    $type = 'link';
}

$base = 'index.php?option=' . $option . '&cn=' . $group->get('cn') . '&active=' . $name;

$dir = $item->get('id');
if (!$dir) {
    $dir = 'tmp' . time();
}

$jbase = rtrim(Request::base(true), '/');
@endphp

@if ($__view->getError())
    <div class="alert alert-error">
        <p>{{ $__view->getError() }}</p>
    </div>
@endif

@php
$formAction = Route::url(
    $base . '&scope=post/save' . ($no_html ? '&no_html=' . $no_html : '')
);
@endphp
<form action="{{ $formAction }}"
    method="post"
    id="hubForm"
    class="full"
    enctype="multipart/form-data">

    <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
        @php
        if ($item->get('id')) {
            $legendTxt = $entry->get('original')
                ? Lang::txt('Edit post')
                : Lang::txt('Edit repost');
        } else {
            $legendTxt = Lang::txt('New post');
        }
        @endphp
        <legend class="fieldset-legend text-lg font-semibold">{{ $legendTxt }}</legend>

        @if ($entry->get('original'))
            <div class="mb-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        @php
                        $uploaderAction = $jbase
                            . '/index.php?option=com_collections&amp;no_html=1&amp;controller=media&amp;task=upload';
                        $uploaderList = '/index.php?option=com_collections&amp;no_html=1&amp;controller=media&amp;task=list&amp;dir=';
                        @endphp
                        <div id="ajax-uploader"
                            data-txt-instructions="{{ Lang::txt('Click or drop file') }}"
                            data-action="{{ $uploaderAction }}"
                            data-list="{{ $uploaderList }}">
                            <noscript>
                                <div class="form-group">
                                    <label for="upload" class="label">
                                        <span class="label-text">{{ Lang::txt('File:') }}</span>
                                    </label>
                                    <input type="file" name="upload" id="upload" class="file-input file-input-bordered w-full" />
                                </div>
                            </noscript>
                        </div>
                    </div>
                    <div>
                        @php
                        $linkAdderBase = rtrim(Request::base(true), '/');
                        $linkAdderAction = $jbase
                            . '/index.php?option=com_collections&amp;no_html=1&amp;controller=media&amp;task=create&amp;dir=';
                        $linkAdderList = '/index.php?option=com_collections&amp;no_html=1&amp;controller=media&amp;task=list&amp;dir=';
                        @endphp
                        <div id="link-adder"
                            data-base="{{ $linkAdderBase }}"
                            data-txt-delete="{{ Lang::txt('JACTION_DELETE') }}"
                            data-txt-instructions="{{ Lang::txt('Click to add link') }}"
                            data-action="{{ $linkAdderAction }}"
                            data-list="{{ $linkAdderList }}">
                            <noscript>
                                <div class="form-group">
                                    <label for="add-link" class="label">
                                        <span class="label-text">{{ Lang::txt('Add a link:') }}</span>
                                    </label>
                                    <input type="text"
                                        name="assets[-1][filename]"
                                        id="add-link"
                                        class="input input-bordered w-full"
                                        value="http://" />
                                    <input type="hidden" name="assets[-1][id]" value="0" />
                                    <input type="hidden" name="assets[-1][type]" value="link" />
                                </div>
                            </noscript>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div id="post-type-form">
            <div id="post-file">
                @if ($entry->get('original'))
                    <div id="ajax-uploader-list" class="mb-4">
                        @php
                        $assets = $item->assets();
                        @endphp
                        @if ($assets->total() > 0)
                            @php $i = 0; @endphp
                            @foreach ($assets as $asset)
                                <div class="flex items-center gap-2 mb-2 p-2 bg-base-200 rounded-box">
                                    <span class="cursor-move opacity-40">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16" /></svg>
                                    </span>
                                    <span class="flex-1">
                                        @if ($asset->get('type') == 'link')
                                            <input type="text"
                                                name="assets[{{ $i }}][filename]"
                                                class="input input-bordered input-sm w-full"
                                                value="{{ e(stripslashes($asset->get('filename'))) }}"
                                                placeholder="http://" />
                                        @else
                                            {{ e(stripslashes($asset->get('filename'))) }}
                                            <input type="hidden"
                                                name="assets[{{ $i }}][filename]"
                                                value="{{ e(stripslashes($asset->get('filename'))) }}" />
                                        @endif
                                    </span>
                                    <input type="hidden" name="assets[{{ $i }}][type]" value="{{ e(stripslashes($asset->get('type'))) }}" />
                                    <input type="hidden" name="assets[{{ $i }}][id]" value="{{ e($asset->get('id')) }}" />
                                    <a class="btn btn-error btn-xs btn-outline"
                                        data-id="{{ e($asset->get('id')) }}"
                                        href="{{ Route::url($base . '&scope=post/' . $entry->get('id') . '/edit&remove=' . $asset->get('id')) }}"
                                        title="{{ Lang::txt('Delete this asset') }}">
                                        {{ Lang::txt('delete') }}
                                    </a>
                                </div>
                                @php $i++; @endphp
                            @endforeach
                        @endif
                    </div>

                    <div class="form-group mb-4">
                        <label for="field-title" class="label">
                            <span class="label-text">{{ Lang::txt('Title') }}</span>
                        </label>
                        <input type="text"
                            name="fields[title]"
                            id="field-title"
                            class="input input-bordered w-full"
                            value="{{ e(stripslashes($item->get('title', ''))) }}" />
                    </div>
                    <input type="hidden" name="fields[type]" value="file" />
                @else
                    <div class="form-group mb-4">
                        <label for="field-title" class="label">
                            <span class="label-text">{{ Lang::txt('Title') }}</span>
                        </label>
                        <input type="text"
                            name="fieldstitle"
                            id="field-title"
                            class="input input-bordered w-full"
                            disabled="disabled"
                            value="{{ e(stripslashes($item->get('title'))) }}" />
                    </div>
                @endif

                <div class="form-group mb-4">
                    <label for="field_description" class="label">
                        <span class="label-text">{{ Lang::txt('Description') }}</span>
                    </label>
                    @if ($entry->get('original'))
                        @php
                        echo $__view->editor(
                            'fields[description]',
                            e(stripslashes($item->description('raw'))),
                            35,
                            5,
                            'field_description',
                            ['class' => 'form-control minimal no-footer']
                        );
                        @endphp
                    @else
                        @php
                        echo $__view->editor(
                            'post[description]',
                            e(stripslashes($entry->description('raw'))),
                            35,
                            5,
                            'field_description',
                            ['class' => 'form-control minimal no-footer']
                        );
                        @endphp
                    @endif
                </div>

                @if ($task == 'save' && !$item->get('description'))
                    <div class="alert alert-error mb-4">
                        {{ Lang::txt('PLG_GROUPS_' . strtoupper($name) . '_ERROR_PROVIDE_CONTENT') }}
                    </div>
                @endif
            </div>
        </div>

        @if ($entry->get('original'))
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
        @endif

        @if ($collections->total() > 0)
            <div class="form-group mb-4">
                <label for="post-collection_id" class="label">
                    <span class="label-text">
                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_SELECT_COLLECTION') }}
                        <span class="badge badge-error badge-sm">{{ Lang::txt('JREQUIRED') }}</span>
                    </span>
                </label>
                <select name="post[collection_id]" id="post-collection_id" class="select select-bordered w-full">
                    @foreach ($collections as $coll)
                        <option value="{{ e($coll->get('id')) }}"
                            @if ($collection->get('id') == $coll->get('id')) selected @endif>
                            {{ e(stripslashes($coll->get('title'))) }}
                        </option>
                    @endforeach
                </select>
                <div class="label">
                    <span class="label-text-alt">{{ Lang::txt('PLG_GROUPS_COLLECTIONS_SELECT_COLLECTION_HINT') }}</span>
                </div>
            </div>
        @else
            <div class="form-group mb-4">
                <label for="post-collection_title" class="label">
                    <span class="label-text">
                        {{ Lang::txt('PLG_GROUPS_COLLECTIONS_NEW_COLLECTION') }}
                        <span class="badge badge-error badge-sm">{{ Lang::txt('JREQUIRED') }}</span>
                    </span>
                </label>
                <input type="text"
                    name="collection_title"
                    id="post-collection_title"
                    class="input input-bordered w-full"
                    value="" />
                <div class="label">
                    <span class="label-text-alt">{{ Lang::txt('PLG_GROUPS_COLLECTIONS_NEW_COLLECTION_HINT') }}</span>
                </div>
            </div>
        @endif

        @if ($entry->get('original'))
                </div>
                <div>
                    <div class="form-group mb-4">
                        <label for="actags" class="label">
                            <span class="label-text">{{ Lang::txt('PLG_GROUPS_' . strtoupper($name) . '_FIELD_TAGS') }}</span>
                        </label>
                        @php
                        echo $__view->autocompleter('tags', 'tags', e($item->tags('string')), 'actags');
                        @endphp
                        <div class="label">
                            <span class="label-text-alt">{{ Lang::txt('PLG_GROUPS_' . strtoupper($name) . '_FIELD_TAGS_HINT') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <input type="hidden" name="tags" value="{{ e($item->tags('string')) }}" />
        @endif
    </fieldset>

    <input type="hidden" name="fields[id]" id="field-id" value="{{ e($item->get('id')) }}" />
    <input type="hidden" name="fields[created]" value="{{ e($item->get('created')) }}" />
    <input type="hidden" name="fields[created_by]" value="{{ e($item->get('created_by')) }}" />
    <input type="hidden" name="fields[dir]" id="field-dir" value="{{ e($dir) }}" />
    <input type="hidden" name="fields[access]" id="field-access" value="{{ e($item->get('access', 0)) }}" />

    <input type="hidden" name="post[id]" value="{{ e($entry->get('id')) }}" />
    <input type="hidden" name="post[item_id]" id="post-item_id" value="{{ e($entry->get('item_id')) }}" />

    <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="active" value="{{ $name }}" />
    <input type="hidden" name="no_html" value="{{ $no_html }}" />
    <input type="hidden" name="action" value="save" />

    {!! Html::input('token') !!}

    <div class="mt-4 flex gap-2">
        <button type="submit" class="btn btn-success">
            {{ Lang::txt('PLG_GROUPS_' . strtoupper($name) . '_SAVE') }}
        </button>

        @if ($item->get('id'))
            <a class="btn btn-ghost"
                href="{{ Route::url($base . ($item->get('id') ? '&scope=' . $collection->get('alias') : '')) }}">
                {{ Lang::txt('Cancel') }}
            </a>
        @endif
    </div>
</form>
