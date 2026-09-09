{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Components\Resources\Models\Entry;
use Components\Resources\Models\Review;

$ratingStarClasses = [
    0   => 'no-stars',
    0.5 => 'half-stars',
    1   => 'one-stars',
    1.5 => 'onehalf-stars',
    2   => 'two-stars',
    2.5 => 'twohalf-stars',
    3   => 'three-stars',
    3.5 => 'threehalf-stars',
    4   => 'four-stars',
    4.5 => 'fourhalf-stars',
    5   => 'five-stars',
];
@endphp

@if ($results)
    <table class="table table-zebra w-full">
        <tbody>
        @foreach ($results as $line)
            @php
            $class = ' ' . ($ratingStarClasses[$line->rating] ?? 'no-stars');

            $entry = Entry::oneOrFail($line->id);
            $contributors = $entry->contributors();

            if (!User::isGuest()) {
                $myrating = Review::oneByUser($line->id, User::get('id'))
                    ->get('rating', 0);
            } else {
                $myrating = 0;
            }

            $myclass = ' ' . ($ratingStarClasses[$myrating] ?? 'no-stars');

            $line->title = e($line->title);

            $d = strstr($line->href, 'option=') ? '&amp;' : '?';

            $line->ranking = round($line->ranking, 1);
            $r = (10 * $line->ranking);
            if (intval($r) < 10) {
                $r = '0' . $r;
            }
            $rankingFormatted = number_format($line->ranking, 1);
            $rankingLabel = Lang::txt('PLG_GROUPS_RESOURCES_RANKING');
            $ratingLabel = Lang::txt(
                'PLG_GROUPS_RESOURCES_OUT_OF_5_STARS',
                $line->rating
            );
            $dateFormatted = Date::of($line->publish_up)->toLocal(
                Lang::txt('DATE_FORMAT_HZ1')
            );
            $avgRatingLabel = Lang::txt('PLG_GROUPS_RESOURCES_AVG_RATING');

            $ratingUrls = [];
            $ratingTitles = [];
            $ratingLabels = [];
            for ($star = 1; $star <= 5; $star++) {
                $ratingUrls[$star] = $line->href . $d
                    . 'task=addreview&amp;myrating=' . $star
                    . '#reviewform';
            }
            $ratingTitles[1] = Lang::txt('PLG_GROUPS_RESOURCES_RATING_POOR');
            $ratingTitles[2] = Lang::txt('PLG_GROUPS_RESOURCES_RATING_FAIR');
            $ratingTitles[3] = Lang::txt('PLG_GROUPS_RESOURCES_RATING_GOOD');
            $ratingTitles[4] = Lang::txt('PLG_GROUPS_RESOURCES_RATING_VERY_GOOD');
            $ratingTitles[5] = Lang::txt('PLG_GROUPS_RESOURCES_RATING_EXCELLENT');
            $ratingLabels[1] = Lang::txt('PLG_GROUPS_RESOURCES_RATING_1_STAR');
            $ratingLabels[2] = Lang::txt('PLG_GROUPS_RESOURCES_RATING_2_STARS');
            $ratingLabels[3] = Lang::txt('PLG_GROUPS_RESOURCES_RATING_3_STARS');
            $ratingLabels[4] = Lang::txt('PLG_GROUPS_RESOURCES_RATING_4_STARS');
            $ratingLabels[5] = Lang::txt('PLG_GROUPS_RESOURCES_RATING_5_STARS');
            @endphp
            <tr>
            @if ($config->get('show_ranking'))
                <td class="ranking">
                    {{ $rankingFormatted }}
                    <span class="rank-{{ $r }}">
                        {{ $rankingLabel }}
                    </span>
                </td>
            @elseif ($config->get('show_rating'))
                <td class="rating">
                    <span class="avgrating{{ $class }}">
                        <span>{{ $ratingLabel }}</span>&nbsp;
                    </span>
                </td>
            @endif
                <td>
                    <a href="{{ $line->href }}"
                        class="link link-hover link-primary fixedResourceTip"
                        title="DOM:rsrce{{ $line->id }}">{{ $line->title }}</a>
                    <div class="hide" id="rsrce{{ $line->id }}">
                        <h4>{{ $line->title }}</h4>
                        <div>
                            <table class="table table-sm" summary="{{ $line->title }}">
                                <tbody>
                                    <tr>
                                        <th>{{ Lang::txt('PLG_GROUPS_RESOURCES_TYPE') }}</th>
                                        <td>{{ $line->section }}</td>
                                    </tr>
                            @if ($contributors && count($contributors))
                                    <tr>
                                        <th>{{ Lang::txt('PLG_GROUPS_RESOURCES_CONTRIBUTORS') }}</th>
                                        <td>
                                            @php
                                            $names = [];
                                            foreach ($contributors as $c) {
                                                $names[] = e($c->name);
                                            }
                                            echo implode(', ', $names);
                                            @endphp
                                        </td>
                                    </tr>
                            @endif
                                    <tr>
                                        <th>{{ Lang::txt('PLG_GROUPS_RESOURCES_DATE') }}</th>
                                        <td>{{ $dateFormatted }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ $avgRatingLabel }}</th>
                                        <td>
                                            <span class="avgrating{{ $class }}">
                                                <span>{{ $ratingLabel }}</span>&nbsp;
                                            </span>
                                            ({{ $line->times_rated }})
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{ Lang::txt('PLG_GROUPS_RESOURCES_RATE_THIS') }}</th>
                                        <td>
                                            <ul class="starsz{{ $myclass }}">
                                            @for ($s = 1; $s <= 5; $s++)
                                                <li class="str{{ $s }}">
                                                    <a href="{{ $ratingUrls[$s] }}"
                                                        title="{{ $ratingTitles[$s] }}">{{ $ratingLabels[$s] }}</a>
                                                </li>
                                            @endfor
                                            </ul>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        {!! \Hubzero\Utility\Str::truncate($line->itext, 300) !!}
                    </div>
                </td>
                <td class="type">{{ $line->area }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
@else
    <p class="alert alert-info">{{ Lang::txt('PLG_GROUPS_RESOURCES_NONE') }}</p>
@endif
