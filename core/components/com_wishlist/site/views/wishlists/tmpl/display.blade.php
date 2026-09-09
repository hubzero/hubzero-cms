{{--
 * Wishlist browse — search, filter/sort tabs, wish listing, pagination
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

@php
    use Hubzero\Facades\Config;
    use Hubzero\Facades\Lang;
    use Hubzero\Facades\Request;
    use Hubzero\Facades\Route;
    use Hubzero\Facades\Session;
    use Hubzero\Facades\User;

    $sitename = Config::get('sitename');
    $base     = $wishlist->link();
    $cloud    = new \Components\Wishlist\Models\Tags($wishlist->get('id'));

    $filterby = $filters['filterby'];
    $sortby   = $filters['sortby'];
    $tag      = $filters['tag'];

    $filterln = '&' . Session::getFormToken() . '=1';
    foreach ($filters as $key => $val) {
        if ($val && $key !== 'comments') {
            $filterln .= '&' . $key . '=' . $val;
        }
    }

    // Sort options
    $sortOptions = [];
    if ($wishlist->get('admin')) {
        $sortOptions['ranking'] = Lang::txt('COM_WISHLIST_SORT_RANKING');
    }
    if ($wishlist->get('banking')) {
        $sortOptions['bonus'] = Lang::txt('COM_WISHLIST_SORT_BONUS');
    }
    $sortOptions['feedback']  = Lang::txt('COM_WISHLIST_SORT_FEEDBACK');
    $sortOptions['submitter'] = Lang::txt('COM_WISHLIST_SORT_SUBMITTER');
    $sortOptions['date']      = Lang::txt('COM_WISHLIST_SORT_DATE');

    // Filter options
    $filterOptions = [
        'all'      => Lang::txt('COM_WISHLIST_FILTER_ALL'),
        'open'     => Lang::txt('COM_WISHLIST_FILTER_OPEN'),
        'accepted' => Lang::txt('COM_WISHLIST_FILTER_ACCEPTED'),
        'rejected' => Lang::txt('COM_WISHLIST_FILTER_REJECTED'),
        'granted'  => Lang::txt('COM_WISHLIST_FILTER_GRANTED'),
    ];
    if (!User::isGuest()) {
        $filterOptions['submitter'] = Lang::txt('COM_WISHLIST_FILTER_SUBMITTER');
    }
    if ($wishlist->access('manage')) {
        $filterOptions['public']  = Lang::txt('COM_WISHLIST_FILTER_PUBLIC');
        $filterOptions['private'] = Lang::txt('COM_WISHLIST_FILTER_PRIVATE');
        if ($wishlist->access('own')) {
            $filterOptions['mine'] = Lang::txt('COM_WISHLIST_FILTER_MINE');
        }
    }
@endphp

@if(!$wishlist->get('id'))
    <x-page-container :title="Lang::txt('COM_WISHLIST')">
        <x-empty-state
            :title="Lang::txt('COM_WISHLIST_ERROR_LIST_NOT_FOUND')"
            message=""
        />
    </x-page-container>
@elseif(!$wishlist->isPublic() && !$wishlist->access('manage'))
    <x-page-container :title="Lang::txt('COM_WISHLIST')">
        <div role="alert" class="alert alert-warning">
            <span>{{ Lang::txt('COM_WISHLIST_WARNING_NOT_AUTHORIZED_PRIVATE_LIST') }}</span>
        </div>
    </x-page-container>
@else
    <x-page-container :title="$__view->title">
        @slot('actions')
            <a class="btn btn-sm btn-primary"
               href="{{ Route::url($wishlist->link('new'), false) }}">
                {{ Lang::txt('COM_WISHLIST_TASK_ADD') }}
            </a>
        @endslot

        @slot('sidebar')
            @php
                // Sidebar content
                $sidebarHtml = '';
                if ($wishlist->get('category') == 'resource') {
                    $resUrl   = Route::url(
                        'index.php?option=com_resources&id=' . $wishlist->get('referenceid'),
                        false
                    );
                    $resTitle = e($wishlist->item('title'));
                    $sidebarHtml = '<p>' . Lang::txt(
                        'COM_WISHLIST_THIS_LIST_IS_FOR_RES',
                        '<a href="' . $resUrl . '">' . $resTitle . '</a>'
                    ) . '</p>';
                } elseif ($wishlist->get('description')) {
                    $sidebarHtml = '<p>' . e($wishlist->get('description')) . '</p>';
                } else {
                    $sidebarHtml = '<p>' . Lang::txt('COM_WISHLIST_HELP_US_IMPROVE', $sitename) . '</p>';
                }

                switch ($wishlist->get('admin')) {
                    case '1':
                        $sidebarHtml .= '<div role="alert" class="alert alert-info mt-4"><span>'
                            . Lang::txt('COM_WISHLIST_NOTICE_SITE_ADMIN') . '</span></div>';
                        break;
                    case '2':
                        $settingsUrl = Route::url($wishlist->link('settings'), false);
                        $sidebarHtml .= '<div role="alert" class="alert alert-info mt-4"><span>'
                            . Lang::txt('COM_WISHLIST_NOTICE_LIST_ADMIN')
                            . ' <a class="link" href="' . $settingsUrl . '">'
                            . Lang::txt('COM_WISHLIST_LIST_SETTINGS') . '</a>'
                            . '</span></div>';
                        break;
                    case '3':
                        $sidebarHtml .= '<div role="alert" class="alert alert-info mt-4"><span>'
                            . Lang::txt('COM_WISHLIST_NOTICE_ADVISORY_ADMIN') . '</span></div>';
                        break;
                }
            @endphp

            @if($wishlist->get('category') == 'general')
                @php
                    $tags = $cloud->render('html', [
                        'limit'    => $config->get('maxtags', 10),
                        'start'    => 0,
                        'sort'     => 'total',
                        'sort_Dir' => '',
                        'scope'    => 'wishlist',
                        'scope_id' => 0,
                        'base'     => $base,
                        'filters'  => $filters,
                    ]);
                @endphp
                @if($tags)
                    <x-sidebar-card :title="Lang::txt('COM_WISHLIST_POPULAR_TAGS')">
                        {!! $tags !!}
                        <p class="text-sm text-base-content/60 mt-2">
                            {{ Lang::txt('COM_WISHLIST_CLICK_TAG_TO_FILTER') }}
                        </p>
                    </x-sidebar-card>
                @endif
            @endif

            <x-sidebar-card :title="Lang::txt('COM_WISHLIST_ABOUT')">
                {!! $sidebarHtml !!}
            </x-sidebar-card>
        @endslot

        {{-- Admin messages --}}
        @if($wishlist->access('manage') && !$__view->getError())
            @if($__view->task == 'deletewish')
                <div role="alert" class="alert alert-success mb-4">
                    <span>{{ Lang::txt('COM_WISHLIST_NOTICE_WISH_DELETED') }}</span>
                </div>
            @endif
            @if($__view->task == 'movewish')
                <div role="alert" class="alert alert-success mb-4">
                    <span>{{ Lang::txt('COM_WISHLIST_NOTICE_WISH_MOVED') }}</span>
                </div>
            @endif
            @if($wishlist->get('saved') == 1)
                <div role="alert" class="alert alert-success mb-4">
                    <span>{{ Lang::txt('COM_WISHLIST_NOTICE_LIST_SETTINGS_SAVED') }}</span>
                </div>
            @elseif($wishlist->get('saved') == 2)
                <div role="alert" class="alert alert-success mb-4">
                    <span>{{ Lang::txt('COM_WISHLIST_NOTICE_WISH_CHANGES_SAVED') }}</span>
                </div>
            @elseif($wishlist->get('saved') == 3)
                <div role="alert" class="alert alert-success mb-4">
                    <span>{{ Lang::txt('COM_WISHLIST_NOTICE_WISH_POSTED') }}</span>
                </div>
            @endif
        @endif

        @if($__view->getError())
            <div role="alert" class="alert alert-error mb-4">
                <span>{{ $__view->getError() }}</span>
            </div>
        @endif

        {{-- Search --}}
        <x-search-bar
            :action="Route::url($base, false)"
            name="search"
            :query="e($filters['search'])"
            :placeholder="Lang::txt('COM_WISHLIST_SEARCH_PLACEHOLDER')"
            :buttonLabel="Lang::txt('COM_WISHLIST_SEARCH')"
        >
            <input type="hidden" name="tags"
                   value="{{ e($filters['tag']) }}" />
            <input type="hidden" name="sortby"
                   value="{{ e($filters['sortby']) }}" />
            <input type="hidden" name="filterby"
                   value="{{ e($filters['filterby']) }}" />
            <input type="hidden" name="task"
                   value="{{ e($__view->task) }}" />
            <input type="hidden" name="newsearch" value="1" />
        </x-search-bar>

        {{-- Active tag filters --}}
        @if($filters['tag'])
            @php
                $activeTags = $cloud->parseTags($filters['tag']);
                $tagBaseUrl = $base;
                $tagBaseUrl .= ($filters['search'] ? '&search=' . e($filters['search']) : '');
                $tagBaseUrl .= ($filters['sortby'] ? '&sortby=' . e($filters['sortby']) : '');
                $tagBaseUrl .= ($filters['filterby'] ? '&filterby=' . e($filters['filterby']) : '');
            @endphp
            <div class="flex flex-wrap gap-2 mb-4">
                @foreach($activeTags as $activeTag)
                    @php
                        $remaining = $cloud->parseTags($filters['tag'], $activeTag);
                        $removeUrl = Route::url($tagBaseUrl . '&tag=' . implode(',', $remaining), false);
                    @endphp
                    <a href="{{ $removeUrl }}"
                       class="badge badge-primary gap-1">
                        {{ e(stripslashes($activeTag)) }}
                        <span aria-label="Remove">&times;</span>
                    </a>
                @endforeach
            </div>
        @endif

        {{-- Sort tabs --}}
        <div role="tablist" class="tabs tabs-border mb-2">
            @foreach($sortOptions as $key => $label)
                @php
                    $sortUrl = Route::url(
                        $base . '&filterby=' . $filterby
                        . '&sortby=' . $key . '&tags=' . $tag,
                        false
                    );
                @endphp
                <a role="tab" href="{{ $sortUrl }}"
                   class="tab {{ $sortby == $key ? 'tab-active' : '' }}"
                   aria-selected="{{ $sortby == $key ? 'true' : 'false' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Filter tabs --}}
        <div role="tablist" class="tabs tabs-border mb-6">
            @foreach($filterOptions as $key => $label)
                @php
                    $fUrl = Route::url(
                        $base . '&filterby=' . $key
                        . '&sortby=' . $sortby . '&tags=' . $tag,
                        false
                    );
                @endphp
                <a role="tab" href="{{ $fUrl }}"
                   class="tab {{ $filterby == $key ? 'tab-active' : '' }}"
                   aria-selected="{{ $filterby == $key ? 'true' : 'false' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        {{-- Results heading --}}
        <x-results-heading
            :title="Lang::txt('COM_WISHLIST_FILTER_' . strtoupper($filterby))
                . ($filters['tag'] ? ' ' . Lang::txt('COM_WISHLIST_WISHES_TAGGED_WITH', e($filters['tag'])) : '')"
            :count="$total"
        />

        {{-- Wish list --}}
        @if($wishes->count())
            <ul class="list bg-base-100 rounded-box shadow-sm"
                aria-label="{{ Lang::txt('COM_WISHLIST_WISHES') }}">
                @foreach($wishes as $item)
                    @php
                        $item->set('category', $wishlist->get('category'));
                        $item->set('referenceid', $wishlist->get('referenceid'));
                        $item->set('bonus', ($wishlist->get('banking') ? $item->get('bonus') : 0));

                        $state = $item->status('alias');
                        $badgeCls = match($state) {
                            'granted'   => 'badge-success',
                            'rejected'  => 'badge-error',
                            'withdrawn' => 'badge-warning',
                            'accepted'  => 'badge-info',
                            default     => 'badge-ghost',
                        };

                        $itemName = Lang::txt('JANONYMOUS');
                        if (!$item->get('anonymous')) {
                            $itemName = e(stripslashes($item->proposer->get('name', $itemName)));
                            if (in_array($item->proposer->get('access'), User::getAuthorisedViewLevels())) {
                                $proposerUrl = Route::url($item->proposer->link(), false);
                                $itemName = '<a class="link link-hover" href="' . $proposerUrl . '">'
                                    . $itemName . '</a>';
                            }
                        }

                        $itemFilters = '';
                        foreach ($filters as $key => $flt) {
                            if ($flt && $key !== 'comments') {
                                $itemFilters .= '&' . $key . '=' . $flt;
                            }
                        }

                        $itemUrl     = Route::url($item->link('permalink', $itemFilters), false);
                        $commentTotal = $item->comments()->total();

                        // Vote data
                        $item->set('positive', $item->votes()->whereEquals('helpful', 'yes')->total());
                        $item->set('negative', $item->votes()->whereEquals('helpful', 'no')->total());

                        $voteDisabled = User::isGuest()
                            || User::get('id') == $item->get('proposed_by')
                            || $item->get('status') == 1
                            || $item->get('status') == 3
                            || $item->get('status') == 4;

                        $likeUrl = $voteDisabled ? '' : Route::url(
                            'index.php?option=com_wishlist&task=rateitem&refid=' . $item->get('id')
                            . '&vote=yes&page=wishlist' . $filterln,
                            false
                        );
                        $dislikeUrl = $voteDisabled ? '' : Route::url(
                            'index.php?option=com_wishlist&task=rateitem&refid=' . $item->get('id')
                            . '&vote=no&page=wishlist' . $filterln,
                            false
                        );
                    @endphp
                    <li class="list-row">
                        <div class="list-col-grow">
                            @if(!$item->isReported())
                                <h3 class="text-base font-semibold">
                                    <a class="link link-hover text-primary"
                                       href="{{ $itemUrl }}">
                                        {{ e(stripslashes($item->get('subject'))) }}
                                    </a>
                                </h3>
                                <div class="flex items-baseline gap-x-3 text-sm text-base-content/60">
                                    <span>#{{ $item->get('id') }}</span>
                                    <span>{{ Lang::txt('COM_WISHLIST_WISH_PROPOSED_BY') }}
                                        {!! $itemName !!}</span>
                                    <time datetime="{{ $item->proposed() }}">
                                        {{ $item->proposed('date') }}
                                    </time>
                                    <a class="link link-hover"
                                       href="{{ Route::url($item->link('comments'), false) }}">
                                        {{ $commentTotal }} {{ Lang::txt('COM_WISHLIST_COMMENTS') }}
                                    </a>
                                </div>
                            @else
                                <span class="text-warning">
                                    {{ Lang::txt('COM_WISHLIST_NOTICE_POSTING_REPORTED') }}
                                </span>
                            @endif
                        </div>
                        @if(!$item->isReported())
                            <div class="flex items-center gap-3">
                                @if($wishlist->get('banking') && $item->get('bonus') > 0
                                    && ($item->isOpen() || $item->isAccepted()))
                                    <span class="badge badge-warning badge-sm"
                                          title="{{ Lang::txt('COM_WISHLIST_WISH_ADD_BONUS') }}">
                                        {{ $item->get('bonus') }} {{ Lang::txt('COM_WISHLIST_POINTS') }}
                                    </span>
                                @endif
                                <x-vote-widget
                                    :likes="$item->get('positive', 0)"
                                    :dislikes="$item->get('negative', 0)"
                                    vote=""
                                    :likeUrl="$likeUrl"
                                    :dislikeUrl="$dislikeUrl"
                                    :disabled="$voteDisabled"
                                />
                                <span class="badge {{ $badgeCls }} badge-sm">
                                    {{ $item->status('text') }}
                                </span>
                                @if($item->isPrivate())
                                    <span class="badge badge-outline badge-sm">
                                        {{ Lang::txt('COM_WISHLIST_PRIVATE') }}
                                    </span>
                                @endif
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>

            {{-- Pagination --}}
            @php
                $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
                $pageNav->setAdditionalUrlParam('filterby', $filters['filterby']);
                $pageNav->setAdditionalUrlParam('sortby', $filters['sortby']);
                $pageNav->setAdditionalUrlParam('tag', $filters['tag']);
                $pageNav->setAdditionalUrlParam('newsearch', 0);
                if ($filters['search']) {
                    $pageNav->setAdditionalUrlParam('search', $filters['search']);
                }
            @endphp
            {!! $pageNav->render() !!}
        @else
            @if($filters['filterby'] == 'all' && !$filters['tag'])
                <x-empty-state
                    :title="Lang::txt('COM_WISHLIST_NO_WISHES_BE_FIRST')"
                    message=""
                >
                    <a class="btn btn-primary"
                       href="{{ Route::url($wishlist->link('new'), false) }}">
                        {{ Lang::txt('COM_WISHLIST_TASK_ADD') }}
                    </a>
                </x-empty-state>
            @else
                <x-empty-state
                    :title="Lang::txt('COM_WISHLIST_NO_WISHES_SELECTION')"
                    message=""
                >
                    <a class="btn btn-ghost"
                       href="{{ Route::url($base, false) }}">
                        {{ Lang::txt('COM_WISHLIST_VIEW_ALL_WISHES') }}
                    </a>
                </x-empty-state>
            @endif
        @endif
    </x-page-container>
@endif
