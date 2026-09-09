@php
use Components\Content\Site\Helpers\Route as HelperRoute;

$pageHeading = $params->get('show_page_heading')
    ? e($params->get('page_heading'))
    : '';
@endphp

<x-page-container :title="$pageHeading">

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

    {{-- No articles --}}
    @if (empty($lead_items) && empty($intro_items) && empty($link_items))
        <x-empty-state :title="Lang::txt('COM_CONTENT_NO_ARTICLES')" />
    @endif

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

</x-page-container>
