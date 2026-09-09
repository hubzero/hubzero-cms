{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->css();
$__view->js();

$citationObj = $citation;
$profile = User::getInstance($citationObj->uid);
$type = $citationObj->relatedType;
$sponsors = $citationObj->sponsors;

// Determine URL separator
$urlSeparator = PHP_EOL;
if (strstr($citationObj->url, ' ') !== false) {
    $urlSeparator = ' ';
} elseif (strstr($citationObj->url, "\t") !== false) {
    $urlSeparator = "\t";
}

// Get citation URL
$urls = array_map('trim', explode($urlSeparator, html_entity_decode($citationObj->url)));
$url = (filter_var($urls[0], FILTER_VALIDATE_URL)) ? $urls[0] : '';

// Get citation eprint
$eprints = array_map('trim', explode(PHP_EOL, html_entity_decode($citationObj->eprint)));
$eprintUrl = (filter_var($eprints[0], FILTER_VALIDATE_URL)) ? $eprints[0] : '';

// Custom URL handling
$customUrl = '';
$citationUrlFormat = $config->get('citation_url', 'url');
$citationUrlFormatString = $config->get('citation_custom_url', '');

if ($citationUrlFormatString != '') {
    preg_match_all('/\{(\w+)\}/', $citationUrlFormatString, $matches, PREG_SET_ORDER);
    if ($matches) {
        foreach ($matches as $match) {
            $field = strtolower($match[1]);
            $replace = $match[0];
            $replaceWith = '';
            if (property_exists($citationObj, $field)) {
                if (strstr($citationObj->$field, 'http')) {
                    $customUrl = $citationObj->$field;
                } else {
                    $replaceWith = $citationObj->$field;
                    $customUrl = str_replace($replace, $replaceWith, $citationUrlFormatString);
                }
            }
        }
    }
}

$citationURL = ($citationUrlFormat == 'custom' && $customUrl != '') ? $customUrl : $url;
$citationURL = ($eprintUrl && $eprintUrl != '') ? $eprintUrl : $citationURL;

// Abstract settings
$showAbstract = $config->get('citation_rollover', 'no');
$showAbstract = ($showAbstract == 'yes') ? 1 : 0;

$params = new \Hubzero\Config\Registry($citationObj->params);
$showThisAbstract = $params->get('rollover', $showAbstract);

// Tags and badges
$citTags = \Components\Citations\Helpers\Format::citationTags($citationObj, $database, false);
$citBadges = \Components\Citations\Helpers\Format::citationBadges($citationObj, $database, false);
$showTags = $config->get('citation_show_tags', 'yes');
$showBadges = $config->get('citation_show_badges', 'yes');

// Internal associations
$associationLinks = [];
foreach ($citationObj->resources()->whereEquals('#_citations_assoc.tbl', 'resource')->rows() as $a) {
    $associationLinks[] = '<a href="' . Route::url($a->link()) . '">' . $a->title . '</a>';
}

$area = Request::getString('area', 'about');
@endphp

{{-- Header --}}
<header class="mb-6">
    <div class="flex flex-col lg:flex-row gap-6">
        <div class="flex-1">
            <h2 class="text-2xl font-bold mb-2">
                {{ $citationObj->title }}
                @if (User::get('id') == $citationObj->uid)
                    <a class="btn btn-ghost btn-sm ml-2"
                        href="{{ Route::url('index.php?option=com_citations&task=edit&id=' . $citationObj->id) }}">
                        Edit
                    </a>
                @endif
            </h2>

            @if ($citationObj->author)
                <div class="text-sm opacity-70 mb-3">
                    <span class="font-semibold">{{ Lang::txt('COM_CITATIONS_BY') }}:</span>
                    @php
                    $a = [];
                    $authorList = array_map('trim', explode(';', $citationObj->author));
                    foreach ($authorList as $author) {
                        preg_match('/\{\{(.*?)\}\}/s', $author, $authorMatches);
                        if (!empty($authorMatches)) {
                            if (is_numeric($authorMatches[1])) {
                                $user = User::getInstance($authorMatches[1]);
                                if (is_object($user)) {
                                    $a[] = '<a class="link link-hover link-primary" rel="external" href="'
                                        . Route::url('index.php?option=com_members&id=' . $authorMatches[1])
                                        . '">' . str_replace($authorMatches[0], '', $author) . '</a>';
                                } else {
                                    $a[] = $author;
                                }
                            }
                        } else {
                            $a[] = $author;
                        }
                    }
                    @endphp
                    {!! implode(', ', $a) !!}
                </div>
            @endif

            @if ($citationObj->abstract && $showThisAbstract)
                <div class="prose max-w-none mb-4">
                    @php
                    $max = 1000;
                    $abstract = nl2br(e($citationObj->abstract));
                    @endphp
                    @if (strlen($abstract) > $max)
                        {!! substr($abstract, 0, $max) !!}
                        <span class="show-more-hellip">&hellip;</span>
                        <a id="show-more-button" href="javascript:void(0);" class="link link-primary text-sm">show more</a>
                        <span class="show-more-text hidden">{!! substr($abstract, $max) !!}</span>
                    @else
                        {!! $abstract !!}
                    @endif
                </div>
            @endif

            {{-- Formatted citation --}}
            <div class="text-sm opacity-80 mb-3">
                @php
                $fmtTemplate = \Components\Citations\Models\Format::getDefault();
                $cf = new \Components\Citations\Helpers\Format();
                $cf->setTemplate($fmtTemplate);
                @endphp
                {{ strip_tags($cf->formatCitation($citationObj, null, false, $config)) }}
            </div>

            {{-- Download links --}}
            <div class="flex gap-2 mb-3">
                @php
                $bibtexUrl = Route::url(
                    'index.php?option=com_citations&task=download&citationFormat=bibtex&id='
                    . $citationObj->id . '&no_html=1'
                );
                $endnoteUrl = Route::url(
                    'index.php?option=com_citations&task=download&citationFormat=endnote&id='
                    . $citationObj->id . '&no_html=1'
                );
                @endphp
                <a class="btn btn-outline btn-sm"
                    href="{{ $bibtexUrl }}"
                    title="Download in BibTex Format">
                    {{ Lang::txt('COM_CITATIONS_DOWNLOAD_BIBTEX') }}
                </a>
                <a class="btn btn-outline btn-sm"
                    href="{{ $endnoteUrl }}"
                    title="Download in Endnote Format">
                    {{ Lang::txt('COM_CITATIONS_DOWNLOAD_ENDNOTE') }}
                </a>
            </div>
        </div>

        <div class="lg:w-64 shrink-0">
            @if ($citationURL != '')
                <a class="btn btn-primary w-full mb-2" rel="external" href="{{ $citationURL }}">
                    {{ Lang::txt('COM_CITATIONS_VIEW_ARTICLE') }}
                </a>
                @php
                $findUrl = Route::url(
                    'index.php?option=com_citations&task=view&id='
                    . $citationObj->id . '&area=find#find'
                );
                @endphp
                <a class="btn btn-outline btn-sm w-full mb-2" href="{{ $findUrl }}">
                    {{ Lang::txt('COM_CITATIONS_FINDTHISTEXT') }}
                </a>
            @else
                @php
                $findUrl = Route::url(
                    'index.php?option=com_citations&task=view&id='
                    . $citationObj->id . '&area=find#find'
                );
                @endphp
                <a class="btn btn-primary w-full mb-2" href="{{ $findUrl }}">
                    {{ Lang::txt('COM_CITATIONS_FINDTHISTEXT') }}
                </a>
            @endif

            @if (count($sponsors) > 0)
                <div class="card bg-base-200 mt-4">
                    <div class="card-body p-4">
                        <h3 class="card-title text-sm">{{ Lang::txt('COM_CITATIONS_SPONSORED_BY') }}</h3>
                        <ul class="space-y-2">
                            @foreach ($sponsors as $s)
                                <li>
                                    <a class="link link-hover" rel="external" href="{{ $s->link }}">
                                        @if ($s->image)
                                            <img src="{{ $s->image }}" alt="{{ $s->sponsor }}" class="max-h-8" />
                                        @else
                                            {{ $s->sponsor }}
                                        @endif
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif
        </div>
    </div>
</header>

{{-- Tab navigation --}}
@php
$menu = [
    'about' => Lang::txt('COM_CITATIONS_ABOUT'),
    'resources' => Lang::txt('COM_CITATIONS_CITED'),
    'reviews' => Lang::txt('COM_CITATIONS_REVIEWS'),
    'find' => Lang::txt('COM_CITATIONS_FINDTHISTEXT'),
];
@endphp
<div role="tablist" class="tabs tabs-border mb-6">
    @foreach ($menu as $k => $v)
        @if ($k == 'resources' && count($associationLinks) < 1)
            @continue
        @endif
        @php
        $tabUrl = Route::url(
            'index.php?option=com_citations&task=view&id='
            . $citationObj->id . '&area=' . $k
        );
        @endphp
        <a role="tab"
            class="tab {{ $k == $area ? 'tab-active' : '' }}"
            href="{{ $tabUrl }}">
            {{ $v }}
        </a>
    @endforeach
</div>

{{-- About tab --}}
@if ($area == 'about')
    <section id="about">
        <h3 class="text-lg font-bold mb-4">{{ Lang::txt('COM_CITATIONS_ABOUT') }}</h3>
        <table class="table table-zebra">
            <tbody>
                <tr>
                    <th class="w-1/4">{{ Lang::txt('COM_CITATIONS_TYPE') }}</th>
                    <td>
                        <a class="link link-hover link-primary"
                            href="{{ Route::url('index.php?option=com_citations&task=browse&type=' . $type->get('id')) }}">
                            {{ $type->get('type_title') }}
                        </a>
                    </td>
                </tr>

                @if ($citationObj->journal)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_JOURNAL') }}</th>
                        <td>{{ $citationObj->journal }}</td>
                    </tr>
                @endif

                @if ($citationObj->publisher)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_PUBLISHER') }}</th>
                        <td>{{ $citationObj->publisher }}</td>
                    </tr>
                @endif

                @if ($citationObj->booktitle)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_BOOK_TITLE') }}</th>
                        <td>{{ $citationObj->booktitle }}</td>
                    </tr>
                @endif

                @if ($citationObj->short_title)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_SHORT_TITLE') }}</th>
                        <td>{{ $citationObj->short_title }}</td>
                    </tr>
                @endif

                @if ($citationObj->editor)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_EDITORS') }}</th>
                        <td>{{ $citationObj->editor }}</td>
                    </tr>
                @endif

                @if ($citationObj->cite)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_CITE_KEY') }}</th>
                        <td>{{ $citationObj->cite }}</td>
                    </tr>
                @endif

                @if ($citationObj->ref_type)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_REF_TYPE') }}</th>
                        <td>{{ $citationObj->ref_type }}</td>
                    </tr>
                @endif

                @if ($citationObj->date_submit && $citationObj->date_submit != '0000-00-00 00:00:00')
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_DATE_SUBMITTED') }}</th>
                        <td>{{ date('F d, Y', strtotime($citationObj->date_submit)) }}</td>
                    </tr>
                @endif

                @if ($citationObj->date_accept && $citationObj->date_accept != '0000-00-00 00:00:00')
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_DATE_ACCEPTED') }}</th>
                        <td>{{ date('F d, Y', strtotime($citationObj->date_accept)) }}</td>
                    </tr>
                @endif

                @if ($citationObj->date_publish && $citationObj->date_publish != '0000-00-00 00:00:00')
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_DATE_PUBLISHED') }}</th>
                        <td>{{ date('F d, Y', strtotime($citationObj->date_publish)) }}</td>
                    </tr>
                @endif

                @if ($citationObj->year)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_YEAR') }}</th>
                        <td>{{ $citationObj->year }}</td>
                    </tr>
                @endif

                @if ($citationObj->month)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_MONTH') }}</th>
                        <td>{{ $citationObj->month }}</td>
                    </tr>
                @endif

                @if ($citationObj->author_address)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_AUTHOR_ADDRESS') }}</th>
                        <td>{!! nl2br(e($citationObj->author_address)) !!}</td>
                    </tr>
                @endif

                @if ($citationObj->volume)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_VOLUME') }}</th>
                        <td>{{ $citationObj->volume }}</td>
                    </tr>
                @endif

                @if ($citationObj->number)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_ISSUE') }}</th>
                        <td>{{ $citationObj->number }}</td>
                    </tr>
                @endif

                @if ($citationObj->pages)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_PAGES') }}</th>
                        <td>{{ $citationObj->pages }}</td>
                    </tr>
                @endif

                @if ($citationObj->isbn)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_ISBN') }}</th>
                        <td>{{ $citationObj->isbn }}</td>
                    </tr>
                @endif

                @if ($citationObj->doi)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_DOI') }}</th>
                        <td>
                            <a class="link link-hover link-primary"
                                href="https://doi.org/{{ $citationObj->doi }}">
                                {{ $citationObj->doi }}
                            </a>
                        </td>
                    </tr>
                @endif

                @if ($citationObj->call_number)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_CALL_NUMBER') }}</th>
                        <td>{{ $citationObj->call_number }}</td>
                    </tr>
                @endif

                @if ($citationObj->accession_number)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_ACCESSION_NUMBER') }}</th>
                        <td>{{ $citationObj->accession_number }}</td>
                    </tr>
                @endif

                @if ($citationObj->series)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_SERIES') }}</th>
                        <td>{{ $citationObj->series }}</td>
                    </tr>
                @endif

                @if ($citationObj->edition)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_EDITION') }}</th>
                        <td>{{ $citationObj->edition }}</td>
                    </tr>
                @endif

                @if ($citationObj->school)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_SCHOOL') }}</th>
                        <td>{{ $citationObj->school }}</td>
                    </tr>
                @endif

                @if ($citationObj->institution)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_INSTITUTION') }}</th>
                        <td>{{ $citationObj->institution }}</td>
                    </tr>
                @endif

                @if ($citationObj->address)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_ADDRESS') }}</th>
                        <td>{{ $citationObj->address }}</td>
                    </tr>
                @endif

                @if ($citationObj->location)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_LOCATION') }}</th>
                        <td>{{ $citationObj->location }}</td>
                    </tr>
                @endif

                @if ($citationObj->howpublished)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_PUBLISH_METHOD') }}</th>
                        <td>{{ $citationObj->howpublished }}</td>
                    </tr>
                @endif

                @if ($citationObj->language)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_LANGUAGE') }}</th>
                        <td>{{ $citationObj->language }}</td>
                    </tr>
                @endif

                @if ($citationObj->label)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_LABEL') }}</th>
                        <td>{{ $citationObj->label }}</td>
                    </tr>
                @endif

                @if ($citationObj->notes)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_NOTES') }}</th>
                        <td>{!! nl2br(e($citationObj->notes)) !!}</td>
                    </tr>
                @endif

                @if ($citationObj->research_notes)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_RESEARCH_NOTES') }}</th>
                        <td>{!! nl2br(e($citationObj->research_notes)) !!}</td>
                    </tr>
                @endif

                @if ($citationObj->keywords)
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_KEYWORDS') }}</th>
                        <td>{!! nl2br(e($citationObj->keywords)) !!}</td>
                    </tr>
                @endif

                @if (is_array($citTags) && count($citTags) > 0 && $showTags == 'yes')
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_TAGS') }}</th>
                        <td>
                            {!! \Components\Citations\Helpers\Format::citationTags($citationObj, App::get('db')) !!}
                        </td>
                    </tr>
                @endif

                @if (is_array($citBadges) && count($citBadges) > 0 && $showBadges == 'yes')
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_BADGES') }}</th>
                        <td>
                            {!! \Components\Citations\Helpers\Format::citationBadges($citationObj, App::get('db')) !!}
                        </td>
                    </tr>
                @endif

                @if (isset($citationObj->uid))
                    @if (is_object($profile) && $profile->get('id'))
                        <tr>
                            <th>{{ Lang::txt('COM_CITATIONS_SUBMITTED_BY') }}</th>
                            <td>
                                <a class="link link-hover link-primary"
                                    href="{{ Route::url('index.php?option=com_members&id=' . $profile->get('id')) }}">
                                    {{ $profile->get('name') }}
                                </a>
                            </td>
                        </tr>
                    @endif
                @endif

                @if (isset($citationObj->created) && $citationObj->created != '0000-00-00 00:00:00')
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_SUBMITTED') }}</th>
                        <td>{{ date('l, F d, Y @ g:ia', strtotime($citationObj->created)) }}</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </section>
@endif

{{-- Resources tab --}}
@if ($area == 'resources')
    <section id="resources">
        <h3 class="text-lg font-bold mb-4">{{ Lang::txt('COM_CITATIONS_CITED') }}</h3>
        @if (count($associationLinks) > 0)
            <p class="mb-3">{{ Lang::txt('COM_CITATIONS_CITED_DESC') }}</p>
            <ul class="list-disc list-inside space-y-1">
                @foreach ($associationLinks as $aLink)
                    <li>{!! $aLink !!}</li>
                @endforeach
            </ul>
        @else
            <div class="alert alert-info">
                <p>{{ Lang::txt('COM_CITATIONS_CITED_NONE') }}</p>
            </div>
        @endif
    </section>
@endif

{{-- Reviews tab --}}
@if ($area == 'reviews')
    <section id="reviews">
        <h3 class="text-lg font-bold mb-4">{{ Lang::txt('COM_CITATIONS_REVIEWS') }}</h3>
        @php
        $reviewParams = [
            $citationObj,
            $option,
            Route::url('index.php?option=' . $option . '&task=view&id=' . $citationObj->id . '&area=reviews'),
            ['png', 'jpg', 'gif', 'tiff', 'pdf']
        ];
        $comments = Event::trigger('hubzero.onAfterDisplayContent', $reviewParams);
        if (isset($comments[0])) {
            echo $comments[0];
        }
        @endphp
    </section>
@endif

{{-- Find this text tab --}}
@if ($area == 'find')
    <section id="find">
        <h3 class="text-lg font-bold mb-4">{{ Lang::txt('COM_CITATIONS_FINDTHISTEXT') }}</h3>
        <p class="mb-4">{{ Lang::txt('COM_CITATIONS_FINDTHISTEXT_DESC') }}</p>

        <table class="table table-zebra">
            <tbody>
                @if ($citationObj->doi)
                    <tr>
                        <th class="w-1/4">{{ Lang::txt('COM_CITATIONS_DOI_RESOLVER') }}</th>
                        <td>
                            <a class="link link-hover link-primary"
                                rel="external"
                                href="https://doi.org/{{ $citationObj->doi }}">
                                https://doi.org/{{ $citationObj->doi }}
                            </a>
                        </td>
                    </tr>
                @endif

                @if ($config->get('citation_openurl', 1))
                    <tr>
                        <th>{{ Lang::txt('COM_CITATIONS_LOCAL_LIBRARY') }}</th>
                        <td>
                            {{ Lang::txt('COM_CITATIONS_LOCAL_LIBRARY_DESC') }}
                            @if ($openUrl)
                                <ul class="mt-2">
                                    <li>
                                        {!! \Components\Citations\Helpers\Format::citationOpenUrl($openUrl, $citationObj) !!}
                                    </li>
                                </ul>
                            @endif
                        </td>
                    </tr>
                @endif

                <tr>
                    <th>{{ Lang::txt('COM_CITATIONS_GOOGLE_SCHOLAR') }}</th>
                    <td>
                        @php
                        $query = '';
                        if ($citationObj->doi) {
                            $query .= $citationObj->doi;
                        } elseif ($citationObj->title) {
                            $query .= $citationObj->title;
                        }
                        $scholarImgSrc = Request::base(true)
                            . '/core/components/com_citations/assets/img/googlescholar.gif';
                        @endphp
                        <a rel="nofollow external"
                            title="Google Scholar Search Results"
                            href="http://scholar.google.com/scholar?q={{ $query }}">
                            <img src="{{ $scholarImgSrc }}"
                                alt="Google Scholar Search Results"
                                width="100" />
                        </a>
                    </td>
                </tr>

                <tr>
                    <th>{{ Lang::txt('COM_CITATIONS_OTHER_SOURCES') }}</th>
                    <td>
                        <ul class="list-disc list-inside">
                            @php
                            $deepDyveQuery = str_replace(' ', '+', $citationObj->title);
                            $deepDyveUrl = 'http://www.deepdyve.com/search?query=' . $deepDyveQuery;
                            @endphp
                            <li>
                                <a class="link link-hover link-primary"
                                    rel="external"
                                    href="{{ $deepDyveUrl }}">
                                    {{ Lang::txt('COM_CITATIONS_DEEP_DYVE') }}
                                </a>
                                {{ Lang::txt('COM_CITATIONS_DEEP_DYVE_RENT') }}
                            </li>
                        </ul>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
@endif

@php
// COinS output
$hubName = Config::get('sitename');
$hubUrl = rtrim(Request::base(), '/');

switch (strtolower($type->get('type_title'))) {
    case 'book':
    case 'book section':
    case 'inbook':
    case 'conference':
    case 'proceedings':
    case 'inproceedings':
    case 'conference proceedings':
        $coinsType = 'book';
        break;
    case 'journal':
    case 'article':
    case 'journal article':
    default:
        $coinsType = 'journal';
        break;
}

$title = html_entity_decode($citationObj->title);

$coinsData = [
    'ctx_ver=Z39.88-2004',
    "rft_val_fmt=info:ofi/fmt:kev:mtx:{$coinsType}",
    "rfr_id=info:sid/{$hubUrl}:{$hubName}",
    "rft.atitle={$title}",
];

if ($citationObj->doi) {
    $coinsData[] = 'rft_id=info:doi/' . $citationObj->doi;
}
if ($citationObj->isbn) {
    $coinsData[] = ($coinsType == 'book') ? 'rft.isbn=' . $citationObj->isbn : 'rft.issn=' . $citationObj->isbn;
}
if ($citationObj->url) {
    $coinsData[] = 'rft_id=' . htmlentities($citationObj->url);
}
if ($citationObj->volume) {
    $coinsData[] = 'rft.volume=' . $citationObj->volume;
}
if ($citationObj->number) {
    $coinsData[] = 'rft.issue=' . $citationObj->number;
}
if ($citationObj->pages) {
    $coinsData[] = 'rft.pages=' . $citationObj->pages;
}
if ($citationObj->journal) {
    $coinsData[] = 'rft.jtitle=' . $citationObj->journal;
}
if ($citationObj->author) {
    $coinAuthors = array_filter(array_values(explode(';', $citationObj->author)));
    foreach ($coinAuthors as $ca) {
        $coinsData[] = 'rft.au=' . trim($ca);
    }
}

$chars = [' ', '/', ':', '"', '&amp;'];
$replace = ['%20', '%2F', '%3A', '%22', '%26'];
$coinsDataStr = str_replace($chars, $replace, implode('&', $coinsData));

if ($config->get('citation_coins', 1)) {
    echo '<span class="Z3988" title="' . $coinsDataStr . '"></span>';
}
@endphp
