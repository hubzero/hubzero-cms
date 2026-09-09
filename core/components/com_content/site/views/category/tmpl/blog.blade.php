@php
use Components\Content\Site\Helpers\Route as HelperRoute;

$pageTitle = $params->get('show_category_title', 1) ? $category->title : '';
$pageHeading = $params->get('show_page_heading') ? e($params->get('page_heading')) : '';
$title = $pageHeading ?: $pageTitle;
@endphp

<x-page-container :title="$title">

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

    {{-- No articles --}}
    @if (empty($lead_items) && empty($intro_items) && empty($link_items))
        @if ($params->get('show_no_articles', 1))
            <x-empty-state :title="Lang::txt('COM_CONTENT_NO_ARTICLES')" />
        @endif
    @endif

    {{-- Leading articles (full width) --}}
    @if (!empty($lead_items))
        <div class="space-y-6 mb-6">
            @foreach ($lead_items as $item)
                @php $__view->set('item', $item); @endphp
                {!! $__view->loadTemplate('item') !!}
            @endforeach
        </div>
    @endif

    {{-- Intro articles (grid) --}}
    @if (!empty($intro_items))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            @foreach ($intro_items as $item)
                @php $__view->set('item', $item); @endphp
                {!! $__view->loadTemplate('item') !!}
            @endforeach
        </div>
    @endif

    {{-- Link articles --}}
    @if (!empty($link_items))
        <div class="card bg-base-100 shadow-sm mb-6">
            <div class="card-body">
                <h3 class="card-title text-base">{{ Lang::txt('COM_CONTENT_MORE_ARTICLES') }}</h3>
                <ul class="menu menu-sm p-0">
                    @foreach ($link_items as $item)
                        @php
                        $articleUrl = Route::url(
                            HelperRoute::getArticleRoute($item->slug, $item->catid, $item->language)
                        );
                        @endphp
                        <li>
                            <a href="{{ $articleUrl }}">{{ e($item->title) }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Subcategories --}}
    @if (!empty($children[$category->id]) && $maxLevel != 0)
        <div class="mb-6">
            @if ($params->get('show_category_heading_title_text', 1))
                <h3 class="text-lg font-semibold mb-3">{{ Lang::txt('JGLOBAL_SUBCATEGORIES') }}</h3>
            @endif
            <ul class="list bg-base-100 rounded-box shadow-sm">
                @foreach ($children[$category->id] as $child)
                    @if ($params->get('show_empty_categories') || $child->numitems || count($child->getChildren()))
                        @php
                        $childUrl = Route::url(HelperRoute::getCategoryRoute($child->id));
                        @endphp
                        <li class="list-row">
                            <div class="list-col-grow">
                                <a class="font-semibold link link-hover" href="{{ $childUrl }}">
                                    {{ e($child->title) }}
                                </a>
                                @if ($params->get('show_subcat_desc') && $child->description)
                                    <div class="prose prose-sm max-w-none mt-1 line-clamp-2">
                                        {!! Html::content('prepare', $child->description, '', 'com_content.category') !!}
                                    </div>
                                @endif
                            </div>
                            @if ($params->get('show_cat_num_articles', 1))
                                <span class="badge badge-ghost badge-sm">{{ $child->getNumItems(true) }}</span>
                            @endif
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Pagination --}}
    @php
    $showPagination = ($params->def('show_pagination', 1) == 1
        || ($params->get('show_pagination') == 2))
        && ($pagination->get('pages.total') > 1);
    @endphp
    @if ($showPagination)
        <div class="mt-4">
            {!! $pagination->render() !!}
        </div>
    @endif

</x-page-container>
