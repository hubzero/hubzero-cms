{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$sitename = \Hubzero\Facades\Config::get('sitename');
$mainUrl = \Hubzero\Facades\Route::url('index.php?option=' . $option);
$formAction = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller . '&task=story'
);
$uploadAction = \Hubzero\Facades\Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=uploadimage&no_html=1'
);
@endphp

<x-page-container :title="$title">
    @slot('actions')
        <a class="btn btn-ghost btn-sm" href="{{ $mainUrl }}">
            {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_MAIN') }}
        </a>
    @endslot

    @if ($__view->getError())
        <div class="alert alert-error mb-4" role="alert">
            {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_ERROR_MISSING_FIELDS') }}
        </div>
    @endif

    <form action="{{ $formAction }}" method="post" id="hubForm"
        enctype="multipart/form-data">

        <x-form-section :heading="\Hubzero\Facades\Lang::txt('COM_FEEDBACK_STORY_YOUR_STORY')">
            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="controller" value="{{ $controller }}" />
            <input type="hidden" name="task" value="sendstory" />
            {!! \Hubzero\Facades\Html::input('token') !!}

            <x-form-field name="field-fullname"
                :label="\Hubzero\Facades\Lang::txt('COM_FEEDBACK_NAME')"
                :required="true">
                <input type="text" id="field-fullname"
                    name="fields[fullname]"
                    class="input input-bordered w-full"
                    value="{{ e($row->fullname) }}"
                    required />
            </x-form-field>

            <x-form-field name="field-org"
                :label="\Hubzero\Facades\Lang::txt('COM_FEEDBACK_ORGANIZATION')"
                :required="true">
                <input type="text" id="field-org"
                    name="fields[org]"
                    class="input input-bordered w-full"
                    value="{{ e($row->org) }}"
                    required />
            </x-form-field>

            <x-form-field name="field-files"
                :label="\Hubzero\Facades\Lang::txt('COM_FEEDBACK_PICTURES')">
                <div id="ajax-uploader"
                    data-instructions="{{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_CLICK_OR_DROP_FILE') }}"
                    data-action="{{ $uploadAction }}">
                    <noscript>
                        <input type="file" name="files[]" id="field-files"
                            multiple="multiple" class="file-input file-input-bordered w-full" />
                    </noscript>
                </div>
            </x-form-field>

            @php
            $quoteError = ($__view->getError() && $row->quote == '');
            @endphp
            <x-form-field name="field-quote"
                :label="\Hubzero\Facades\Lang::txt('COM_FEEDBACK_STORY_DESCRIPTION')"
                :required="true"
                :error="$quoteError ? \Hubzero\Facades\Lang::txt('COM_FEEDBACK_STORY_MISSING_DESCRIPTION') : ''">
                <textarea name="fields[quote]" id="field-quote"
                    class="textarea textarea-bordered w-full"
                    rows="10"
                    required>{{ e($row->quote) }}</textarea>
            </x-form-field>

            <div class="space-y-3 mt-4">
                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="fields[publish_ok]"
                        id="field-publish_ok" value="1"
                        class="checkbox checkbox-sm mt-0.5"
                        @checked($row->publish_ok) />
                    <span class="text-sm">
                        {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_STORY_AUTHORIZE_QUOTE', $sitename, $sitename) }}
                    </span>
                </label>

                <label class="flex items-start gap-3 cursor-pointer">
                    <input type="checkbox" name="fields[contact_ok]"
                        id="field-contact_ok" value="1"
                        class="checkbox checkbox-sm mt-0.5"
                        @checked($row->contact_ok) />
                    <span class="text-sm">
                        {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_STORY_AUTHORIZE_CONTACT', $sitename) }}
                    </span>
                </label>
            </div>
        </x-form-section>

        <div class="form-actions flex gap-3 mt-6">
            <button type="submit" class="btn btn-primary">
                {{ \Hubzero\Facades\Lang::txt('COM_FEEDBACK_SUBMIT') }}
            </button>
            <a class="btn btn-ghost" href="{{ $mainUrl }}">
                {{ \Hubzero\Facades\Lang::txt('JCANCEL') }}
            </a>
        </div>
    </form>
</x-page-container>
