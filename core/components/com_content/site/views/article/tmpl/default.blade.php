@php
use Components\Content\Site\Helpers\Route as HelperRoute;

$params  = $item->params;
$images  = json_decode($item->images);
$urls    = json_decode($item->urls);
$canEdit = $params->get('access-edit');
@endphp

<x-page-container :title="$params->get('show_title') ? e($item->title) : ''">

    {{-- Plugin event: after title --}}
    @if (!$params->get('show_intro'))
        {!! $item->event->afterDisplayTitle !!}
    @endif

    {{-- Plugin event: before content --}}
    {!! $item->event->beforeDisplayContent !!}

    {{-- Article metadata --}}
    @php
    $showMeta = $params->get('show_author')
        || $params->get('show_category')
        || $params->get('show_parent_category')
        || $params->get('show_create_date')
        || $params->get('show_modify_date')
        || $params->get('show_publish_date')
        || $params->get('show_hits');
    @endphp

    @if ($showMeta)
        <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-sm text-base-content/60 mb-4">
            @if ($params->get('show_author') && !empty($item->author))
                @php
                $author = $item->created_by_alias ?: $item->author;
                @endphp
                <span>
                    @if (!empty($item->contactid) && $params->get('link_author'))
                        @php
                        $contactUrl = Route::url('index.php?option=com_contact&view=contact&id=' . $item->contactid);
                        @endphp
                        {!! Lang::txt('COM_CONTENT_WRITTEN_BY', '<a class="link link-hover" href="' . $contactUrl . '">' . e($author) . '</a>') !!}
                    @else
                        {{ Lang::txt('COM_CONTENT_WRITTEN_BY', $author) }}
                    @endif
                </span>
            @endif

            @if ($params->get('show_parent_category') && $item->parent_slug != '1:root')
                @php
                $parentTitle = e($item->parent_title);
                $parentUrl = Route::url(HelperRoute::getCategoryRoute($item->parent_slug));
                @endphp
                <span>
                    @if ($params->get('link_parent_category') && $item->parent_slug)
                        {!! Lang::txt('COM_CONTENT_PARENT', '<a class="link link-hover" href="' . $parentUrl . '">' . $parentTitle . '</a>') !!}
                    @else
                        {{ Lang::txt('COM_CONTENT_PARENT', $parentTitle) }}
                    @endif
                </span>
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
                    {{ Lang::txt('COM_CONTENT_CREATED_DATE_ON', Date::of($item->created)->toLocal(Lang::txt('DATE_FORMAT_LC2'))) }}
                </time>
            @endif

            @if ($params->get('show_modify_date'))
                <time datetime="{{ $item->modified }}">
                    {{ Lang::txt('COM_CONTENT_LAST_UPDATED', Date::of($item->modified)->toLocal(Lang::txt('DATE_FORMAT_LC2'))) }}
                </time>
            @endif

            @if ($params->get('show_publish_date'))
                <time datetime="{{ $item->publish_up }}">
                    {{ Lang::txt('COM_CONTENT_PUBLISHED_DATE_ON', Date::of($item->publish_up)->toLocal(Lang::txt('DATE_FORMAT_LC2'))) }}
                </time>
            @endif

            @if ($params->get('show_hits'))
                <span>{{ Lang::txt('COM_CONTENT_ARTICLE_HITS', $item->hits) }}</span>
            @endif
        </div>
    @endif

    {{-- Table of contents (from pagebreak plugin) --}}
    @if (isset($item->toc))
        {!! $item->toc !!}
    @endif

    {{-- URLs before content (position 0) --}}
    @if (isset($urls) && (
        (!empty($urls->urls_position) && $urls->urls_position == '0')
        || ($params->get('urls_position') == '0' && empty($urls->urls_position))
        || (empty($urls->urls_position) && !$params->get('urls_position'))
    ))
        @include('com_content.site.article._links', ['urls' => $urls, 'params' => $params])
    @endif

    {{-- Article content --}}
    @if ($params->get('access-view'))

        @if (isset($images->image_fulltext) && !empty($images->image_fulltext))
            @php
            $imgfloat = empty($images->float_fulltext)
                ? $params->get('float_fulltext')
                : $images->float_fulltext;
            $floatClass = $imgfloat === 'left' ? 'float-left mr-4 mb-2'
                : ($imgfloat === 'right' ? 'float-right ml-4 mb-2' : 'mb-4');
            @endphp
            <figure class="{{ $floatClass }} max-w-sm">
                <img
                    src="{{ e($images->image_fulltext) }}"
                    alt="{{ e($images->image_fulltext_alt ?? '') }}"
                    class="rounded-lg"
                />
                @if (!empty($images->image_fulltext_caption))
                    <figcaption class="text-sm text-base-content/60 mt-1">
                        {{ $images->image_fulltext_caption }}
                    </figcaption>
                @endif
            </figure>
        @endif

        {{-- Pagination before content --}}
        @if (!empty($item->pagination) && $item->pagination && !$item->paginationposition && !$item->paginationrelative)
            {!! $item->pagination !!}
        @endif

        <div class="prose max-w-none">
            {!! $item->text !!}
        </div>

        {{-- Pagination after content --}}
        @if (!empty($item->pagination) && $item->pagination && $item->paginationposition && !$item->paginationrelative)
            {!! $item->pagination !!}
        @endif

        {{-- URLs after content (position 1) --}}
        @if (isset($urls) && (
            (!empty($urls->urls_position) && $urls->urls_position == '1')
            || $params->get('urls_position') == '1'
        ))
            @include('com_content.site.article._links', ['urls' => $urls, 'params' => $params])
        @endif

    @elseif ($params->get('show_noauth') && User::isGuest())
        {{-- Guest teaser: show intro text --}}
        <div class="prose max-w-none">
            {!! $item->introtext !!}
        </div>

        @if ($params->get('show_readmore') && $item->fulltext != null)
            @php
            $loginUrl = Route::url('index.php?option=com_login');
            @endphp
            <p class="mt-4">
                <a class="btn btn-primary btn-sm" href="{{ $loginUrl }}">
                    {{ Lang::txt('COM_CONTENT_REGISTER_TO_READ_MORE') }}
                </a>
            </p>
        @endif
    @endif

    {{-- Relative pagination --}}
    @if (!empty($item->pagination) && $item->pagination && $item->paginationposition && $item->paginationrelative)
        {!! $item->pagination !!}
    @endif

    {{-- Plugin event: after content --}}
    {!! $item->event->afterDisplayContent !!}

</x-page-container>
