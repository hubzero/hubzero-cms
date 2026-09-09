{{--
 * Project image upload partial
 *
 * Variables:
 *   $model  - Project model object
 *   $option - Component option string
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $imageUrl = Route::url(
        'index.php?option=' . $option . '&controller=media&alias=' . $model->get('alias') . '&media=master'
    );
    $deleteUrl = Route::url(
        'index.php?option=' . $option . '&task=deleteimg&alias=' . $model->get('alias')
    );
    $uploadUrl = Route::url(
        'index.php?option=' . $option . '&alias=' . $model->get('alias')
        . '&task=doajaxupload&no_html=1'
    );
@endphp

@if ($model->exists())
    <div class="grid pictureframe js grid-cols-12 gap-4">
        <div class="col-span-3">
            <div id="project-image-box" class="project-image-box">
                <img id="project-image-content" src="{{ $imageUrl }}" alt="" />
            </div>
            @if ($model->get('picture'))
                <p class="mt-2">
                    <a href="{{ $deleteUrl }}" id="deleteimg" class="link link-error text-sm mt-2">
                        [ {{ Lang::txt('JACTION_DELETE') }} ]
                    </a>
                </p>
            @endif
        </div>
        <div class="col-span-9" id="ajax-upload" data-action="{{ $uploadUrl }}">
            <div class="mb-4">
                <label for="uploader">
                    {{ Lang::txt('COM_PROJECTS_UPLOAD_NEW_IMAGE') }}
                    <span class="text-sm text-base-content/60">{{ Lang::txt('COM_PROJECTS_WILL_REPLACE_EXISTING_IMAGE') }}</span>
                    <span id="status-box"></span>
                    <input
                        name="upload"
                        type="file"
                        class="file-input file-input-bordered w-full"
                        id="uploader"
                    />
                </label>
            </div>
            <input
                type="button"
                value="{{ Lang::txt('COM_PROJECTS_UPLOAD') }}"
                class="btn btn-sm"
                id="upload-file"
            />
        </div>
    </div>
@endif
