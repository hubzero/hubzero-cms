{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
$__view->css('import.css')->js('import.js');
$base = $member->link() . '&active=citations';
@endphp

<section id="import">
    @if (isset($messages))
        @foreach ($messages as $message)
            <div class="alert alert-{{ $message['type'] }}">{{ $message['message'] }}</div>
        @endforeach
    @endif

    {{-- Step indicator --}}
    <ul class="steps w-full mb-6">
        <li class="step step-primary">
            <a href="{{ Route::url($base . '&task=import') }}">
                {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP1') }}
                <span class="block text-xs">{{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP1_NAME') }}</span>
            </a>
        </li>
        <li class="step">
            {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP2') }}
            <span class="block text-xs">{{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP2_NAME') }}</span>
        </li>
        <li class="step">
            {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP3') }}
            <span class="block text-xs">{{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_STEP3_NAME') }}</span>
        </li>
    </ul>

    <form id="hubForm"
        enctype="multipart/form-data"
        method="post"
        action="{{ Route::url($base . '&task=upload') }}">

        <fieldset class="space-y-4">
            <legend class="text-lg font-semibold">
                {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_UPLOAD') }}:
            </legend>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <label class="form-control w-full" for="citations_file">
                    <div class="label">
                        <span class="label-text">
                            {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_UPLOAD_FILE') }}:
                            <span class="text-error">*</span>
                        </span>
                    </div>
                    <input type="file"
                        name="citations_file"
                        id="citations_file"
                        class="file-input file-input-bordered w-full" />
                    <div class="label">
                        <span class="label-text-alt text-base-content/50">
                            {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_UPLOAD_MAX') }}
                        </span>
                    </div>
                </label>

                <div class="card bg-base-200">
                    <div class="card-body">
                        <strong>{{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_ACCEPTABLE') }}</strong>
                        <p class="text-sm mt-2">
                            {!! implode('<br />', $accepted_files) !!}
                        </p>
                    </div>
                </div>
            </div>
        </fieldset>

        <div class="flex gap-2 mt-6">
            <button type="submit" name="submit" class="btn btn-primary">
                {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_UPLOAD') }}
            </button>
            <a class="btn btn-ghost" href="{{ Route::url($base) }}">
                {{ Lang::txt('JCANCEL') }}
            </a>
        </div>

        {!! Html::input('token') !!}
        <input type="hidden" name="option" value="com_members" />
        <input type="hidden" name="id" value="{{ $member->get('id') }}" />
        <input type="hidden" name="active" value="citations" />
        <input type="hidden" name="action" value="upload" />
    </form>
</section>
