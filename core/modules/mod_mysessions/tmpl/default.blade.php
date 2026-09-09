{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    $base = rtrim(Request::base(true), '/');
@endphp

<div>
    @if (count($sessions) > 0)
        <ul class="list bg-base-100 rounded-box">
            @foreach ($sessions as $k => $session)
                @php
                    $bits = explode('_', $session->appname);
                    $bit = (count($bits) > 1) ? array_pop($bits) : '';
                    $appname = implode('_', $bits);

                    $resumeLink = Route::url('index.php?option=com_tools&task=session&sess=' . $session->sessnum . '&app=' . $appname);
                    $terminateLink = Route::url('index.php?option=com_tools&task=stop&sess=' . $session->sessnum . '&app=' . $appname);
                    $disconnectLink = Route::url('index.php?option=com_tools&task=unshare&sess=' . $session->sessnum . '&app=' . $appname);
                    $snapshot = $base . '/api/$tools/screenshot?sessionid=' . $session->sessnum . '&notfound=1';
                @endphp
                <li class="list-row">
                    @if ($params->get('show_screenshots', 1))
                        <div class="list-col">
                            <img class="w-16 h-12 object-cover rounded" src="{{ $snapshot }}" alt="" />
                        </div>
                    @endif
                    <div class="list-col grow">
                        <div class="font-semibold">{{ $session->sessname }}</div>
                        <span class="text-xs text-base-content/60">
                            <span>{{ Lang::txt('MOD_MYSESSIONS_LAST_ACCESSED') }}</span>
                            {{ date("F d, Y @ g:ia", strtotime($session->accesstime)) }}
                        </span>

                        @if (User::get('username') != $session->username)
                            @php
                                $ownerName = $session->username;
                                $ownerUser = User::getInstance($session->username);
                                if ($ownerUser->get('id')) {
                                    $ownerName = $ownerUser->get('name');
                                    if (in_array($ownerUser->get('access'), User::getAuthorisedViewLevels())) {
                                        $ownerName = '<a href="' . Route::url($ownerUser->link()) . '">' . $ownerName . '</a>';
                                    }
                                }
                            @endphp
                            <span class="text-xs text-base-content/60">
                                <span>{{ Lang::txt('MOD_MYSESSIONS_SESSION_OWNER') }}</span>
                                {!! $ownerName !!}
                            </span>
                        @endif
                    </div>
                    <div class="list-col">
                        <div class="flex gap-1">
                            <a class="btn btn-sm btn-outline btn-primary"
                                href="{{ $resumeLink }}"
                                $title="{{ Lang::txt('MOD_MYSESSIONS_RESUME_TITLE') }}"
                            >{{ ucfirst(Lang::txt('MOD_MYSESSIONS_RESUME')) }}</a>

                            @if (User::get('username') == $session->username)
                                <a class="btn btn-sm btn-outline btn-error"
                                    href="{{ $terminateLink }}"
                                    $title="{{ Lang::txt('MOD_MYSESSIONS_TERMINATE_TITLE') }}"
                                >{{ ucfirst(Lang::txt('MOD_MYSESSIONS_TERMINATE')) }}</a>
                            @else
                                <a class="btn btn-sm btn-outline btn-warning"
                                    href="{{ $disconnectLink }}"
                                    $title="{{ Lang::txt('MOD_MYSESSIONS_DISCONNECT_TITLE') }}"
                                >{{ ucfirst(Lang::txt('MOD_MYSESSIONS_DISCONNECT')) }}</a>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>
    @else
        <p class="text-base-content/60">{{ Lang::txt('MOD_MYSESSIONS_NONE') }}</p>
    @endif
</div>

@if ($params->get('show_storage', 1))
    <div class="mt-3">
        <span class="text-sm">{{ Lang::txt('MOD_MYSESSIONS_STORAGE') }}
            (<a href="{{ Route::url('index.php?option=com_tools&task=storage') }}">{{ Lang::txt('MOD_MYSESSIONS_MANAGE') }}</a>)
        </span>
        @php
            $diskUsage = \Components\Tools\Helpers\Utils::getDiskUsage(User::get('username'));
        @endphp
        @if (!is_array($diskUsage) || !isset($diskUsage['space']))
            <div role="alert" class="alert alert-error">{{ Lang::txt('MOD_MYSESSIONS_ERROR_RETRIEVING_STORAGE') }}</div>
        @elseif (isset($diskUsage['softspace']) && $diskUsage['softspace'] == 0)
            <div role="alert" class="alert alert-info">{{ Lang::txt('MOD_MYSESSIONS_NO_QUOTA') }}</div>
        @else
            @php
                bcscale(6);
                $total = $diskUsage['softspace'] / 1024000000;
                $val = ($diskUsage['softspace'] > 0) ? bcdiv($diskUsage['space'], $diskUsage['softspace']) : 0;
                $percent = round($val * 100);
                $percent = ($percent > 100) ? 100 : $percent;
                $amount = ($percent > 100) ? 100 : $percent;
                $progressClass = ($percent < 50) ? 'progress-success' : 'progress-error';
            @endphp

            <progress class="progress {{ $progressClass }} w-full" value="{{ $amount }}" max="100"></progress>
            <span class="text-xs text-base-content/60">{{ $amount }}% of {{ $total }}GB</span>

            @if ($percent >= 100)
                <div role="alert" class="alert alert-warning mt-1">
                    {{ Lang::txt('MOD_MYSESSIONS_MAXIMUM_STORAGE') }}
                </div>
            @endif
        @endif
    </div>
@endif
