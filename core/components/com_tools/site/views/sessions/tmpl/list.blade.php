@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
@endphp

@php
    $newSession = Request::getString(
        'REQUEST_URI',
        Route::url('index.php?option=' . $option . '&task=invoke&app=' . $app->toolname . '&version=' . $app->version),
        'server'
    );
    $newSession .= strstr($newSession, '?') ? '&newinstance=1' : '?newinstance=1';
@endphp

<x-page-container :title="Lang::txt('COM_TOOLS_MYSESSIONS')">
    <div role="alert" class="alert alert-info mb-6">
        <span>{{ Lang::txt('COM_TOOLS_MYSESSIONS_WARNING_INSTANCE_RUNNING') }}</span>
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
            <tfoot>
                <tr>
                    <td colspan="4">
                        <a href="{{ $newSession }}" class="btn btn-primary btn-sm">
                            {{ Lang::txt('COM_TOOLS_MYSESSIONS_START_NEW') }}
                        </a>
                    </td>
                </tr>
            </tfoot>
            <tbody>
                @if($sessions)
                    @foreach($sessions as $session)
                        @php
                            $sessionUrl = Route::url('index.php?option=' . $option . '&task=session&app=' . $session->appname . '&sess=' . $session->sessnum);
                            $stopUrl = Route::url('index.php?option=' . $option . '&task=stop&app=' . $session->appname . '&sess=' . $session->sessnum);
                            $unshareUrl = Route::url('index.php?option=' . $option . '&task=unshare&app=' . $session->appname . '&sess=' . $session->sessnum);
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ $sessionUrl }}" class="link"
                                   title="{{ Lang::txt('COM_TOOLS_RESUME_TITLE') }}">
                                    {{ $session->sessname }}
                                </a>
                            </td>
                            <td>{{ $session->start }}</td>
                            <td>{{ $session->accesstime }}</td>
                            <td>
                                @if(User::get('username') == $session->username)
                                    <a href="{{ $stopUrl }}" class="btn btn-error btn-sm"
                                       title="{{ Lang::txt('COM_TOOLS_TERMINATE_TITLE') }}">
                                        {{ Lang::txt('COM_TOOLS_TERMINATE') }}
                                    </a>
                                @else
                                    <a href="{{ $unshareUrl }}" class="btn btn-warning btn-sm"
                                       title="{{ Lang::txt('COM_TOOLS_DISCONNECT_TITLE') }}">
                                        {{ Lang::txt('COM_TOOLS_DISCONNECT') }}
                                    </a>
                                    <span class="text-sm opacity-70">
                                        {{ Lang::txt('COM_TOOLS_MY_SESSIONS_OWNER') }}: {{ $session->username }}
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</x-page-container>
