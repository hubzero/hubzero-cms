{{--
 * Resource editing wizard — main wrapper with step navigation.
 *
 * Variables:
 *   $title      — Page title
 *   $option     — Component option (com_tools)
 *   $controller — Controller name
 *   $step       — Current step number (1-4)
 *   $version    — 'dev' or 'current'
 *   $row        — Resource row object
 *   $status     — Status array
 *   $tags       — Tags string
 *   $tagfa      — Selected focus area tag
 *   $fats       — Focus area tags array
 *   $authors    — Authors array
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$nextstep = $step + 1;
$task = ($nextstep == 5) ? 'preview' : 'start';
$dev = ($version == 'dev') ? 1 : 0;

if ($version == 'dev') {
    $v = ($status['version'] && $status['version'] != $status['currentversion'])
        ? $status['version'] : '';
} else {
    $v = $status['version'];
}

$statusUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=pipeline&task=status&app=' . $row->alias
);
$newUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=pipeline&task=create'
);
$formAction = Route::url('index.php?option=' . $option);
$prevLabel = '< ' . ucfirst(Lang::txt('COM_TOOLS_PREVIOUS'));
$nextLabel = ucfirst(Lang::txt('COM_TOOLS_SAVE_AND_GO_NEXT'));

// Determine step layout
$layout = match ($step) {
    1 => 'compose',
    2 => 'authors',
    3 => 'attach',
    4 => 'tags',
    default => 'compose',
};
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
             ->set('row', $row)
             ->set('status', $status)
             ->set('vnum', $v)
             ->display() !!}
    </section>

    {{-- Wizard form --}}
    <section>
        <form action="{{ $formAction }}" method="post" id="hubForm">
            @if ($__view->getError())
                <div class="alert alert-error mb-4">
                    {!! implode('<br />', $__view->getErrors()) !!}
                </div>
            @endif

            {{-- Top navigation --}}
            <div class="flex justify-between items-center mb-6">
                <div>
                    @if ($step != 1)
                        <button type="button"
                            class="btn btn-outline btn-sm returntoedit">
                            {!! $prevLabel !!}
                        </button>
                    @endif
                </div>
                <div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        {{ $nextLabel }}
                    </button>
                </div>
            </div>

            {{-- Step content --}}
            {!! $__view->view($layout)
                 ->set('step', $step)
                 ->set('option', $option)
                 ->set('controller', $controller)
                 ->set('version', $version)
                 ->set('row', $row)
                 ->set('status', $status)
                 ->set('dev', $dev)
                 ->set('tags', $tags)
                 ->set('tagfa', $tagfa)
                 ->set('fats', $fats)
                 ->set('authors', $authors)
                 ->display() !!}

            {{-- Hidden fields --}}
            <input type="hidden" name="app" value="{{ $row->alias }}" />
            <input type="hidden" name="rid" value="{{ $row->id }}" />
            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="controller" value="{{ $controller }}" />
            <input type="hidden" name="task" value="{{ $task }}" />
            <input type="hidden" name="step" value="{{ $nextstep }}" />
            <input type="hidden" name="editversion" value="{{ $version }}" />
            <input type="hidden" name="toolname"
                value="{{ $status['toolname'] }}" />
            {!! Html::input('token') !!}

            {{-- Bottom navigation --}}
            <div class="flex justify-between items-center mt-6">
                <div>
                    @if ($step != 1)
                        <button type="button"
                            class="btn btn-outline btn-sm returntoedit">
                            {!! $prevLabel !!}
                        </button>
                    @endif
                </div>
                <div>
                    <button type="submit" class="btn btn-primary btn-sm">
                        {{ $nextLabel }}
                    </button>
                </div>
            </div>
        </form>
    </section>
</x-page-container>
