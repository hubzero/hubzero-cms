@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
@endphp

@php
    $actionUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=status');
@endphp

<x-page-container :title="$title">
    <form action="{{ $actionUrl }}">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Last Session</h3>
                    <div class="text-sm space-y-1">
                        <div>Session Number: {{ $lastsession->sessnum }}</div>
                        <div>Username: {{ $lastsession->username }}</div>
                        <div>Started: {{ $lastsession->start }}</div>
                        <div>Last Accessed: {{ $lastsession->accesstime }}</div>
                        <div>Tool Alias: {{ $lastsession->sessname }}</div>
                        <div>Tool Revision: {{ $lastsession->appname }}</div>
                    </div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Current Sessions</h3>
                    <div class="text-3xl font-bold">{{ $sessions }}</div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Used Displays</h3>
                    <div class="text-3xl font-bold">{{ $used_displays }}</div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Ready Displays</h3>
                    <div class="text-3xl font-bold">{{ $ready_displays }}</div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Absent Displays</h3>
                    <div class="text-3xl font-bold">{{ $absent_displays }}</div>
                </div>
            </div>

            <div class="card bg-base-100 shadow-sm">
                <div class="card-body">
                    <h3 class="card-title text-base">Broken Displays</h3>
                    <div class="text-3xl font-bold">{{ $broken_displays }}</div>
                </div>
            </div>
        </div>
    </form>
</x-page-container>
