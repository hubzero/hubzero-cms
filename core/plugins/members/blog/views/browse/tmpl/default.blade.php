{{--
 * @package   hubzero-cms
 * @copyright Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license   http://opensource.org/licenses/MIT MIT
--}}

@php
use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

$base = $member->link() . '&active=blog';

$first = $archive->entries([
    'state'    => 1,
    'scope'    => $filters['scope'],
    'scope_id' => $filters['scope_id'],
])
    ->order('publish_up', 'asc')
    ->limit(1)
    ->row();

$rows = $archive->entries($filters)
    ->ordered()
    ->paginated()
    ->rows();

$__view->css()->js();

$isOwner = (User::get('id') == $member->get('id'));
$noFilters = !$filters['year'] && !$filters['search'];
$noEntries = !$rows->count();
@endphp

@if ($isOwner)
  <div class="flex flex-wrap gap-2 mb-4">
    <a class="btn btn-primary btn-sm" href="{{ Route::url($base . '&task=new') }}">
      {{ Lang::txt('PLG_MEMBERS_BLOG_NEW_ENTRY') }}
    </a>
    <a class="btn btn-ghost btn-sm" href="{{ Route::url($base . '&task=settings') }}">
      {{ Lang::txt('PLG_MEMBERS_BLOG_SETTINGS') }}
    </a>
  </div>
@endif

@if ($isOwner && $noFilters && $noEntries)
  <div class="card bg-base-100 shadow-sm">
    <div class="card-body">
      <p>{{ Lang::txt('PLG_MEMBERS_BLOG_INTRO_EMPTY') }}</p>
      <div class="mt-4 space-y-2">
        <p><strong>{{ Lang::txt('PLG_MEMBERS_BLOG_INTRO_WHAT_IS_A_BLOG') }}</strong></p>
        <p>{{ Lang::txt('PLG_MEMBERS_BLOG_INTRO_WHAT_IS_A_BLOG_EXPLANATION') }}</p>
        <p><strong>{{ Lang::txt('PLG_MEMBERS_BLOG_INTRO_HOW_TO_START') }}</strong></p>
        <p>{{ Lang::txt('PLG_MEMBERS_BLOG_INTRO_HOW_TO_START_EXPLANATION') }}</p>
      </div>
    </div>
  </div>
@else
  <form method="get" action="{{ Route::url($base) }}">
    <div class="flex flex-col lg:flex-row gap-6">
      {{-- Main content --}}
      <div class="flex-1 min-w-0">
        @if ($__view->getError())
          <div class="alert alert-error mb-4">{{ $__view->getError() }}</div>
        @endif

        {{-- Search --}}
        <div class="mb-4">
          <div class="join w-full">
            <label for="entry-search-field" class="sr-only">
              {{ Lang::txt('PLG_MEMBERS_BLOG_SEARCH_LABEL') }}
            </label>
            @php
              $searchVal = $filters['search']
                  ? e(stripslashes($filters['search']))
                  : '';
            @endphp
            <input type="text"
                   name="search"
                   id="entry-search-field"
                   class="input input-bordered join-item flex-1"
                   value="{{ $searchVal }}"
                   placeholder="{{ Lang::txt('PLG_MEMBERS_BLOG_SEARCH_PLACEHOLDER') }}" />
            <button type="submit" class="btn btn-neutral join-item">
              {{ Lang::txt('PLG_MEMBERS_BLOG_SEARCH') }}
            </button>
            @if ($searchVal)
              <a href="{{ Route::url($base) }}" class="btn btn-ghost join-item">
                {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
              </a>
            @endif
          </div>
        </div>

        {{-- Heading --}}
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-semibold">
            @if (isset($search) && $search)
              {{ Lang::txt('PLG_MEMBERS_BLOG_SEARCH_FOR', e($filters['search'])) }}
            @elseif (!isset($filters['year']) || !$filters['year'])
              {{ Lang::txt('PLG_MEMBERS_BLOG_LATEST_ENTRIES') }}
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
          </h3>

          @if ($config->get('feeds_enabled', 1))
            @php
              $live_site = rtrim(Request::base(), '/');
              $path  = $base . '&task=feed.rss';
              $path .= ($filters['year'])  ? '&year=' . $filters['year']   : '';
              $path .= ($filters['month']) ? '&month=' . $filters['month'] : '';
              $feed = Route::url($path);
              if (substr($feed, 0, 4) != 'http') {
                  $feed = rtrim($live_site, DS) . DS . ltrim($feed, DS);
              }
              $feed = str_replace('https:://', 'http://', $feed);
            @endphp
            <a class="btn btn-ghost btn-xs" href="{{ $feed }}">
              {{ Lang::txt('PLG_MEMBERS_BLOG_RSS_FEED') }}
            </a>
          @endif
        </div>

        @if ($rows->count() > 0)
          <div class="space-y-4">
            @foreach ($rows as $row)
              <article class="card bg-base-100 shadow-sm {{ $row->ended() ? 'opacity-60' : '' }}"
                       id="e{{ $row->get('id') }}">
                <div class="card-body p-4">
                  <h4 class="card-title text-base">
                    <a class="link link-hover" href="{{ Route::url($row->link()) }}">
                      {{ e(stripslashes($row->get('title'))) }}
                    </a>
                  </h4>

                  <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-sm text-base-content/60">
                    <time datetime="{{ $row->published() }}">
                      {{ $row->published('date') }}
                    </time>
                    <span>{{ $row->published('time') }}</span>

                    @php
                      $viewLevels = User::getAuthorisedViewLevels();
                      $canView = in_array($row->creator->get('access'), $viewLevels);
                    @endphp
                    <span>
                      @if ($canView)
                        <a class="link link-hover" href="{{ Route::url($row->creator->link()) }}">
                          {{ e(stripslashes($row->creator->get('name'))) }}
                        </a>
                      @else
                        {{ e(stripslashes($row->creator->get('name'))) }}
                      @endif
                    </span>

                    @if ($row->get('allow_comments') == 1)
                      @php
                        $comments = $row->comments()
                            ->whereIn('state', [
                                \Components\Blog\Models\Comment::STATE_PUBLISHED,
                                \Components\Blog\Models\Comment::STATE_FLAGGED
                            ])
                            ->count();
                      @endphp
                      <a class="link link-hover" href="{{ Route::url($row->link('comments')) }}">
                        {{ Lang::txt('PLG_MEMBERS_BLOG_NUM_COMMENTS', $comments) }}
                      </a>
                    @else
                      <span>{{ Lang::txt('PLG_MEMBERS_BLOG_COMMENTS_OFF') }}</span>
                    @endif

                    @if (User::get('id') == $row->get('created_by'))
                      <span class="badge badge-ghost badge-sm">{{ $row->visibility('text') }}</span>
                    @endif
                  </div>

                  <div class="mt-2 text-sm">
                    @php
                      $introLength = $config->get('introlength', 300);
                    @endphp
                    @if ($config->get('cleanintro', 1))
                      <p>{{ \Hubzero\Utility\Str::truncate(strip_tags($row->content), $introLength) }}</p>
                    @else
                      {!! \Hubzero\Utility\Str::truncate($row->content, $introLength) !!}
                    @endif
                  </div>

                  @if (User::get('id') == $row->get('created_by'))
                    <div class="card-actions justify-end mt-2">
                      <a class="btn btn-ghost btn-xs"
                         href="{{ Route::url($row->link('edit')) }}">
                        {{ Lang::txt('PLG_MEMBERS_BLOG_EDIT') }}
                      </a>
                      <a class="btn btn-ghost btn-xs text-error"
                         data-confirm="{{ Lang::txt('PLG_MEMBERS_BLOG_CONFIRM_DELETE') }}"
                         href="{{ Route::url($row->link('delete')) }}">
                        {{ Lang::txt('PLG_MEMBERS_BLOG_DELETE') }}
                      </a>
                    </div>
                  @endif
                </div>
              </article>
            @endforeach
          </div>

          @php
            $pageNav = $rows->pagination;
            $pageNav->setAdditionalUrlParam('id', $member->get('id'));
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
          @endphp
          <div class="mt-4">
            {!! $pageNav->render() !!}
          </div>
        @else
          <div class="alert alert-warning">
            {{ Lang::txt('PLG_MEMBERS_BLOG_NO_ENTRIES_FOUND') }}
          </div>
        @endif
      </div>

      {{-- Sidebar --}}
      <aside class="w-full lg:w-64 shrink-0 space-y-4">
        @if ($first->get('id'))
          <div class="card bg-base-100 shadow-sm">
            <div class="card-body p-4">
              <h4 class="font-semibold mb-2">{{ Lang::txt('PLG_MEMBERS_BLOG_ENTRIES_BY_YEAR') }}</h4>
              @php
                $startYear = intval(substr($first->get('publish_up'), 0, 4));
                $nowYear = (int) date('Y');
                $months = [
                    'JANUARY', 'FEBRUARY', 'MARCH', 'APRIL', 'MAY', 'JUNE',
                    'JULY', 'AUGUST', 'SEPTEMBER', 'OCTOBER', 'NOVEMBER', 'DECEMBER'
                ];
              @endphp
              <ul class="menu menu-sm">
                @for ($i = $nowYear; $i >= $startYear; $i--)
                  <li>
                    <a href="{{ Route::url($base . '&task=' . $i) }}">{{ $i }}</a>
                    @php
                      $isFilteredYear = $filters['year'] && $i == $filters['year'];
                      $isCurrentYear = !$filters['year'] && $i == $nowYear;
                    @endphp
                    @if ($isFilteredYear || $isCurrentYear)
                      <ul>
                        @php $monthCount = ($i == $nowYear) ? date('m') : 12; @endphp
                        @for ($k = 0; $k < $monthCount; $k++)
                          @php
                            $isActive = $filters['month'] && $filters['month'] == ($k + 1);
                          @endphp
                          <li>
                            <a class="{{ $isActive ? 'active' : '' }}"
                               href="{{ Route::url($base . '&task=' . $i . '/' . sprintf('%02d', ($k + 1))) }}">
                              {{ Lang::txt($months[$k]) }}
                            </a>
                          </li>
                        @endfor
                      </ul>
                    @endif
                  </li>
                @endfor
              </ul>
            </div>
          </div>
        @endif

        <div class="card bg-base-100 shadow-sm">
          <div class="card-body p-4">
            <h4 class="font-semibold mb-2">{{ Lang::txt('PLG_MEMBERS_BLOG_POPULAR_ENTRIES') }}</h4>
            @php
              $popular = $archive->entries([
                  'state'  => $filters['state'],
                  'access' => $filters['access'],
              ])
                  ->order('hits', 'desc')
                  ->limit(5)
                  ->rows();
            @endphp
            @if ($popular->count())
              <ol class="list-decimal list-inside space-y-1 text-sm">
                @foreach ($popular as $popRow)
                  <li>
                    <a class="link link-hover" href="{{ Route::url($popRow->link()) }}">
                      {{ e(stripslashes($popRow->get('title'))) }}
                    </a>
                  </li>
                @endforeach
              </ol>
            @else
              <p class="text-sm text-base-content/60">{{ Lang::txt('PLG_MEMBERS_BLOG_NO_ENTRIES_FOUND') }}</p>
            @endif
          </div>
        </div>
      </aside>
    </div>
  </form>
@endif
