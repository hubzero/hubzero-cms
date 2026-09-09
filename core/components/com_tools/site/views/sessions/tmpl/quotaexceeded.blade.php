@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
@endphp

@php
    $sectionHidden = (!$config->get('access-manage-session') && $active == 'all');
@endphp

<x-page-container :title="Lang::txt('COM_TOOLS_QUOTAEXCEEDED')">
    <div class="{{ $sectionHidden ? 'hidden' : '' }}">
        <div role="alert" class="alert alert-warning mb-6">
            <span>{{ Lang::txt('COM_TOOLS_ERROR_QUOTAEXCEEDED') }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table table-zebra">
                <thead>
                    <tr>
                        <th>{{ Lang::txt('COM_TOOLS_MYSESSIONS_COL_SESSION') }}</th>
                        <th>{{ Lang::txt('COM_TOOLS_MYSESSIONS_COL_STARTED') }}</th>
                        <th>{{ Lang::txt('COM_TOOLS_MYSESSIONS_COL_LAST_ACCESSED') }}</th>
                        <th>{{ Lang::txt('COM_TOOLS_MYSESSIONS_COL_OPTION') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @if($sessions)
                        @foreach($sessions as $session)
                            @php
                                $resumeUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=session&app=' . $session->appname . '&sess=' . $session->sessnum);
                            @endphp
                            <tr>
                                <td>
                                    <a href="{{ $resumeUrl }}" class="link"
                                       title="{{ Lang::txt('COM_TOOLS_RESUME_TITLE') }}">
                                        {{ $session->sessname }}
                                    </a>
                                </td>
                                <td>{{ $session->start }}</td>
                                <td>{{ $session->accesstime }}</td>
                                <td>
                                    @if(User::get('username') == $session->username)
                                        @php
                                            $stopUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=stop&app=' . $session->appname . '&sess=' . $session->sessnum);
                                        @endphp
                                        <a href="{{ $stopUrl }}" class="btn btn-error btn-sm"
                                           title="{{ Lang::txt('COM_TOOLS_TERMINATE_TITLE') }}">
                                            {{ Lang::txt('COM_TOOLS_TERMINATE') }}
                                        </a>
                                    @else
                                        @php
                                            $unshareUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=unshare&app=' . $session->appname . '&sess=' . $session->sessnum);
                                        @endphp
                                        <a href="{{ $unshareUrl }}" class="btn btn-warning btn-sm"
                                           title="{{ Lang::txt('COM_TOOLS_DISCONNECT_TITLE') }}">
                                            {{ Lang::txt('COM_TOOLS_DISCONNECT') }}
                                        </a>
                                        <span class="text-sm opacity-70">{{ Lang::txt('COM_TOOLS_MY_SESSIONS_OWNER') }}: {{ $session->username }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</x-page-container>
