{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
use Hubzero\Facades\Date;
use Hubzero\Facades\User;

$__view->css();
$__view->js();

$base = 'index.php?option=com_groups&cn=' . $group->get('cn') . '&active=blog';

$first = $archive->entries([
        'state'    => 1,
        'scope'    => $filters['scope'],
        'scope_id' => $filters['scope_id']
    ])
    ->order('publish_up', 'asc')
    ->limit(1)
    ->row();

$rows = $archive->entries($filters)
    ->ordered()
    ->paginated()
    ->rows();

$isManager = $authorized == 'manager' || $authorized == 'admin';
$canManage = $canpost || $isManager;
$noFilters = !$filters['year'] && !$filters['search'];
$isEmpty = $noFilters && !$rows->count();
@endphp

@if ($group->published == 1 && $canManage)
    <ul id="page_options">
        @if ($canpost)
            <li>
                <a class="btn btn-primary gap-2" href="{{ Route::url($base . '&action=new') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    {{ Lang::txt('PLG_GROUPS_BLOG_NEW_ENTRY') }}
                </a>
            </li>
        @endif
        @if ($isManager)
            <li>
                <a class="btn btn-ghost gap-2" href="{{ Route::url($base . '&action=settings') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    {{ Lang::txt('PLG_GROUPS_BLOG_SETTINGS') }}
                </a>
            </li>
        @endif
    </ul>
@endif

@if ($isManager && $isEmpty)
    <div class="hero bg-base-200 rounded-box p-8">
        <div class="hero-content text-center">
            <div class="max-w-lg">
                <p class="text-lg mb-6">{{ Lang::txt('PLG_GROUPS_BLOG_INTRO_EMPTY') }}</p>

                <div class="text-left space-y-4">
                    <p class="font-bold">{{ Lang::txt('PLG_GROUPS_BLOG_INTRO_WHAT_IS_A_BLOG') }}</p>
                    <p>{{ Lang::txt('PLG_GROUPS_BLOG_INTRO_WHAT_IS_A_BLOG_EXPLANATION') }}</p>

                    <p class="font-bold">{{ Lang::txt('PLG_GROUPS_BLOG_INTRO_HOW_TO_START') }}</p>
                    <p>{{ Lang::txt('PLG_GROUPS_BLOG_INTRO_HOW_TO_START_EXPLANATION') }}</p>
                </div>
            </div>
        </div>
    </div>
@else
    <form method="get" action="{{ Route::url($base . '&action=browse') }}" id="blogentries">
        <section class="section">
            <div class="subject">
                @if ($__view->getError())
                    <div class="alert alert-error">
                        <p>{{ $__view->getError() }}</p>
                    </div>
                @endif

                <div class="container data-entry">
                    <fieldset class="entry-search">
                        <legend>{{ Lang::txt('PLG_GROUPS_BLOG_SEARCH_LEGEND') }}</legend>
                        <label for="entry-search-field" class="sr-only">
                            {{ Lang::txt('PLG_GROUPS_BLOG_SEARCH_LABEL') }}
                        </label>
                        <div class="join w-full">
                            <input type="text"
                                name="search"
                                id="entry-search-field"
                                class="input input-bordered join-item flex-1"
                                value="{{ e(stripslashes($filters['search'])) }}"
                                placeholder="{{ Lang::txt('PLG_GROUPS_BLOG_SEARCH_PLACEHOLDER') }}" />
                            <button type="submit" class="btn btn-neutral join-item">
                                {{ Lang::txt('PLG_GROUPS_BLOG_SEARCH') }}
                            </button>
                        </div>
                    </fieldset>
                </div>

                <div class="container">
                    <h3>
                        @if (isset($filters['search']) && $filters['search'])
                            {{ Lang::txt('PLG_GROUPS_BLOG_SEARCH_FOR', e($filters['search'])) }}
                        @elseif (!isset($filters['year']) || !$filters['year'])
                            {{ Lang::txt('PLG_GROUPS_BLOG_LATEST_ENTRIES') }}
                        @elseif (isset($filters['year']) && isset($filters['month']) && $filters['month'] == 0)
                            {{ Lang::txt('PLG_GROUPS_BLOG_YEAR_ENTRIES_FOR', $filters['year']) }}
                        @else
                            @php
                            $archiveDate  = $filters['year'];
                            $archiveDate .= ($filters['month']) ? '-' . $filters['month'] : '-01';
                            $archiveDate .= '-01 00:00:00';
                            @endphp
                            @if ($filters['month'])
                                {{ Date::of($archiveDate)->format('M Y') }}
                            @else
                                {{ Date::of($archiveDate)->format('Y') }}
                            @endif
                        @endif

                        @if ($config->get('feeds_enabled', 1))
                            @php
                            $path  = $base . '&scope=feed.rss';
                            $path .= ($filters['year'])  ? '&year=' . $filters['year']   : '';
                            $path .= ($filters['month']) ? '&month=' . $filters['month'] : '';
                            $feed = Route::url($path);
                            $live_site = 'https://' . $_SERVER['HTTP_HOST'];
                            if (substr($feed, 0, 4) != 'http') {
                                $feed = rtrim($live_site, '/') . '/' . ltrim($feed, '/');
                            }
                            $feed = str_replace('https://', 'http://', $feed);
                            @endphp
                            <a class="btn btn-ghost btn-xs gap-1" href="{{ $feed }}">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 5c7.18 0 13 5.82 13 13M6 11a7 7 0 017 7m-6 0a1 1 0 11-2 0 1 1 0 012 0z" /></svg>
                                {{ Lang::txt('PLG_GROUPS_BLOG_RSS_FEED') }}
                            </a>
                        @endif
                    </h3>

                    @if ($rows->count() > 0)
                        <div class="space-y-4">
                            @php $cls = 'even'; @endphp
                            @foreach ($rows as $row)
                                @php
                                $cls = ($cls == 'even') ? 'odd' : 'even';
                                $clse = '';
                                if (!$row->isAvailable()) {
                                    if ($row->get('created_by') != User::get('id')) {
                                        continue;
                                    }
                                    $clse = ' pending';
                                }
                                if ($row->ended()) {
                                    $clse = ' expired';
                                }
                                if ($row->get('state') == 0) {
                                    $clse = ' private';
                                }
                                @endphp
                                <article class="card card-compact bg-base-100 shadow-sm {{ $clse }}" id="e{{ $row->get('id') }}">
                                    <div class="card-body">
                                        <h4 class="card-title text-lg">
                                            <a class="link link-hover" href="{{ Route::url($row->link()) }}">
                                                {{ e(stripslashes($row->get('title'))) }}
                                            </a>
                                        </h4>

                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-base-content/70">
                                            <span>
                                                <time datetime="{{ $row->published() }}">
                                                    {{ $row->published('date') }}
                                                </time>
                                            </span>
                                            <span>
                                                <time datetime="{{ $row->published() }}">
                                                    {{ $row->published('time') }}
                                                </time>
                                            </span>
                                            <span>
                                                @php
                                                $viewLevels = User::getAuthorisedViewLevels();
                                                $creatorAccess = $row->creator->get('access');
                                                @endphp
                                                @if (in_array($creatorAccess, $viewLevels))
                                                    <a class="link link-hover" href="{{ Route::url($row->creator->link()) }}">
                                                        {{ e(stripslashes($row->creator->get('name'))) }}
                                                    </a>
                                                @else
                                                    {{ e(stripslashes($row->creator->get('name'))) }}
                                                @endif
                                            </span>
                                            @if ($row->get('allow_comments') == 1)
                                                @php
                                                $commentCount = $row->comments()
                                                    ->whereIn('state', [
                                                        \Components\Blog\Models\Comment::STATE_PUBLISHED,
                                                        \Components\Blog\Models\Comment::STATE_FLAGGED
                                                    ])
                                                    ->count();
                                                @endphp
                                                <a class="link link-hover" href="{{ Route::url($row->link('comments')) }}">
                                                    {{ Lang::txt('PLG_GROUPS_BLOG_NUM_COMMENTS', $commentCount) }}
                                                </a>
                                            @else
                                                <span>{{ Lang::txt('PLG_GROUPS_BLOG_COMMENTS_OFF') }}</span>
                                            @endif

                                            @php
                                            $isAuthor = User::get('id') == $row->get('created_by');
                                            $canEdit = $isAuthor || $isManager;
                                            @endphp
                                            @if ($canEdit)
                                                <span class="badge badge-outline badge-sm">
                                                    {{ $row->visibility('text') }}
                                                </span>
                                            @endif
                                        </div>

                                        <div class="entry-content text-base-content/80">
                                            @php $introLen = $config->get('introlength', 300); @endphp
                                            @if ($config->get('cleanintro', 1))
                                                <p>{{ \Hubzero\Utility\Str::truncate(strip_tags($row->content), $introLen) }}</p>
                                            @else
                                                {!! \Hubzero\Utility\Str::truncate($row->content, $introLen) !!}
                                            @endif
                                        </div>

                                        @if ($group->published == 1 && $canEdit)
                                            <div class="card-actions justify-end">
                                                <a class="btn btn-ghost btn-xs gap-1" href="{{ Route::url($row->link('edit')) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                    {{ Lang::txt('PLG_GROUPS_BLOG_EDIT') }}
                                                </a>
                                                <a class="btn btn-ghost btn-xs text-error gap-1"
                                                    data-confirm="{{ Lang::txt('PLG_GROUPS_BLOG_CONFIRM_DELETE') }}"
                                                    href="{{ Route::url($row->link('delete')) }}">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                    {{ Lang::txt('PLG_GROUPS_BLOG_DELETE') }}
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>

                        @php
                        $pageNav = $rows->pagination;
                        $pageNav->setAdditionalUrlParam('cn', $group->get('cn'));
                        $pageNav->setAdditionalUrlParam('active', 'blog');
                        if ($filters['year']) {
                            $pageNav->setAdditionalUrlParam('year', $filters['year']);
                        }
                        if ($filters['month']) {
                            $pageNav->setAdditionalUrlParam('month', $filters['month']);
                        }
                        if ($filters['search']) {
                            $pageNav->setAdditionalUrlParam('search', $filters['search']);
                        }
                        echo $pageNav;
                        @endphp
                    @else
                        <div class="alert alert-warning">
                            <p>{{ Lang::txt('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <aside class="aside">
                <div class="card bg-base-100 shadow-sm mb-4">
                    <div class="card-body">
                        <h4 class="card-title text-base">{{ Lang::txt('PLG_GROUPS_BLOG_ENTRIES_BY_YEAR') }}</h4>
                        <ul class="menu menu-sm bg-base-100 rounded-box p-0">
                            @if ($first->get('id'))
                                @php
                                $start = intval(substr($first->get('publish_up'), 0, 4));
                                $now = date('Y');
                                @endphp
                                @for ($i = $now, $n = $start; $i >= $n; $i--)
                                    <li>
                                        <a href="{{ Route::url($base . '&scope=' . $i) }}">{{ $i }}</a>
                                        @php
                                        $isSelectedYear = $filters['year'] && $i == $filters['year'];
                                        $isCurrentYear = !$filters['year'] && $i == $now;
                                        @endphp
                                        @if ($isSelectedYear || $isCurrentYear)
                                            @php
                                            $m = [
                                                'PLG_GROUPS_BLOG_JANUARY', 'PLG_GROUPS_BLOG_FEBRUARY',
                                                'PLG_GROUPS_BLOG_MARCH', 'PLG_GROUPS_BLOG_APRIL',
                                                'PLG_GROUPS_BLOG_MAY', 'PLG_GROUPS_BLOG_JUNE',
                                                'PLG_GROUPS_BLOG_JULY', 'PLG_GROUPS_BLOG_AUGUST',
                                                'PLG_GROUPS_BLOG_SEPTEMBER', 'PLG_GROUPS_BLOG_OCTOBER',
                                                'PLG_GROUPS_BLOG_NOVEMBER', 'PLG_GROUPS_BLOG_DECEMBER'
                                            ];
                                            $months = ($i == $now) ? date('m') : 12;
                                            @endphp
                                            <ul>
                                                @for ($k = 0, $z = $months; $k < $z; $k++)
                                                    @php
                                                    $monthNum = $k + 1;
                                                    $activeClass = ($filters['month'] && $filters['month'] == $monthNum) ? 'active' : '';
                                                    $monthUrl = Route::url($base . '&scope=' . $i . '/' . sprintf('%02d', $monthNum));
                                                    @endphp
                                                    <li>
                                                        <a class="{{ $activeClass }}" href="{{ $monthUrl }}">
                                                            {{ Lang::txt($m[$k]) }}
                                                        </a>
                                                    </li>
                                                @endfor
                                            </ul>
                                        @endif
                                    </li>
                                @endfor
                            @endif
                        </ul>
                    </div>
                </div>

                <div class="card bg-base-100 shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title text-base">{{ Lang::txt('PLG_GROUPS_BLOG_POPULAR_ENTRIES') }}</h4>
                        @php
                        $popular = $archive->entries([
                                'state'  => $filters['state'],
                                'access' => $filters['access']
                            ])
                            ->order('hits', 'desc')
                            ->limit(5)
                            ->rows();
                        @endphp
                        @if ($popular->count())
                            <ul class="menu menu-sm bg-base-100 rounded-box p-0">
                                @foreach ($popular as $prow)
                                    @if ($prow->isAvailable() || $prow->get('created_by') == User::get('id'))
                                        <li>
                                            <a href="{{ Route::url($prow->link()) }}">
                                                {{ e(stripslashes($prow->get('title'))) }}
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        @else
                            <p class="text-base-content/60">{{ Lang::txt('PLG_GROUPS_BLOG_NO_ENTRIES_FOUND') }}</p>
                        @endif
                    </div>
                </div>
            </aside>
        </section>
    </form>
@endif
