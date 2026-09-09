{{--
 * Resource editing wizard — step progress indicator.
 *
 * Variables:
 *   $stage      — Current step number (1-5)
 *   $option     — Component option (com_tools)
 *   $controller — Controller name
 *   $version    — 'dev' or 'current'
 *   $row        — Resource row object
 *   $status     — Status array (with 'published' key)
 *   $vnum       — Version number string
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$stages = [
    Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_DESCRIPTION'),
    Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_CONTRIBUTORS'),
    Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_ATTACHMENTS'),
    Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_TAGS'),
    Lang::txt('COM_TOOLS_CONTRIBTOOL_STEP_FINALIZE'),
];

$key = $stage - 1;
$stageEditTip = ($version == 'dev')
    ? Lang::txt('COM_TOOLS_CONTRIBTOOL_TIP_NEXT_TOOL_RELEASE')
    : Lang::txt('COM_TOOLS_CONTRIBTOOL_TIP_CURRENT_VERSION');
@endphp

<p class="text-sm text-base-content/70 mb-2">
    {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_EDIT_PAGE_FOR') }} {{ $stageEditTip }}:
</p>

<ul class="steps steps-horizontal w-full">
    @for ($i = 0; $i < count($stages); $i++)
        @php
            $isActive = ($i == $key);
            $isDone = ($i < $key);
            $isFinalize = ($i + 1 == count($stages));
            $isClickable = false;
            $stepUrl = '';

            if ($version == 'dev' && !$isActive && !$isFinalize) {
                $isClickable = true;
                $stepUrl = Route::url(
                    'index.php?option=' . $option
                    . '&task=' . $controller
                    . '&step=' . ($i + 1)
                    . '&app=' . $row->alias
                );
            } elseif (
                $version == 'current' && !$isActive && !$isFinalize
                && ($i == 0 || $i == 2 || $i == 3)
            ) {
                $isClickable = true;
                $stepUrl = Route::url(
                    'index.php?option=' . $option
                    . '&task=' . $controller
                    . '&step=' . ($i + 1)
                    . '&app=' . $row->alias
                    . '&editversion=current'
                );
            }
        @endphp
        <li @class(['step', 'step-primary' => ($isActive || $isDone)])>
            @if ($isClickable)
                <a href="{{ $stepUrl }}" class="link link-hover">
                    {{ $stages[$i] }}
                </a>
            @else
                {{ $stages[$i] }}
            @endif
        </li>
    @endfor
</ul>

@php
    $versionClass = ($version == 'dev') ? 'badge-warning' : 'badge-success';

    if ($version == 'dev') {
        $versionText = $vnum
            ? ucfirst(Lang::txt('COM_TOOLS_VERSION')) . ' ' . $vnum
            : ucfirst(Lang::txt('COM_TOOLS_CONTRIBTOOL_NEXT_VERSION'));
        $versionText .= ' - ' . Lang::txt('COM_TOOLS_CONTRIBTOOL_NOT_PUBLISHED_YET');
    } else {
        $versionText = ucfirst(Lang::txt('COM_TOOLS_VERSION')) . ' ' . $vnum
            . ' - ' . Lang::txt('COM_TOOLS_CONTRIBTOOL_PUBLISHED_NOW');
    }
@endphp

<p class="mt-3 text-sm">
    <span class="badge {{ $versionClass }} badge-sm">{{ $versionText }}</span>

    @if ($version == 'dev' && $status['published'])
        @php
            $changeCurUrl = Route::url(
                'index.php?option=' . $option
                . '&task=' . $controller
                . '&step=' . $stage
                . '&app=' . $row->alias
                . '&editversion=current'
            );
        @endphp
        <a href="{{ $changeCurUrl }}" class="link link-primary link-hover text-sm ml-2">
            {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_CHANGE_CURRENT_VERSION') }}
        </a>
    @endif

    @if ($version == 'current' && $status['published'])
        @php
            $changeUpUrl = Route::url(
                'index.php?option=' . $option
                . '&task=' . $controller
                . '&step=' . $stage
                . '&app=' . $row->alias
            );
        @endphp
        <a href="{{ $changeUpUrl }}" class="link link-primary link-hover text-sm ml-2">
            {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_CHANGE_UPCOMING_VERSION') }}
        </a>
    @endif
</p>
