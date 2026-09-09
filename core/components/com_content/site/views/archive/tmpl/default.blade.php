@php
use Components\Content\Site\Helpers\Route as HelperRoute;
@endphp

<x-page-container :title="e($params->get('page_heading', Lang::txt('JGLOBAL_ARTICLES')))">

    {{-- Filter form --}}
    @if ($params->get('filter_field') != 'hide')
        <x-search-bar
            :action="Route::url('index.php?option=com_content')"
            :query="$filter ?? ''"
            name="filter-search"
            :placeholder="Lang::txt('COM_CONTENT_FILTER_SEARCH_DESC')"
            :buttonLabel="Lang::txt('JGLOBAL_FILTER_BUTTON')"
            :clearUrl="($filter ?? '') ? Route::url('index.php?option=com_content&view=archive') : ''"
        >
            <div class="flex flex-wrap items-end gap-3 mt-3">
                {!! $form->monthField !!}
                {!! $form->yearField !!}
            </div>
            <input type="hidden" name="view" value="archive" />
            <input type="hidden" name="option" value="com_content" />
            <input type="hidden" name="limitstart" value="0" />
        </x-search-bar>
    @else
        <form action="{{ Route::url('index.php?option=com_content') }}"
              method="get"
              class="mb-6">
            <div class="flex flex-wrap items-end gap-3">
                {!! $form->monthField !!}
                {!! $form->yearField !!}
                <button type="submit" class="btn btn-primary">
                    {{ Lang::txt('JGLOBAL_FILTER_BUTTON') }}
                </button>
            </div>
            <input type="hidden" name="view" value="archive" />
            <input type="hidden" name="option" value="com_content" />
            <input type="hidden" name="limitstart" value="0" />
        </form>
    @endif

    {{-- Article list --}}
    @if (count($items) > 0)
        <ul class="list bg-base-100 rounded-box shadow-sm">
            @foreach ($items as $item)
                @php
                $articleUrl = Route::url(
                    HelperRoute::getArticleRoute($item->slug, $item->catslug, $item->language)
                );
                $itemParams = $item->params;
                @endphp
                <li class="list-row">
                    <div class="list-col-grow">
                        <h3 class="font-semibold">
                            @if ($params->get('link_titles'))
                                <a class="link link-hover" href="{{ $articleUrl }}">
                                    {{ e($item->title) }}
                                </a>
                            @else
                                {{ e($item->title) }}
                            @endif
                        </h3>

                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-base-content/60 mt-1">
                            @if ($params->get('show_author') && !empty($item->author))
                                @php
                                $author = $item->created_by_alias ?: $item->author;
                                @endphp
                                <span>{{ Lang::txt('COM_CONTENT_WRITTEN_BY', $author) }}</span>
                            @endif

                            @if ($params->get('show_category'))
                                @php
                                $catTitle = e($item->category_title);
                                $catUrl = Route::url(HelperRoute::getCategoryRoute($item->catslug));
                                @endphp
                                <span>
                                    @if ($params->get('link_category') && $item->catslug)
                                        {!! Lang::txt('COM_CONTENT_CATEGORY', '<a class="link link-hover" href="' . $catUrl . '">' . $catTitle . '</a>') !!}
                                    @else
                                        {{ Lang::txt('COM_CONTENT_CATEGORY', $catTitle) }}
                                    @endif
                                </span>
                            @endif

                            @if ($params->get('show_create_date'))
                                <time datetime="{{ $item->created }}">
                                    {{ Date::of($item->created)->toLocal(Lang::txt('DATE_FORMAT_LC2')) }}
                                </time>
                            @endif

                            @if ($params->get('show_hits'))
                                <span>{{ Lang::txt('COM_CONTENT_ARTICLE_HITS', $item->hits) }}</span>
                            @endif
                        </div>

                        @if ($params->get('show_intro') && $item->introtext)
                            <div class="prose prose-sm max-w-none mt-2 line-clamp-2">
                                {!! Hubzero\Utility\Str::truncate($item->introtext, $params->get('introtext_limit', 100)) !!}
                            </div>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>

        <div class="mt-4">
            {!! $pagination->render() !!}
        </div>
    @else
        <x-empty-state :title="Lang::txt('COM_CONTENT_NO_ARTICLES')" />
    @endif

</x-page-container>
