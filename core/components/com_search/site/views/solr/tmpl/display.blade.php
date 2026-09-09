{{--
 * Solr search — form, faceted results, and category sidebar
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\Event;

    $terms = isset($__view->terms) ? $__view->terms : '';
    $noResult = ($results && count($results)) > 0 ? false : true;
    $tagSearchEnabled = $__view->tagSearchEnabled;
    $formUrl = Route::url('index.php?option=com_search');
@endphp

<x-page-container :title="Lang::txt('COM_SEARCH_SEARCH')">
    @if(!$noResult)
        @slot('sidebar')
            <x-sidebar-card :title="Lang::txt('COM_SEARCH_CATEGORY')">
                @php
                    $allUrl = Route::url(
                        'index.php?option=com_search&terms=' . $terms
                    );
                    $isAllActive = ($type == '');
                @endphp
                <ul class="menu menu-sm">
                    @if($isAllActive)
                        <li>
                            <a class="active" href="{{ $allUrl }}">
                                {{ Lang::txt('COM_SEARCH_FILTER_ALL') }}
                                <span class="badge badge-sm badge-primary">{{ $total }}</span>
                            </a>
                        </li>
                        @foreach($facets as $facet)
                            {!! $facet->formatWithCounts(
                                $facetCounts,
                                $type,
                                $terms,
                                $childTermsString
                            ) !!}
                        @endforeach
                    @else
                        <li>
                            <a href="{{ $allUrl }}">
                                {{ Lang::txt('COM_SEARCH_ALL_COMPONENTS') }}
                            </a>
                        </li>
                        {!! $searchComponent->formatWithCounts(
                            $facetCounts,
                            $type,
                            $terms,
                            $childTermsString,
                            $filters
                        ) !!}
                    @endif
                </ul>
            </x-sidebar-card>
        @endslot
    @endif

    {{-- Search form --}}
    <x-search-bar
        :action="$formUrl"
        name="terms"
        :query="$terms"
        :placeholder="Lang::txt('COM_SEARCH_ENTER_PROMPT')"
        :buttonLabel="Lang::txt('COM_SEARCH_SEARCH')"
    >
        <input type="hidden" name="section" value="{{ $__view->escape($section) }}" />
        @if(!empty($type))
            <input type="hidden" name="type" value="{{ $type }}" />
        @endif

        @if($tagSearchEnabled)
            @php
                $tags_list = Event::trigger(
                    'hubzero.onGetMultiEntry',
                    array(
                        array('tags', 'tags', 'actags', '', $tags)
                    )
                );
            @endphp
            @if(count($tags_list) > 0)
                <div class="mt-2">
                    {!! $tags_list[0] !!}
                </div>
            @else
                <div class="mt-2">
                    <input type="text" name="tags"
                           value="{{ $__view->escape($tags) }}"
                           class="input input-bordered w-full"
                           placeholder="{{ Lang::txt('COM_SEARCH_TAGS_PLACEHOLDER') }}" />
                </div>
            @endif
        @endif
    </x-search-bar>

    @if($noResult)
        @if(!empty($terms))
            @if(isset($spellSuggestions))
                <div class="alert alert-info mb-4">
                    <div>
                        <h3 class="font-semibold">{{ Lang::txt('COM_SEARCH_DIDYOUMEAN') }}</h3>
                        @foreach($spellSuggestions as $suggestion)
                            @foreach($suggestion->getWords() as $word)
                                @php
                                    $wordUrl = Route::url(
                                        'search?terms=' . $word['word']
                                        . '&section=content'
                                    );
                                @endphp
                                <a href="{{ $wordUrl }}" class="link link-primary">
                                    {{ $word['word'] }}
                                </a>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            @else
                <x-empty-state
                    :title="Lang::txt('COM_SEARCH_NO_RESULTS')"
                    :message="Lang::txt('COM_SEARCH_NO_RESULTS_HINT')"
                />
            @endif
        @endif
    @else
        {{-- Results list --}}
        <div class="space-y-6">
            @foreach($results as $result)
                @php
                    if (is_array($result)) {
                        $hubType = isset($result['hubtype'])
                            ? strtolower($result['hubtype'])
                            : '';
                    } else {
                        $hubType = isset($result->result['hubtype'])
                            ? strtolower($result->result['hubtype'])
                            : '';
                    }

                    $hasOverride = !empty($viewOverrides[$hubType]);
                @endphp

                @if($hasOverride)
                    @php
                        $overrideView = new \Hubzero\View\View(
                            $viewOverrides[$hubType]
                        );
                        $overrideView->set('result', $result)
                            ->set('terms', $terms)
                            ->set('tagSearch', $tagSearchEnabled)
                            ->display();
                    @endphp
                @else
                    @php
                        $resultData = is_array($result)
                            ? $result
                            : $result->result;
                    @endphp
                    @include('solr._result', [
                        'result' => $resultData,
                        'terms' => $terms,
                        'tagSearch' => $tagSearchEnabled
                    ])
                @endif
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {!! $pagination->render() !!}
        </div>
    @endif
</x-page-container>
