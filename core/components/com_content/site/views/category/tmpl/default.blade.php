@php
use Components\Content\Site\Helpers\Route as HelperRoute;

$hasChildren = !empty($children[$category->id]) && $maxLevel != 0;
$pageTitle = $params->get('show_category_title', 1) ? $category->title : '';
$pageHeading = $params->get('show_page_heading') ? e($params->get('page_heading')) : '';
$title = $pageHeading ?: $pageTitle;
@endphp

<x-page-container :title="$title">
    @if ($hasChildren)
        @slot('sidebar')
            <x-sidebar-card :title="Lang::txt('JGLOBAL_SUBCATEGORIES')">
                <ul class="menu menu-sm p-0">
                    @foreach ($children[$category->id] as $child)
                        @if ($params->get('show_empty_categories') || $child->getNumItems(true) || count($child->getChildren()))
                            @php
                            $childUrl = Route::url(HelperRoute::getCategoryRoute($child->id));
                            @endphp
                            <li>
                                <a href="{{ $childUrl }}">
                                    {{ e($child->title) }}
                                    @if ($params->get('show_cat_num_articles', 1))
                                        <span class="badge badge-ghost badge-sm">{{ $child->getNumItems(true) }}</span>
                                    @endif
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </x-sidebar-card>
        @endslot
    @endif

    {{-- Category description --}}
    @if ($params->get('show_description', 1) || $params->def('show_description_image', 1))
        @php
        $catImage = $category->getParams()->get('image');
        @endphp
        @if (($params->get('show_description_image') && $catImage) || ($params->get('show_description') && $category->description))
            <div class="prose max-w-none mb-6">
                @if ($params->get('show_description_image') && $catImage)
                    <img src="{{ e($catImage) }}" alt="" class="rounded-lg" />
                @endif
                @if ($params->get('show_description') && $category->description)
                    {!! Html::content('prepare', $category->description, '', 'com_content.category') !!}
                @endif
            </div>
        @endif
    @endif

    {{-- Filter/search --}}
    @if ($params->get('filter_field') != 'hide')
        <x-search-bar
            :action="Request::current()"
            :query="$filters['filter'] ?? ''"
            :placeholder="Lang::txt('COM_CONTENT_FILTER_SEARCH_DESC')"
            :buttonLabel="Lang::txt('JGLOBAL_FILTER_LABEL')"
            :clearUrl="($filters['filter'] ?? '') ? Request::current() : ''"
        >
            <input type="hidden" name="filter_order" value="{{ e($filters['ordering']) }}" />
            <input type="hidden" name="filter_order_Dir" value="{{ e($filters['direction']) }}" />
            <input type="hidden" name="limitstart" value="0" />
        </x-search-bar>
    @endif

    {{-- Articles list --}}
    @if (empty($items))
        @if ($params->get('show_no_articles', 1))
            <x-empty-state :title="Lang::txt('COM_CONTENT_NO_ARTICLES')" />
        @endif
    @else
        <ul class="list bg-base-100 rounded-box shadow-sm">
            @foreach ($items as $i => $article)
                @php
                $hasAccess = in_array($article->access, $user->getAuthorisedViewLevels());
                $articleUrl = $hasAccess
                    ? Route::url(HelperRoute::getArticleRoute($article->slug, $article->catid, $article->language))
                    : Route::url('index.php?option=com_users&view=login');
                @endphp
                <li class="list-row {{ $article->state == 0 ? 'opacity-50' : '' }}">
                    <div class="list-col-grow">
                        @if ($hasAccess)
                            <a class="font-semibold link link-hover" href="{{ $articleUrl }}">
                                {{ e($article->title) }}
                            </a>
                        @else
                            <span class="font-semibold">{{ e($article->title) }}</span>
                            <a class="link link-primary text-xs ml-2" href="{{ $articleUrl }}">
                                {{ Lang::txt('COM_CONTENT_REGISTER_TO_READ_MORE') }}
                            </a>
                        @endif

                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-base-content/60 mt-1">
                            @if ($params->get('list_show_date') && isset($article->displayDate))
                                @php
                                $dateFmt = $params->get('date_format', Lang::txt('DATE_FORMAT_LC3'));
                                @endphp
                                <time datetime="{{ $article->displayDate }}">
                                    {{ Date::of($article->displayDate)->toLocal($dateFmt) }}
                                </time>
                            @endif

                            @if ($params->get('list_show_author', 1) && (!empty($article->author) || !empty($article->created_by_alias)))
                                @php
                                $author = $article->created_by_alias ?: $article->author;
                                @endphp
                                <span>{{ $author }}</span>
                            @endif

                            @if ($params->get('list_show_hits', 1))
                                <span>{{ Lang::txt('COM_CONTENT_ARTICLE_HITS', $article->hits) }}</span>
                            @endif
                        </div>
                    </div>
                </li>
            @endforeach
        </ul>

        {{-- Pagination --}}
        @php
        $showPagination = ($params->def('show_pagination', 2) == 1
            || ($params->get('show_pagination') == 2))
            && ($pagination->get('pages.total') > 1);
        @endphp
        @if ($showPagination)
            <div class="mt-4">
                {!! $pagination->render() !!}
            </div>
        @endif
    @endif

    {{-- Create article link --}}
    @if ($category->getParams()->get('access-create'))
        <div class="mt-4">
            <a class="btn btn-primary btn-sm"
               href="{{ Route::url('index.php?option=com_content&task=edit&a_id=0&catid=' . $category->id) }}">
                {{ Lang::txt('JNEW') }}
            </a>
        </div>
    @endif

</x-page-container>
