{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$cancelUrl = Route::url('index.php?option=' . $option . '&task=browse');
$formUrl = Route::url('index.php?option=' . $option);
$tagsUrl = Route::url('index.php?option=' . $option);
@endphp

<x-page-container :title="$title">
    @slot('actions')
        <a class="btn btn-sm" href="{{ $tagsUrl }}">
            {{ Lang::txt('COM_TAGS_MORE_TAGS') }}
        </a>
    @endslot

    @if ($__view->getError())
        <div class="alert alert-error mb-4" role="alert">
            {!! implode("\n", $__view->getErrors()) !!}
        </div>
    @endif

    <form action="{{ $formUrl }}" method="post" id="hubForm">
        <x-form-section :heading="Lang::txt('COM_TAGS_DETAILS')">
            <p class="text-sm text-base-content/70 mb-4">
                {!! Lang::txt('COM_TAGS_NORMALIZED_TAG_EXPLANATION') !!}
            </p>

            <x-form-field
                name="fields[raw_tag]"
                :label="Lang::txt('COM_TAGS_FIELD_TAG')"
                required>
                <input type="text" name="fields[raw_tag]" id="fields[raw_tag]"
                    class="input input-bordered w-full"
                    value="{{ e(stripslashes($tag->get('raw_tag'))) }}"
                    data-error="{{ Lang::txt('COM_TAGS_FIELD_TAG_BLANK') }}"
                    required />
            </x-form-field>

            <x-form-field
                name="fields[admin]"
                type="checkbox"
                :label="Lang::txt('COM_TAGS_FIELD_ADMINISTRATION')"
                :hint="Lang::txt('COM_TAGS_FIELD_ADMINISTRATION_EXPLANATION')">
                <input type="checkbox" name="fields[admin]" id="fields[admin]"
                    class="checkbox" value="1"
                    @checked($tag->get('admin'))
                />
            </x-form-field>

            <x-form-field
                name="field-description"
                :label="Lang::txt('COM_TAGS_FIELD_DESCRIPTION')">
                <textarea name="fields[description]" id="field-description"
                    class="textarea textarea-bordered h-32 w-full"
                    rows="7">{{ e(stripslashes($tag->get('description'))) }}</textarea>
            </x-form-field>

            <x-form-field
                name="field-substitutions"
                :label="Lang::txt('COM_TAGS_FIELD_ALIAS')"
                :hint="Lang::txt('COM_TAGS_FIELD_ALIAS_HINT')">
                <textarea name="fields[substitutions]" id="field-substitutions"
                    class="textarea textarea-bordered h-24 w-full"
                    rows="5">{{ e(stripslashes($tag->substitutes)) }}</textarea>
            </x-form-field>
        </x-form-section>

        <input type="hidden" name="fields[tag]"
            value="{{ $tag->get('tag') }}" />
        <input type="hidden" name="fields[id]"
            value="{{ $tag->get('id') }}" />
        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="task" value="save" />

        {!! Html::input('token') !!}

        <input type="hidden" name="limit"
            value="{{ e($filters['limit']) }}" />
        <input type="hidden" name="limitstart"
            value="{{ e($filters['start']) }}" />
        <input type="hidden" name="sort"
            value="{{ e($filters['sort']) }}" />
        <input type="hidden" name="sortdir"
            value="{{ e($filters['sort_Dir']) }}" />
        <input type="hidden" name="search"
            value="{{ e($filters['search']) }}" />

        <div class="flex gap-3 mt-6">
            <button type="submit" class="btn btn-primary">
                {{ Lang::txt('COM_TAGS_SUBMIT') }}
            </button>
            <a class="btn btn-ghost" href="{{ $cancelUrl }}">
                {{ Lang::txt('JCANCEL') }}
            </a>
        </div>
    </form>
</x-page-container>
