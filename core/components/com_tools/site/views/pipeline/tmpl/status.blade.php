{{--
 * Tool status dashboard — shows current state, tool info, developer tools,
 * admin controls, and "What's Next" guidance.
 *
 * Variables from controller:
 *   $title       — Page title
 *   $option      — Component option (com_tools)
 *   $controller  — Controller name
 *   $status      — Array of tool status data
 *   $msg         — Success/flash message
 *   $config      — Component params (Registry)
 *   $statusClass — CSS class for status styling
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Config;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// Get configurations / defaults
$developer_site = $config->get('developer_site', 'hubFORGE');
$live_site = rtrim(Request::base(), '/');
$developer_url = $live_site = 'https://' . preg_replace('#^(https://|http://)#', '', $live_site);
$project_path = $config->get('project_path', '/tools/');
$dev_suffix = $config->get('dev_suffix', '_dev');

// Get status name and class
\Components\Tools\Helpers\Html::getStatusName($status['state'], $state);
\Components\Tools\Helpers\Html::getStatusClass($status['state'], $statusClass);

// Pre-compute commonly used URLs
$pipelineUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=pipeline'
);
$newToolUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=create'
);
$editToolUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=edit&app=' . $status['toolname']
);
$statusUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=status&app=' . $status['toolname']
);
$cancelUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=cancel&app=' . $status['toolname']
);
$licenseUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=license&app=' . $status['toolname']
);
$versionsUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=versions&app=' . $status['toolname']
);
$resourcePreviewUrl = Route::url(
    'index.php?option=com_resources&id=' . $status['resourceid'] . '&rev=dev'
);
$resourceEditUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=resource&step=1&app=' . $status['toolname']
);
$resourcePreviewUrl2 = Route::url(
    'index.php?option=' . $option
    . '&task=resource&task=preview&app=' . $status['toolname']
);
$resourceCreateUrl = Route::url(
    'index.php?option=' . $option
    . '&task=resource&step=1&app=' . $status['toolname']
);
$toolUrl = Route::url(
    'index.php?option=' . $option . '&app=' . $status['toolname']
);
$uploadedUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=update&newstate=Uploaded&app=' . $status['toolname']
);
$approvedUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=update&newstate=Approved&app=' . $status['toolname']
);
$updatedUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=' . $controller
    . '&task=update&newstate=Updated&app=' . $status['toolname']
);
$addrepoUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=admin&task=addrepo&app=' . $status['toolname']
);
$installUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=admin&task=install&app=' . $status['toolname']
);
$publishUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=admin&task=publish&app=' . $status['toolname']
);
$retireAdminUrl = Route::url(
    'index.php?option=' . $option
    . '&controller=admin&task=retire&app=' . $status['toolname']
);
$ticketUrl = Route::url(
    'index.php?option=com_support&task=ticket&id=' . $status['ticketid']
);
$wikiUrl = $developer_url . $project_path . $status['toolname'] . '/wiki';
$sourceUrl = $developer_url . $project_path . $status['toolname'] . '/browser';
$timelineUrl = $developer_url . $project_path . $status['toolname'] . '/timeline';
$wikiStartUrl = $developer_url . $project_path . $status['toolname']
    . '/wiki/GettingStarted';
$devToolsUrl = $developer_url . '/tools';
$toolAccess = \Components\Tools\Helpers\Html::getToolAccess(
    $status['exec'],
    $status['membergroups']
);
$codeAccess = \Components\Tools\Helpers\Html::getCodeAccess($status['code']);
$wikiAccess = \Components\Tools\Helpers\Html::getWikiAccess($status['wiki']);
$devTeam = \Components\Tools\Helpers\Html::getDevTeam($status['developers']);

$isActive = \Components\Tools\Helpers\Html::toolActive($status['state']);
$isWIP = \Components\Tools\Helpers\Html::toolWIP($status['state']);
$numTools = \Components\Tools\Helpers\Html::getNumofTools($status);

$sitename = Config::get('sitename');
$hubShortURL = rtrim(str_replace('https://', '', Request::base()), '/');
$testpath = Route::url(
    'index.php?option=' . $option
    . '&controller=sessions&task=invoke&app=' . $status['toolname']
    . '&version=test'
);
$resModified = $status['resource_modified'];
$isAdmin = $config->get('access-admin-component');

// Message targets
$msgTarget = $isAdmin
    ? strtolower(Lang::txt('COM_TOOLS_DEVELOPMENT_TEAM'))
    : Lang::txt('COM_TOOLS_SITE_ADMIN');
$msgTitle = Lang::txt('COM_TOOLS_SEND_MESSAGE')
    . ' ' . Lang::txt('COM_TOOLS_TO') . ' ' . $msgTarget;
$ctTarget = $isAdmin
    ? strtolower(Lang::txt('COM_TOOLS_DEVELOPMENT_TEAM'))
    : strtolower(Lang::txt('COM_TOOLS_SITE_ADMIN'));
$ctHead = Lang::txt('COM_TOOLS_SEND_MESSAGE')
    . ' ' . Lang::txt('COM_TOOLS_TO');
@endphp

<x-page-container title="{{ $title }} - {{ $state }}">

{{-- Header --}}
<div class="flex flex-wrap items-center justify-between gap-4 mb-6">
    <h2 class="text-2xl font-bold">
        {{ $title }} &mdash;
        <span class="text-base-content/70">{{ $state }}</span>
    </h2>
    <div class="flex gap-2">
        <a class="btn btn-sm btn-outline" href="{{ $pipelineUrl }}">
            {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_ALL_TOOLS') }}
        </a>
        <a class="btn btn-sm btn-primary" href="{{ $newToolUrl }}">
            {{ Lang::txt('COM_TOOLS_CONTRIBTOOL_NEW_TOOL') }}
        </a>
    </div>
</div>

{{-- Status progress bar --}}
@if ($isActive)
    @php
        $states = [
            Lang::txt('COM_TOOLS_REGISTERED'),
            Lang::txt('COM_TOOLS_CREATED'),
            Lang::txt('COM_TOOLS_UPLOADED'),
            Lang::txt('COM_TOOLS_INSTALLED'),
            Lang::txt('COM_TOOLS_APPROVED'),
            Lang::txt('COM_TOOLS_PUBLISHED'),
        ];

        if ($state == Lang::txt('COM_TOOLS_RETIRED')) {
            $states[] = Lang::txt('COM_TOOLS_RETIRED');
        }

        if ($state == Lang::txt('COM_TOOLS_UPDATED')) {
            $states[2] = Lang::txt('COM_TOOLS_UPDATED');
        }

        $key = array_keys($states, $state);
        $currentStep = count($key) > 0 ? $key[0] : count($states) - 1;
    @endphp
    <x-step-nav :steps="$states" :current="$currentStep" />
@endif

{{-- Success message and tool count --}}
@if ($msg)
    <div class="alert alert-success mb-4">{{ $msg }}</div>
@endif
@if ($numTools)
    <p class="mb-4">{{ $numTools }}.</p>
@endif

{{-- Two-column layout --}}
<div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

    {{-- Left column: Tool Info + Developer Tools + Admin Controls --}}
    <div class="lg:col-span-7">

        {{-- Tool Info Card --}}
        <div class="card bg-base-100 shadow-sm border border-base-300 mb-6">
            <div class="card-body p-0">
                <div class="flex items-center justify-between px-4 py-3 bg-base-200 rounded-t-2xl">
                    <h3 class="font-bold text-lg">
                        {{ Lang::txt('COM_TOOLS_TOOL_INFO') }}
                    </h3>
                    @if ($isActive)
                        <a class="btn btn-sm btn-ghost"
                           href="{{ $editToolUrl }}"
                           title="{{ Lang::txt('COM_TOOLS_EDIT_TIPS') }}">
                            {{ Lang::txt('COM_TOOLS_EDIT') }}
                        </a>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <tbody>
                            <tr>
                                <th class="w-40">{{ Lang::txt('COM_TOOLS_TITLE') }}</th>
                                <td>
                                    {{ $__view->escape(stripslashes($status['title'])) }}
                                    ({{ $status['toolname'] }}
                                    &mdash; {{ strtolower(Lang::txt('COM_TOOLS_ID')) }}
                                    #{{ $status['toolid'] }})
                                </td>
                            </tr>
                            <tr>
                                <th>{{ Lang::txt('COM_TOOLS_VERSION') }}</th>
                                <td>
                                    @if ($status['version'])
                                        {{ Lang::txt('COM_TOOLS_THIS_VERSION') }}
                                        {{ $status['version'] }}
                                    @else
                                        {{ Lang::txt('COM_TOOLS_THIS_VERSION') }}:
                                        {{ Lang::txt('COM_TOOLS_NO_LABEL') }}
                                    @endif
                                    @if (!$status['published']
                                        || ($status['version'] != $status['currentversion']
                                            && $isActive))
                                        ({{ Lang::txt('COM_TOOLS_UNDER_DEVELOPMENT') }})
                                    @endif
                                    @if ($status['published'])
                                        [<a class="link link-primary"
                                            href="{{ $versionsUrl }}">{{ strtolower(Lang::txt('COM_TOOLS_ALL_VERSIONS')) }}</a>]
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ Lang::txt('COM_TOOLS_AT_A_GLANCE') }}</th>
                                <td>{{ $__view->escape(stripslashes($status['description'])) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Lang::txt('COM_TOOLS_DESCRIPTION') }}</th>
                                <td>
                                    <a class="link link-primary"
                                       href="{{ $resourcePreviewUrl }}">{{ Lang::txt('COM_TOOLS_PREVIEW') }}</a>
                                    |
                                    <a class="link link-primary"
                                       href="{{ $resourceEditUrl }}">{{ Lang::txt('COM_TOOLS_EDIT_THIS_PAGE') }}</a>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ Lang::txt('COM_TOOLS_VNC_GEOMETRY') }}</th>
                                <td>{{ $status['vncGeometryX'] }}x{{ $status['vncGeometryY'] }}</td>
                            </tr>
                            @if ($isAdmin)
                                <tr>
                                    <th>{{ Lang::txt('COM_TOOLS_HOSTREQ') }}</th>
                                    <td>{{ $status['hostreq'] }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>{{ Lang::txt('COM_TOOLS_REPOHOST') }}</th>
                                <td>{{ $status['repohost'] }}</td>
                            </tr>
                            <tr>
                                <th>{{ Lang::txt('COM_TOOLS_TOOL_EXEC') }}</th>
                                <td>{!! $toolAccess !!}</td>
                            </tr>
                            <tr>
                                <th>{{ Lang::txt('COM_TOOLS_SOURCE_CODE') }}</th>
                                <td>
                                    {!! $codeAccess !!}
                                    @if ($isActive && $isWIP)
                                        [<a class="link link-primary"
                                            href="{{ $licenseUrl }}">{{ Lang::txt('COM_TOOLS_CHANGE_LICENSE') }}</a>]
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>{{ Lang::txt('COM_TOOLS_PROJECT_AREA') }}</th>
                                <td>{!! $wikiAccess !!}</td>
                            </tr>
                            @if ($status['github'])
                                <tr>
                                    <th>{{ Lang::txt('Git URL:') }}</th>
                                    <td>
                                        <a class="link link-primary"
                                           href="{{ $status['github'] }}">{{ $status['github'] }}</a>
                                    </td>
                                </tr>
                            @endif
                            <tr>
                                <th>{{ Lang::txt('Publishing Option') }}</th>
                                <td>{{ $status['publishType'] }}</td>
                            </tr>
                            <tr>
                                <th>{{ Lang::txt('COM_TOOLS_DEVELOPMENT_TEAM') }}</th>
                                <td>{!! $devTeam !!}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Developer Tools --}}
                <div class="px-4 py-3 bg-base-200">
                    <h3 class="font-bold text-lg">
                        {{ Lang::txt('COM_TOOLS_DEVELOPER_TOOLS') }}
                    </h3>
                </div>
                <div class="px-4 py-3">
                    <div class="flex flex-wrap gap-2 mb-4">
                        <a class="btn btn-sm btn-outline"
                           href="{{ $ticketUrl }}"
                           title="{{ Lang::txt('COM_TOOLS_HISTORY_TIPS') }}">
                            History
                        </a>

                        @if ($status['state'] != 'Registered')
                            <a class="btn btn-sm btn-outline"
                               href="{{ $wikiUrl }}"
                               title="{{ Lang::txt('COM_TOOLS_WIKI_TIPS') }}">
                                Wiki
                            </a>
                            <a class="btn btn-sm btn-outline"
                               href="{{ $sourceUrl }}"
                               title="{{ Lang::txt('COM_TOOLS_SOURCE_TIPS') }}">
                                {{ Lang::txt('COM_TOOLS_SOURCE') }}
                            </a>
                            <a class="btn btn-sm btn-outline"
                               href="{{ $timelineUrl }}"
                               title="{{ Lang::txt('COM_TOOLS_TIMELINE_TIPS') }}">
                                {{ Lang::txt('COM_TOOLS_TIMELINE') }}
                            </a>
                        @else
                            <span class="btn btn-sm btn-disabled">
                                {{ Lang::txt('COM_TOOLS_WIKI') }}
                            </span>
                            <span class="btn btn-sm btn-disabled">
                                {{ Lang::txt('COM_TOOLS_SOURCE_CODE') }}
                            </span>
                            <span class="btn btn-sm btn-disabled">
                                {{ Lang::txt('COM_TOOLS_TIMELINE') }}
                            </span>
                        @endif

                        <a href="javascript:void(0);"
                           class="btn btn-sm btn-outline showmsg"
                           title="{{ $msgTitle }}">
                            {{ Lang::txt('COM_TOOLS_MESSAGE') }}
                        </a>

                        @if ($status['published'] != 1 && $isActive)
                            <a href="javascript:void(0);"
                               class="btn btn-sm btn-outline btn-error showcancel"
                               title="{{ Lang::txt('COM_TOOLS_CANCEL_TIPS') }}">
                                {{ Lang::txt('JCANCEL') }}
                            </a>
                        @endif
                    </div>

                    {{-- Cancel confirmation --}}
                    <div id="ctCancel" class="hidden">
                        <div class="alert alert-error">
                            <span>{{ Lang::txt('COM_TOOLS_CANCEL_WARNING') }}</span>
                            <div class="flex gap-2">
                                <a class="btn btn-sm btn-error"
                                   href="{{ $cancelUrl }}">{{ Lang::txt('COM_TOOLS_CANCEL_YES') }}</a>
                                <a class="btn btn-sm btn-ghost hidecancel"
                                   href="javascript:void(0);">{{ Lang::txt('COM_TOOLS_CANCEL_NO') }}</a>
                            </div>
                        </div>
                    </div>

                    {{-- Message form --}}
                    <div id="ctComment" class="hidden">
                        <div class="card bg-base-100 border border-base-300">
                            <div class="card-body">
                                <div class="flex items-center justify-between mb-2">
                                    <h4 class="font-semibold">
                                        {{ $ctHead }} {{ $ctTarget }}:
                                    </h4>
                                    <a href="javascript:void(0);"
                                       class="btn btn-sm btn-ghost btn-circle hidemsg">&times;</a>
                                </div>
                                <form action="{{ $statusUrl }}" method="post" id="commentForm">
                                    @if ($isAdmin)
                                        <label class="label cursor-pointer justify-start gap-2 mb-2">
                                            <input type="checkbox"
                                                   name="access"
                                                   value="1"
                                                   class="checkbox checkbox-sm" />
                                            <span class="label-text">
                                                {{ Lang::txt('COM_TOOLS_COMMENT_PRIVACY_TIPS') }}
                                            </span>
                                        </label>
                                    @endif
                                    <textarea name="comment"
                                              cols="50"
                                              rows="5"
                                              class="textarea textarea-bordered w-full mb-2"></textarea>
                                    <input type="hidden" name="option" value="{{ $option }}" />
                                    <input type="hidden" name="controller" value="{{ $controller }}" />
                                    <input type="hidden" name="task" value="message" />
                                    <input type="hidden" name="id" value="{{ $status['toolid'] }}" />
                                    <input type="hidden" name="app" value="{{ $status['toolname'] }}" />
                                    {!! Html::input('token') !!}
                                    <input type="submit"
                                           class="btn btn-sm btn-primary"
                                           value="{{ Lang::txt('COM_TOOLS_SEND_MESSAGE') }}" />
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>{{-- / Tool Info Card --}}

        {{-- Admin Controls --}}
        @if ($isAdmin)
            <div class="card bg-base-100 shadow-sm border border-base-300">
                <div class="card-body">
                    <h3 class="card-title">{{ Lang::txt('COM_TOOLS_ADMIN_CONTROLS') }}</h3>

                    <form action="{{ $statusUrl }}" method="post" id="hubForm">
                        {{-- Action buttons --}}
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-4">
                            <div id="createtool">
                                <a class="btn btn-sm btn-outline w-full admincall"
                                   data-action-txt="{{ Lang::txt('Creating tool project area...') }}"
                                   href="{{ $addrepoUrl }}"
                                   title="{{ Lang::txt('COM_TOOLS_COMMAND_ADD_REPO_TIPS') }}">
                                    {{ Lang::txt('COM_TOOLS_COMMAND_ADD_REPO') }}
                                </a>
                            </div>
                            <div id="installtool">
                                <a class="btn btn-sm btn-outline w-full admincall"
                                   data-action-txt="{{ Lang::txt('Installing tool...') }}"
                                   href="{{ $installUrl }}"
                                   title="{{ Lang::txt('COM_TOOLS_COMMAND_INSTALL_TIPS') }}">
                                    {{ Lang::txt('COM_TOOLS_COMMAND_INSTALL') }}
                                </a>
                            </div>
                            <div id="publishtool">
                                <a class="btn btn-sm btn-outline w-full admincall"
                                   data-action-txt="{{ Lang::txt('Publishing tool...') }}"
                                   href="{{ $publishUrl }}"
                                   title="{{ Lang::txt('COM_TOOLS_COMMAND_PUBLISH_TIPS') }}">
                                    {{ Lang::txt('COM_TOOLS_COMMAND_PUBLISH') }}
                                </a>
                            </div>
                            <div id="retiretool">
                                <a class="btn btn-sm btn-outline w-full admincall"
                                   data-action-txt="{{ Lang::txt('Retiring tool...') }}"
                                   href="{{ $retireAdminUrl }}"
                                   title="{{ Lang::txt('COM_TOOLS_COMMAND_RETIRE_TIPS') }}">
                                    {{ Lang::txt('COM_TOOLS_COMMAND_RETIRE') }}
                                </a>
                            </div>
                        </div>

                        <div id="ctSending"></div>
                        <div id="ctSuccess"></div>

                        {{-- State & Priority selects --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div class="form-control">
                                <label class="label" for="tool-newstate">
                                    <span class="label-text">
                                        {{ Lang::txt('COM_TOOLS_FLIP_STATUS') }}:
                                    </span>
                                </label>
                                <select name="newstate"
                                        id="tool-newstate"
                                        class="select select-bordered w-full">
                                    <option value="1" @selected($status['state'] == 1)>
                                        {{ Lang::txt('COM_TOOLS_REGISTERED') }}
                                    </option>
                                    <option value="2" @selected($status['state'] == 2)>
                                        {{ Lang::txt('COM_TOOLS_CREATED') }}
                                    </option>
                                    <option value="3" @selected($status['state'] == 3)>
                                        {{ Lang::txt('COM_TOOLS_UPLOADED') }}
                                    </option>
                                    <option value="4" @selected($status['state'] == 4)>
                                        {{ Lang::txt('COM_TOOLS_INSTALLED') }}
                                    </option>
                                    <option value="5" @selected($status['state'] == 5)>
                                        {{ Lang::txt('COM_TOOLS_UPDATED') }}
                                    </option>
                                    <option value="6" @selected($status['state'] == 6)>
                                        {{ Lang::txt('COM_TOOLS_APPROVED') }}
                                    </option>
                                    <option value="7" @selected($status['state'] == 7)>
                                        {{ Lang::txt('COM_TOOLS_PUBLISHED') }}
                                    </option>
                                    @if ($status['published'] == 1)
                                        <option value="8" @selected($status['state'] == 8)>
                                            {{ Lang::txt('COM_TOOLS_RETIRED') }}
                                        </option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-control">
                                <label class="label" for="tool-priority">
                                    <span class="label-text">
                                        {{ Lang::txt('COM_TOOLS_PRIORITY') }}:
                                    </span>
                                </label>
                                <select name="priority"
                                        id="tool-priority"
                                        class="select select-bordered w-full">
                                    <option value="3" @selected($status['priority'] == 3)>
                                        {{ Lang::txt('COM_TOOLS_NORMAL') }}
                                    </option>
                                    <option value="2" @selected($status['priority'] == 2)>
                                        {{ Lang::txt('COM_TOOLS_HIGH') }}
                                    </option>
                                    <option value="1" @selected($status['priority'] == 1)>
                                        {{ Lang::txt('COM_TOOLS_CRITICAL') }}
                                    </option>
                                    <option value="4" @selected($status['priority'] == 4)>
                                        {{ Lang::txt('COM_TOOLS_LOW') }}
                                    </option>
                                    <option value="5" @selected($status['priority'] == 5)>
                                        {{ Lang::txt('COM_TOOLS_LOWEST') }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <input type="hidden" name="option" value="{{ $option }}" />
                        <input type="hidden" name="controller" value="{{ $controller }}" />
                        <input type="hidden" name="task" value="update" />
                        <input type="hidden" name="id" value="{{ $status['toolid'] }}" />
                        <input type="hidden" name="app" value="{{ $status['toolname'] }}" />
                        {!! Html::input('token') !!}

                        <div class="form-control mb-4">
                            <label class="label" for="comment">
                                <span class="label-text">
                                    {{ Lang::txt('COM_TOOLS_MESSAGE_TO_DEV_TEAM') }}
                                    ({{ Lang::txt('COM_TOOLS_OPTIONAL') }})
                                </span>
                            </label>
                            <textarea name="comment"
                                      id="comment"
                                      class="textarea textarea-bordered w-full"
                                      cols="40"
                                      rows="5"></textarea>
                        </div>

                        <button type="submit" class="btn btn-success">
                            {{ Lang::txt('COM_TOOLS_APPLY_CHANGE') }}
                        </button>
                    </form>
                </div>
            </div>
        @endif

    </div>{{-- / Left column --}}

    {{-- Right column: What's Next --}}
    <div class="lg:col-span-5">
        <div class="card bg-base-100 shadow-sm border border-base-300">
            <div class="card-body">
                <h2 class="card-title text-xl">
                    {{ Lang::txt('COM_TOOLS_WHAT_NEXT') }}
                </h2>

                <form action="{{ $statusUrl }}" method="post" id="statusForm">
                    <input type="hidden" name="option" value="{{ $option }}" />
                    <input type="hidden" name="task" value="update" />
                    <input type="hidden" name="id" value="{{ $status['toolid'] }}" />
                    <input type="hidden" name="toolname" value="{{ $status['toolname'] }}" />
                    <input type="hidden" name="newstate" id="newstate" value="" />
                </form>

                @switch($status['state'])
                    {{-- State 1: Registered --}}
                    @case(1)
                        <p>
                            {{ Lang::txt('COM_TOOLS_TEAM_WILL_CREATE') }}
                            <a class="link link-primary"
                               href="{{ $devToolsUrl }}">{{ $developer_site }}</a>,
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_REGISTERED_INSTRUCTIONS') }}.
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_IT_HAS_BEEN') }}
                            {{ \Components\Tools\Helpers\Html::timeAgo($status['changed']) }}
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_SINCE_YOUR_REQUEST') }}.
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_YOU_WILL_RECEIVE_RESPONSE') }}
                            24 {{ Lang::txt('COM_TOOLS_HOURS') }}
                        </p>
                        <h4 class="font-semibold mt-4">
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_REMAINING_STEPS') }}:
                        </h4>
                        <ul class="list-disc list-inside space-y-1 mt-2">
                            <li class="text-success">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_REGISTER') }}
                                {{ $sitename }}
                            </li>
                            <li class="text-base-content/50">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_UPLOAD_CODE') }}
                                {{ $developer_site }}
                            </li>
                            @if ($resModified == '1')
                                <li class="text-success">
                                    {{ Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE') }}.
                                    <a class="link link-primary"
                                       href="{{ $resourcePreviewUrl2 }}">{{ Lang::txt('COM_TOOLS_PREVIEW') }}</a>
                                    |
                                    <a class="link link-primary"
                                       href="{{ $resourceCreateUrl }}">{{ Lang::txt('COM_TOOLS_TODO_EDIT_PAGE') }}...</a>
                                </li>
                            @else
                                <li class="text-warning">
                                    {{ Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE') }}.
                                    <a class="link link-primary"
                                       href="{{ $resourceCreateUrl }}">{{ Lang::txt('COM_TOOLS_TODO_CREATE_PAGE') }}...</a>
                                </li>
                            @endif
                            <li class="text-base-content/50">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_TEST_AND_APPROVE') }}
                            </li>
                            <li class="text-base-content/50">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_PUBLISH') }}
                                {{ $hubShortURL }}
                            </li>
                        </ul>
                        @break

                    {{-- State 2: Created --}}
                    @case(2)
                        <p>
                            {{ ucfirst(Lang::txt('COM_TOOLS_THE')) }}
                            {{ $sitename }}
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_AREA_CREATED1') }}
                            <a class="link link-primary"
                               href="{{ $wikiUrl }}">{{ Lang::txt('COM_TOOLS_PROJECT_AREA') }}</a>
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_AREA_CREATED2') }}
                            <a class="link link-primary"
                               href="{{ $devToolsUrl }}">{{ $developer_site }}</a>.
                        </p>
                        <p>{{ Lang::txt('COM_TOOLS_WHATSNEXT_FOLLOW_STEPS') }}:</p>
                        <ul class="list-disc list-inside space-y-1 mt-2">
                            @if (!empty($learn_url))
                                <li>
                                    <a class="link link-primary"
                                       href="{{ $learn_url }}">{{ Lang::txt('COM_TOOLS_LEARN_MORE') }}</a>
                                    {{ Lang::txt('COM_TOOLS_WHATSNEXT_ABOUT_UPLOADING') }}
                                </li>
                            @endif
                            @if (!empty($rappture_url))
                                <li>
                                    {{ Lang::txt('COM_TOOLS_LEARN_MORE') }}
                                    {{ Lang::txt('COM_TOOLS_ABOUT') }}
                                    {{ Lang::txt('COM_TOOLS_THE') }}
                                    <a class="link link-primary"
                                       href="{{ $rappture_url }}">Rappture toolkit</a>.
                                </li>
                            @endif
                            <li>{!! Lang::txt('COM_TOOLS_WHATSNEXT_GIT_INSTRUCTIONS') !!}</li>
                            <li>
                                <a class="link link-primary"
                                   href="{{ $wikiStartUrl }}">{{ Lang::txt('COM_TOOLS_WHATSNEXT_FOLLOW_THESE_INSTRUCTIONS') }}</a>
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_TO_ACCESS_CODE') }}.
                            </li>
                        </ul>

                        <h2 class="text-lg font-bold mt-4">
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_WE_ARE_WAITING') }}
                        </h2>
                        <p>{{ Lang::txt('COM_TOOLS_WHATSNEXT_CREATED_LET_US_KNOW') }}:</p>
                        <ul class="list-disc list-inside space-y-1 mt-2">
                            <li class="text-warning">
                                <span id="Uploaded">
                                    <a class="link link-primary flip"
                                       href="{{ $uploadedUrl }}">{{ Lang::txt('COM_TOOLS_WHATSNEXT_CREATED_CODE_UPLOADED') }}</a>
                                </span>
                            </li>
                        </ul>

                        <h4 class="font-semibold mt-4">
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_REMAINING_STEPS') }}:
                        </h4>
                        <ul class="list-disc list-inside space-y-1 mt-2">
                            <li class="text-success">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_REGISTER') }}
                                {{ $sitename }}
                            </li>
                            <li class="text-base-content/50">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_UPLOAD_COMMIT_FINAL_CODE') }}
                                <span id="Uploaded_">
                                    <a class="link link-primary flip"
                                       href="{{ $uploadedUrl }}">{{ Lang::txt('COM_TOOLS_WHATSNEXT_DONE') }}</a>
                                </span>
                                <br />
                                <a class="link link-primary"
                                   href="{{ $wikiStartUrl }}">{{ Lang::txt('COM_TOOLS_WHATSNEXT_UPLOAD_HOW_DO_I_DO_THIS') }}</a>
                            </li>
                            @if ($resModified == '1')
                                <li class="text-success">
                                    {{ Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE') }}.
                                    <a class="link link-primary"
                                       href="{{ $resourcePreviewUrl2 }}">{{ Lang::txt('COM_TOOLS_PREVIEW') }}</a>
                                    |
                                    <a class="link link-primary"
                                       href="{{ $resourceCreateUrl }}">{{ Lang::txt('COM_TOOLS_TODO_EDIT_PAGE') }}...</a>
                                </li>
                            @else
                                <li class="text-warning">
                                    {{ Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE') }}.
                                    <a class="link link-primary"
                                       href="{{ $resourceCreateUrl }}">{{ Lang::txt('COM_TOOLS_TODO_CREATE_PAGE') }}...</a>
                                </li>
                            @endif
                            <li class="text-base-content/50">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_TEST_AND_APPROVE') }}
                            </li>
                            <li class="text-base-content/50">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_PUBLISH') }}
                                {{ $hubShortURL }}
                            </li>
                        </ul>
                        @break

                    {{-- State 3: Uploaded --}}
                    @case(3)
                        <p>
                            {{ ucfirst(Lang::txt('COM_TOOLS_THE')) }}
                            {{ $sitename }}
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_UPLOADED_TEAM_NEEDS') }}
                            {{ $sitename }}
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_UPLOADED_SO_YOU_CAN_TEST') }}.
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_IT_HAS_BEEN') }}
                            {{ \Components\Tools\Helpers\Html::timeAgo($status['changed']) }}
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_SINCE_LAST_STATUS_CHANGE') }}.
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_YOU_WILL_RECEIVE_RESPONSE') }}
                            3 {{ Lang::txt('COM_TOOLS_DAYS') }}.
                        </p>
                        <h4 class="font-semibold mt-4">
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_REMAINING_STEPS') }}:
                        </h4>
                        <ul class="list-disc list-inside space-y-1 mt-2">
                            <li class="text-success">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_REGISTER') }}
                                {{ $sitename }}
                            </li>
                            <li class="text-success">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_UPLOAD_CODE') }}
                                {{ $developer_site }}
                            </li>
                            @if ($resModified == '1')
                                <li class="text-success">
                                    {{ Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE') }}.
                                    <a class="link link-primary"
                                       href="{{ $resourcePreviewUrl2 }}">{{ Lang::txt('COM_TOOLS_PREVIEW') }}</a>
                                    |
                                    <a class="link link-primary"
                                       href="{{ $resourceCreateUrl }}">{{ Lang::txt('COM_TOOLS_TODO_EDIT_PAGE') }}...</a>
                                </li>
                            @else
                                <li class="text-warning">
                                    {{ Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE') }}.
                                    <a class="link link-primary"
                                       href="{{ $resourceCreateUrl }}">{{ Lang::txt('COM_TOOLS_TODO_CREATE_PAGE') }}...</a>
                                </li>
                            @endif
                            <li class="text-base-content/50">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_TEST_AND_APPROVE') }}
                            </li>
                            <li class="text-base-content/50">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_PUBLISH') }}
                                {{ $hubShortURL }}
                            </li>
                        </ul>
                        @break

                    {{-- State 4: Installed --}}
                    @case(4)
                        <p>
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_INSTALLED_CODE_READY') }}
                            {{ $hubShortURL }}.
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_INSTALLED_PLS_TEST') }}:
                        </p>
                        <ul class="list-disc list-inside space-y-2 mt-2">
                            <li class="text-warning">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_INSTALLED_TEST') }}:
                                <a class="btn btn-sm btn-primary ml-2"
                                   href="{{ $testpath }}">{{ Lang::txt('COM_TOOLS_LAUNCH_TOOL') }}</a>
                            </li>
                            <li class="text-warning">
                                @if ($status['resource_modified'])
                                    <a class="link link-primary"
                                       href="{{ $resourcePreviewUrl2 }}">{{ Lang::txt('COM_TOOLS_TODO_REVIEW_RES_PAGE') }}</a>
                                @else
                                    <a class="link link-primary"
                                       href="{{ $resourceCreateUrl }}">{{ Lang::txt('COM_TOOLS_TODO_CREATE_PAGE') }}</a>
                                    <div class="alert alert-warning mt-1">
                                        {{ Lang::txt('COM_TOOLS_PLEASE') }}
                                        <a class="link"
                                           href="{{ $resourceCreateUrl }}">{{ strtolower(Lang::txt('COM_TOOLS_CREATE')) }}</a>
                                        {{ Lang::txt('COM_TOOLS_WHATSNEXT_PAGE_DESC') }}.
                                    </div>
                                @endif
                            </li>
                        </ul>

                        <h2 class="text-lg font-bold mt-4">
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_WE_ARE_WAITING') }}
                        </h2>
                        <p>{{ Lang::txt('COM_TOOLS_WHATSNEXT_INSTALLED_CLICK_AFTER_TESTING') }}:</p>
                        <ul class="list-disc list-inside space-y-1 mt-2">
                            @if ($status['resource_modified'])
                                <li class="text-warning">
                                    <span id="Approved">
                                        <a class="link link-primary flip"
                                           href="{{ $approvedUrl }}">{{ Lang::txt('COM_TOOLS_WHATSNEXT_INSTALLED_TOOL_WORKS') }}</a>
                                    </span>
                                </li>
                            @else
                                <li class="text-base-content/30">
                                    {{ Lang::txt('COM_TOOLS_WHATSNEXT_INSTALLED_TOOL_WORKS') }}
                                </li>
                            @endif
                        </ul>

                        <p class="mt-2">{{ Lang::txt('COM_TOOLS_WHATSNEXT_INSTALLED_NEED_CHANGES') }}:</p>
                        <ul class="list-disc list-inside space-y-1 mt-2">
                            <li class="text-warning">
                                <span id="Updated">
                                    <a class="link link-primary flip"
                                       href="{{ $updatedUrl }}">{{ Lang::txt('COM_TOOLS_WHATSNEXT_CODE_FIXED_PLS_INSTALL') }}.</a>
                                </span>
                            </li>
                        </ul>

                        <h4 class="font-semibold mt-4">
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_REMAINING_STEPS') }}:
                        </h4>
                        <ul class="list-disc list-inside space-y-1 mt-2">
                            <li class="text-success">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_REGISTER') }}
                                {{ $sitename }}
                            </li>
                            <li class="text-success">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_UPLOAD_CODE') }}
                                {{ $developer_site }}
                            </li>
                            @if ($resModified == '1')
                                <li class="text-success">
                                    {{ Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE') }}.
                                    <a class="link link-primary"
                                       href="{{ $resourcePreviewUrl }}">{{ Lang::txt('COM_TOOLS_PREVIEW') }}</a>
                                    |
                                    <a class="link link-primary"
                                       href="{{ $resourceCreateUrl }}">{{ Lang::txt('COM_TOOLS_TODO_EDIT_PAGE') }}...</a>
                                </li>
                            @else
                                <li class="text-warning">
                                    {{ Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE') }}.
                                    <a class="link link-primary"
                                       href="{{ $resourceCreateUrl }}">{{ Lang::txt('COM_TOOLS_TODO_CREATE_PAGE') }}...</a>
                                </li>
                            @endif
                            <li class="text-warning">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_TEST_AND_APPROVE') }}.
                                @if ($resModified == '1')
                                    <span id="Approved_">
                                        <a class="link link-primary flip"
                                           href="{{ $approvedUrl }}">{{ Lang::txt('COM_TOOLS_WHATSNEXT_I_APPROVE') }}</a>
                                    </span>
                                @else
                                    <span class="text-base-content/30">
                                        {{ Lang::txt('COM_TOOLS_WHATSNEXT_I_APPROVE') }}
                                    </span>
                                @endif
                                |
                                <span id="Updated_">
                                    <a class="link link-primary flip"
                                       href="{{ $updatedUrl }}">{{ Lang::txt('COM_TOOLS_WHATSNEXT_CHANGES_MADE') }}</a>
                                </span>
                            </li>
                            <li class="text-base-content/50">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_PUBLISH') }}
                                {{ $hubShortURL }}
                            </li>
                        </ul>
                        @break

                    {{-- State 5: Updated --}}
                    @case(5)
                        <p>
                            {{ ucfirst(Lang::txt('COM_TOOLS_THE')) }}
                            {{ $sitename }}
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_UPLOADED_TEAM_NEEDS') }}
                            {{ $sitename }}
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_UPLOADED_SO_YOU_CAN_TEST') }}.
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_IT_HAS_BEEN') }}
                            {{ \Components\Tools\Helpers\Html::timeAgo($status['changed']) }}
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_SINCE_LAST_STATUS_CHANGE') }}.
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_YOU_WILL_RECEIVE_RESPONSE') }}
                            3 {{ Lang::txt('COM_TOOLS_DAYS') }}.
                        </p>
                        <h4 class="font-semibold mt-4">
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_REMAINING_STEPS') }}:
                        </h4>
                        <ul class="list-disc list-inside space-y-1 mt-2">
                            <li class="text-success">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_REGISTER') }}
                                {{ $sitename }}
                            </li>
                            <li class="text-success">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_UPLOAD_CODE') }}
                                {{ $developer_site }}
                            </li>
                            @if ($resModified == '1')
                                <li class="text-success">
                                    {{ Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE') }}.
                                    <a class="link link-primary"
                                       href="{{ $resourcePreviewUrl2 }}">{{ Lang::txt('COM_TOOLS_PREVIEW') }}</a>
                                    |
                                    <a class="link link-primary"
                                       href="{{ $resourceCreateUrl }}">{{ Lang::txt('COM_TOOLS_TODO_EDIT_PAGE') }}...</a>
                                </li>
                            @else
                                <li class="text-warning">
                                    {{ Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE') }}.
                                    <a class="link link-primary"
                                       href="{{ $resourceCreateUrl }}">{{ Lang::txt('COM_TOOLS_TODO_CREATE_PAGE') }}...</a>
                                </li>
                            @endif
                            <li class="text-base-content/50">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_TEST_AND_APPROVE') }}
                            </li>
                            <li class="text-base-content/50">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_PUBLISH') }}
                                {{ $hubShortURL }}
                            </li>
                        </ul>
                        @break

                    {{-- State 6: Approved --}}
                    @case(6)
                        <p>
                            {{ ucfirst(Lang::txt('COM_TOOLS_THE')) }}
                            {{ $sitename }}
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_APPROVED_TEAM_WILL_FINALIZE') }}
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_IT_HAS_BEEN') }}
                            {{ \Components\Tools\Helpers\Html::timeAgo($status['changed']) }}
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_APPROVED_SINCE') }}
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_APPROVED_WHAT_WILL_HAPPEN') }}
                            {!! $toolAccess !!}.
                        </p>
                        <p>
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_APPROVED_PLS_CLICK') }}
                            {{ $sitename }}:
                            <br />
                            <a class="link link-primary"
                               href="{{ $toolUrl }}">{{ $toolUrl }}</a>
                        </p>
                        <h4 class="font-semibold mt-4">
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_REMAINING_STEPS') }}:
                        </h4>
                        <ul class="list-disc list-inside space-y-1 mt-2">
                            <li class="text-success">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_REGISTER') }}
                                {{ $sitename }}
                            </li>
                            <li class="text-success">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_UPLOAD_CODE') }}
                                {{ $developer_site }}
                            </li>
                            @if ($resModified == '1')
                                <li class="text-success">
                                    {{ Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE') }}.
                                    <a class="link link-primary"
                                       href="{{ $resourcePreviewUrl }}">{{ Lang::txt('COM_TOOLS_PREVIEW') }}</a>
                                    |
                                    <a class="link link-primary"
                                       href="{{ $resourceCreateUrl }}">{{ Lang::txt('COM_TOOLS_TODO_EDIT_PAGE') }}...</a>
                                </li>
                            @else
                                <li class="text-warning">
                                    {{ Lang::txt('COM_TOOLS_TODO_MAKE_RES_PAGE') }}.
                                    <a class="link link-primary"
                                       href="{{ $resourceCreateUrl }}">{{ Lang::txt('COM_TOOLS_TODO_CREATE_PAGE') }}...</a>
                                </li>
                            @endif
                            <li class="text-success">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_TEST_AND_APPROVE') }}
                            </li>
                            <li class="text-base-content/50">
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_PUBLISH') }}
                                {{ $hubShortURL }}
                                <br />
                                <span id="Updated">
                                    <a class="link link-primary flip"
                                       href="{{ $updatedUrl }}">{{ Lang::txt('COM_TOOLS_WHATSNEXT_WAIT') }}</a>
                                </span>
                            </li>
                        </ul>
                        @break

                    {{-- State 7: Published --}}
                    @case(7)
                        @php
                            $pubToolUrl = Route::url(
                                'index.php?option=' . $option
                                . '&controller=' . $controller
                                . '&app=' . $status['toolname']
                            );
                        @endphp
                        <p>
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_PUBLISHED_MSG') }}:
                            <br />
                            <a class="link link-primary"
                               href="{{ $pubToolUrl }}">{{ $pubToolUrl }}</a>
                        </p>
                        <h3 class="font-bold mt-4">
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_YOUR_OPTIONS') }}:
                        </h3>
                        <ul class="list-disc list-inside space-y-1 mt-2">
                            <li>
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_CHANGES_MADE') }}
                                <span id="Updated">
                                    <a class="link link-primary flip"
                                       href="{{ $updatedUrl }}">{{ Lang::txt('COM_TOOLS_WHATSNEXT_PUBLISHED_PLS_INSTALL') }}</a>
                                </span>
                            </li>
                        </ul>
                        @break

                    {{-- State 8: Retired --}}
                    @case(8)
                        <p>
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_RETIRED_FROM') }}
                            {{ $hubShortURL }}.
                            {{ Lang::txt('COM_TOOLS_CONTACT') }}
                            {{ $sitename }}
                            {{ Lang::txt('COM_TOOLS_CONTACT_SUPPORT_TO_REPUBLISH') }}.
                        </p>
                        <h3 class="font-bold mt-4">
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_YOUR_OPTIONS') }}:
                        </h3>
                        <ul class="list-disc list-inside space-y-1 mt-2">
                            <li>
                                {{ Lang::txt('COM_TOOLS_WHATSNEXT_RETIRED_WANT_REPUBLISH') }}.
                                <span id="Updated">
                                    <a class="link link-primary flip"
                                       href="{{ $updatedUrl }}">{{ Lang::txt('COM_TOOLS_WHATSNEXT_RETIRED_PLS_REPUBLISH') }}</a>
                                </span>
                            </li>
                        </ul>
                        @break

                    {{-- State 9: Abandoned --}}
                    @case(9)
                        <p>
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_ABANDONED_MSG') }}
                            {{ $sitename }}
                            {{ Lang::txt('COM_TOOLS_WHATSNEXT_ABANDONED_CONTACT') }}.
                        </p>
                        @break
                @endswitch
            </div>
        </div>
    </div>{{-- / Right column --}}

</div>{{-- / Two-column grid --}}

</x-page-container>
