{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
if (count($citations) > 0) {
    $__view->js();

    $formatter = new \Components\Citations\Helpers\Format();
    $formatter->setTemplate($citationTemplate);

    $base = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=citations';
}
@endphp

@if (count($citations) > 0)
    <div id="browsebox">
        <table class="table table-zebra citations entries">
            <thead>
                <tr>
                    <th class="w-10">
                        <input type="checkbox" class="checkbox checkbox-sm checkall-download" />
                    </th>
                    <th colspan="5">{{ Lang::txt('PLG_GROUPS_CITATIONS') }}</th>
                </tr>
            </thead>
            <tbody>
                @php $x = 0; @endphp
                @foreach ($citations as $cite)
                    <tr>
                        <td>
                            <input type="checkbox"
                                class="checkbox checkbox-sm download-marker"
                                name="download_marker[]"
                                value="{{ $cite->id }}" />
                        </td>
                        @if ($label != 'none')
                            <td class="citation-label {{ $citations_label_class }}">
                                @php
                                $type = '';
                                foreach ($types as $t) {
                                    if ($t->id == $cite->type) {
                                        $type = $t->type_title;
                                    }
                                }
                                $type = ($type != '') ? $type : 'Generic';
                                @endphp

                                @switch($label)
                                    @case('number')
                                        <span class="badge badge-ghost">{{ $x }}.</span>
                                        @break
                                    @case('type')
                                        <span class="badge badge-outline">{{ $type }}</span>
                                        @break
                                    @case('both')
                                        <span class="badge badge-ghost">{{ $x }}.</span>
                                        <span class="badge badge-outline">{{ $type }}</span>
                                        @break
                                @endswitch
                            </td>
                        @endif
                        <td class="citation-container">
                            @php
                            $formatted = $cite->formatted
                                ? $cite->formatted
                                : $formatter->formatCitation(
                                    $cite,
                                    $filters['search'],
                                    $coins,
                                    $config
                                );

                            if ($cite->doi) {
                                $formatted = str_replace(
                                    'doi:' . $cite->doi,
                                    '<a href="' . $cite->url . '" rel="external">'
                                    . 'doi:' . $cite->doi . '</a>',
                                    $formatted
                                );
                            }
                            @endphp
                            {!! $formatted !!}

                            @php
                            $params = new \Hubzero\Html\Parameter($cite->params);
                            $citation_rollover = 0;
                            @endphp

                            @if ($citation_rollover && $cite->abstract != '')
                                <div class="citation-notes mt-2 text-sm opacity-70">
                                    @php
                                    $final = '';
                                    foreach ($cite->sponsors as $s) {
                                        $final .= '<a rel="external" href="'
                                            . $s->get('link') . '">'
                                            . $s->get('sponsor') . '</a>, ';
                                    }
                                    $showSponsors = $final != ''
                                        && $config->get('citation_sponsors', 'yes') == 'yes';
                                    @endphp
                                    @if ($showSponsors)
                                        <p class="sponsor">
                                            {{ Lang::txt('PLG_GROUPS_CITATIONS_ABSTRACT_BY') }}
                                            {!! substr($final, 0, -2) !!}
                                        </p>
                                    @endif
                                    <p>{!! nl2br(e($cite->abstract)) !!}</p>
                                </div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="bg-base-200 p-2 rounded text-sm">
                            @php
                            $singleCitationView = $config->get('citation_single_view', 0);
                            if (!$singleCitationView) {
                                echo $formatter->citationDetails(
                                    $cite,
                                    $database,
                                    $config,
                                    $openurl,
                                    true
                                );
                            }
                            @endphp

                            @if ($config->get('citation_show_badges', 'no') == 'yes')
                                {!! \Components\Citations\Helpers\Format::citationBadges($cite, $database) !!}
                            @endif

                            @if ($config->get('citation_show_tags', 'no') == 'yes')
                                {!! \Components\Citations\Helpers\Format::citationTags($cite, $database) !!}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endif
