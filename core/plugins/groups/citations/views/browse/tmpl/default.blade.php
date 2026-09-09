{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->js();

$base = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=citations';
@endphp

@if (isset($messages))
    @foreach ($messages as $message)
        <div class="alert {{ $message['type'] === 'error' ? 'alert-error' : 'alert-info' }}">
            <p>{!! $message['message'] !!}</p>
        </div>
    @endforeach
@endif

@if ($isManager)
    <div id="content-header-extra" class="flex flex-wrap gap-2">
        <a class="btn btn-primary gap-2"
            href="{{ Route::url($base . '&action=add') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            {{ Lang::txt('PLG_GROUPS_CITATIONS_SUBMIT_CITATION') }}
        </a>
        <a class="btn btn-secondary gap-2"
            href="{{ Route::url($base . '&action=import') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5m0 0l5-5m-5 5V3" /></svg>
            {{ Lang::txt('PLG_GROUPS_CITATIONS_IMPORT_CITATION') }}
        </a>
        <a class="btn btn-ghost gap-2"
            href="{{ Route::url($base . '&action=settings') }}">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
            {{ Lang::txt('PLG_GROUPS_CITATIONS_SET_FORMAT') }}
        </a>
    </div>
@endif

<div id="browsebox">
    <form action="{{ Route::url(Request::current()) }}"
        id="citeform"
        method="GET"
        class="withBatchDownload">
        <section class="main section">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {{-- Main content area --}}
                <div class="lg:col-span-3">
                    {{-- Search box --}}
                    <div class="mb-4">
                        <fieldset class="entry-search">
                            <legend class="sr-only">{{ Lang::txt('PLG_GROUPS_CITATIONS_SEARCH_CITATIONS') }}</legend>
                            <div class="join w-full">
                                <input type="text"
                                    name="filters[search]"
                                    id="entry-search-field"
                                    class="input input-bordered join-item flex-1"
                                    value="{{ e($filters['search']) }}"
                                    placeholder="{{ Lang::txt('PLG_GROUPS_CITATIONS_SEARCH_CITATIONS_PLACEHOLDER') }}" />
                                <button type="submit" class="btn btn-neutral join-item">
                                    {{ Lang::txt('JSEARCH') }}
                                </button>
                            </div>
                        </fieldset>
                    </div>

                    {{-- Filter tabs --}}
                    @if ($config->get('display') != 'group')
                        @php
                        $queryString = '';
                        $exclude = ['filter'];
                        foreach ($filters as $k => $v) {
                            if ($v != '' && !in_array($k, $exclude)) {
                                if (is_array($v)) {
                                    foreach ($v as $k2 => $v2) {
                                        $queryString .= "&{$k}[{$k2}]={$v2}";
                                    }
                                } else {
                                    $queryString .= "&{$k}={$v}";
                                }
                            }
                        }
                        @endphp
                        <div role="tablist" class="tabs tabs-border mb-4">
                            @php
                            $allActive = ($filters['filter'] == '' || $filters['filter'] == 'all');
                            $memberActive = ($filters['filter'] == 'member');
                            $allUrl = Route::url($base . '&action=browse' . $queryString . '&filters[filter]=all');
                            $memberUrl = Route::url($base . '&action=browse' . $queryString . '&filters[filter]=member');
                            @endphp
                            <a role="tab"
                                class="tab {{ $allActive ? 'tab-active' : '' }}"
                                href="{{ $allUrl }}">
                                {{ Lang::txt('PLG_GROUPS_CITATIONS_ALL') }}
                            </a>
                            <a role="tab"
                                class="tab {{ $memberActive ? 'tab-active' : '' }}"
                                href="{{ $memberUrl }}">
                                {{ Lang::txt('PLG_GROUPS_CITATIONS_MEMBERCONTRIBUTED') }}
                            </a>
                        </div>
                    @endif

                    {{-- Citations table --}}
                    @if ($citations->count() > 0)
                        @if ($isManager)
                            <div class="flex gap-2 mb-3">
                                <a class="btn btn-sm btn-outline bulk"
                                    data-link="{{ Route::url($base . '&action=publish&bulk=true') }}">
                                    {{ Lang::txt('PLG_GROUPS_CITATIONS_PUBLISH_SELECTED') }}
                                </a>
                                <a class="btn btn-sm btn-outline btn-error bulk"
                                    data-protected="true"
                                    data-link="{{ Route::url($base . '&action=delete&bulk=true') }}">
                                    {{ Lang::txt('PLG_GROUPS_CITATIONS_DELETE_SELECTED') }}
                                </a>
                            </div>
                        @endif

                        <table class="table table-zebra citations entries">
                            <thead>
                                <tr>
                                    <th class="w-10">
                                        <input type="checkbox"
                                            class="checkbox checkbox-sm checkall-download" />
                                    </th>
                                    <th colspan="6">{{ Lang::txt('PLG_GROUPS_CITATIONS') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $x = 1 + Request::getInt('start', 0); @endphp
                                @foreach ($citations as $cite)
                                    @if (!$isManager && $cite->published == $cite::STATE_UNPUBLISHED)
                                        @continue
                                    @endif

                                    @php
                                    $unpubClass = ($cite->published == $cite::STATE_UNPUBLISHED) ? 'opacity-50' : '';
                                    @endphp
                                    <tr class="{{ $unpubClass }}">
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
                                                    @case('id')
                                                        <span class="badge badge-ghost">{{ $cite->id }}.</span>
                                                        @break
                                                    @case('number')
                                                        <span class="badge badge-ghost">{{ $x }}.</span>
                                                        @break
                                                    @case('type')
                                                        <span class="badge badge-outline">{{ $type }}</span>
                                                        @break
                                                    @case('numtype')
                                                        <span class="badge badge-ghost">{{ $x }}.</span>
                                                        <span class="badge badge-outline">{{ $type }}</span>
                                                        @break
                                                    @default
                                                        <span class="badge badge-ghost">{{ $cite->id }}.</span>
                                                        <span class="badge badge-outline">{{ $type }}</span>
                                                @endswitch
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
                                                    '<a href="' . $cite->url . '" rel="external">'
                                                        . 'doi:' . $cite->doi . '</a>',
                                                    $formatted
                                                );
                                            }
                                            @endphp
                                            {!! $formatted !!}

                                            @php
                                            $citation_rollover = 0;
                                            $links = $cite->links()->rows();
                                            @endphp

                                            @if ($links->count() > 0)
                                                <ul class="mt-2 flex flex-wrap gap-2">
                                                    @foreach ($links as $link)
                                                        <li>
                                                            <a class="link link-hover link-primary text-sm"
                                                                href="{{ $link->url }}">
                                                                {{ e($link->title) }}
                                                            </a>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif

                                            @if ($citation_rollover && $cite->abstract != '')
                                                <div class="citation-notes mt-2 text-sm opacity-70">
                                                    @php
                                                    $final = '';
                                                    foreach ($cite->sponsors as $s) {
                                                        $final .= '<a rel="external" href="'
                                                            . $s->get('link') . '">'
                                                            . $s->get('sponsor') . '</a>, ';
                                                    }
                                                    $showSponsors = ($final != ''
                                                        && $config->get('citation_sponsors', 'yes') == 'yes');
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

                                            @php
                                            $detailsClass = ($cite->published == $cite::STATE_UNPUBLISHED)
                                                ? 'opacity-50' : '';
                                            @endphp
                                            <div class="bg-base-200 p-2 rounded text-sm mt-2 {{ $detailsClass }}">
                                                @if ($config->get('citations_show_badges', 'yes') == 'yes')
                                                    {!! $cite->badgeCloud() !!}
                                                @endif
                                                @if ($config->get('citations_show_tags', 'yes') == 'yes')
                                                    {!! $cite->tagCloud() !!}
                                                @endif
                                                {!! $cite->citationDetails($openurl) !!}
                                            </div>
                                        </td>
                                        @if ($isManager === true && $cite->scope == 'group')
                                            <td class="w-10">
                                                <a class="btn btn-ghost btn-sm btn-square"
                                                    href="{{ Route::url($base . '&action=edit&id=' . $cite->id) }}"
                                                    title="{{ Lang::txt('PLG_GROUPS_CITATIONS_EDIT') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                </a>
                                            </td>
                                            <td class="w-10">
                                                <a class="btn btn-ghost btn-sm btn-square text-error"
                                                    href="{{ Route::url($base . '&action=delete&id=' . $cite->id) }}"
                                                    title="{{ Lang::txt('PLG_GROUPS_CITATIONS_DELETE') }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </a>
                                            </td>
                                            <td class="w-10">
                                                @php
                                                $isPublished = ($cite->published == $cite::STATE_PUBLISHED);
                                                $publishTitle = $isPublished
                                                    ? Lang::txt('PLG_GROUPS_CITATIONS_UNPUBLISH')
                                                    : Lang::txt('PLG_GROUPS_CITATIONS_PUBLISH');
                                                @endphp
                                                <a class="btn btn-ghost btn-sm btn-square {{ $isPublished ? '' : 'text-warning' }}"
                                                    href="{{ Route::url($base . '&action=publish&id=' . $cite->id) }}"
                                                    title="{{ $publishTitle }}">
                                                    @if ($isPublished)
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L3 3m6.878 6.878L21 21" /></svg>
                                                    @else
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                                    @endif
                                                </a>
                                            </td>
                                        @endif
                                    </tr>
                                    @php $x++; @endphp
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div class="alert alert-warning">
                            <p>{{ Lang::txt('PLG_GROUPS_CITATIONS_NO_CITATIONS_FOUND') }}</p>
                        </div>
                    @endif

                    {!! $citations->pagination !!}
                </div>

                {{-- Sidebar --}}
                <div class="lg:col-span-1">
                    {{-- Export batch --}}
                    <div class="card bg-base-200 mb-4">
                        <div class="card-body p-4">
                            <h3 class="card-title text-sm">
                                {{ Lang::txt('PLG_GROUPS_CITATIONS_EXPORT_MULTIPLE') }}
                            </h3>
                            <p class="text-sm opacity-70">
                                {{ Lang::txt('PLG_GROUPS_CITATIONS_EXPORT_MULTIPLE_DESC') }}
                            </p>
                            <div class="flex gap-2 mt-2">
                                <input type="submit"
                                    name="download"
                                    class="btn btn-sm btn-outline"
                                    id="download-endnote"
                                    value="{{ Lang::txt('PLG_GROUPS_CITATIONS_ENDNOTE') }}" />
                                <input type="submit"
                                    name="download"
                                    class="btn btn-sm btn-outline"
                                    id="download-bibtex"
                                    value="{{ Lang::txt('PLG_GROUPS_CITATIONS_BIBTEX') }}" />
                            </div>
                            <iframe id="download-frame" class="hidden"></iframe>
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="card bg-base-200">
                        <div class="card-body p-4">
                            <label class="form-control mb-3" for="filter_type">
                                <div class="label">
                                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_TYPE') }}</span>
                                </div>
                                <select name="filters[type]"
                                    id="filter_type"
                                    class="select select-bordered select-sm w-full">
                                    <option value="">{{ Lang::txt('PLG_GROUPS_CITATIONS_ALL') }}</option>
                                    @foreach ($types as $t)
                                        <option value="{{ $t->id }}"
                                            {{ $filters['type'] == $t->id ? 'selected' : '' }}>
                                            {{ $t->type_title }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="form-control mb-3" for="actags">
                                <div class="label">
                                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_TAGS') }}</span>
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
                                        class="input input-bordered input-sm w-full"
                                        value="{{ e($filters['tag']) }}" />
                                @endif
                            </label>

                            <label class="form-control mb-3" for="filter_author">
                                <div class="label">
                                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_AUTHORED_BY') }}</span>
                                </div>
                                <input type="text"
                                    name="filters[author]"
                                    id="filter_author"
                                    class="input input-bordered input-sm w-full"
                                    value="{{ e($filters['author']) }}" />
                            </label>

                            <label class="form-control mb-3" for="filter_publishedin">
                                <div class="label">
                                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_PUBLISHED_IN') }}</span>
                                </div>
                                <input type="text"
                                    name="filters[publishedin]"
                                    id="filter_publishedin"
                                    class="input input-bordered input-sm w-full"
                                    value="{{ e($filters['publishedin']) }}" />
                            </label>

                            <div class="form-control mb-3">
                                <div class="label">
                                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_YEAR') }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input type="text"
                                        name="filters[year_start]"
                                        id="filter_year_start"
                                        class="input input-bordered input-sm w-full"
                                        value="{{ e($filters['year_start']) }}" />
                                    <span class="text-sm">to</span>
                                    <input type="text"
                                        name="filters[year_end]"
                                        id="filter_year_end"
                                        class="input input-bordered input-sm w-full"
                                        value="{{ e($filters['year_end']) }}" />
                                </div>
                            </div>

                            <label class="form-control mb-3" for="filter_sort">
                                <div class="label">
                                    <span class="label-text">{{ Lang::txt('PLG_GROUPS_CITATIONS_SORT_BY') }}</span>
                                </div>
                                <select name="filters[sort]"
                                    id="filter_sort"
                                    class="select select-bordered select-sm w-full">
                                    @foreach ($sorts as $k => $v)
                                        <option value="{{ $k }}"
                                            {{ trim($filters['sort']) == $k ? 'selected' : '' }}>
                                            {{ $v }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>

                            <input type="hidden"
                                name="idlist"
                                value="{{ e($filters['idlist']) }}" />
                            <input type="hidden" name="action" value="browse" />

                            <div class="flex gap-2 mt-2">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    {{ Lang::txt('PLG_GROUPS_CITATIONS_FILTER') }}
                                </button>
                                @php
                                $resetUrl = Route::url(
                                    'index.php?option=com_groups&cn='
                                    . $group->get('cn')
                                    . '&active=citations'
                                );
                                @endphp
                                <a href="{{ $resetUrl }}" class="btn btn-ghost btn-sm">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </form>
</div>
