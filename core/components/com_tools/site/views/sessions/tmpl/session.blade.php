@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// Determine read-only status
$readOnly = false;
foreach ($shares as $share) {
    if (User::get('username') == $share->viewuser && strtolower($share->readonly) == 'yes') {
        $readOnly = true;
    }
}

$database = App::get('db');
$preferences = new \Components\Tools\Tables\Preferences($database);
$preferences->loadByUser(User::get('id'));

$declared = Request::getWord('viewer');
if ($declared) {
    if (Request::getInt('preferred', 0)) {
        $preferences->set('user_id', User::get('id'));
        $preferences->param()->set('viewer', $declared);
        $preferences->store();
    }
} elseif ($declared = $preferences->param('viewer')) {
    Request::setVar('viewer', $declared);
}

$pluginOutput = Event::trigger('tools.onToolSessionView', [$app, $output, $readOnly]);
$plugins = Event::trigger('tools.onToolSessionIdentify');

$keepUrl = Route::url('index.php?option=com_members&task=myaccount');
$stopUrl = Route::url('index.php?option=' . $option . '&app=' . $toolname . '&task=stop&sess=' . $app->sess . '&return=' . $rtrn);
$unshareUrl = Route::url('index.php?option=' . $option . '&app=' . $toolname . '&task=unshare&sess=' . $app->sess . '&return=' . $rtrn);
$optionsUrl = Route::url('index.php?option=' . $option . '&app=' . $toolname . '&task=session&sess=' . $app->sess);
$shareFormAction = Route::url('index.php?option=' . $option . '&app=' . $toolname . '&task=session&sess=' . $app->sess);
$reinvokeAction = Route::url('index.php?option=' . $option . '&app=' . $toolname . '&task=reinvoke&sess=' . $app->sess);
$shareReturn = base64_encode(Route::url('index.php?option=' . $option . '&app=' . $toolname . '&task=session&sess=' . $app->sess));
@endphp

<div id="session">
@if(!$app->sess)
    <div role="alert" class="alert alert-error">
        <span>{!! implode('<br />', $output) !!}</span>
    </div>
@else
    @if($readOnly)
        <div role="alert" class="alert alert-warning mb-4">
            <span>{{ Lang::txt('COM_TOOLS_WARNING_SESSION_READ_ONLY') }}</span>
        </div>
    @endif

    {!! implode("\n", Event::trigger('tools.onToolSessionViewBefore', [$app, $output, $readOnly])) !!}

    <div id="app-wrap" style="width: {{ $output->width }}px;">
        <div id="app-header">
            @php
                $h2Class = 'session-title item:name id:' . $app->sess;
                if (is_object($app->owns)) {
                    $h2Class .= ' editable';
                }
            @endphp
            <h2 id="session-title" class="{{ $h2Class }}" rel="{{ $app->sess }}">
                {{ $app->caption }}
            </h2>

            @if($app->sess)
                <ul class="app-toolbar" id="session-options">
                    <li>
                        <a id="app-btn-keep" class="keep" href="{{ $keepUrl }}">
                            <span>{{ Lang::txt('COM_TOOLS_KEEP_FOR_LATER') }}</span>
                        </a>
                    </li>
                    @if($app->owns)
                        <li>
                            <a id="app-btn-close" class="terminate sessiontips"
                               href="{{ $stopUrl }}"
                               title="{{ Lang::txt('COM_TOOLS_TERMINATE_WARNING') }}">
                                <span>{{ Lang::txt('COM_TOOLS_TERMINATE') }}</span>
                            </a>
                        </li>
                    @else
                        <li>
                            <a id="app-btn-close" class="terminate sessiontips"
                               href="{{ $unshareUrl }}"
                               title="{{ Lang::txt('COM_TOOLS_TERMINATE_WARNING') }}">
                                <span>{{ Lang::txt('COM_TOOLS_STOP_SHARING') }}</span>
                            </a>
                        </li>
                    @endif
                    @if(count($plugins) > 1)
                        <li>
                            <a id="app-btn-options" class="options" href="{{ $optionsUrl }}">
                                <span>{{ Lang::txt('COM_TOOLS_SESSION_OPTIONS') }}</span>
                            </a>
                        </li>
                    @endif
                </ul>
            @endif
        </div>

        @if(count($plugins) > 1)
            <div id="app-options">
                <form method="get" action="{{ $optionsUrl }}">
                    <fieldset>
                        @php
                            $viewer = $declared ?: null;
                            if (isset($output->rendered)) {
                                $viewer = $output->rendered;
                            }
                        @endphp
                        {{ $viewer
                            ? Lang::txt('COM_TOOLS_SESSION_USING_VIEWER', Lang::txt('PLG_TOOLS_' . $viewer . '_TITLE'))
                            : Lang::txt('COM_TOOLS_UNKNOWN_VIEWER') }}

                        <span class="input-wrap">
                            <label for="app-viewer">{{ Lang::txt('COM_TOOLS_SESSION_VIEWER_CHANGE') }}</label>
                            <select name="viewer" id="app-viewer" class="select select-bordered select-sm">
                                @foreach($plugins as $plugin)
                                    @if($viewer != $plugin->name)
                                        <option value="{{ $plugin->name }}">{{ $plugin->title }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </span>

                        <span class="input-wrap">
                            <input type="checkbox" name="preferred" id="app-viewer-preferred"
                                   value="1" class="checkbox checkbox-sm" />
                            <label for="app-viewer-preferred">{{ Lang::txt('Use for future sessions.') }}</label>
                        </span>

                        <span class="input-wrap">
                            <input type="submit" value="{{ Lang::txt('COM_TOOLS_APPLY') }}"
                                   class="btn btn-primary btn-sm" />
                        </span>
                        <input type="hidden" name="sess" value="{{ $app->sess }}" />
                    </fieldset>
                </form>
            </div>
        @endif

        <div id="app-content" tabindex="1"
             class="{{ $readOnly ? 'view-only' : '' }}"
             style="width: {{ $output->width }}px; height: {{ $output->height }}px">
            <noscript>
                <div role="alert" class="alert alert-warning">
                    <span>{{ Lang::txt('COM_TOOLS_ERROR_NOSCRIPT') }}</span>
                </div>
            </noscript>
            <input type="hidden" id="app-orig-width" name="apporigwidth"
                   value="{{ e($output->width) }}" />
            <input type="hidden" id="app-orig-height" name="apporigheight"
                   value="{{ e($output->height) }}" />
            @php
                $renderedOutput = implode("\n", $pluginOutput);
                if (!trim($renderedOutput)) {
                    $renderedOutput = '<div role="alert" class="alert alert-error"><span>' . Lang::txt('COM_TOOLS_ERROR_NOVIEWER') . '</span></div>';
                }
            @endphp
            {!! $renderedOutput !!}
        </div>

        <div id="app-footer">
            @if($config->get('show_storage'))
                {!! $__view->loadTemplate('diskusage_embed', 'storage') !!}
            @endif
        </div>

        @if($zone->config('zones') && $zone->exists())
            <div id="app-zone">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        @if($logo = $zone->logo())
                            <img src="{{ $logo }}" alt="" class="mb-2" />
                        @endif
                        <p>{{ $zone->get('description', Lang::txt('COM_TOOLS_POWERED_BY_MIRROR', $zone->get('title', $zone->get('zone')))) }}</p>
                    </div>
                    <div>
                        <form name="share" id="app-zone" method="post" action="{{ $reinvokeAction }}">
                            <p>{!! Lang::txt('COM_TOOLS_ZONE_WARNING_CHANGE') !!}</p>
                            <label for="field-zone">
                                {{ Lang::txt('COM_TOOLS_ZONE_RELAUNCH') }}
                                <select name="zone" id="field-zone" class="select select-bordered select-sm">
                                    <option value="">{{ Lang::txt('COM_TOOLS_SELECT') }}</option>
                                    @php
                                        $zoneList = $middleware->zones('list', ['state' => 'up', 'id' => $middleware->get('allowed')]);
                                    @endphp
                                    @foreach($zoneList as $z)
                                        @if($z->get('id') != $zone->get('id'))
                                            <option value="{{ $z->get('id') }}">{{ e($z->get('title', $z->get('zone'))) }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </label>
                            <input type="submit" value="Go" class="btn btn-primary btn-sm" />
                        </form>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {!! implode("\n", Event::trigger('tools.onToolSessionViewAfter', [$app, $output, $readOnly])) !!}

    @if($config->get('shareable', 0))
        <form name="share" id="app-share" method="post" action="{{ $shareFormAction }}">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-6">
                @if(is_object($app->owns))
                    <div class="lg:col-span-2">
                        <div class="card bg-base-100 shadow-sm">
                            <div class="card-body">
                                <h3 class="card-title">{{ Lang::txt('COM_TOOLS_SHARE_SESSION') }}</h3>

                                <input type="hidden" name="option" value="{{ e($option) }}" />
                                <input type="hidden" name="controller" value="{{ e($controller) }}" />
                                <input type="hidden" name="task" value="share" />
                                <input type="hidden" name="sess" value="{{ e($app->sess) }}" />
                                <input type="hidden" name="app" value="{{ e($toolname) }}" />
                                <input type="hidden" name="return" value="{{ $shareReturn }}" />

                                <div class="space-y-4">
                                    <div>
                                        <label for="field-username" class="label">
                                            <span class="label-text">{{ Lang::txt('COM_TOOLS_SHARE_SESSION_WITH') }}</span>
                                        </label>
                                        @php
                                            $mc = Event::trigger('hubzero.onGetMultiEntry', [['members', 'username', 'acmembers']]);
                                        @endphp
                                        @if(count($mc) > 0)
                                            <p class="text-sm opacity-70 mb-1">{{ Lang::txt('COM_TOOLS_SHARE_SESSION_HINT_AUTOCOMPLETE') }}</p>
                                            {!! $mc[0] !!}
                                        @else
                                            <p class="text-sm opacity-70 mb-1">{{ Lang::txt('COM_TOOLS_SHARE_SESSION_HINT') }}</p>
                                            <input type="text" name="username" id="field-username"
                                                   class="input input-bordered w-full" value="" />
                                        @endif
                                    </div>

                                    <div>
                                        <label for="group" class="label">
                                            <span class="label-text">{{ Lang::txt('COM_TOOLS_SHARE_SESSION_WITH_GROUP') }}</span>
                                        </label>
                                        <select name="group" id="group" class="select select-bordered w-full">
                                            <option value="">- Select Group &mdash;</option>
                                            @if(!empty($mygroups))
                                                @foreach($mygroups as $group)
                                                    <option value="{{ $group->gidNumber }}">{{ $group->description }}</option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>

                                    <label class="flex items-center gap-2 cursor-pointer" for="confirm-share">
                                        <input type="checkbox" value="Yes" name="confirm"
                                               id="confirm-share" class="checkbox" />
                                        <span>{{ Lang::txt('COM_TOOLS_SHARE_SESSION_CONFIRM') }}</span>
                                    </label>

                                    <button type="submit" id="share-btn" class="btn btn-primary">
                                        {{ Lang::txt('COM_TOOLS_SHARE') }}
                                    </button>

                                    <p class="text-sm opacity-70">{!! Lang::txt('COM_TOOLS_SHARE_SESSION_NOTES') !!}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <div>
                    <div class="card bg-base-100 shadow-sm">
                        <div class="card-body">
                            <h3 class="card-title text-base">{{ Lang::txt('COM_TOOLS_SESSION_SHARED_WITH') }}</h3>
                            @if(count($shares) <= 1)
                                <p class="opacity-70">{{ Lang::txt('COM_TOOLS_SHARE_SESSION_NONE') }}</p>
                            @else
                                <div class="space-y-3">
                                    @foreach($shares as $row)
                                        @if($row->viewuser != User::get('username'))
                                            @php
                                                $user = User::getInstance($row->viewuser);
                                                $id = ($user->get('id') < 0) ? 'n' . -$user->get('id') : $user->get('id');
                                                $memberUrl = Route::url('index.php?option=com_members&id=' . $id);
                                                $removeUrl = Route::url('index.php?option=' . $option . '&app=' . $toolname . '&task=unshare&sess=' . $app->sess . '&username=' . $row->viewuser . '&return=' . $rtrn);
                                            @endphp
                                            <div class="flex items-center gap-3">
                                                <img width="40" height="40" src="{{ $user->picture() }}"
                                                     alt="{{ e(stripslashes($user->get('name'))) }}"
                                                     class="rounded-full" />
                                                <div class="flex-1 min-w-0">
                                                    <a href="{{ $memberUrl }}" class="link font-medium">
                                                        {{ e(stripslashes($user->get('name'))) }}
                                                    </a>
                                                    <div class="text-sm opacity-70">{{ e(stripslashes($user->get('username'))) }}</div>
                                                </div>
                                                @if(is_object($app->owns))
                                                    <div class="flex items-center gap-2">
                                                        @if(strtolower($row->readonly) == 'yes')
                                                            <span class="badge badge-ghost">{{ Lang::txt('COM_TOOLS_SESSION_READ_ONLY') }}</span>
                                                        @endif
                                                        <a href="{{ $removeUrl }}" class="btn btn-ghost btn-xs text-error"
                                                           title="{{ Lang::txt('COM_TOOLS_SESSION_SHARED_REMOVE_USER') }}">
                                                            {{ Lang::txt('COM_TOOLS_SESSION_SHARED_REMOVE_USER') }}
                                                        </a>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @endif

    @if($config->get('access-manage-session'))
        <p id="app-manager" class="text-sm opacity-70 mt-4">
            {!! Lang::txt('COM_TOOLS_SESSION_ADMIN_INFO', $app->username, $app->ip, $app->sess) !!}
        </p>
    @endif

    @php
        $mwOutput = Event::trigger('mw.onSessionView', [$option, $toolname, $app->sess]);
    @endphp
    @if(count($mwOutput) > 0)
        <div id="app-info" class="mt-6">
            <h2 class="text-lg font-semibold mb-4">{{ Lang::txt('COM_TOOLS_SESSION_APP_INFO') }}</h2>
            <div id="app-info-content">
                @foreach($mwOutput as $out)
                    {!! $out !!}
                @endforeach
            </div>
        </div>
    @endif
@endif
</div>
