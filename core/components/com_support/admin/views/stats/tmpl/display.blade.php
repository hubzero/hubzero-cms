{{--
  Support — Ticket statistics

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;
@endphp

@php
  \Hubzero\Facades\Toolbar::title(
      Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_STATS'),
      'support'
  );
  \Hubzero\Facades\Toolbar::spacer();
  \Hubzero\Facades\Toolbar::help('stats');
@endphp

@php
  $__view->css('stats')->css('stats.blade')
         ->js('flot/jquery.flot.min.js', 'system')
         ->js('flot/jquery.flot.time.min.js', 'system')
         ->js('flot/jquery.flot.pie.min.js', 'system')
         ->js('flot/jquery.flot.resize.js', 'system')
         ->js('flot/jquery.flot.tooltip.min.js', 'system')
         ->js('stats.blade.js');

  $gidNumber = 0;
  if ($group = \Hubzero\User\Group::getInstance($filters['group'])) {
      $gidNumber = $group->get('gidNumber');
  }

  $database = App::get('db');

  // Resolution data
  $sql = "SELECT status FROM `#__support_tickets` WHERE open=0 AND type=" . $database->quote($filters['type']);
  if (!$filters['group'] || $filters['group'] == '_none_') {
      $sql .= " AND group_id=0";
  } elseif ($filters['group']) {
      $sql .= " AND `group_id`=" . $database->quote($gidNumber);
  }
  $sql .= " ORDER BY status ASC";
  $database->setQuery($sql);
  $resolutionRows = $database->loadObjectList();
  $resTotal = count($resolutionRows);
  $res = [];
  foreach ($resolutionRows as $resolution) {
      if (!isset($res[$resolution->status])) {
          $res[$resolution->status] = 1;
      } else {
          $res[$resolution->status]++;
      }
  }

  // Severity data
  $sql2 = "SELECT severity FROM `#__support_tickets` WHERE type=" . $database->quote($filters['type']);
  if (!$filters['group'] || $filters['group'] == '_none_') {
      $sql2 .= " AND group_id=0";
  } elseif ($filters['group']) {
      $sql2 .= " AND `group_id`=" . $database->quote($gidNumber);
  }
  $sql2 .= " ORDER BY severity ASC";
  $database->setQuery($sql2);
  $severityRows = $database->loadObjectList();
  $sevTotal = count($severityRows);
  $sev = [];
  foreach ($severityRows as $sevRow) {
      if (!isset($sev[$sevRow->severity])) {
          $sev[$sevRow->severity] = 1;
      } else {
          $sev[$sevRow->severity]++;
      }
  }

  $colors = [
      '#7c7c7c', '#515151', '#404040', '#3d3d3d', '#797979',
      '#595959', '#e5e5e5', '#828282', '#404040', '#6a6a6a',
      '#bcbcbc', '#515151', '#d9d9d9', '#3d3d3d', '#797979',
      '#595959', '#e5e5e5', '#828282', '#404040', '#3a3a3a',
  ];

  // Chart data for opened/closed over time
  $top = 0;
  $closedChartParts = [];
  if ($closedmonths) {
      foreach ($closedmonths as $year => $data) {
          foreach ($data as $k => $v) {
              $top = ($v > $top) ? $v : $top;
              $mp = \Hubzero\Utility\Str::pad(($k - 1), 2);
              $closedChartParts[] = '[' . Date::of($year . '-' . $mp . '-01')->toUnix() . ',' . $v . ']';
          }
      }
  }
  $openedChartParts = [];
  if ($openedmonths) {
      foreach ($openedmonths as $year => $data) {
          foreach ($data as $k => $v) {
              $top = ($v > $top) ? $v : $top;
              $mp2 = \Hubzero\Utility\Str::pad(($k - 1), 2);
              $openedChartParts[] = '[' . Date::of($year . '-' . $mp2 . '-01')->toUnix() . ',' . $v . ']';
          }
      }
  }
  $closeddata = implode(',', $closedChartParts);
  $openeddata = implode(',', $openedChartParts);

  // Severity pie data
  $severityList = \Components\Support\Helpers\Utilities::getSeverities($config->get('severities'));
  $sevPieData = [];
  $sevCls = 'odd';
  foreach ($severityList as $si => $severity) {
      $r  = '{"label": "' . e($severity) . '", "data": ';
      $r .= (isset($sev[$severity])) ? round(($sev[$severity] / $sevTotal) * 100, 2) : 0;
      $r .= ', "color": "' . $colors[$si] . '"}';
      $sevPieData[] = $r;
  }

  // Resolution pie data
  $resolutionModels = \Components\Support\Models\Status::all()->whereEquals('open', 0)->rows();
  $noResLabel = Lang::txt('COM_SUPPORT_STATS_NO_RESOLUTION');
  $noResVal = isset($res[0]) ? $res[0] / $resTotal : '0';
  $resPieData = [
      '{"label": "' . $noResLabel . '", "data": ' . $noResVal . ', "color": "' . $colors[0] . '"}',
  ];
  foreach ($resolutionModels as $ri => $resolution) {
      $r  = '{"label": "' . e($resolution->title) . '", "data": ';
      $r .= (isset($res[$resolution->id])) ? round(($res[$resolution->id] / $resTotal) * 100, 2) : 0;
      $r .= ', "color": "' . $colors[$ri + 1] . '"}';
      $resPieData[] = $r;
  }

  $formAction = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);
@endphp

<form
    action="{{ $formAction }}"
    method="get"
    name="adminForm"
    id="item-form"
    enctype="multipart/form-data"
>
    <div id="ticket-stats">
        <fieldset id="filter-bar" class="support-stats-filter">
            <label for="ticket-group">
                {{ Lang::txt('COM_SUPPORT_STATS_FOR_GROUP') }}
            </label>
            <select name="group" id="ticket-group">
                <option value="" @selected(!$filters['group'])>{{ Lang::txt('COM_SUPPORT_NONE') }}</option>
                @if ($groups)
                    @foreach ($groups as $grp)
                        @php
                          $groupLabel = $grp->description
                              ? $grp->description
                              : $grp->cn;
                        @endphp
                        <option
                            value="{{ $grp->cn }}"
                            @selected($filters['group'] == $grp->cn)
                        >{{ $groupLabel }}</option>
                    @endforeach
                @endif
            </select>
            <input type="submit" value="Go" />
        </fieldset>

        <fieldset class="adminform">
            <legend>
                <span>
                    {{ Date::of($first . '-01-01')->format('M Y') }}
                    -
                    {{ Date::of($filters['year'] . '-' . $filters['month'] . '-01')->format('M Y') }}
                </span>
            </legend>

            <div
                id="container"
                class="chart stats-tickets-chart"
                data-datasets="{{ $option }}-data-openedclosed"
            ></div>
            <template id="{{ $option }}-data-openedclosed">
                {
                    "datasets": [
                        {
                            "color": "#AA4643",
                            "label": "{{ Lang::txt('COM_SUPPORT_STATS_COL_OPENED_ALL') }}",
                            "data": [{!! $openeddata !!}]
                        },
                        {
                            "color": "#656565",
                            "label": "{{ Lang::txt('COM_SUPPORT_STATS_COL_CLOSED_ALL') }}",
                            "data": [{!! $closeddata !!}]
                        }
                    ]
                }
            </template>
            <div class="clr"></div>
        </fieldset>

        <fieldset class="adminform breakdown">
            <div class="breakdown">
                @php
                  $lifetime = \Components\Support\Helpers\Utilities::calculateAverageLife($closedTickets);
                @endphp
                <table class="support-stats-overview">
                    <thead>
                        <tr>
                            <th scope="col">
                                {{ Lang::txt('COM_SUPPORT_STATS_COL_OPENED_ALL') }}
                            </th>
                            <th scope="col">
                                {{ Lang::txt('COM_SUPPORT_STATS_COL_CLOSED_ALL') }}
                            </th>
                            <th scope="col" class="block">
                                {{ Lang::txt('COM_SUPPORT_STATS_COL_AVERAGE') }}
                            </th>
                            <th scope="col" class="major">
                                {{ Lang::txt('COM_SUPPORT_STATS_COL_UNASSIGNED') }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $opened['open'] }}</td>
                            <td>{{ $opened['closed'] }}</td>
                            <td class="block">
                                {{ isset($lifetime[0]) ? $lifetime[0] : 0 }}
                                <span>{{ Lang::txt('COM_SUPPORT_STATS_DAYS') }}</span>
                                {{ isset($lifetime[1]) ? $lifetime[1] : 0 }}
                                <span>{{ Lang::txt('COM_SUPPORT_STATS_HOURS') }}</span>
                                {{ isset($lifetime[2]) ? $lifetime[2] : 0 }}
                                <span>{{ Lang::txt('COM_SUPPORT_STATS_MINUTES') }}</span>
                            </td>
                            <td class="major">{{ $opened['unassigned'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </fieldset>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <fieldset class="adminform breakdown pies">
                <legend>
                    <span>{{ Lang::txt('COM_SUPPORT_STATS_FIELDSET_BY_SEVERITY') }}</span>
                </legend>
                <div
                    id="severities-container"
                    class="stats-pie-chart"
                    data-datasets="{{ $option }}-data-severity"
                >
                    <table class="support-stats-resolutions">
                        <thead>
                            <tr>
                                <th scope="col">
                                    {{ Lang::txt('COM_SUPPORT_STATS_COL_SEVERITY') }}
                                </th>
                                <th scope="col">
                                    {{ Lang::txt('COM_SUPPORT_STATS_COL_NUMBER') }}
                                </th>
                                <th scope="col">
                                    {{ Lang::txt('COM_SUPPORT_STATS_COL_PERCENT') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $sevCls = 'odd'; @endphp
                            @foreach ($severityList as $severity)
                                <tr class="{{ $sevCls }}">
                                    <th scope="row">{{ $severity }}</th>
                                    <td>{{ isset($sev[$severity]) ? $sev[$severity] : '0' }}</td>
                                    <td>
                                        {{ isset($sev[$severity])
                                            ? round($sev[$severity] / $sevTotal * 100, 2)
                                            : '0' }}
                                    </td>
                                </tr>
                                @php $sevCls = ($sevCls == 'even') ? 'odd' : 'even'; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div><!-- / #severities-container -->
                <template id="{{ $option }}-data-severity">
                    {
                        "datasets": [{!! implode(',' . "\n", $sevPieData) !!}]
                    }
                </template>
            </fieldset>
        </div>

        <div>
            <fieldset class="adminform breakdown pies">
                <legend>
                    <span>{{ Lang::txt('COM_SUPPORT_STATS_FIELDSET_BY_RESOLUTION') }}</span>
                </legend>
                <div
                    id="resolutions-container"
                    class="stats-pie-chart"
                    data-datasets="{{ $option }}-data-resolution"
                >
                    <table class="support-stats-resolutions">
                        <thead>
                            <tr>
                                <th scope="col">
                                    {{ Lang::txt('COM_SUPPORT_STATS_COL_RESOLUTION') }}
                                </th>
                                <th scope="col">
                                    {{ Lang::txt('COM_SUPPORT_STATS_COL_NUMBER') }}
                                </th>
                                <th scope="col">
                                    {{ Lang::txt('COM_SUPPORT_STATS_COL_PERCENT') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="odd">
                                <th scope="row">
                                    {{ Lang::txt('COM_SUPPORT_STATS_NO_RESOLUTION') }}
                                </th>
                                <td>{{ isset($res[0]) ? $res[0] : '0' }}</td>
                                <td>{{ isset($res[0]) ? $res[0] / $resTotal : '0' }}</td>
                            </tr>
                            @php $resCls = 'odd'; @endphp
                            @foreach ($resolutionModels as $resolution)
                                <tr class="{{ $resCls }}">
                                    <th scope="row">
                                        {{ $resolution->title }}
                                    </th>
                                    <td>
                                        {{ isset($res[$resolution->id]) ? $res[$resolution->id] : '0' }}
                                    </td>
                                    <td>
                                        {{ isset($res[$resolution->id])
                                            ? round($res[$resolution->id] / $resTotal * 100, 2)
                                            : '0' }}
                                    </td>
                                </tr>
                                @php $resCls = ($resCls == 'even') ? 'odd' : 'even'; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div><!-- / #resolutions-container -->
                <template id="{{ $option }}-data-resolution">
                    {
                        "datasets": [{!! implode(',' . "\n", $resPieData) !!}]
                    }
                </template>
            </fieldset>
        </div>
        </div>

        @if ($users)
            @php
              $chunked = array_chunk($users, 2);
              $z = 0;
              $j = 1;
            @endphp
            @foreach ($chunked as $chunk)
                @foreach ($chunk as $user)
                    @php
                      if ($z == 1) {
                          $openGrid = false;
                          $closeFirst = true;
                          $closeGrid = false;
                      } elseif ($z == 2) {
                          $z = 0;
                          $openGrid = true;
                          $closeFirst = true;
                          $closeGrid = true;
                      } else {
                          $openGrid = true;
                          $closeFirst = false;
                          $closeGrid = false;
                      }

                      $userClosedParts = [];
                      if ($user->closed) {
                          foreach ($user->closed as $year => $data) {
                              foreach ($data as $k => $v) {
                                  $mp3 = \Hubzero\Utility\Str::pad(($k - 1), 2);
                                  $userClosedParts[] = '[' . Date::of($year . '-' . $mp3 . '-01')->toUnix() . ',' . $v . ']';
                              }
                          }
                      }
                      $userCloseddata = implode(',', $userClosedParts);

                      $anon = 0;
                      $profile = User::getInstance($user->id);
                      if (!$profile) {
                          $anon = 1;
                      }
                      $photoAlt = Lang::txt('COM_SUPPORT_STATS_PHOTO_FOR', $user->name);
                    @endphp
                    @if ($closeGrid)
                        </div><!-- / .grid > div -->
                        </div><!-- / .grid -->
                    @elseif ($closeFirst)
                        </div><!-- / .grid > div -->
                    @endif
                    @if ($openGrid)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @endif
                    <div>
                    <fieldset class="adminform">
                        <div class="breakdown">
                            <div class="entry-head">
                                <p class="entry-rank">
                                    <strong>#{{ $j }}</strong>
                                </p>
                                <p class="entry-member-photo">
                                    <img
                                        src="{{ $profile->picture($anon) }}"
                                        alt="{{ $photoAlt }}"
                                    />
                                </p>
                                <p class="entry-title">
                                    {{ $user->name }}<br />
                                    <span>
                                        {{ Lang::txt('COM_SUPPORT_STATS_NUM_ASSIGNED', number_format($user->assigned)) }}
                                    </span>
                                </p>
                            </div>
                            <div class="entry-content">
                                <div
                                    id="user-{{ $user->username }}"
                                    class="stats-user-chart"
                                    data-datasets="{{ $option }}-data-user{{ $user->id }}"
                                >
                                    <template id="{{ $option }}-data-user{{ $user->id }}">
                                        {
                                            "top": {{ $top }},
                                            "datasets": [{
                                                "color": "#656565",
                                                "label": "Closed",
                                                "data": [{!! $userCloseddata !!}]
                                            }]
                                        }
                                    </template>
                                </div><!-- / #user -->
                                @php
                                  $userLifetime = \Components\Support\Helpers\Utilities::calculateAverageLife($user->tickets);
                                @endphp
                                <table class="support-stats-overview">
                                    <thead>
                                        <tr>
                                            <th scope="col">
                                                {{ Lang::txt('COM_SUPPORT_STATS_COL_CLOSED') }}
                                            </th>
                                            <th scope="col" class="block">
                                                {{ Lang::txt('COM_SUPPORT_STATS_COL_AVERAGE') }}
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ number_format($user->total) }}</td>
                                            <td class="block">
                                                {{ isset($userLifetime[0]) ? $userLifetime[0] : 0 }}
                                                <span>
                                                    {{ Lang::txt('COM_SUPPORT_STATS_DAYS') }}
                                                </span>
                                                {{ isset($userLifetime[1]) ? $userLifetime[1] : 0 }}
                                                <span>
                                                    {{ Lang::txt('COM_SUPPORT_STATS_HOURS') }}
                                                </span>
                                                {{ isset($userLifetime[2]) ? $userLifetime[2] : 0 }}
                                                <span>
                                                    {{ Lang::txt('COM_SUPPORT_STATS_MINUTES') }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div><!-- / .entry-content -->
                        </div>
                    </fieldset><!-- / .container -->
                    @php
                      $j++;
                      $z++;
                    @endphp
                @endforeach
            @endforeach
            </div><!-- / .grid > div -->
            </div>
        @endif

    </div><!-- / .section -->

    <input type="hidden" name="task" value="display" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />

    {!! Html::input('token') !!}
</form>
