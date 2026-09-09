{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\App;
    use Hubzero\Facades\Date;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\User;

    $__view->js('flot/jquery.flot.min.js', 'system')
        ->js('flot/jquery.flot.time.min.js', 'system')
        ->js('flot/jquery.flot.pie.min.js', 'system')
        ->js('flot/jquery.flot.resize.js', 'system');

    $__view->css();
    $__view->js('stats.js');

    $base = rtrim(Request::base(true), '/');

    $gidNumber = 0;
    if ($groupObj = \Hubzero\User\Group::getInstance($group)) {
        $gidNumber = $groupObj->get('gidNumber');
    }

    // Query resolutions
    $database = App::get('db');
    $sql = "SELECT status
            FROM `#__support_tickets`
            WHERE open=0
            AND type=" . $database->quote($type);
    if ($group == '_none_') {
        $sql .= " AND group_id=0";
    } elseif ($group) {
        $sql .= " AND `group_id`=" . $database->quote($gidNumber);
    }
    $sql .= " ORDER BY status ASC";
    $database->setQuery($sql);
    $resolutions = $database->loadObjectList();

    $resTotal = count($resolutions);
    $res = [];
    foreach ($resolutions as $resolution) {
        if (!isset($res[$resolution->status])) {
            $res[$resolution->status] = 1;
        } else {
            $res[$resolution->status]++;
        }
    }

    // Query severities
    $sql = "SELECT severity
            FROM `#__support_tickets`
            WHERE type=" . $database->quote($type);
    if ($group == '_none_') {
        $sql .= " AND group_id=0";
    } elseif ($group) {
        $sql .= " AND `group_id`=" . $database->quote($gidNumber);
    }
    $sql .= " ORDER BY severity ASC";
    $database->setQuery($sql);
    $severitiesData = $database->loadObjectList();

    $sevTotal = count($severitiesData);
    $sev = [];
    foreach ($severitiesData as $severity) {
        if (!isset($sev[$severity->severity])) {
            $sev[$severity->severity] = 1;
        } else {
            $sev[$severity->severity]++;
        }
    }

    // Build opened/closed chart data
    $top = 0;

    $closeddata = '';
    if ($closedmonths) {
        $c = [];
        foreach ($closedmonths as $yr => $data) {
            foreach ($data as $k => $v) {
                $top = ($v > $top) ? $v : $top;
                $monthPad = \Hubzero\Utility\Str::pad(($k - 1), 2);
                $c[] = '[' . Date::of($yr . '-' . $monthPad . '-01')->toUnix() . ',' . $v . ']';
            }
        }
        $closeddata = implode(',', $c);
    }

    $openeddata = '';
    if ($openedmonths) {
        $o = [];
        foreach ($openedmonths as $yr => $data) {
            foreach ($data as $k => $v) {
                $top = ($v > $top) ? $v : $top;
                $monthPad = \Hubzero\Utility\Str::pad(($k - 1), 2);
                $o[] = '[' . Date::of($yr . '-' . $monthPad . '-01')->toUnix() . ',' . $v . ']';
            }
        }
        $openeddata = implode(',', $o);
    }

    // Colors for pie charts
    $colors = [
        '#656565', '#7c94c2', '#c67c6b', '#d8aa65', '#5f9c63',
        '#9b569b', '#5ca1b6', '#ce89a0', '#86a558', '#b57676',
        '#738aa0', '#dfe6ef', '#93ACCA', '#83ae92', '#4a6f81',
        '#dfbd5b', '#e88f87', '#CFCFAB', '#598ba4', '#82b5c6',
        '#99B1A5',
    ];

    // Build severity pie data
    $severities = \Components\Support\Helpers\Utilities::getSeverities(
        $config->get('severities')
    );

    $severtes = [];
    foreach ($severities as $k => $sname) {
        $key = (isset($sev[$sname])) ? (string) $sev[$sname] : '0';
        if (isset($severtes[$key])) {
            $key .= '.' . $k;
        }
        $severtes[$key] = $sname;
    }
    krsort($severtes);

    $sevChartData = [];
    $sevI = 0;
    foreach ($severtes as $sname) {
        $pct = (isset($sev[$sname]) && $sevTotal > 0)
            ? round(($sev[$sname] / $sevTotal) * 100, 2)
            : 0;
        $sevChartData[] = '{"label": "' . $__view->escape(addslashes($sname))
            . '", "data": ' . $pct . ', "color": "' . $colors[$sevI] . '"}';
        $sevI++;
    }

    // Build resolution pie data
    $resolutionModels = \Components\Support\Models\Status::all()
        ->whereEquals('open', 0)
        ->rows();

    $resolutns = [];
    foreach ($resolutionModels as $k => $resModel) {
        $key = (isset($res[$resModel->id])) ? (string) $res[$resModel->id] : '0';
        if (isset($resolutns[$key])) {
            $key .= '.' . $k;
        }
        $resolutns[$key] = $resModel;
    }
    krsort($resolutns);

    $resChartData = [];
    $resI = 0;
    foreach ($resolutns as $resModel) {
        $pct = (isset($res[$resModel->id]) && $resTotal > 0)
            ? round(($res[$resModel->id] / $resTotal) * 100, 2)
            : 0;
        $resChartData[] = '{"label": "' . $__view->escape($resModel->title)
            . '", "data": ' . $pct . ', "color": "' . $colors[$resI] . '"}';
        $resI++;
    }
    // Add "none" resolution
    $noresLabel = Lang::txt('COM_SUPPORT_STATS_RESOLUTION_NONE');
    $noresData = (isset($res[0]) && $resTotal > 0) ? $res[0] / $resTotal : 0;
    $resChartData[] = '{"label": "' . $noresLabel . '", "data": '
        . $noresData . ', "color": "' . $colors[$resI] . '"}';

    // Lifetime calculation
    $lifetime = \Components\Support\Helpers\Utilities::calculateAverageLife($closedTickets);

    // URLs
    $browseUrl = Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller . '&task=display'
    );
    $newUrl = Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller . '&task=new'
    );
    $statsUrl = Route::url('index.php?option=com_support&task=stats');
    $autoUrl = Route::url('index.php?option=com_support&task=stats&type=automatic');
    $formAction = Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller . '&task=stats'
    );

    $optDataId = $option . '-data-openedclosed';
    $sevDataId = $option . '-data-severity';
    $resDataId = $option . '-data-resolution';
@endphp

<x-page-container :title="$title">
    {{-- Action buttons --}}
    <div class="flex gap-2 mb-6">
        <a class="btn btn-outline btn-sm" href="{{ $browseUrl }}">
            {{ Lang::txt('COM_SUPPORT_TICKETS') }}
        </a>
        <a class="btn btn-primary btn-sm" href="{{ $newUrl }}">
            {{ Lang::txt('COM_SUPPORT_NEW_TICKET') }}
        </a>
    </div>

    {{-- Sub-menu tabs --}}
    <div role="tablist" class="tabs tabs-border mb-6">
        <a
            role="tab"
            class="tab {{ $type == 0 ? 'tab-active' : '' }}"
            href="{{ $statsUrl }}"
        >{{ Lang::txt('COM_SUPPORT_TICKETS_SUBMITTED') }}</a>
        <a
            role="tab"
            class="tab {{ $type == 1 ? 'tab-active' : '' }}"
            href="{{ $autoUrl }}"
        >{{ Lang::txt('COM_SUPPORT_TICKETS_AUTOMATIC') }}</a>
    </div>

    <form action="{{ $formAction }}" method="get" enctype="multipart/form-data">
        <section id="ticket-stats">
            {{-- Filter form --}}
            <div class="card bg-base-100 shadow-sm mb-6">
                <div class="card-body">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                        <div class="form-control">
                            <label class="label" for="start-date">
                                <span class="label-text">
                                    {{ Lang::txt('COM_SUPPORT_DATE_FROM') }}
                                </span>
                            </label>
                            <input
                                type="text"
                                class="input input-bordered w-full"
                                name="start"
                                id="start-date"
                                value="{{ $__view->escape($start) }}"
                            />
                        </div>
                        <div class="form-control">
                            <label class="label" for="end-date">
                                <span class="label-text">
                                    {{ Lang::txt('COM_SUPPORT_DATE_TO') }}
                                </span>
                            </label>
                            <input
                                type="text"
                                class="input input-bordered w-full"
                                name="end"
                                id="end-date"
                                value="{{ $__view->escape($end) }}"
                            />
                        </div>
                        <div class="form-control">
                            <label class="label" for="ticket-group">
                                <span class="label-text">
                                    {{ Lang::txt('COM_SUPPORT_FILTER_GROUP') }}
                                </span>
                            </label>
                            <select
                                name="group"
                                id="ticket-group"
                                class="select select-bordered w-full"
                            >
                                <option
                                    value=""
                                    {{ !$group ? 'selected' : '' }}
                                >{{ Lang::txt('COM_SUPPORT_ALL') }}</option>
                                <option
                                    value="_none_"
                                    {{ $group == '_none_' ? 'selected' : '' }}
                                >{{ Lang::txt('COM_SUPPORT_NONE') }}</option>
                                @if ($groups)
                                    @foreach ($groups as $grp)
                                        @php
                                            $grpDisp = $grp->description
                                                ? stripslashes($__view->escape($grp->description))
                                                : $__view->escape($grp->cn);
                                        @endphp
                                        <option
                                            value="{{ $grp->cn }}"
                                            {{ $group == $grp->cn ? 'selected' : '' }}
                                        >{{ $grpDisp }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-primary">
                                {{ Lang::txt('Go') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Opened/Closed time series chart --}}
            <div class="card bg-base-100 shadow-sm mb-6">
                <div class="card-body">
                    <div
                        id="container"
                        class="stats-tickets-chart"
                        style="min-height: 300px;"
                        data-datasets="{{ $optDataId }}"
                    ></div>
                    <script type="application/json" id="{{ $optDataId }}">
                        {
                            "datasets": [
                                {
                                    "color": "#AA4643",
                                    "label": "{!! Lang::txt('COM_SUPPORT_OPENED') !!}",
                                    "data": [{!! $openeddata !!}]
                                },
                                {
                                    "color": "#656565",
                                    "label": "{!! Lang::txt('COM_SUPPORT_CLOSED') !!}",
                                    "data": [{!! $closeddata !!}]
                                }
                            ]
                        }
                    </script>
                </div>
            </div>

            {{-- Overview stats --}}
            <div class="card bg-base-100 shadow-sm mb-6">
                <div class="card-body">
                    <div class="stats stats-horizontal w-full shadow-none">
                        <div class="stat">
                            <div class="stat-title">
                                {{ Lang::txt('COM_SUPPORT_STATS_OPENED') }}
                            </div>
                            <div class="stat-value text-2xl">
                                {{ $opened['open'] }}
                            </div>
                        </div>
                        <div class="stat">
                            <div class="stat-title">
                                {{ Lang::txt('COM_SUPPORT_STATS_CLOSED') }}
                            </div>
                            <div class="stat-value text-2xl">
                                {{ $opened['closed'] }}
                            </div>
                        </div>
                        <div class="stat">
                            <div class="stat-title">
                                {{ Lang::txt('COM_SUPPORT_STATS_AVERAGE_LIFETIME') }}
                            </div>
                            <div class="stat-value text-2xl">
                                {{ $lifetime[0] ?? 0 }}
                                <span class="text-sm font-normal">
                                    {{ Lang::txt('COM_SUPPORT_STATS_DAYS') }}
                                </span>
                                {{ $lifetime[1] ?? 0 }}
                                <span class="text-sm font-normal">
                                    {{ Lang::txt('COM_SUPPORT_STATS_HOURS') }}
                                </span>
                                {{ $lifetime[2] ?? 0 }}
                                <span class="text-sm font-normal">
                                    {{ Lang::txt('COM_SUPPORT_STATS_MINUTES') }}
                                </span>
                            </div>
                        </div>
                        <div class="stat">
                            <div class="stat-title">
                                {{ Lang::txt('COM_SUPPORT_STATS_UNASSIGNED') }}
                            </div>
                            <div class="stat-value text-2xl text-warning">
                                {{ $opened['unassigned'] }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pie charts: Severity and Resolution --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                {{-- Severity pie --}}
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">
                            {{ Lang::txt('COM_SUPPORT_TICKETS_BY_SEVERITY') }}
                        </h3>
                        <div
                            id="severities-container"
                            class="stats-pie-chart"
                            style="min-height: 250px;"
                            data-datasets="{{ $sevDataId }}"
                        >
                            <div class="overflow-x-auto">
                                <table class="table table-zebra table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ Lang::txt('COM_SUPPORT_STATS_SEVERITY') }}</th>
                                            <th>{{ Lang::txt('COM_SUPPORT_STATS_NUMBER') }}</th>
                                            <th>{{ Lang::txt('COM_SUPPORT_STATS_PERCENT') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($severtes as $sname)
                                            <tr>
                                                <th scope="row">
                                                    {{ $__view->escape(stripslashes($sname)) }}
                                                </th>
                                                <td>{{ $sev[$sname] ?? 0 }}</td>
                                                <td>
                                                    {{ (isset($sev[$sname]) && $sevTotal > 0)
                                                        ? round($sev[$sname] / $sevTotal * 100, 2)
                                                        : 0 }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <script type="application/json" id="{{ $sevDataId }}">
                            {
                                "datasets": [{!! implode(',' . "\n", $sevChartData) !!}]
                            }
                        </script>
                    </div>
                </div>

                {{-- Resolution pie --}}
                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title">
                            {{ Lang::txt('COM_SUPPORT_TICKETS_BY_RESOLUTION') }}
                        </h3>
                        <div
                            id="resolutions-container"
                            class="stats-pie-chart"
                            style="min-height: 250px;"
                            data-datasets="{{ $resDataId }}"
                        >
                            <div class="overflow-x-auto">
                                <table class="table table-zebra table-sm">
                                    <thead>
                                        <tr>
                                            <th>
                                                {{ Lang::txt('COM_SUPPORT_STATS_RESOLUTION') }}
                                            </th>
                                            <th>{{ Lang::txt('COM_SUPPORT_STATS_NUMBER') }}</th>
                                            <th>{{ Lang::txt('COM_SUPPORT_STATS_PERCENT') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <th scope="row">
                                                {{ Lang::txt('COM_SUPPORT_STATS_RESOLUTION_NONE') }}
                                            </th>
                                            <td>{{ $res[0] ?? 0 }}</td>
                                            <td>
                                                {{ (isset($res[0]) && $resTotal > 0)
                                                    ? round($res[0] / $resTotal * 100, 2)
                                                    : 0 }}
                                            </td>
                                        </tr>
                                        @foreach ($resolutns as $resModel)
                                            <tr>
                                                <th scope="row">
                                                    {{ $__view->escape($resModel->title) }}
                                                </th>
                                                <td>{{ $res[$resModel->id] ?? 0 }}</td>
                                                <td>
                                                    {{ (isset($res[$resModel->id]) && $resTotal > 0)
                                                        ? round($res[$resModel->id] / $resTotal * 100, 2)
                                                        : 0 }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <script type="application/json" id="{{ $resDataId }}">
                            {
                                "datasets": [{!! implode(',' . "\n", $resChartData) !!}]
                            }
                        </script>
                    </div>
                </div>
            </div>

            {{-- Per-user stats --}}
            @if ($users)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach ($users as $j => $usr)
                        @php
                            // Build per-user closed data
                            $userClosedData = '';
                            if ($usr->closed) {
                                $uc = [];
                                foreach ($usr->closed as $yr => $data) {
                                    foreach ($data as $k => $v) {
                                        $monthPad = \Hubzero\Utility\Str::pad(($k - 1), 2);
                                        $uc[] = '[' . Date::of($yr . '-' . $monthPad . '-01')->toUnix()
                                            . ',' . $v . ']';
                                    }
                                }
                                $userClosedData = implode(',', $uc);
                            }
                            $anon = 0;
                            $profile = User::getInstance($usr->id);
                            if (!$profile) {
                                $anon = 1;
                            }
                            $userChartId = $option . '-data-user' . $usr->id;
                            $userLifetime = \Components\Support\Helpers\Utilities::calculateAverageLife(
                                $usr->tickets
                            );
                        @endphp
                        <div class="card bg-base-100 shadow-sm">
                            <div class="card-body">
                                <div class="flex items-center gap-4 mb-4">
                                    <div class="badge badge-lg badge-neutral font-bold">
                                        #{{ $j + 1 }}
                                    </div>
                                    <div class="avatar">
                                        <div class="w-12 rounded-full">
                                            <img
                                                src="{{ $profile->picture($anon) }}"
                                                alt="{{ $__view->escape(stripslashes($usr->name)) }}"
                                            />
                                        </div>
                                    </div>
                                    <div>
                                        <div class="font-semibold">
                                            {{ $__view->escape($usr->name) }}
                                        </div>
                                        <div class="text-sm opacity-60">
                                            {{ Lang::txt('COM_SUPPORT_STATS_NUM_ASSIGNED', number_format($usr->assigned)) }}
                                        </div>
                                    </div>
                                </div>

                                <div
                                    id="user-{{ $__view->escape($usr->username) }}"
                                    class="stats-user-chart"
                                    style="min-height: 200px;"
                                    data-datasets="{{ $userChartId }}"
                                >
                                    <script type="application/json" id="{{ $userChartId }}">
                                        {
                                            "top": {{ $top }},
                                            "datasets": [{
                                                "color": "#656565",
                                                "label": "{!! Lang::txt('COM_SUPPORT_CLOSED') !!}",
                                                "data": [{!! $userClosedData !!}]
                                            }]
                                        }
                                    </script>
                                </div>

                                <div class="stats stats-horizontal w-full shadow-none mt-4">
                                    <div class="stat px-0">
                                        <div class="stat-title">
                                            {{ Lang::txt('COM_SUPPORT_CLOSED') }}
                                        </div>
                                        <div class="stat-value text-xl">
                                            {{ number_format($usr->total) }}
                                        </div>
                                    </div>
                                    <div class="stat px-0">
                                        <div class="stat-title">
                                            {{ Lang::txt('COM_SUPPORT_STATS_AVERAGE_LIFETIME') }}
                                        </div>
                                        <div class="stat-value text-xl">
                                            {{ $userLifetime[0] ?? 0 }}
                                            <span class="text-sm font-normal">
                                                {{ Lang::txt('COM_SUPPORT_STATS_DAYS') }}
                                            </span>
                                            {{ $userLifetime[1] ?? 0 }}
                                            <span class="text-sm font-normal">
                                                {{ Lang::txt('COM_SUPPORT_STATS_HOURS') }}
                                            </span>
                                            {{ $userLifetime[2] ?? 0 }}
                                            <span class="text-sm font-normal">
                                                {{ Lang::txt('COM_SUPPORT_STATS_MINUTES') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>
    </form>
</x-page-container>
