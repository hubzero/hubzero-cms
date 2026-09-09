@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Event;

$base = $member->link() . '&active=' . $name;

$legend = !$entry->exists()
    ? 'PLG_MEMBERS_COLLECTIONS_LEGEND_NEW_COLLECTION'
    : 'PLG_MEMBERS_COLLECTIONS_LEGEND_EDIT_COLLECTION';

$__view->css();
@endphp

@if ($__view->getError())
    <p class="error">{!! implode('<br />', $__view->getErrors()) !!}</p>
@endif

<form action="{{ Route::url($base . '&task=save') }}"
    method="post"
    id="hubForm"
    class="full"
    enctype="multipart/form-data">
    <fieldset>
        <legend>{{ Lang::txt($legend) }}</legend>

        <div class="form-group">
            <label for="field-access">
                {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_PRIVACY') }}
                @php
                    $access = $entry->get('access');
                @endphp
                <select name="fields[access]" id="field-access" class="form-control">
                    <option value="0"{{ $access == 0 ? ' selected="selected"' : '' }}>
                        {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_PRIVACY_PUBLIC') }}
                    </option>
                    <option value="1"{{ $access == 1 ? ' selected="selected"' : '' }}>
                        {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_PRIVACY_REGISTERED') }}
                    </option>
                    <option value="4"{{ $access == 4 ? ' selected="selected"' : '' }}>
                        {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_PRIVACY_PRIVATE') }}
                    </option>
                </select>
            </label>
        </div>

        <div class="form-group">
            <label for="field-title"{!! ($task == 'save' && !$entry->get('title')) ? ' class="fieldWithErrors"' : '' !!}>
                {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_TITLE') }}
                <span class="required">{{ Lang::txt('JREQUIRED') }}</span>
                <input type="text"
                    name="fields[title]"
                    id="field-title"
                    class="form-control"
                    size="35"
                    value="{{ e(stripslashes($entry->get('title', ''))) }}" />
            </label>
        </div>

        <div class="form-group">
            <label for="field-description">
                {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_DESCRIPTION') }}
                {!! $__view->editor(
                    'fields[description]',
                    e(stripslashes($entry->description('raw'))),
                    35,
                    5,
                    'field-description',
                    ['class' => 'form-control minimal no-footer']
                ) !!}
            </label>
        </div>

        <div class="form-group">
            <label for="actags">
                {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_TAGS') }}
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
                        class="form-control"
                        value="{{ e($tags) }}" />
                @endif
                <span class="hint">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_TAGS_HINT') }}</span>
            </label>
        </div>

        <div class="grid">
            <div class="col span6">
                <div class="form-group">
                    <label for="field-layout">
                        {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_LAYOUT') }}
                        @php $layout = $entry->get('layout'); @endphp
                        <select name="fields[layout]" id="field-layout" class="form-control">
                            <option value="grid"{{ $layout == 'grid' ? ' selected="selected"' : '' }}>
                                {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_LAYOUT_GRID') }}
                            </option>
                            <option value="list"{{ $layout == 'list' ? ' selected="selected"' : '' }}>
                                {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_LAYOUT_LIST') }}
                            </option>
                        </select>
                    </label>
                </div>
            </div>
            <div class="col span6 omega">
                <div class="form-group">
                    <label for="field-sort">
                        {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_SORT') }}
                        @php $sort = $entry->get('sort'); @endphp
                        <select name="fields[sort]" id="field-sort" class="form-control">
                            <option value="created"{{ $sort == 'created' ? ' selected="selected"' : '' }}>
                                {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_SORT_CREATED') }}
                            </option>
                            <option value="ordering"{{ $sort == 'ordering' ? ' selected="selected"' : '' }}>
                                {{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_SORT_ORDERING') }}
                            </option>
                        </select>
                    </label>
                </div>
            </div>
        </div>
        <p class="hint">{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_FIELD_SORT_DETAILS') }}</p>
    </fieldset>

    <input type="hidden" name="fields[id]" value="{{ e($entry->get('id')) }}" />
    <input type="hidden" name="fields[object_id]" value="{{ e($member->get('id')) }}" />
    <input type="hidden" name="fields[object_type]" value="member" />
    <input type="hidden" name="fields[created]" value="{{ e($entry->get('created')) }}" />
    <input type="hidden" name="fields[created_by]" value="{{ e($entry->get('created_by')) }}" />
    <input type="hidden" name="fields[state]" value="{{ e($entry->get('state')) }}" />

    <input type="hidden" name="id" value="{{ $member->get('id') }}" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="active" value="{{ $name }}" />
    <input type="hidden" name="action" value="savecollection" />

    {!! Html::input('token') !!}

    <p class="submit">
        <input class="btn btn-success" type="submit" value="{{ Lang::txt('PLG_MEMBERS_COLLECTIONS_SAVE') }}" />
        <a class="btn btn-secondary" href="{{ Route::url($base . '&task=all') }}">
            {{ Lang::txt('JCANCEL') }}
        </a>
    </p>
</form>
