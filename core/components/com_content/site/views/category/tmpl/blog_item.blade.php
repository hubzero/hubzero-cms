@php
use Components\Content\Site\Helpers\Route as HelperRoute;

$params  = $item->params;
$images  = json_decode($item->images);
$canEdit = $params->get('access-edit');

$articleUrl = Route::url(
    HelperRoute::getArticleRoute($item->slug, $item->catid, $item->language)
);

$showMeta = $params->get('show_author')
    || $params->get('show_category')
    || $params->get('show_parent_category')
    || $params->get('show_create_date')
    || $params->get('show_modify_date')
    || $params->get('show_publish_date')
    || $params->get('show_hits');
@endphp

<div class="card bg-base-100 shadow-sm {{ $item->state == 0 ? 'opacity-50' : '' }}">
    @if (isset($images->image_intro) && !empty($images->image_intro))
        @php
        $imgfloat = empty($images->float_intro) ? $params->get('float_intro') : $images->float_intro;
        @endphp
        <figure>
            <img
                src="{{ e($images->image_intro) }}"
                alt="{{ e($images->image_intro_alt ?? '') }}"
                class="w-full h-48 object-cover"
            />
        </figure>
    @endif

    <div class="card-body">
        @if ($params->get('show_title', 1))
            <h2 class="card-title text-lg">
                @if ($params->get('link_titles', 1) && $params->get('access-view'))
                    <a class="link link-hover" href="{{ $articleUrl }}">
                        {{ e($item->title) }}
                    </a>
                @else
                    {{ e($item->title) }}
                @endif
            </h2>
        @endif

        @if (!$params->get('show_intro'))
            {!! $item->event->afterDisplayTitle !!}
        @endif

        {!! $item->event->beforeDisplayContent !!}

        @if ($showMeta)
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-base-content/60">
                @if ($params->get('show_parent_category') && $item->parent_id != 1)
                    @php
                    $parentTitle = e($item->parent_title);
                    $parentUrl = Route::url(HelperRoute::getCategoryRoute($item->parent_id));
                    @endphp
                    <span>
                        @if ($params->get('link_parent_category'))
                            {!! Lang::txt('COM_CONTENT_PARENT', '<a class="link link-hover" href="' . $parentUrl . '">' . $parentTitle . '</a>') !!}
                        @else
                            {{ Lang::txt('COM_CONTENT_PARENT', $parentTitle) }}
                        @endif
                    </span>
                @endif

                @if ($params->get('show_category'))
                    @php
                    $catTitle = e($item->category_title);
                    $catUrl = Route::url(HelperRoute::getCategoryRoute($item->catid));
                    @endphp
                    <span>
                        @if ($params->get('link_category'))
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

                @if ($params->get('show_modify_date'))
                    <time datetime="{{ $item->modified }}">
                        {{ Lang::txt('COM_CONTENT_LAST_UPDATED', Date::of($item->modified)->toLocal(Lang::txt('DATE_FORMAT_LC2'))) }}
                    </time>
                @endif

                @if ($params->get('show_publish_date'))
                    <time datetime="{{ $item->publish_up }}">
                        {{ Date::of($item->publish_up)->toLocal(Lang::txt('DATE_FORMAT_LC2')) }}
                    </time>
                @endif

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

                @if ($params->get('show_hits'))
                    <span>{{ Lang::txt('COM_CONTENT_ARTICLE_HITS', $item->hits) }}</span>
                @endif
            </div>
        @endif

        @if ($item->introtext)
            <div class="prose prose-sm max-w-none mt-2 line-clamp-3">
                {!! $item->introtext !!}
            </div>
        @endif

        @if ($params->get('show_readmore', 1) && $item->readmore)
            <div class="card-actions mt-3">
                @if ($params->get('access-view'))
                    <a class="btn btn-primary btn-sm" href="{{ $articleUrl }}">
                        @if ($readmore = $item->alternative_readmore)
                            {{ $readmore }}
                        @else
                            {{ Lang::txt('COM_CONTENT_READ_MORE_TITLE') }}
                        @endif
                    </a>
                @else
                    <a class="btn btn-primary btn-sm"
                       href="{{ Route::url('index.php?option=com_users&view=login') }}">
                        {{ Lang::txt('COM_CONTENT_REGISTER_TO_READ_MORE') }}
                    </a>
                @endif
            </div>
        @endif

        {!! $item->event->afterDisplayContent !!}
    </div>
</div>
