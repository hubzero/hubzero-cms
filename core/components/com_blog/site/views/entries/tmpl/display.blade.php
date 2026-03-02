{{--
  Blog landing page — list of entries with sidebar archive/popular navigation.

  Variables from controller (displayTask):
    $archive  — Archive model instance
    $config   — Component params (Registry) with access-* flags
    $filters  — array: year, month, scope, scope_id, search, authorized, state, access

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  // Build page title and breadcrumbs
  $title = Lang::txt('COM_BLOG');

  if (Pathway::count() <= 0) {
      Pathway::append(Lang::txt('COM_BLOG'), 'index.php?option=' . $option);
  }
  if ($year = $filters['year']) {
      $title .= ': ' . $year;
      Pathway::append($year, 'index.php?option=' . $option . '&year=' . $year);
  }
  if ($month = $filters['month']) {
      $title .= ': ' . $month;
      Pathway::append(
          sprintf('%02d', $month),
          'index.php?option=' . $option . '&year=' . $year . '&month=' . sprintf('%02d', $month)
      );
  }

  Document::setTitle($title);

  // Load component CSS/JS
  $__view->css();
  $__view->js();

  // Query: oldest entry (for year/month sidebar)
  $first = $archive->entries([
      'state'      => 1,
      'authorized' => $filters['authorized'],
      'scope'      => $filters['scope'],
      'scope_id'   => $filters['scope_id'],
  ])->order('publish_up', 'asc')->limit(1)->row();

  // Query: paginated entry list
  $rows = $archive->entries($filters)->ordered()->paginated()->rows();

  // RSS feed URL
  $feedPath  = 'index.php?option=' . $option . '&task=feed.rss';
  $feedPath .= $filters['year']  ? '&year=' . $filters['year']   : '';
  $feedPath .= $filters['month'] ? '&month=' . $filters['month'] : '';
  $feedUrl   = Route::url($feedPath);

  // Search/browse URL
  $browseUrl = Route::url('index.php?option=' . $option . '&task=browse');

  // Intro length config
  $introLength = $config->get('introlength', 300);
  $cleanIntro  = $config->get('cleanintro', 1);
@endphp

{{-- Page header — styled by template CSS (.page-header in blade.src.css) --}}
<header class="page-header">
  <h1>{{ Lang::txt('COM_BLOG') }}</h1>
  @if($config->get('feeds_enabled', 1))
    <div class="page-header-actions">
      <a class="btn" href="{{ $feedUrl }}">
        <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
          <path d="M3.75 3a.75.75 0 0 0 0 1.5A15.75 15.75 0 0 1 19.5 20.25a.75.75 0 0 0 1.5 0C21 11.26 13.74 4.5 3.75 4.5a.75.75 0 0 0 0-1.5Zm0 6a.75.75 0 0 0 0 1.5 9.75 9.75 0 0 1 9.75 9.75.75.75 0 0 0 1.5 0A11.25 11.25 0 0 0 3.75 9ZM6 18.75a2.25 2.25 0 1 0-4.5 0 2.25 2.25 0 0 0 4.5 0Z"/>
        </svg>
        {{ Lang::txt('COM_BLOG_FEED') }}
      </a>
    </div>
  @endif
</header>

<section class="page-body">
  <div class="page-layout">
    <div class="page-main">

      {{-- Search --}}
      <form action="{{ $browseUrl }}" method="get" role="search" class="mb-6">
        <label for="entry-search-field" class="sr-only">
          {{ Lang::txt('COM_BLOG_SEARCH_LABEL') }}
        </label>
        <input type="search"
               id="entry-search-field"
               name="search"
               class="input input-bordered w-full"
               value="{{ e($filters['search']) }}"
               placeholder="{{ Lang::txt('COM_BLOG_SEARCH_PLACEHOLDER') }}" />
        <input type="hidden" name="option" value="{{ $option }}" />
      </form>

      {{-- Section heading --}}
      <h2 class="text-lg font-semibold mb-4">
        @if(!empty($filters['search']))
          {{ Lang::txt('COM_BLOG_SEARCH_FOR', e($filters['search'])) }}
        @elseif(empty($filters['year']))
          {{ Lang::txt('COM_BLOG_LATEST_ENTRIES') }}
        @else
          @php
            $archiveDate  = $filters['year'];
            $archiveDate .= $filters['month'] ? '-' . $filters['month'] : '-01';
            $archiveDate .= '-01 00:00:00';
          @endphp
          {{ $filters['month'] ? Date::of($archiveDate)->format('M Y') : Date::of($archiveDate)->format('Y') }}
        @endif
      </h2>

      @if($rows->count() > 0)
        {{-- Entry list --}}
        <ul class="list bg-base-100 rounded-box shadow-sm" aria-label="{{ Lang::txt('COM_BLOG') }}">
          @foreach($rows as $row)
            @php
              $isOwnerOrAdmin = (
                  User::get('id') == $row->get('created_by')
                  || User::authorise('core.manage', $option)
              );
            @endphp
            <li class="list-row" id="e{{ $row->get('id') }}">
              <div class="list-col-grow">
                <h3 class="text-base font-semibold">
                  <a class="link link-hover text-primary" href="{{ Route::url($row->link()) }}">
                    {{ e(stripslashes($row->get('title'))) }}
                  </a>
                </h3>
                <div class="flex items-baseline gap-x-3 text-sm text-base-content/60">
                  <time datetime="{{ $row->published() }}">
                    {{ $row->published('date') }}
                  </time>
                  @if($config->get('show_authors'))
                    @php
                      $authorAccess = $row->creator->get('access');
                      $viewLevels   = User::getAuthorisedViewLevels();
                    @endphp
                    <span>
                      by
                      @if(in_array($authorAccess, $viewLevels))
                        <a class="link link-hover"
                           href="{{ Route::url($row->creator->link()) }}">
                          {{ e(stripslashes($row->creator->get('name'))) }}
                        </a>
                      @else
                        {{ e(stripslashes($row->creator->get('name'))) }}
                      @endif
                    </span>
                  @endif
                  @if($row->get('allow_comments') == 1)
                    <a class="link link-hover"
                       href="{{ Route::url($row->link('comments')) }}">
                      {{ Lang::txt('COM_BLOG_NUM_COMMENTS', $row->comments->count()) }}
                    </a>
                  @else
                    <span>{{ Lang::txt('COM_BLOG_COMMENTS_OFF') }}</span>
                  @endif
                </div>
                <p class="text-sm text-base-content/70 mt-1 line-clamp-2">
                  @if($cleanIntro)
                    {{ \Hubzero\Utility\Str::truncate(strip_tags($row->content), $introLength) }}
                  @else
                    {!! \Hubzero\Utility\Str::truncate($row->content, $introLength, ['html' => true]) !!}
                  @endif
                </p>
              </div>
              @if($isOwnerOrAdmin)
                <div>
                  <span class="badge badge-sm {{ $row->ended() ? 'badge-warning' : 'badge-success' }}">
                    {{ $row->visibility('text') }}
                  </span>
                  <div class="flex gap-1 mt-1">
                    <a class="btn btn-xs btn-ghost"
                       href="{{ Route::url($row->link('edit')) }}">
                      {{ Lang::txt('JACTION_EDIT') }}
                    </a>
                    <a class="btn btn-xs btn-ghost text-error"
                       href="{{ Route::url($row->link('delete')) }}"
                       data-confirm="{{ Lang::txt('COM_BLOG_CONFIRM_DELETE') }}">
                      {{ Lang::txt('JACTION_DELETE') }}
                    </a>
                  </div>
                </div>
              @endif
            </li>
          @endforeach
        </ul>

        {{-- Pagination --}}
        <nav aria-label="Page navigation" class="flex justify-center mt-8">
          {!! $rows->pagination
              ->setAdditionalUrlParam('year', $filters['year'])
              ->setAdditionalUrlParam('month', $filters['month'])
              ->setAdditionalUrlParam('search', $filters['search'])
          !!}
        </nav>
      @else
        {{-- Empty state --}}
        <div class="text-center py-12" role="status">
          <p class="text-base-content/60 mb-4">{{ Lang::txt('COM_BLOG_NO_ENTRIES_FOUND') }}</p>
        </div>
      @endif
    </div>

    {{-- Sidebar --}}
    <aside class="page-sidebar">

      {{-- New entry button --}}
      @if($config->get('access-create-entry'))
        <a class="btn btn-primary w-full"
           href="{{ Route::url('index.php?option=' . $option . '&task=new') }}">
          {{ Lang::txt('COM_BLOG_NEW_ENTRY') }}
        </a>
      @endif

      {{-- Archive by year/month --}}
      <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
          <h2 class="card-title text-sm">{{ Lang::txt('COM_BLOG_ENTRIES_BY_YEAR') }}</h2>
          @if($first->get('id'))
            @php
              $startYear = intval(substr($first->get('publish_up'), 0, 4));
              $nowYear   = intval(Date::format('Y'));
              $nowMonth  = intval(Date::format('m'));
              $months    = [
                  'COM_BLOG_JANUARY', 'COM_BLOG_FEBRUARY', 'COM_BLOG_MARCH',
                  'COM_BLOG_APRIL',   'COM_BLOG_MAY',      'COM_BLOG_JUNE',
                  'COM_BLOG_JULY',    'COM_BLOG_AUGUST',    'COM_BLOG_SEPTEMBER',
                  'COM_BLOG_OCTOBER', 'COM_BLOG_NOVEMBER',  'COM_BLOG_DECEMBER',
              ];
            @endphp
            <ul class="menu menu-sm p-0">
              @for($i = $nowYear; $i >= $startYear; $i--)
                <li>
                  <a href="{{ Route::url('index.php?option=' . $option . '&year=' . $i) }}"
                     @if($filters['year'] == $i && !$filters['month']) class="active" @endif>
                    {{ $i }}
                  </a>
                  @php
                    $showMonths = ($filters['year'] && $i == $filters['year'])
                        || (!$filters['year'] && $i == $nowYear);
                    $maxMonth = ($i == $nowYear) ? $nowMonth : 12;
                  @endphp
                  @if($showMonths)
                    <ul>
                      @for($k = 0; $k < $maxMonth; $k++)
                        <li>
                          <a href="{{ Route::url('index.php?option=' . $option . '&year=' . $i . '&month=' . sprintf('%02d', $k + 1)) }}"
                             @if($filters['month'] == $k + 1 && $filters['year'] == $i) class="active" @endif>
                            {{ Lang::txt($months[$k]) }}
                          </a>
                        </li>
                      @endfor
                    </ul>
                  @endif
                </li>
              @endfor
            </ul>
          @else
            <p class="text-sm text-base-content/60">{{ Lang::txt('COM_BLOG_NO_ENTRIES_FOUND') }}</p>
          @endif
        </div>
      </div>

      {{-- Popular entries --}}
      <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
          <h2 class="card-title text-sm">{{ Lang::txt('COM_BLOG_POPULAR_ENTRIES') }}</h2>
          @php
            $popular = $archive->entries([
                'state'  => $filters['state'],
                'access' => $filters['access'],
            ])->order('hits', 'desc')->limit(5)->rows();
          @endphp
          @if($popular->count())
            <ul class="menu menu-sm p-0">
              @foreach($popular as $prow)
                <li>
                  <a href="{{ Route::url($prow->link()) }}">
                    {{ e(stripslashes($prow->get('title'))) }}
                  </a>
                </li>
              @endforeach
            </ul>
          @else
            <p class="text-sm text-base-content/60">{{ Lang::txt('COM_BLOG_NO_ENTRIES_FOUND') }}</p>
          @endif
        </div>
      </div>

    </aside>
  </div>
</section>
