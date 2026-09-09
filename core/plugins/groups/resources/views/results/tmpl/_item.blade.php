{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
$params = $row->params;

$accessClasses = [
    0 => 'public',
    1 => 'registered',
    2 => 'special',
    3 => 'protected',
    4 => 'private',
];
$cls = $accessClasses[$row->access] ?? 'public';

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

<li class="{{ $cls }} resource card card-compact bg-base-100 shadow-sm mb-3">
    <div class="card-body">
        <p class="card-title text-base">
            <a class="link link-hover link-primary" href="{{ $row->href }}">
                {{ e(stripslashes($row->title)) }}
            </a>
        </p>

        @if ($params->get('show_ranking'))
            @php
            $row->ranking = round($row->ranking, 1);
            $r = (10 * $row->ranking);
            if (intval($r) < 10) {
                $r = '0' . $r;
            }
            @endphp
            <div class="metadata">
                <dl class="rankinfo">
                    <dt class="ranking">
                        <span class="rank-{{ $r }}">
                            {{ Lang::txt('PLG_GROUPS_RESOURCES_THIS_HAS') }}
                        </span>
                        {{ number_format($row->ranking, 1) . ' ' . Lang::txt('PLG_GROUPS_RESOURCES_RANKING') }}
                    </dt>
                    <dd>
                        <p>{{ Lang::txt('PLG_GROUPS_RESOURCES_RANKING_EXPLANATION') }}</p>
                        <div>
                            @php
                            $database = App::get('db');

                            if ($row->isTool()) {
                                $stats = new \Components\Resources\Helpers\Usage\Tools(
                                    $database,
                                    $row->id,
                                    $row->category,
                                    $row->rating
                                );
                            } else {
                                $stats = new \Components\Resources\Helpers\Usage\Andmore(
                                    $database,
                                    $row->id,
                                    $row->category,
                                    $row->rating
                                );
                            }
                            echo $stats->display();
                            @endphp
                        </div>
                    </dd>
                </dl>
            </div>
        @elseif ($params->get('show_rating'))
            @php
            $class = ' ' . ($ratingStarClasses[$row->rating] ?? 'no-stars');
            @endphp
            <div class="metadata">
                <p class="rating">
                    <span class="avgrating{{ $class }}">
                        <span>
                            {{ Lang::txt(
                                'PLG_GROUPS_RESOURCES_OUT_OF_5_STARS',
                                $row->rating
                            ) }}
                        </span>&nbsp;
                    </span>
                </p>
            </div>
        @endif

        <p class="details text-sm opacity-70">
            {{ $row->date }} <span>|</span> {{ stripslashes($row->type->get('type')) }}
            @if ($authors = $row->authorsList())
                <span>|</span> {{ Lang::txt('PLG_GROUPS_RESOURCES_CONTRIBUTORS') . ': ' . $authors }}
            @endif
        </p>

        @php
        $text = $row->ftext;
        if ($row->itext) {
            $text = $row->itext;
        }
        $text = strip_tags($text == null ? '' : $text);
        echo \Hubzero\Utility\Str::truncate(\Hubzero\Utility\Sanitize::clean(stripslashes($text)), 200);
        @endphp

        <p class="href text-xs opacity-50">{{ Request::base() . ltrim($row->href, '/') }}</p>
    </div>
</li>
