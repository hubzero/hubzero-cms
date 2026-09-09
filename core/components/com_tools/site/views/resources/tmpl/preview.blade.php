{{--
 * Resource editing wizard — preview/finalize step.
 *
 * Variables:
 *   $title      — Page title
 *   $option     — Component option (com_tools)
 *   $controller — Controller name
 *   $step       — Current step number (5)
 *   $version    — 'dev' or 'current'
 *   $resource   — Resource object
 *   $status     — Status array
 *   $config     — Component params (Registry)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// Merge resource params with component config
$rparams = $resource->params;
$params = $config;
$params->merge($rparams);

$statusUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=pipeline&task=status&app=' . $resource->alias
);
$newUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=pipeline&task=create'
);
$formAction = Route::url('index.php?option=' . $option);
$prevLabel = '< ' . ucfirst(Lang::txt('COM_TOOLS_PREVIOUS'));
$finalizeLabel = ucfirst(Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_FINALIZE'));
$previewSrc = Route::url(
    'index.php?option=com_resources&id=' . $resource->id
    . '&tmpl=component&mode=preview&rev=' . $version
);
@endphp

<x-page-container :title="$title">
    @slot('actions')
        <a class="btn btn-sm btn-outline" href="{{ $statusUrl }}">
            {{ Lang::txt('COM_TOOLS_TOOL_STATUS') }}
        </a>
        <a class="btn btn-sm btn-primary" href="{{ $newUrl }}">
            {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL') }}
        </a>
    @endslot

    {{-- Step progress indicator --}}
    <section class="mb-6">
        {!! $__view->view('stage')
             ->set('stage', $step)
             ->set('option', $option)
             ->set('controller', $controller)
             ->set('version', $version)
             ->set('row', $resource)
             ->set('status', $status)
             ->set('vnum', 0)
             ->display() !!}
    </section>

    {{-- Form with navigation --}}
    <section>
        <form action="{{ $formAction }}" method="post" id="hubForm">
            <input type="hidden" name="app"
                value="{{ $resource->alias }}" />
            <input type="hidden" name="rid"
                value="{{ $resource->id }}" />
            <input type="hidden" name="option"
                value="{{ $option }}" />
            <input type="hidden" name="controller"
                value="pipeline" />
            <input type="hidden" name="task"
                value="status" />
            <input type="hidden" name="msg"
                value="{{ Lang::txt('COM_TOOLS_NOTICE_RES_UPDATED') }}" />
            <input type="hidden" name="step" value="6" />
            <input type="hidden" name="editversion"
                value="{{ $version }}" />
            <input type="hidden" name="toolname"
                value="{{ $resource->alias }}" />

            <div class="flex justify-between items-center mb-6">
                <button type="button"
                    class="btn btn-outline btn-sm returntoedit">
                    {!! $prevLabel !!}
                </button>
                <button type="submit" class="btn btn-primary btn-sm">
                    {{ $finalizeLabel }}
                </button>
            </div>
        </form>

        {{-- Preview --}}
        <h2 class="text-xl font-semibold mb-4">
            {{ Lang::txt('COM_TOOLS_Preview') }}
        </h2>
        <div class="border border-base-300 rounded-lg overflow-hidden">
            <iframe id="preview-frame" name="preview-frame"
                class="w-full min-h-[600px]"
                frameborder="0"
                src="{{ $previewSrc }}"></iframe>
        </div>
    </section>
</x-page-container>
