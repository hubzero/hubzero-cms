{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->js('import.js');

$base = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=citations';
@endphp

<section id="import" class="section">
    <div class="section-inner">
        @if ($messages)
            <div class="alert alert-info">
                <p>{!! $messages['message'] !!}</p>
            </div>
        @endif

        {{-- Steps indicator --}}
        <ul class="steps steps-horizontal w-full mb-6">
            <li class="step step-primary">
                <a href="{{ Route::url($base . '&action=import') }}">
                    {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP1') }}
                    <span class="block text-xs opacity-70">{{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP1_NAME') }}</span>
                </a>
            </li>
            <li class="step">
                <span>
                    {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP2') }}
                    <span class="block text-xs opacity-70">{{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP2_NAME') }}</span>
                </span>
            </li>
            <li class="step">
                <span>
                    {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP3') }}
                    <span class="block text-xs opacity-70">{{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_STEP3_NAME') }}</span>
                </span>
            </li>
        </ul>

        <form id="hubForm"
            enctype="multipart/form-data"
            method="post"
            action="{{ Route::url($base . '&action=upload') }}">

            <div class="card bg-base-200">
                <div class="card-body">
                    <h2 class="card-title">{{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_UPLOAD') }}</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <label class="form-control" for="citations_file">
                            <div class="label">
                                <span class="label-text">
                                    {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_UPLOAD_FILE') }}
                                    <span class="text-error">*</span>
                                </span>
                            </div>
                            <input type="file"
                                name="citations_file"
                                id="citations_file"
                                class="file-input file-input-bordered w-full" />
                            <div class="label">
                                <span class="label-text-alt opacity-60">
                                    {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_UPLOAD_MAX') }}
                                </span>
                            </div>
                        </label>

                        <div>
                            <p class="font-semibold mb-2">
                                {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_ACCEPTABLE') }}
                            </p>
                            <p class="text-sm opacity-70">
                                {!! implode('<br />', $accepted_files) !!}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex gap-2 mt-6">
                <button type="submit" name="submit" class="btn btn-primary">
                    {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_UPLOAD') }}
                </button>
                <a class="btn btn-ghost" href="{{ Route::url($base) }}">
                    {{ Lang::txt('JCANCEL') }}
                </a>
            </div>

            {!! Html::input('token') !!}
            <input type="hidden" name="option" value="com_groups" />
            <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
            <input type="hidden" name="active" value="citations" />
            <input type="hidden" name="action" value="upload" />
        </form>
    </div>
</section>
