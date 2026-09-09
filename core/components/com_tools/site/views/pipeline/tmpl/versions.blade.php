@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Components\Tools\Helpers\Html as ToolsHtml;
use Hubzero\Facades\Component;

$hint = ($status['published'] != 1 && !$status['version']) ? '1.0' : '';

$statusUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=status&app=' . $status['toolname']
);
$newToolUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=create'
);

$newstate = ($action == 'edit')
    ? $status['state']
    : ToolsHtml::getStatusNum('Approved');
$submitlabel = ($action == 'edit')
    ? Lang::txt('COM_TOOLS_SAVE')
    : Lang::txt('COM_TOOLS_USE_THIS_VERSION');

$rconfig = Component::params('com_resources');
$tconfig = Component::params('com_tools');
$hubDOIpath = $rconfig->get('doi');
$publishedNum = ToolsHtml::getStatusNum('Published');

$saveVersionUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=saveversion&app=' . $status['toolname']
);
@endphp

<x-page-container :title="$title">

    @slot('actions')
        <a class="btn btn-sm btn-ghost gap-1" href="{{ $statusUrl }}">
            <x-icon name="info" class="size-4" />
            {{ Lang::txt('COM_TOOLS_TOOL_STATUS') }}
        </a>
        <a class="btn btn-sm btn-primary gap-1" href="{{ $newToolUrl }}">
            <x-icon name="plus" class="size-4" />
            {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL') }}
        </a>
    @endslot

    @slot('sidebar')
        <x-sidebar-card :title="Lang::txt('COM_TOOLS_CONTRIBTOOL_VERSION_WHY_NEED_NUMBER')">
            <p class="text-sm">{{ Lang::txt('COM_TOOLS_CONTRIBTOOL_VERSION_WHY_NEED_NUMBER_ANSWER') }}</p>
        </x-sidebar-card>

        <x-sidebar-card :title="Lang::txt('COM_TOOLS_CONTRIBTOOL_VERSION_HOW_DECIDE')">
            <div class="text-sm space-y-2">
                <p>{{ Lang::txt('COM_TOOLS_CONTRIBTOOL_VERSION_HOW_DECIDE_ANSWER_ONE') }}</p>
                <p>{{ Lang::txt('COM_TOOLS_CONTRIBTOOL_VERSION_HOW_DECIDE_ANSWER_TWO') }}</p>
                <p>{{ Lang::txt('COM_TOOLS_CONTRIBTOOL_VERSION_HOW_DECIDE_ANSWER_THREE') }}</p>
            </div>
        </x-sidebar-card>
    @endslot

    @if($action == 'confirm')
        @php
            ToolsHtml::writeApproval(Lang::txt('COM_TOOLS_CONFIRM_VERSION'));
        @endphp
    @endif

    @if($error)
        <div role="alert" class="alert alert-error mb-4">
            <span>{{ $error }}</span>
        </div>
    @endif

    {{-- Version entry form --}}
    @if($action != 'dev' && $status['state'] != $publishedNum)
        @if($action == 'confirm' || $action == 'edit')
            <h4 class="text-lg font-semibold mb-2">
                {{ Lang::txt('COM_TOOLS_VERSION_PLS_CONFIRM') }}
                {{ ($action == 'edit') ? Lang::txt('COM_TOOLS_NEXT') : Lang::txt('COM_TOOLS_THIS') }}
                {{ Lang::txt('COM_TOOLS_TOOL_RELEASE') }}:
            </h4>
        @elseif($action == 'new' && $status['toolname'])
            <h4 class="text-lg font-semibold mb-2">
                {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_ENTER_UNIQUE_VERSION') }}:
            </h4>
        @endif

        <form action="{{ $saveVersionUrl }}" method="post" id="versionForm" class="mb-6">
            <div class="flex items-end gap-3">
                <div class="form-control">
                    <label class="label" for="newversion">
                        <span class="label-text">{{ ucfirst(Lang::txt('COM_TOOLS_VERSION')) }}</span>
                    </label>
                    <input type="text"
                           name="newversion"
                           id="newversion"
                           value="{{ $status['version'] }}"
                           maxlength="15"
                           placeholder="{{ $hint }}"
                           class="input input-bordered w-48" />
                </div>
                <button type="submit" class="btn btn-primary">
                    {{ $submitlabel }}
                </button>
            </div>
            <input type="hidden" name="option" value="{{ $option }}" />
            <input type="hidden" name="controller" value="{{ $controller }}" />
            <input type="hidden" name="task" value="saveversion" />
            <input type="hidden" name="newstate" value="{{ $__view->escape($newstate) }}" />
            <input type="hidden" name="action" value="{{ $__view->escape($action) }}" />
            <input type="hidden" name="id" value="{{ $__view->escape($status['toolid']) }}" />
            <input type="hidden" name="toolname" value="{{ $__view->escape($status['toolname']) }}" />
            {!! Html::input('token') !!}
        </form>
    @endif

    {{-- Existing versions --}}
    <h3 class="text-lg font-semibold mb-3">
        {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_EXISTING_VERSIONS') }}:
    </h3>

    @if($versions && $status['toolname'])
        <div class="space-y-2">
            {{-- Table header --}}
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">
                    <thead>
                        <tr>
                            <th>{{ ucfirst(Lang::txt('COM_TOOLS_VERSION')) }}</th>
                            <th>{{ ucfirst(Lang::txt('COM_TOOLS_RELEASED')) }}</th>
                            <th>{{ ucfirst(Lang::txt('COM_TOOLS_SUBVERSION')) }}</th>
                            <th>{{ ucfirst(Lang::txt('COM_TOOLS_PUBLISHED')) }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($versions as $i => $t)
                            @php
                                $toolaccess = ToolsHtml::getToolAccess(
                                    $t->toolaccess,
                                    $status['membergroups']
                                );
                                $codeaccess = ToolsHtml::getCodeAccess($t->codeaccess);
                                $wikiaccess = ToolsHtml::getWikiAccess($t->wikiaccess);

                                $handle = '';
                                if (isset($t->doi) && $t->doi && $tconfig->get('doi_shoulder')) {
                                    $doiShoulder = isset($t->doi_shoulder)
                                        ? $t->doi_shoulder
                                        : $tconfig->get('doi_shoulder');
                                    $handle = 'doi:' . $doiShoulder . '/' . strtoupper($t->doi);
                                    $doiResolve = $tconfig->get('doi_resolve', 'https://doi.org/');
                                    $handleLink = $doiResolve . $handle;
                                } elseif (isset($t->doi_label) && $t->doi_label) {
                                    $handle = 'doi:10254/' . $tconfig->get('doi_prefix')
                                        . $resource->id . '.' . $t->doi_label;
                                    $handleLink = 'http://hdl.handle.net/' . $handle;
                                }

                                $versionLabel = ($t->state == 3 && $t->version == $status['currentversion'])
                                    ? Lang::txt('COM_TOOLS_NO_LABEL')
                                    : $t->version;

                                $editCurrentUrl = Route::url(
                                    'index.php?option=' . $option
                                    . '&controller=' . $controller
                                    . '&task=edit&app=' . $status['toolname']
                                    . '&editversion=current'
                                );
                                $editDevUrl = Route::url(
                                    'index.php?option=' . $option
                                    . '&controller=' . $controller
                                    . '&task=edit&app=' . $status['toolname']
                                    . '&editversion=dev'
                                );
                                $dateReleased = $t->released
                                    ? Date::of($t->released)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
                                    : 'N/A';
                            @endphp
                            <tr>
                                <td>
                                    {{ $versionLabel ?: Lang::txt('COM_TOOLS_NA') }}
                                </td>
                                <td>
                                    @if($t->state != 3)
                                        {{ $dateReleased }}
                                    @else
                                        <span class="badge badge-info badge-sm">
                                            {{ Lang::txt('COM_TOOLS_UNDER_DEVELOPMENT') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($t->state != 3 || ($t->state == 3 && $t->revision != $status['currentrevision']))
                                        {{ $t->revision }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    @if($t->state == '1')
                                        <span class="badge badge-success badge-sm">
                                            {{ Lang::txt('COM_TOOLS_PUBLISHED') }}
                                        </span>
                                    @else
                                        <span class="badge badge-ghost badge-sm">
                                            {{ Lang::txt('COM_TOOLS_UNPUBLISHED') }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    @if($t->state == 1 && $admin)
                                        <a href="{{ $editCurrentUrl }}" class="btn btn-xs btn-ghost">
                                            {{ Lang::txt('COM_TOOLS_EDIT') }}
                                        </a>
                                    @elseif($t->state == 3)
                                        <a href="{{ $editDevUrl }}" class="btn btn-xs btn-ghost">
                                            {{ Lang::txt('COM_TOOLS_EDIT') }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            {{-- Expandable details row using daisyUI collapse --}}
                            <tr>
                                <td colspan="5" class="p-0">
                                    <div class="collapse collapse-arrow bg-base-200/50">
                                        <input type="checkbox" />
                                        <div class="collapse-title text-sm font-medium py-2 min-h-0">
                                            {{ Lang::txt('COM_TOOLS_DETAILS') }}
                                        </div>
                                        <div class="collapse-content">
                                            <div class="space-y-1 text-sm">
                                                <p>
                                                    <span class="font-semibold">{{ ucfirst(Lang::txt('COM_TOOLS_TITLE')) }}:</span>
                                                    {{ $t->title }}
                                                </p>
                                                <p>
                                                    <span class="font-semibold">{{ ucfirst(Lang::txt('COM_TOOLS_DESCRIPTION')) }}:</span>
                                                    {{ $t->description }}
                                                </p>
                                                <p>
                                                    <span class="font-semibold">{{ ucfirst(Lang::txt('COM_TOOLS_AUTHORS')) }}:</span>
                                                    {!! ToolsHtml::getDevTeam($t->authors) !!}
                                                </p>
                                                <p>
                                                    <span class="font-semibold">{{ ucfirst(Lang::txt('COM_TOOLS_TOOL_ACCESS')) }}:</span>
                                                    {{ $toolaccess }}
                                                </p>
                                                <p>
                                                    <span class="font-semibold">{{ ucfirst(Lang::txt('COM_TOOLS_CODE_ACCESS')) }}:</span>
                                                    {{ $codeaccess }}
                                                </p>
                                                @if($handle)
                                                    <p>
                                                        <span class="font-semibold">{{ Lang::txt('COM_TOOLS_DOI') }}:</span>
                                                        <a href="{{ $handleLink }}" class="link link-primary">{{ $handle }}</a>
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <p>
            {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_NO_VERSIONS') }}
            {{ $status['toolname'] }}.
            {{ ucfirst(Lang::txt('COM_TOOLS_GO_BACK_TO')) }}
            <a href="{{ $statusUrl }}" class="link link-primary">
                {{ strtolower(Lang::txt('COM_TOOLS_TOOL_STATUS')) }}</a>.
        </p>
    @endif

</x-page-container>
