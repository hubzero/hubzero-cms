@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$formAction = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=storage');
$filelistSrc = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=filelist&tmpl=component');
@endphp

<x-page-container :title="Lang::txt('COM_TOOLS_STORAGE')">
    @if($exceeded)
        <div role="alert" class="alert alert-warning mb-4">
            <span>{{ Lang::txt('COM_TOOLS_ERROR_STORAGE_EXCEEDED') }}</span>
        </div>
    @endif

    @if($output)
        <div role="alert" class="alert alert-success mb-4">
            <span>{{ $output }}</span>
        </div>
    @elseif($__view->getError())
        <div role="alert" class="alert alert-error mb-4">
            <span>{!! $__view->getError() !!}</span>
        </div>
    @endif

    <form action="{{ $formAction }}" method="post" id="hubForm">
        <div class="card bg-base-100 shadow-sm mb-6">
            <div class="card-body">
                <h3 class="card-title">{{ Lang::txt('COM_TOOLS_STORAGE_AUTOMATIC') }}</h3>
                <p class="text-sm opacity-70 mb-4">
                    <strong>{{ Lang::txt('COM_TOOLS_STORAGE_WHAT_DOES_PURGE_DO') }}</strong><br />
                    {!! Lang::txt('COM_TOOLS_STORAGE_WHAT_PURGE_DOES') !!}
                </p>
                <div class="flex flex-wrap items-end gap-4">
                    <div class="form-control">
                        <label class="label" for="degree">
                            <span class="label-text">{{ Lang::txt('COM_TOOLS_STORAGE_CLEAN_UP_DISCK_SPACE') }}</span>
                        </label>
                        <select name="degree" id="degree" class="select select-bordered">
                            <option value="default">{{ Lang::txt('COM_TOOLS_STORAGE_OPT_MINIMALLY') }}</option>
                            <option value="olderthan1">{{ Lang::txt('COM_TOOLS_STORAGE_OPT_OLDER_DAY') }}</option>
                            <option value="olderthan7">{{ Lang::txt('COM_TOOLS_STORAGE_OPT_OLDER_WEEK') }}</option>
                            <option value="olderthan30">{{ Lang::txt('COM_TOOLS_STORAGE_OPT_OLDER_MONTH') }}</option>
                            <option value="all">{{ Lang::txt('COM_TOOLS_STORAGE_OPT_ALL') }}</option>
                        </select>
                    </div>
                    <button type="submit" name="action" value="Purge" class="btn btn-warning">
                        Purge
                    </button>
                </div>
                <p class="text-sm opacity-70 mt-2">{!! Lang::txt('COM_TOOLS_STORAGE_AUTOMATIC_HINT') !!}</p>
            </div>
        </div>

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">
                <h3 class="card-title">{{ Lang::txt('COM_TOOLS_STORAGE_MANUAL') }}</h3>
                <p class="mb-4">{{ Lang::txt('COM_TOOLS_STORAGE_BROWSE_STORAGE') }}</p>
                <iframe src="{{ $filelistSrc }}" name="filer" id="filer"
                        width="100%" height="500" frameborder="0"></iframe>
            </div>
        </div>

        <input type="hidden" name="option" value="{{ $option }}" />
        <input type="hidden" name="controller" value="{{ $controller }}" />
        <input type="hidden" name="task" value="purge" />
        {!! Html::input('token') !!}
    </form>
</x-page-container>
