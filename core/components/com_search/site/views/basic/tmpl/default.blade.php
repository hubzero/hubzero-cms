{{--
 * Basic search — form, results list, and category sidebar
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Date;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\Event;

    $searchUrl = Route::url('index.php?option=com_search');
    $termsRaw  = $terms->get_raw();
    $hasResults = $results->valid();
    $show_weight = array_key_exists('show_weight', $_GET);
@endphp

<x-page-container :title="Lang::txt('COM_SEARCH')">
    @slot('sidebar')
        <x-sidebar-card :title="Lang::txt('COM_SEARCH_FILTER_RESULTS')">
            @if($results->get_total_count())
                <ul class="menu menu-sm">
                    <li>
                        @php
                            $filterAllUrl = Route::url(
                                'index.php?option=com_search&terms=' . $url_terms
                            );
                            $totalCount = $results->get_total_count();
                        @endphp
                        @if($plugin)
                            <a href="{{ $filterAllUrl }}">
                                {{ Lang::txt('COM_SEARCH_FILTER_ALL') }}
                                <span class="badge badge-sm">{{ $totalCount }}</span>
                            </a>
                        @else
                            <span class="font-semibold">
                                {{ Lang::txt('COM_SEARCH_FILTER_ALL') }}
                                <span class="badge badge-sm badge-primary">{{ $totalCount }}</span>
                            </span>
                        @endif
                    </li>
                    @foreach($results->get_result_counts() as $cat => $def)
                        @if($def['count'])
                            <li>
                                @if($plugin == $cat && !$section)
                                    <span class="font-semibold">
                                        {{ $def['friendly_name'] }}
                                        <span class="badge badge-sm badge-primary">{{ $def['count'] }}</span>
                                    </span>
                                @else
                                    @php
                                        $catUrl = Route::url(
                                            'index.php?option=com_search&terms='
                                            . $cat . ':' . $url_terms
                                        );
                                    @endphp
                                    <a href="{{ $catUrl }}">
                                        {{ $def['friendly_name'] }}
                                        <span class="badge badge-sm">{{ $def['count'] }}</span>
                                    </a>
                                @endif
                                @php
                                    $searchPluginName = ucfirst($def['plugin_name']);
                                    $fc_child_flag = 'Plugins\\Search\\'
                                        . $searchPluginName . '\\'
                                        . $searchPluginName
                                        . '::FIRST_CLASS_CHILDREN';
                                    $hasChildren = (!defined($fc_child_flag)
                                        || constant($fc_child_flag))
                                        && array_key_exists('sections', $def)
                                        && count($def['sections']) > 1;
                                @endphp
                                @if($hasChildren)
                                    <ul>
                                        @foreach($def['sections'] as $section_key => $sdef)
                                            <li>
                                                @php
                                                    $isActiveSection = $plugin
                                                        && $section
                                                        && $cat == $plugin
                                                        && $section == $section_key;
                                                @endphp
                                                @if($isActiveSection)
                                                    <span class="font-semibold">
                                                        {{ $sdef['name'] }}
                                                        <span class="badge badge-sm badge-primary">{{ $sdef['count'] }}</span>
                                                    </span>
                                                @else
                                                    @php
                                                        $sectionUrl = Route::url(
                                                            'index.php?option=com_search&terms='
                                                            . $cat . ':' . $section_key
                                                            . ':' . $url_terms
                                                        );
                                                    @endphp
                                                    <a href="{{ $sectionUrl }}">
                                                        {{ $sdef['name'] }}
                                                        <span class="badge badge-sm">{{ $sdef['count'] }}</span>
                                                    </a>
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @endif
                    @endforeach
                </ul>
            @endif
        </x-sidebar-card>
    @endslot

    {{-- Search form --}}
    <x-search-bar
        :action="$searchUrl"
        name="terms"
        :query="$termsRaw"
        :placeholder="Lang::txt('COM_SEARCH_TERMS_PLACEHOLDER')"
        :buttonLabel="Lang::txt('COM_SEARCH_SEARCH')"
    />

    @if($hasResults)
        {{-- Custom title override --}}
        @if(($ct = $results->get_custom_title()))
            @php
                $genericUrl = Route::url(
                    'index.php?option=com_search&terms='
                    . urlencode($terms) . '&force-generic=1'
                );
            @endphp
            <div class="alert alert-info mb-4">
                <span>
                    {!! Lang::txt('COM_SEARCH_VIEWING_CUSTOM', e($ct)) !!}
                    <a href="{{ $genericUrl }}" class="link">{{ Lang::txt('COM_SEARCH_VIEW_ALL_RESULTS') }}</a>
                </span>
            </div>
        @endif

        @php
            $totalResults = $results->get_plugin_list_count();
            $offset       = $results->get_offset();
            $limit        = $results->get_limit();
            $limit        = ($limit == 0) ? ($limit + 1) : $limit;
            $current_page = $offset / $limit + 1;
            $total_pages  = ceil($totalResults / $limit);
        @endphp

        <h3 class="text-lg font-semibold mb-2">
            {{ Lang::txt('COM_SEARCH_RESULTS') }}
            <span class="text-base-content/60 text-sm font-normal">
                ({{ Lang::txt('COM_SEARCH_RESULTS_PAGE_OF', $current_page, $total_pages) }})
            </span>
        </h3>

        {{-- Tags --}}
        @if(($tags = $results->get_tags()))
            <div class="flex flex-wrap gap-1 mb-4">
                @foreach($tags as $tag)
                    <a class="badge badge-outline" href="{{ Route::url($tag->get_link()) }}">
                        {{ $tag->get_title() }}
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Widgets --}}
        @foreach($results->get_widgets() as $widget)
            {!! $widget !!}
        @endforeach

        {{-- Results list --}}
        <div class="space-y-6">
            @foreach($results as $res)
                @php
                    $eventName = 'search.onBeforeSearchRender' . ucfirst($res->get_plugin());
                    $before = Event::trigger($eventName, array($res));
                @endphp
                <div class="search-result">
                    <h4 class="text-base font-semibold mb-1">
                        <a href="{{ $res->get_link() }}" class="link link-hover link-primary">
                            {!! $res->get_highlighted_title() !!}
                        </a>
                    </h4>

                    <div class="text-sm">
                        @if($res->has_metadata())
                            <p class="flex flex-wrap gap-2 text-base-content/60 mb-1">
                                @if(($resSection = $res->get_section()))
                                    <span class="badge badge-sm">{{ $resSection }}</span>
                                @endif
                                @if(($date = $res->get_date()))
                                    <span>{{ Date::of($date)->format('j M Y') }}</span>
                                @endif
                                @if(($contributors = $res->get_contributors()))
                                    @php
                                        $contrib_ids = $res->get_contributor_ids();
                                        $contrib_len = count($contributors);
                                    @endphp
                                    <span>
                                        {{ Lang::txt('COM_SEARCH_CONTRIBUTORS') }}
                                        @foreach($contributors as $idx => $contrib)
                                            @if(isset($contrib_ids[$idx]))
                                                @php
                                                    $memberUrl = Route::url(
                                                        'index.php?option=com_members&id='
                                                        . $contrib_ids[$idx]
                                                    );
                                                @endphp
                                                <a href="{{ $memberUrl }}" class="link">{{ $contrib }}</a>{{ $idx != $contrib_len - 1 ? ', ' : '' }}
                                            @else
                                                {{ $contrib }}
                                            @endif
                                        @endforeach
                                    </span>
                                @endif
                            </p>
                        @endif

                        @if($before)
                            <div class="mb-1">
                                @foreach($before as $html)
                                    {!! $html !!}
                                @endforeach
                            </div>
                        @endif

                        @if($show_weight)
                            <ul class="text-xs text-base-content/50 mb-1">
                                @foreach($res->get_weight_log() as $entry)
                                    <li>{{ $entry }}</li>
                                @endforeach
                            </ul>
                        @endif

                        <p class="text-base-content/80">
                            {!! $res->get_highlighted_excerpt() !!}
                        </p>
                    </div>

                    {{-- Child results --}}
                    @php $children = $res->get_children(); @endphp
                    @if($children)
                        @php
                            $ctypec = [];
                            foreach ($children as $child) {
                                if (($childSection = $child->get_section())) {
                                    $ctypec[$childSection] = ($ctypec[$childSection] ?? 0) + 1;
                                }
                            }
                        @endphp
                        @if($ctypec)
                            @php $last_type = null; @endphp
                            <div class="mt-2 ml-4 space-y-2">
                                @foreach($children as $idx => $child)
                                    @php $current_type = $child->get_section(); @endphp
                                    @if(!$current_type) @continue @endif

                                    @if(!$last_type || $last_type != $current_type)
                                        @php
                                            $typeLabel = $current_type == 'Questions'
                                                ? Lang::txt('COM_SEARCH_CHILD_ANSWERS') : $current_type;
                                        @endphp
                                        <h5 class="text-sm font-medium text-base-content/70 mt-3">
                                            {{ $typeLabel }}
                                            <span class="text-base-content/50">({{ $ctypec[$current_type] }})</span>
                                        </h5>
                                    @endif

                                    <div class="pl-3 border-l-2 border-base-300">
                                        <a href="{{ $child->get_link() }}" class="link link-hover text-sm">
                                            {!! $child->get_highlighted_title() !!}
                                        </a>
                                        <p class="text-xs text-base-content/60">
                                            {!! $child->get_highlighted_excerpt() !!}
                                        </p>
                                    </div>
                                    @php $last_type = $current_type; @endphp
                                @endforeach
                            </div>
                        @endif
                    @endif

                    <p class="text-xs text-base-content/40 mt-1">
                        <a href="{{ $res->get_link() }}" class="link link-hover">
                            {{ $res->get_link() }}
                        </a>
                    </p>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            <form action="{{ $searchUrl }}" method="get">
                @php
                    $pagination = $__view->pagination(
                        $total,
                        $results->get_offset(),
                        $results->get_limit()
                    );
                    $pagination->setAdditionalUrlParam('terms', $terms);
                @endphp
                {!! $pagination->render() !!}
                <input type="hidden" name="terms" value="{{ $termsRaw }}" />
            </form>
        </div>
    @elseif(($raw = $terms->get_raw()))
        <p>{{ Lang::txt('COM_SEARCH_RESULTS_NONE', $__view->escape($raw)) }}</p>
        @if(!$terms->any() || strlen($raw) <= 3)
            <div class="alert alert-warning">
                <span>{{ Lang::txt('COM_SEARCH_WARNING_SHORT_WORDS') }}</span>
            </div>
        @endif
    @endif
</x-page-container>
