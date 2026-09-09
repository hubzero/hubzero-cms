{{--
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$database = \Hubzero\Facades\App::get('db');
@endphp

{{-- Launcher layout metadata (big icons) --}}
@if (!empty($launcherLayout) && $publication->main == 1 && $publication->state == 1)
    <ul class="flex gap-4">
        @foreach ($sections as $section)
            @if (isset($section['name']) && isset($section['count']))
                <li class="meta-{{ $section['name'] }}">
                    @if ($section['name'] != 'usage')
                        <a href="{{ Route::url($publication->link() . '&active=' . $section['name']) }}"
                           title="{{ Lang::txt('COM_PUBLICATIONS_META_TITLE_' . strtoupper($section['name'])) }}">
                    @endif
                    <span class="icon"></span><span class="label">{{ $section['count'] }}</span>
                    @if ($section['name'] != 'usage')
                        </a>
                    @endif
                </li>
            @endif
        @endforeach
    </ul>
@elseif (!$publication->isPublished())
    {{-- Non-published version --}}
    @php
        $text = $publication->isDev()
            ? Lang::txt('COM_PUBLICATIONS_METADATA_DEV')
            : Lang::txt('COM_PUBLICATIONS_METADATA_UNAVAILABLE');
    @endphp
    <div class="alert alert-info">
        <p>{{ $text }}</p>
    </div>
@else
    @php
        $data = '';
        foreach ($sections as $section) {
            $data .= (!empty($section['metadata'])) ? $section['metadata'] : '';
        }

        $showRanking  = $params->get('show_ranking');
        $showAudience = $params->get('show_audience');
        $supportedTag = $params->get('supportedtag');
    @endphp

    @if ($showRanking || $showAudience || $supportedTag || $data)
        <div class="card bg-base-100 shadow-sm">
            <div class="card-body">

                @if ($params->get('show_ranking', 0))
                    @php
                        $rank = round($publication->ranking, 1);
                        $r = (10 * $rank);
                        $reviewsUrl = Route::url(
                            'index.php?option=' . $option
                            . '&id=' . $publication->id
                            . '&active=reviews'
                        );
                    @endphp
                    <dl class="rankinfo">
                        <dt class="ranking">
                            <span class="rank">
                                <span style="width: {{ $r }}%"
                                      id="rank-{{ $publication->id }}"
                                      aria-label="Ranking: {{ number_format($rank, 1) }} out of 10">
                                    This publication has a
                                </span>
                            </span>
                            {{ number_format($rank, 1) }} Ranking
                        </dt>
                        <dd>
                            <p>
                                Ranking is calculated from a formula comprised of
                                <a href="{{ $reviewsUrl }}">user reviews</a>
                                and usage statistics. <a href="about/ranking/">Learn more &rsaquo;</a>
                            </p>
                        </dd>
                    </dl>
                @endif

                {{-- Supported publication? --}}
                @php
                    $rt = new \Components\Publications\Helpers\Tags($database);
                    $supported = $rt->checkTagUsage(
                        $config->get('supportedtag'),
                        $publication->id
                    );
                @endphp

                @if ($supported)
                    @php
                        $tag = \Components\Tags\Models\Tag::oneByTag(
                            $config->get('supportedtag')
                        );
                        $sl = $config->get('supportedlink');
                        $tagLink = $sl
                            ? $sl
                            : Route::url('index.php?option=com_tags&tag=' . $tag->get('tag'));
                    @endphp
                    <p>
                        <a href="{{ $tagLink }}" class="badge badge-success">
                            {{ $tag->get('raw_tag') }}
                        </a>
                    </p>
                @endif

                {{-- Show audience --}}
                @if ($params->get('show_audience'))
                    @php
                        $ra = new \Components\Publications\Tables\Audience($database);
                        $audienceData = $ra->getAudience(
                            $publication->id,
                            $publication->version_id,
                            $getlabels = 1,
                            $numlevels = 4
                        );
                    @endphp
                    @include('view::_audience', [
                        'audience'     => $audienceData,
                        'showtips'     => true,
                        'numlevels'    => 4,
                        'audiencelink' => $params->get('audiencelink'),
                    ])
                @endif

                {{-- Archive version? --}}
                @if ($lastPubRelease && $lastPubRelease->id != $publication->version_id)
                    @php
                        $archiveUrl = Route::url(
                            'index.php?option=' . $option
                            . '&id=' . $publication->id
                            . '&v=' . $lastPubRelease->version_number
                        );
                    @endphp
                    <p>
                        {{ Lang::txt('COM_PUBLICATIONS_METADATA_ARCHIVE') }}
                        [<a href="{{ $archiveUrl }}">{{ $lastPubRelease->version_label }}</a>]
                        {{ Lang::txt('COM_PUBLICATIONS_METADATA_ARCHIVE_INFO') }}
                    </p>
                @endif

                {{-- Section data --}}
                {!! $data !!}
            </div>
        </div>
    @endif
@endif
