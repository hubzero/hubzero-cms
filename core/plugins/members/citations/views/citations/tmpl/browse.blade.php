{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
$__view->css('citations.css')->js();
$base = 'index.php?option=com_members&id=' . $member->get('id') . '&active=citations';
@endphp

@if (isset($messages))
    @foreach ($messages as $message)
        <div class="alert alert-{{ $message['type'] }}">{{ $message['message'] }}</div>
    @endforeach
@endif

@if ($isAdmin)
    <div id="content-header-extra" class="flex flex-wrap gap-2 mb-6">
        <a class="btn btn-primary btn-sm" href="{{ Route::url($base . '&action=add') }}">
            {{ Lang::txt('PLG_MEMBERS_CITATIONS_SUBMIT_CITATION') }}
        </a>
        <a class="btn btn-secondary btn-sm" href="{{ Route::url($base . '&action=import') }}">
            {{ Lang::txt('PLG_MEMBERS_CITATIONS_IMPORT_CITATION') }}
        </a>
        <a class="btn btn-ghost btn-sm" href="{{ Route::url($base . '&action=settings') }}">
            {{ Lang::txt('PLG_MEMBERS_CITATIONS_SET_FORMAT') }}
        </a>
    </div>
@endif

<div id="browsebox">
    <form action="{{ Route::url(Request::current()) }}" id="citeform" method="GET" class="withBatchDownload">
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
            {{-- Main content --}}
            <div class="lg:col-span-3">
                {{-- Search bar --}}
                <div class="flex gap-2 mb-6">
                    <fieldset class="flex-1">
                        <legend class="sr-only">
                            {{ Lang::txt('PLG_MEMBERS_CITATIONS_SEARCH_CITATIONS') }}
                        </legend>
                        <input type="text"
                            name="filters[search]"
                            id="entry-search-field"
                            class="input input-bordered w-full"
                            value="{{ e($filters['search']) }}"
                            placeholder="{{ Lang::txt('PLG_MEMBERS_CITATIONS_SEARCH_CITATIONS_PLACEHOLDER') }}" />
                    </fieldset>
                    <button type="submit" class="btn btn-primary btn-sm">
                        Search
                    </button>
                </div>

                {{-- Citations table --}}
                @if ($citations->count() > 0)
                    <table class="table table-zebra w-full">
                        <thead>
                            <tr>
                                <th class="w-10">
                                    <input type="checkbox" class="checkbox checkbox-sm checkall-download" />
                                </th>
                                <th colspan="6">{{ Lang::txt('PLG_MEMBERS_CITATIONS') }}</th>
                            </tr>
                            @if ($isAdmin)
                                <tr class="hidden">
                                    <td colspan="7">
                                        <div class="flex gap-2">
                                            <a class="btn btn-sm btn-success bulk"
                                                data-link="{{ Route::url($base . '&action=publish&bulk=true') }}">
                                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_PUBLISH_SELECTED') }}
                                            </a>
                                            <a class="btn btn-sm btn-error bulk"
                                                data-protected="true"
                                                data-link="{{ Route::url($base . '&action=delete&bulk=true') }}">
                                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_DELETE_SELECTED') }}
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </thead>
                        <tbody>
                            @foreach ($citations as $cite)
                                @php
                                    $unpubClass = ($cite->published == $cite::STATE_UNPUBLISHED) ? 'opacity-50' : '';
                                @endphp
                                <tr class="citation-row {{ $unpubClass }}">
                                    <td>
                                        <input type="checkbox"
                                            class="checkbox checkbox-sm download-marker"
                                            name="download_marker[]"
                                            value="{{ $cite->id }}" />
                                    </td>
                                    @if ($label != 'none')
                                        <td class="citation-label {{ $citations_label_class }}">
                                            @php
                                                $type = 'Generic';
                                                foreach ($types as $t) {
                                                    if ($t->id == $cite->type) {
                                                        $type = $t->type_title;
                                                    }
                                                }
                                            @endphp
                                            @if ($label == 'number')
                                                <span class="badge badge-ghost">{{ $cite->id }}.</span>
                                            @elseif ($label == 'type')
                                                <span class="badge badge-outline">{{ $type }}</span>
                                            @elseif ($label == 'both')
                                                <span class="badge badge-ghost">{{ $cite->id }}.</span>
                                                <span class="badge badge-outline">{{ $type }}</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="citation-container">
                                        @php
                                            $formatted = $cite->formatted(
                                                $config->toArray(),
                                                $filters['search']
                                            );
                                            if ($cite->doi) {
                                                $formatted = str_replace(
                                                    'doi:' . $cite->doi,
                                                    '<a href="' . $cite->url . '" rel="external">doi:' . $cite->doi . '</a>',
                                                    $formatted
                                                );
                                            }
                                        @endphp
                                        {!! $formatted !!}

                                        @php
                                            $citation_rollover = 0;
                                        @endphp
                                        @if ($citation_rollover && $cite->abstract != '')
                                            <div class="citation-notes mt-2 text-sm text-base-content/70">
                                                @php
                                                    $sponsors = $cite->sponsors;
                                                    $final = '';
                                                    if ($sponsors) {
                                                        foreach ($sponsors as $s) {
                                                            $final .= '<a rel="external" href="' . $s->get('link') . '">' . $s->get('sponsor') . '</a>, ';
                                                        }
                                                    }
                                                    $showSponsors = $config->get('citation_sponsors', 'yes');
                                                @endphp
                                                @if ($final != '' && $showSponsors == 'yes')
                                                    @php $final = substr($final, 0, -2); @endphp
                                                    <p class="sponsor">
                                                        {{ Lang::txt('PLG_MEMBERS_CITATIONS_ABSTRACT_BY') }}
                                                        {!! $final !!}
                                                    </p>
                                                @endif
                                                <p>{!! nl2br(e($cite->abstract)) !!}</p>
                                            </div>
                                        @endif
                                    </td>
                                    @if ($isAdmin === true)
                                        @php
                                            $editUrl = Route::url($base . '&action=edit&cid=' . $cite->id);
                                            $deleteUrl = Route::url($base . '&action=delete&cid=' . $cite->id);
                                            $publishUrl = Route::url($base . '&action=publish&cid=' . $cite->id);
                                            $publishLabel = ($cite->published == $cite::STATE_PUBLISHED)
                                                ? Lang::txt('PLG_MEMBERS_CITATIONS_UNPUBLISH')
                                                : Lang::txt('PLG_MEMBERS_CITATIONS_PUBLISH');
                                            $publishIsStrong = ($cite->published != $cite::STATE_PUBLISHED);
                                        @endphp
                                        <td class="w-10">
                                            <a class="btn btn-ghost btn-xs" href="{{ $editUrl }}">
                                                <span>{{ Lang::txt('PLG_MEMBERS_CITATIONS_EDIT') }}</span>
                                            </a>
                                        </td>
                                        <td class="w-10">
                                            <a class="btn btn-ghost btn-xs text-error protected" href="{{ $deleteUrl }}">
                                                <span>{{ Lang::txt('PLG_MEMBERS_CITATIONS_DELETE') }}</span>
                                            </a>
                                        </td>
                                        <td class="w-10">
                                            <a class="btn btn-ghost btn-xs {{ $publishIsStrong ? 'font-bold' : '' }}" href="{{ $publishUrl }}">
                                                <span>{{ $publishLabel }}</span>
                                            </a>
                                        </td>
                                    @endif
                                </tr>
                                <tr>
                                    @php
                                        $colspan = ($label == 'none') ? 5 : 6;
                                        $detailsClass = ($cite->published == $cite::STATE_UNPUBLISHED) ? 'opacity-50' : '';
                                    @endphp
                                    <td colspan="{{ $colspan }}" class="citation-details {{ $detailsClass }}">
                                        {!! $cite->citationDetails($openurl) !!}
                                        @if ($config->get('citations_show_badges', 'yes') == 'yes')
                                            {!! $cite->badgeCloud() !!}
                                        @endif
                                        @if ($config->get('citations_show_tags', 'yes') == 'yes')
                                            {!! $cite->tagCloud() !!}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-warning">
                        {{ Lang::txt('PLG_MEMBERS_CITATIONS_NO_CITATIONS_FOUND') }}
                    </div>
                @endif

                {!! $citations->pagination !!}
            </div>

            {{-- Sidebar --}}
            <div class="lg:col-span-1 space-y-6">
                {{-- Batch download --}}
                <fieldset id="download-batch" class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <strong>{{ Lang::txt('PLG_MEMBERS_CITATIONS_EXPORT_MULTIPLE') }}</strong>
                        <p class="text-sm text-base-content/70 my-2">
                            {{ Lang::txt('PLG_MEMBERS_CITATIONS_EXPORT_MULTIPLE_DESC') }}
                        </p>
                        <div class="flex gap-2">
                            <input type="submit"
                                name="download"
                                class="btn btn-sm btn-outline"
                                id="download-endnote"
                                value="{{ Lang::txt('PLG_MEMBERS_CITATIONS_ENDNOTE') }}" />
                            <input type="submit"
                                name="download"
                                class="btn btn-sm btn-outline"
                                id="download-bibtex"
                                value="{{ Lang::txt('PLG_MEMBERS_CITATIONS_BIBTEX') }}" />
                        </div>
                        <iframe id="download-frame" class="hidden"></iframe>
                    </div>
                </fieldset>

                {{-- Filters --}}
                <fieldset class="card bg-base-100 shadow-sm">
                    <div class="card-body space-y-4">
                        <label class="form-control w-full" for="filter_type">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_TYPE') }}</span>
                            </div>
                            <select name="filters[type]" id="filter_type" class="select select-bordered w-full select-sm">
                                <option value="">{{ Lang::txt('PLG_MEMBERS_CITATIONS_ALL') }}</option>
                                @foreach ($types as $t)
                                    <option value="{{ $t->id }}" {{ $filters['type'] == $t->id ? 'selected' : '' }}>
                                        {{ $t->type_title }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <label class="form-control w-full" for="actags">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_TAGS') }}:</span>
                            </div>
                            @php
                                $tf = Event::trigger('hubzero.onGetMultiEntry', [['tags', 'filters[tag]', 'actags', '', $filters['tag']]]);
                            @endphp
                            @if (count($tf) > 0)
                                {!! $tf[0] !!}
                            @else
                                <input type="text"
                                    name="filters[tag]"
                                    id="actags"
                                    class="input input-bordered w-full input-sm"
                                    value="{{ e($filters['tag']) }}" />
                            @endif
                        </label>

                        <label class="form-control w-full" for="filter_author">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_AUTHORED_BY') }}</span>
                            </div>
                            <input type="text"
                                name="filters[author]"
                                id="filter_author"
                                class="input input-bordered w-full input-sm"
                                value="{{ e($filters['author']) }}" />
                        </label>

                        <label class="form-control w-full" for="filter_publishedin">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_PUBLISHED_IN') }}</span>
                            </div>
                            <input type="text"
                                name="filters[publishedin]"
                                id="filter_publishedin"
                                class="input input-bordered w-full input-sm"
                                value="{{ e($filters['publishedin']) }}" />
                        </label>

                        <div>
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_YEAR') }}</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="text"
                                    name="filters[year_start]"
                                    id="filter_year_start"
                                    class="input input-bordered w-full input-sm"
                                    value="{{ e($filters['year_start']) }}" />
                                <span class="text-base-content/50">to</span>
                                <input type="text"
                                    name="filters[year_end]"
                                    id="filter_year_end"
                                    class="input input-bordered w-full input-sm"
                                    value="{{ e($filters['year_end']) }}" />
                            </div>
                        </div>

                        <label class="form-control w-full" for="filter_sort">
                            <div class="label">
                                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_CITATIONS_SORT_BY') }}</span>
                            </div>
                            <select name="filters[sort]" id="filter_sort" class="select select-bordered w-full select-sm">
                                @foreach ($sorts as $k => $v)
                                    <option value="{{ $k }}" {{ trim($filters['sort']) == $k ? 'selected' : '' }}>
                                        {{ $v }}
                                    </option>
                                @endforeach
                            </select>
                        </label>

                        <input type="hidden" name="idlist" value="{{ e($filters['idlist']) }}" />
                        <input type="hidden" name="referer" value="{{ $_SERVER['HTTP_REFERER'] ?? '' }}" />
                        <input type="hidden" name="action" value="browse" />

                        <div class="flex gap-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                {{ Lang::txt('PLG_MEMBERS_CITATIONS_FILTER') }}
                            </button>
                            @php
                                $resetUrl = Route::url('index.php?option=com_members&id=' . $member->get('id') . '&active=citations');
                            @endphp
                            <a href="{{ $resetUrl }}" class="btn btn-ghost btn-sm">Reset</a>
                        </div>
                    </div>
                </fieldset>
            </div>
        </div>
    </form>
</div>
