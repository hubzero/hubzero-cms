{{--
 * Reports display: Project statistics dashboard with Flot charts
 *
 * Variables:
 *   $option     - Component option string
 *   $title      - Page title
 *   $msg        - Status message string
 *   $stats      - Statistics array
 *   $monthly    - Monthly data array (or null)
 *   $admin      - Whether user is admin (bool)
 *   $publishing - Whether publishing is enabled (bool)
 *   $config     - Component config Registry
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;

    $__view->css('reports')
        ->css('impact.css', 'projects', 'publications')
        ->js('flot/jquery.flot.min.js', 'system')
        ->js('flot/jquery.flot.time.min.js', 'system')
        ->js('flot/jquery.flot.pie.min.js', 'system')
        ->js('flot/jquery.flot.resize.js', 'system')
        ->js('reports-charts');

    /**
     * Build chart data arrays from monthly stats.
     *
     * @param  array  $monthly  Monthly stats keyed by month label
     * @param  string $path     Dot-path into each month's data (e.g. 'general.new')
     * @param  bool   $stripPct Whether to strip '%' from values
     * @return array  [data => [[x,y],...], ticks => [[x,"label"],...]]
     */
    $buildChartData = function (array $monthly, string $path, bool $stripPct = false): array {
        $data = [];
        $ticks = [];
        $y = 0;
        $keys = explode('.', $path);

        foreach ($monthly as $month => $mdata) {
            $val = $mdata;
            foreach ($keys as $k) {
                $val = $val[$k] ?? 0;
            }
            if ($stripPct) {
                $val = str_replace('%', '', $val);
            }
            $data[] = [$y, (int) $val];
            $ticks[] = [$y, $month];
            $y++;
        }

        return ['data' => $data, 'ticks' => $ticks];
    };
@endphp

<header id="content-header" class="reports mb-6">
    <h2>{{ $title }}</h2>
</header>

<section class="main section">
    <div id="project-stats">
        @include('projects::_statusmsg', [
            'error' => $__view->getError(),
            'msg'   => $msg,
        ])

        @if (empty($stats))
            <div role="alert" class="alert alert-error">{{ Lang::txt('Statistics unavailable') }}</div>
        @else
            {{-- Overview --}}
            <table class="table w-full stats-wrap">
                <tr class="stats-general">
                    <th scope="row" class="stats-h icon-cogs" rowspan="2">
                        <span>{{ Lang::txt('Overview') }}</span>
                    </th>
                    <th></th>
                    <th></th>
                    <th></th>
                    @if ($monthly)
                        <th class="stats-graph">{{ Lang::txt('New projects') }}</th>
                    @endif
                    <th class="stats-more">{{ Lang::txt('More breakdown') }}</th>
                </tr>
                <tr>
                    <td>
                        <span class="text-3xl font-bold">{{ $stats['general']['total'] }}</span>
                        <span class="text-sm text-base-content/60">{{ Lang::txt('Total projects') }}</span>
                    </td>
                    <td>
                        <span class="text-3xl font-bold">{{ $stats['general']['setup'] }}</span>
                        <span class="text-sm text-base-content/60">{{ Lang::txt('Projects in setup') }}</span>
                    </td>
                    <td>
                        <span class="text-3xl font-bold">{{ $stats['general']['active'] }}</span>
                        <span class="text-sm text-base-content/60">{{ Lang::txt('Active projects') }}</span>
                    </td>
                    @if ($monthly)
                        @php
                            $chart = $buildChartData($monthly, 'general.new');
                        @endphp
                        <td class="stats-graph">
                            <div
                                id="stat-total"
                                class="ph"
                                data-chart-data="{{ json_encode($chart['data']) }}"
                                data-chart-ticks="{{ json_encode($chart['ticks']) }}"
                                data-chart-yticksize="{{ $stats['general']['new'] }}"
                                data-chart-tip-format="%y"
                                data-chart-label-append=""
                            ></div>
                        </td>
                    @endif
                    <td class="stats-more">
                        <ul>
                            <li>
                                <span class="text-lg font-semibold">{{ $stats['general']['new'] }}</span>
                                {{ Lang::txt('new projects this month') }}
                            </li>
                            <li>
                                <span class="text-lg font-semibold">{{ $stats['general']['public'] }}</span>
                                {{ Lang::txt('public projects') }}
                            </li>
                            @if ($config->get('grantinfo', 0))
                                <li>
                                    <span class="text-lg font-semibold">{{ $stats['general']['sponsored'] }}</span>
                                    {{ Lang::txt('grant-sponsored projects') }}
                                </li>
                            @endif
                            @if ($config->get('restricted_data', 0))
                                <li>
                                    <span class="text-lg font-semibold">{{ $stats['general']['sensitive'] }}</span>
                                    {{ Lang::txt('projects with sensitive data') }}
                                </li>
                            @endif
                        </ul>
                    </td>
                </tr>
            </table>

            {{-- Activity --}}
            <table class="table w-full stats-wrap">
                <tr class="stats-activity">
                    <th scope="row" class="stats-h icon-bar-chart" rowspan="2">
                        <span>{{ Lang::txt('Activity') }}</span>
                    </th>
                    <th></th>
                    <th></th>
                    <th></th>
                    @if ($monthly)
                        <th class="stats-graph">{{ Lang::txt('Active projects') }}</th>
                    @endif
                    <th class="stats-more">
                        {{ Lang::txt('Top active projects') }}
                        @if (!$admin) ({{ Lang::txt('public') }}) @endif
                    </th>
                </tr>
                <tr>
                    <td>
                        <span class="text-3xl font-bold">{{ $stats['activity']['total'] }}</span>
                        <span class="text-sm text-base-content/60">{{ Lang::txt('total activity records') }}</span>
                    </td>
                    <td>
                        <span class="text-3xl font-bold">{{ $stats['activity']['average'] }}</span>
                        <span class="text-sm text-base-content/60">{{ Lang::txt('average activity records per project') }}</span>
                    </td>
                    <td>
                        <span class="text-3xl font-bold">{{ $stats['activity']['usage'] }}</span>
                        <span class="text-sm text-base-content/60">{{ Lang::txt('projects active in past 30 days') }}</span>
                    </td>
                    @if ($monthly)
                        @php
                            $chart = $buildChartData($monthly, 'activity.usage', true);
                            $yTickSize = str_replace('%', '', $stats['activity']['usage']);
                        @endphp
                        <td class="stats-graph">
                            <div
                                id="stat-activity"
                                class="ph"
                                data-chart-data="{{ json_encode($chart['data']) }}"
                                data-chart-ticks="{{ json_encode($chart['ticks']) }}"
                                data-chart-yticksize="{{ $yTickSize }}"
                                data-chart-tip-format="%y%"
                                data-chart-label-append="%"
                            ></div>
                        </td>
                    @endif
                    <td class="stats-more">
                        @if (!empty($stats['topActiveProjects']))
                            <ul>
                                @foreach ($stats['topActiveProjects'] as $topProject)
                                    @php
                                        $project = new \Components\Projects\Models\Project($topProject->scope_id);
                                        $viewUrl = Route::url('index.php?option=' . $option . '&task=view&alias=' . $project->get('alias'));
                                    @endphp
                                    <li>
                                        <span class="stats-ima-small">
                                            <img src="{{ $project->picture('thumb') }}" alt="" />
                                        </span>
                                        @if (!$project->get('private'))
                                            <a href="{{ $viewUrl }}">
                                        @endif
                                        {{ $project->get('title') }}
                                        @if (!$project->get('private'))
                                            </a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-base-content/60 italic">{{ Lang::txt('Detailed information currently unavailable') }}</p>
                        @endif
                    </td>
                </tr>
            </table>

            {{-- Team --}}
            <table class="table w-full stats-wrap">
                <tr class="stats-team">
                    <th scope="row" class="stats-h icon-group" rowspan="2">
                        <span>{{ Lang::txt('Team') }}</span>
                    </th>
                    <th></th>
                    <th></th>
                    <th></th>
                    @if ($monthly)
                        <th class="stats-graph">{{ Lang::txt('New team members added') }}</th>
                    @endif
                    <th class="stats-more">
                        {{ Lang::txt('Top biggest team projects') }}
                        @if (!$admin) ({{ Lang::txt('public') }}) @endif
                    </th>
                </tr>
                <tr>
                    <td>
                        <span class="text-3xl font-bold">{{ $stats['team']['total'] }}</span>
                        <span class="text-sm text-base-content/60">{{ Lang::txt('total members in all teams') }}</span>
                    </td>
                    <td>
                        <span class="text-3xl font-bold">{{ $stats['team']['average'] }}</span>
                        <span class="text-sm text-base-content/60">{{ Lang::txt('average project team size') }}</span>
                    </td>
                    <td>
                        <span class="text-3xl font-bold">{{ $stats['team']['multi'] }}</span>
                        <span class="text-sm text-base-content/60">{{ Lang::txt('projects have multi-person teams') }}</span>
                    </td>
                    @if ($monthly)
                        @php
                            $chart = $buildChartData($monthly, 'team.new');
                            $yTickSize = $stats['general']['total'] > 0
                                ? round($stats['team']['total'] / $stats['general']['total'], 0)
                                : 0;
                        @endphp
                        <td class="stats-graph">
                            <div
                                id="stat-team"
                                class="ph"
                                data-chart-data="{{ json_encode($chart['data']) }}"
                                data-chart-ticks="{{ json_encode($chart['ticks']) }}"
                                data-chart-yticksize="{{ $yTickSize }}"
                                data-chart-tip-format="%y"
                                data-chart-label-append=""
                            ></div>
                        </td>
                    @endif
                    <td class="stats-more">
                        @if (!empty($stats['topTeamProjects']))
                            <ul>
                                @foreach ($stats['topTeamProjects'] as $topProject)
                                    @php
                                        $mediaUrl = Route::url('index.php?option=' . $option . '&alias=' . $topProject->alias . '&task=media');
                                        $viewUrl  = Route::url('index.php?option=' . $option . '&task=view&alias=' . $topProject->alias);
                                    @endphp
                                    <li>
                                        <span class="stats-ima-small">
                                            <img src="{{ $mediaUrl }}" alt="" />
                                        </span>
                                        @if (!$topProject->private)
                                            <a href="{{ $viewUrl }}">
                                        @endif
                                        {{ $topProject->title }} ({{ $topProject->team }} {{ Lang::txt('members') }})
                                        @if (!$topProject->private)
                                            </a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                            <span class="block">&nbsp;</span>
                            <ul>
                                <li>
                                    <span class="text-lg font-semibold">{{ $stats['team']['multiusers'] }}</span>
                                    {{ Lang::txt('unique users with multiple projects') }}
                                </li>
                            </ul>
                        @else
                            <p class="text-base-content/60 italic">{{ Lang::txt('Detailed information currently unavailable') }}</p>
                        @endif
                    </td>
                </tr>
            </table>

            {{-- Files --}}
            <table class="table w-full stats-wrap">
                <tr class="stats-files">
                    <th scope="row" class="stats-h icon-file" rowspan="2">
                        <span>{{ Lang::txt('Files') }}@if (isset($stats['updated']))*@endif</span>
                    </th>
                    <th></th>
                    <th></th>
                    <th></th>
                    @if ($monthly)
                        <th class="stats-graph">{{ Lang::txt('Total files stored') }}</th>
                    @endif
                    <th class="stats-more">{{ Lang::txt('More stats') }}</th>
                </tr>
                <tr>
                    <td>
                        <span class="text-3xl font-bold">{{ $stats['files']['total'] }}</span>
                        <span class="text-sm text-base-content/60">{{ Lang::txt('files stored') }}</span>
                    </td>
                    <td>
                        <span class="text-3xl font-bold">{{ $stats['files']['average'] }}</span>
                        <span class="text-sm text-base-content/60">{{ Lang::txt('average files per project') }}</span>
                    </td>
                    <td>
                        <span class="text-3xl font-bold">{{ $stats['files']['usage'] }}</span>
                        <span class="text-sm text-base-content/60">{{ Lang::txt('projects store files') }}</span>
                    </td>
                    @if ($monthly)
                        @php
                            $chart = $buildChartData($monthly, 'files.total');
                        @endphp
                        <td class="stats-graph">
                            <div
                                id="stat-files"
                                class="ph"
                                data-chart-data="{{ json_encode($chart['data']) }}"
                                data-chart-ticks="{{ json_encode($chart['ticks']) }}"
                                data-chart-yticksize="{{ $stats['files']['total'] }}"
                                data-chart-tip-format="%y"
                                data-chart-label-append=""
                            ></div>
                        </td>
                    @endif
                    <td class="stats-more">
                        <ul>
                            <li>
                                <span class="text-lg font-semibold">{{ $stats['files']['diskspace'] }}</span>
                                {{ Lang::txt('total used disk space') }}
                            </li>
                        </ul>
                    </td>
                </tr>
            </table>

            {{-- Publications --}}
            @if ($publishing)
                <table class="table w-full stats-wrap">
                    <tr class="stats-publications">
                        <th scope="row" class="stats-h icon-success-sign" rowspan="2">
                            <span>{{ Lang::txt('Publications') }}</span>
                        </th>
                        <th></th>
                        <th></th>
                        <th></th>
                        @if ($monthly)
                            <th class="stats-graph">{{ Lang::txt('Publication releases') }}</th>
                        @endif
                        <th class="stats-more">{{ Lang::txt('More stats') }}</th>
                    </tr>
                    <tr>
                        <td>
                            <span class="text-3xl font-bold">{{ $stats['pub']['total'] }}</span>
                            <span class="text-sm text-base-content/60">{{ Lang::txt('publications started') }}</span>
                        </td>
                        <td>
                            <span class="text-3xl font-bold">{{ $stats['pub']['average'] }}</span>
                            <span class="text-sm text-base-content/60">{{ Lang::txt('average publications per project') }}</span>
                        </td>
                        <td>
                            <span class="text-3xl font-bold">{{ $stats['pub']['usage'] }}</span>
                            <span class="text-sm text-base-content/60">{{ Lang::txt('projects have used publications') }}</span>
                        </td>
                        @if ($monthly)
                            @php
                                $chart = $buildChartData($monthly, 'pub.new');
                                $yTickSize = $stats['general']['total'] > 0
                                    ? round($stats['pub']['total'] / $stats['general']['total'], 0)
                                    : 0;
                            @endphp
                            <td class="stats-graph">
                                <div
                                    id="stat-pub"
                                    class="ph"
                                    data-chart-data="{{ json_encode($chart['data']) }}"
                                    data-chart-ticks="{{ json_encode($chart['ticks']) }}"
                                    data-chart-yticksize="{{ $yTickSize }}"
                                    data-chart-tip-format="%y"
                                    data-chart-label-append=""
                                ></div>
                            </td>
                        @endif
                        <td class="stats-more">
                            <ul>
                                <li>
                                    <span class="text-lg font-semibold">{{ $stats['pub']['released'] }}</span>
                                    {{ Lang::txt('publicly released publications') }}
                                </li>
                                <li>
                                    <span class="text-lg font-semibold">{{ $stats['pub']['versions'] }}</span>
                                    {{ Lang::txt('total publication versions') }}
                                </li>
                                <li>
                                    @php
                                        $pubspace = $stats['files']['pubspace'] ?: 'N/A';
                                    @endphp
                                    <span class="text-lg font-semibold">{{ $pubspace }}</span>
                                    {{ Lang::txt('allocated to published files') }}
                                </li>
                            </ul>
                        </td>
                    </tr>
                </table>
            @endif

            @if (isset($stats['updated']))
                <p>{{ Lang::txt('*Last updated %s', $stats['updated']) }}</p>
            @endif
        @endif
    </div>
</section>
