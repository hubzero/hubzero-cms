@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Event;

$item = $entry->item();

if (!$entry->exists()) {
    $entry->set('original', 1);
}

$tf = Event::trigger('hubzero.onGetMultiEntry', [['tags', 'tags', 'actags', '', $item->tags('string')]]);

$type = 'file';
if ($type && !in_array($type, ['file', 'image', 'text', 'link'])) {
    $type = 'link';
}

$base = $member->link() . '&active=' . $name;

$dir = $item->get('id');
if (!$dir) {
    $dir = 'tmp' . time();
}

$jbase = rtrim(Request::base(true), '/');

$__view->css()
    ->js('jquery.fileuploader.js', 'system')
    ->js('fileupload.js')
    ->js();
@endphp

@if ($__view->getError())
    <p class="error">{{ $__view->getError() }}</p>
@endif

<form action="{{ Route::url($base . '&task=post/save' . ($no_html ? '&no_html=' . $no_html : '')) }}"
    method="post"
    id="hubForm"
    class="full"
    enctype="multipart/form-data">
    <fieldset>
        @php
            if ($item->get('id')) {
                $legendText = $entry->get('original')
                    ? Lang::txt('PLG_MEMBERS_COLLECTIONS_EDIT_POST')
                    : Lang::txt('PLG_MEMBERS_COLLECTIONS_EDIT_REPOST');
            } else {
                $legendText = Lang::txt('PLG_MEMBERS_COLLECTIONS_NEW_POST');
            }
        @endphp
        <legend>{{ $legendText }}</legend>

        @if ($entry->get('original'))
            <div class="field-wrap">
                <div class="asset-uploader">
                    <div class="grid">
                        <div class="col span-half">
                            <div id="ajax-uploader"
                                data-txt-instructions="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_CLICK_OR_DROP_FILE') }}"
                                data-action="{{ $jbase }}/index.php?option=com_collections&amp;no_html=1&amp;controller=media&amp;task=upload"
                                data-list="{{ $jbase }}/index.php?option=com_collections&amp;no_html=1&amp;controller=media&amp;task=list&amp;dir=">
                                <noscript>
                                    <div class="form-group">
                                        <label for="upload">
                                            {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_ADD_FILE') }}
                                            <input type="file" name="upload" id="upload" class="form-control-file" />
                                        </label>
                                    </div>
                                </noscript>
                            </div>
                        </div>
                        <div class="col span-half omega">
                            <div id="link-adder"
                                data-base="{{ rtrim(Request::base(true), '/') }}"
                                data-txt-delete="{{ Lang::txt('JACTION_DELETE') }}"
                                data-txt-instructions="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_CLICK_TO_ADD_LINK') }}"
                                data-action="{{ $jbase }}/index.php?option=com_collections&amp;no_html=1&amp;controller=media&amp;task=create&amp;dir="
                                data-list="{{ $jbase }}/index.php?option=com_collections&amp;no_html=1&amp;controller=media&amp;task=list&amp;dir=">
                                <noscript>
                                    <div class="form-group">
                                        <label for="add-link">
                                            {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_ADD_LINK') }}
                                            <input type="text" name="assets[-1][filename]" id="add-link" class="form-control" value="http://" />
                                            <input type="hidden" name="assets[-1][id]" value="0" />
                                            <input type="hidden" name="assets[-1][type]" value="link" />
                                        </label>
                                    </div>
                                </noscript>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div id="post-type-form">
            <div id="post-file" class="fieldset">
                @if ($entry->get('original'))
                    <div class="field-wrap" id="ajax-uploader-list">
                        @php
                            $assets = $item->assets();
                        @endphp
                        @if ($assets->total() > 0)
                            @foreach ($assets as $i => $asset)
                                <p class="item-asset">
                                    <span class="asset-handle"></span>
                                    <span class="asset-file">
                                        @if ($asset->get('type') == 'link')
                                            <input type="text"
                                                name="assets[{{ $i }}][filename]"
                                                size="35"
                                                value="{{ e(stripslashes($asset->get('filename'))) }}"
                                                placeholder="http://" />
                                        @else
                                            {{ e(stripslashes($asset->get('filename'))) }}
                                            <input type="hidden"
                                                name="assets[{{ $i }}][filename]"
                                                value="{{ e(stripslashes($asset->get('filename'))) }}" />
                                        @endif
                                    </span>
                                    <span class="asset-description">
                                        <input type="hidden" name="assets[{{ $i }}][type]" value="{{ e(stripslashes($asset->get('type'))) }}" />
                                        <input type="hidden" name="assets[{{ $i }}][id]" value="{{ e($asset->get('id')) }}" />
                                        <a class="delete"
                                            data-id="{{ e($asset->get('id')) }}"
                                            href="{{ Route::url($base . '&task=post/' . $entry->get('id') . '/edit&remove=' . $asset->get('id')) }}"
                                            title="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE') }}">
                                            {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_DELETE') }}
                                        </a>
                                    </span>
                                </p>
                            @endforeach
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="field-title">
                            {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_TITLE') }}
                            <input type="text"
                                name="fields[title]"
                                id="field-title"
                                class="form-control"
                                value="{{ e(stripslashes($item->get('title', ''))) }}" />
                        </label>
                    </div>
                    <input type="hidden" name="fields[type]" value="file" />
                @else
                    <div class="form-group">
                        <label for="field-title">
                            {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_TITLE') }}
                            <input type="text"
                                name="fieldstitle"
                                id="field-title"
                                class="form-control disabled"
                                disabled="disabled"
                                value="{{ e(stripslashes($item->get('title', ''))) }}" />
                        </label>
                    </div>
                @endif

                <div class="form-group">
                    <label for="field_description">
                        {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_DESCRIPTION') }}
                        @if ($entry->get('original'))
                            {!! $__view->editor(
                                'fields[description]',
                                e(stripslashes($item->description('raw'))),
                                35, 5, 'field_description',
                                ['class' => 'form-control minimal no-footer']
                            ) !!}
                        @else
                            {!! $__view->editor(
                                'post[description]',
                                e(stripslashes($entry->description('raw'))),
                                35, 5, 'field_description',
                                ['class' => 'form-control minimal no-footer']
                            ) !!}
                        @endif
                    </label>
                </div>

                @if ($task == 'save' && !$item->get('description'))
                    <p class="error">{{ Lang::txt('PLG_MEMBERS_' . strtoupper($name) . '_ERROR_PROVIDE_CONTENT') }}</p>
                @endif
            </div>
        </div>

        @if ($entry->get('original'))
            <div class="grid">
                <div class="col span6">
        @endif

        @if ($collections->total() > 0)
            <div class="form-group">
                <label for="post-collection_id">
                    {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_SELECT_COLLECTION') }}
                    <select name="post[collection_id]" id="post-collection_id" class="form-control">
                        @foreach ($collections as $collection)
                            <option value="{{ e($collection->get('id')) }}"{{ $collection->get('id') == $collection->get('id') ? ' selected="selected"' : '' }}>
                                {{ e(stripslashes($collection->get('title'))) }}
                            </option>
                        @endforeach
                    </select>
                    <span class="hint">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_SELECT_COLLECTION_HINT') }}</span>
                </label>
            </div>
        @else
            <div class="form-group">
                <label for="post-collection_title">
                    {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_CREATE_COLLECTION') }}
                    <input type="text" name="collection_title" id="post-collection_title" class="form-control" value="" />
                    <span class="hint">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_CREATE_COLLECTION_HINT') }}</span>
                </label>
            </div>
        @endif

        @if ($entry->get('original'))
                </div>
                <div class="col span6 omega">
                    <div class="form-group">
                        <label for="actags">
                            {{ Lang::txt('PLG_MEMBERS_' . strtoupper($name) . '_FIELD_TAGS') }}
                            @if (count($tf) > 0)
                                {!! $tf[0] !!}
                            @else
                                <input type="text"
                                    name="tags"
                                    id="actags"
                                    class="form-control"
                                    value="{{ e($item->tags('string')) }}" />
                            @endif
                            <span class="hint">{{ Lang::txt('PLG_MEMBERS_' . strtoupper($name) . '_FIELD_TAGS_HINT') }}</span>
                        </label>
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

    <input type="hidden" name="id" value="{{ $member->get('id') }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="active" value="{{ $name }}" />
    <input type="hidden" name="no_html" value="{{ $no_html }}" />
    <input type="hidden" name="action" value="save" />

    {!! Html::input('token') !!}

    <p class="submit">
        <input class="btn btn-success" type="submit"
            value="{{ Lang::txt('PLG_MEMBERS_' . strtoupper($name) . '_SAVE') }}" />
        @if ($item->get('id'))
            <a class="btn btn-secondary"
                href="{{ Route::url($base . ($item->get('id') ? '&task=' . $collection->get('alias') : '')) }}">
                {{ Lang::txt('JCANCEL') }}
            </a>
        @endif
    </p>
</form>
