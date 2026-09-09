{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
$__view->css('impact.css', 'plg_projects_publications');
$__view->js('impact.js');

$thisMonth = date('M Y');
$lastMonth = date('M Y', strtotime('-1 month'));

$nowMonth = date('M');
$oneMonth = date('M', strtotime('-1 month'));
$twoMonth = date('M', strtotime('-2 month'));
$threeMonth = date('M', strtotime('-3 month'));

$i = 0;
@endphp

<div class="space-y-6">

@if ($pubstats)

    @if ($totals && count($pubstats) > 1)
        @php
            $pubCount = count($pubstats);
            $totalPrimary = $totals->all_total_primary;
        @endphp
        <p class="text-base-content/80">
            {{ Lang::txt('PLG_MEMBERS_IMPACT_YOUR') }}
            <span class="font-bold">{{ $pubCount }}</span>
            {{ Lang::txt('PLG_MEMBERS_IMPACT_PUBLICATIONS_S') }}
            {{ Lang::txt('PLG_MEMBERS_IMPACT_HAVE_BEEN_ACCESSED') }}
            <span class="font-bold">{{ $totalPrimary }}</span>
            {{ Lang::txt('PLG_MEMBERS_IMPACT_TIMES') }}.
        </p>
    @endif

    @foreach ($pubstats as $stat)
        @php
            $i++;

            $toDate = strtotime($stat->first_published) > strtotime($firstlog)
                ? $stat->first_published
                : $firstlog;

            $thumbUrl = Route::url(
                'index.php?option=com_publications&id='
                . $stat->publication_id
                . '&v=' . $stat->publication_version_id
            ) . '/Image:thumb';

            $pubUrl = Route::url(
                'index.php?option=com_publications&id='
                . $stat->publication_id
            ) . '?version=' . $stat->version_number;

            $publishedDate = Date::of($stat->published_up)
                ->toLocal(Lang::txt('DATE_FORMAT_HZ1'));

            $projectUrl = Route::url(
                'index.php?option=com_projects&task=view&alias='
                . $stat->project_alias
            );
            $projectTitle = \Hubzero\Utility\Str::truncate(
                $stat->project_title,
                65
            );
            $toDateFormatted = Date::of($toDate)
                ->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
        @endphp

        <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
                {{-- Header: thumbnail + title + details --}}
                <div class="flex items-start gap-4 mb-4">
                    <img src="{{ $thumbUrl }}" alt="" class="w-16 h-16 rounded object-cover shrink-0" />
                    <div class="min-w-0">
                        <a href="{{ $pubUrl }}" class="link link-hover text-lg font-semibold">
                            {{ $stat->title }}
                        </a>
                        <p class="text-sm text-base-content/70">
                            {{ Lang::txt('PLG_MEMBERS_IMPACT_PUBLISHED') }}
                            {{ $publishedDate }}
                            {{ Lang::txt('PLG_MEMBERS_IMPACT_IN') }}
                            {{ $stat->cat_name }}
                            |
                            {{ Lang::txt('PLG_MEMBERS_IMPACT_FROM_PROJECT') }}
                            <a href="{{ $projectUrl }}" class="link link-hover">{{ $projectTitle }}</a>
                        </p>
                    </div>
                </div>

                {{-- Stats grid --}}
                <div class="overflow-x-auto">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th></th>
                                <th class="text-center">{{ Lang::txt('PLG_MEMBERS_IMPACT_STATS_THIS_MONTH') }}<br><span class="text-xs font-normal text-base-content/60">{{ $thisMonth }}</span></th>
                                <th class="text-center">{{ Lang::txt('PLG_MEMBERS_IMPACT_STATS_LAST_MONTH') }}<br><span class="text-xs font-normal text-base-content/60">{{ $lastMonth }}</span></th>
                                <th class="text-center font-bold">{{ Lang::txt('PLG_MEMBERS_IMPACT_STATS_TOTAL') }}*<br><span class="text-xs font-normal text-base-content/60">*{{ Lang::txt('PLG_MEMBERS_IMPACT_SINCE') }} {{ $toDateFormatted }}</span></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="font-medium">
                                    {{ Lang::txt('PLG_MEMBERS_IMPACT_STATS_VIEWS') }}
                                    @if ($i == 1)
                                        <span class="tooltip" data-tip="{{ Lang::txt('PLG_MEMBERS_IMPACT_STATS_VIEWS_TIPS_TITLE_ABOUT') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-4 h-4 inline stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center text-lg">{{ $stat->thismonth_views }}</td>
                                <td class="text-center text-lg">{{ $stat->lastmonth_views }}</td>
                                <td class="text-center text-lg font-bold">{{ $stat->total_views }}</td>
                            </tr>
                            <tr>
                                <td class="font-medium">
                                    {{ Lang::txt('PLG_MEMBERS_IMPACT_STATS_ACCESSES') }}
                                    @if ($i == 1)
                                        <span class="tooltip" data-tip="{{ Lang::txt('PLG_MEMBERS_IMPACT_STATS_ACCESSES_TIPS_TITLE_ABOUT') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" class="w-4 h-4 inline stroke-current"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </span>
                                    @endif
                                </td>
                                <td class="text-center text-lg">{{ $stat->thismonth_primary }}</td>
                                <td class="text-center text-lg">{{ $stat->lastmonth_primary }}</td>
                                <td class="text-center text-lg font-bold">{{ $stat->total_primary }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                {{-- Chart containers with data attributes for external JS --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <p class="text-xs text-base-content/60 mb-1">{{ Lang::txt('PLG_MEMBERS_IMPACT_STATS_VIEWS') }}</p>
                        <div class="h-24 w-full" id="view-{{ $stat->publication_id }}"
                             data-chart="line"
                             data-color="#f8e7b3"
                             data-line-color="#e8b83d"
                             data-values="{{ json_encode([$stat->threemonth_views, $stat->twomonth_views, $stat->lastmonth_views, $stat->thismonth_views]) }}"
                             data-labels="{{ json_encode([$threeMonth, $twoMonth, $oneMonth, $nowMonth]) }}">
                        </div>
                    </div>
                    <div>
                        <p class="text-xs text-base-content/60 mb-1">{{ Lang::txt('PLG_MEMBERS_IMPACT_STATS_ACCESSES') }}</p>
                        <div class="h-24 w-full" id="access-{{ $stat->publication_id }}"
                             data-chart="line"
                             data-color="#cdf0c1"
                             data-line-color="#aed3a1"
                             data-values="{{ json_encode([$stat->threemonth_primary, $stat->twomonth_primary, $stat->lastmonth_primary, $stat->thismonth_primary]) }}"
                             data-labels="{{ json_encode([$threeMonth, $twoMonth, $oneMonth, $nowMonth]) }}">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach

@else
    <p>{{ Lang::txt('PLG_MEMBERS_IMPACT_STATS_NO_INFO') }}</p>
@endif

</div>
