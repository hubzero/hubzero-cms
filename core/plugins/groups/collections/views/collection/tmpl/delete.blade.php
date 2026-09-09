{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$base = 'index.php?option=' . $option . '&cn=' . $group->get('cn') . '&active=' . $name;
@endphp

@if ($__view->getError())
    <div class="alert alert-error">
        <p>{{ $__view->getError() }}</p>
    </div>
@endif

<form action="{{ Route::url($base . '&scope=' . $collection->get('alias') . '/delete') }}"
    method="post"
    id="hubForm"
    class="full">

    <fieldset class="fieldset bg-base-100 border border-base-300 p-4 rounded-box">
        <legend class="fieldset-legend text-lg font-semibold">
            {{ Lang::txt('PLG_GROUPS_COLLECTIONS_DELETE_COLLECTION_HEADER') }}
        </legend>

        <div class="alert alert-warning mb-4">
            {!! Lang::txt('PLG_GROUPS_COLLECTIONS_DELETE_COLLECTION_WARNING', stripslashes($collection->get('title'))) !!}
        </div>

        <div class="form-control">
            <label class="label cursor-pointer justify-start gap-3" for="confirmdel">
                <input type="checkbox"
                    class="checkbox checkbox-error"
                    name="confirmdel"
                    id="confirmdel"
                    value="1" />
                <span class="label-text">
                    {{ Lang::txt('PLG_GROUPS_COLLECTIONS_DELETE_COLLECTION_CONFIRM') }}
                </span>
            </label>
        </div>
    </fieldset>

    <input type="hidden" name="cn" value="{{ e($group->get('cn')) }}" />
    <input type="hidden" name="process" value="1" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="active" value="{{ $name }}" />
    <input type="hidden" name="action" value="deletecollection" />
    <input type="hidden" name="board" value="{{ e($collection->get('id')) }}" />
    <input type="hidden" name="no_html" value="{{ $no_html }}" />

    {!! Html::input('token') !!}

    <div class="mt-4 flex gap-2">
        <button type="submit" class="btn btn-error">
            {{ Lang::txt('PLG_GROUPS_COLLECTIONS_DELETE') }}
        </button>

        @if (!$no_html)
            <a class="btn btn-ghost"
                href="{{ Route::url($base) }}">
                {{ Lang::txt('JCANCEL') }}
            </a>
        @endif
    </div>
</form>
