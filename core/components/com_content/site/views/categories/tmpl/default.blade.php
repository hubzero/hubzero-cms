@php
use Components\Content\Site\Helpers\Route as HelperRoute;
@endphp

<x-page-container :title="e($params->get('page_heading', Lang::txt('JGLOBAL_ARTICLES')))">

    {{-- Base description --}}
    @if ($params->get('show_base_description'))
        @if ($params->get('categories_description'))
            <div class="prose max-w-none mb-6">
                {!! Html::content('prepare', $params->get('categories_description'), '', 'com_content.categories') !!}
            </div>
        @elseif ($parent->description)
            <div class="prose max-w-none mb-6">
                {!! Html::content('prepare', $parent->description, '', 'com_content.categories') !!}
            </div>
        @endif
    @endif

    {{-- Category list --}}
    @if (count($items[$parent->id] ?? []) > 0 && $maxLevelcat != 0)
        <ul class="list bg-base-100 rounded-box shadow-sm">
            @foreach ($items[$parent->id] as $id => $cat)
                @if ($params->get('show_empty_categories_cat') || $cat->numitems || count($cat->getChildren()))
                    @php
                    $catUrl = Route::url(HelperRoute::getCategoryRoute($cat->id));
                    @endphp
                    <li class="list-row">
                        <div class="list-col-grow">
                            <a class="font-semibold link link-hover" href="{{ $catUrl }}">
                                {{ e($cat->title) }}
                            </a>

                            @if ($params->get('show_subcat_desc_cat') && $cat->description)
                                <div class="prose prose-sm max-w-none mt-1 line-clamp-2">
                                    {!! Html::content('prepare', $cat->description, '', 'com_content.categories') !!}
                                </div>
                            @endif
                        </div>

                        @if ($params->get('show_cat_num_articles_cat'))
                            <span class="badge badge-ghost">{{ $cat->numitems }}</span>
                        @endif
                    </li>
                @endif
            @endforeach
        </ul>
    @else
        <x-empty-state :title="Lang::txt('COM_CONTENT_NO_ARTICLES')" />
    @endif

</x-page-container>
