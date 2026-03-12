{{--
  Knowledge Base — article list for a category (or all).

  Variables from controller (categoryTask):
    $archive   — Archive model
    $category  — Category object (or blank with alias='all')
    $filters   — array: sort, category, search
    $catid     — Active parent category ID (0 for "all")

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  if (Pathway::count() <= 0) {
      Pathway::append(
          Lang::txt('COM_KB'),
          'index.php?option=' . $option
      );
  }
  Pathway::append(
      $category->get('title'),
      $category->link()
  );

  Document::setTitle(
      Lang::txt('COM_KB') . ': ' . $category->get('title')
  );

  $viewLevels = User::getAuthorisedViewLevels();
  $allUrl = Route::url('index.php?option=' . $option . '&section=all', false);

  $catFilters = [
      'state'  => 1,
      'access' => $viewLevels,
  ];
  $categories = $archive->categories($catFilters);

  // Build article query
  if (!$category->get('id')) {
      $articles = $archive->articles();
  } else {
      $articles = $category->articles();
  }

  $articles->whereEquals('state', 1)
      ->whereIn('access', $viewLevels);

  if (!empty($filters['search'])) {
      $articles->whereLike('title', $filters['search'], 1)
          ->orWhereLike('fulltxt', $filters['search'], 1)
          ->resetDepth();
  }

  if ($filters['sort'] == 'popularity') {
      $articles->order('helpful', 'desc');
  } else {
      $articles->order('modified', 'desc')
          ->order('created', 'desc');
  }

  $articles = $articles->paginated();

  // Clean sort labels (strip &darr; entity prefix)
  $cleanLabel = function ($key) {
      return trim(preg_replace('/^&[a-z]+;\s*/i', '', Lang::txt($key)));
  };
@endphp

<x-page-container :title="Lang::txt('COM_KB')">
  @slot('actions')
    <a class="btn"
       href="{{ Route::url('index.php?option=' . $option, false) }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
      </svg>
      {{ Lang::txt('COM_KB_MAIN') }}
    </a>
  @endslot

  @slot('sidebar')
    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <h3 class="card-title text-sm">{{ Lang::txt('COM_KB_CATEGORIES') }}</h3>
        <ul class="menu menu-sm">
          <li>
            <a class="{{ $catid <= 0 ? 'active' : '' }}"
               href="{{ $allUrl }}">
              {{ Lang::txt('COM_KB_ALL_ARTICLES') }}
            </a>
          </li>
          @foreach($categories as $row)
            @if($row->get('articles', 0) > 0)
              <li>
                <a class="{{ $catid == $row->get('id') ? 'active' : '' }}"
                   href="{{ Route::url($row->link(), false) }}">
                  {{ $row->get('title') }}
                  <span class="badge badge-sm badge-ghost">{{ $row->get('articles', 0) }}</span>
                </a>
                @if($catid == $row->get('id'))
                  @php
                    $children = $row->children($catFilters)->rows();
                  @endphp
                  @if(count($children) > 0)
                    <ul>
                      @foreach($children as $child)
                        <li>
                          <a class="{{ $category->get('id') == $child->get('id') ? 'active' : '' }}"
                             href="{{ Route::url($child->link(), false) }}">
                            {{ $child->get('title') }}
                            <span class="badge badge-sm badge-ghost">{{ $child->get('articles', 0) }}</span>
                          </a>
                        </li>
                      @endforeach
                    </ul>
                  @endif
                @endif
              </li>
            @endif
          @endforeach
        </ul>
      </div>
    </div>
  @endslot

  {{-- Search --}}
  <form method="get"
        action="{{ $allUrl }}"
        role="search"
        class="mb-6">
    <label for="entry-search-field" class="sr-only">
      {{ Lang::txt('COM_KB_SEARCH_LABEL') }}
    </label>
    <input type="search"
           id="entry-search-field"
           name="search"
           class="input input-bordered w-full"
           value="{{ $filters['search'] }}"
           placeholder="{{ Lang::txt('COM_KB_SEARCH_PLACEHOLDER') }}" />
  </form>

  {{-- Sort tabs --}}
  <div class="flex flex-wrap items-center gap-3 mb-4">
    <div role="tablist" class="tabs tabs-border">
      @php
        $sortBase = $category->get('id')
            ? $category->link()
            : 'index.php?option=' . $option . '&section=all';
      @endphp
      <a role="tab"
         class="tab {{ $filters['sort'] == 'popularity' ? 'tab-active' : '' }}"
         href="{{ Route::url($sortBase . '&sort=popularity', false) }}">
        {{ $cleanLabel('COM_KB_SORT_POPULAR') }}
      </a>
      <a role="tab"
         class="tab {{ $filters['sort'] == 'recent' ? 'tab-active' : '' }}"
         href="{{ Route::url($sortBase . '&sort=recent', false) }}">
        {{ $cleanLabel('COM_KB_SORT_RECENT') }}
      </a>
    </div>

    <span class="text-sm text-base-content/50 ml-auto">
      {{ $category->get('title') }}
      ({{ $articles->count() }})
    </span>
  </div>

  {{-- Results --}}
  @if($articles->count() > 0)
    <ul class="list bg-base-100 rounded-box shadow-sm" aria-label="{{ Lang::txt('COM_KB_ARTICLES') }}">
      @foreach($articles as $row)
        @php
          // Set category alias for URL generation
          if (!$category->get('id')) {
              foreach ($categories as $cat) {
                  if ($cat->get('id') == $row->get('category')) {
                      $row->set('ctitle', $cat->get('title'));
                      $row->set('calias', $cat->get('path'));
                      break;
                  }
              }
          } else {
              $row->set('calias', $category->get('path'));
              $row->set('ctitle', $category->get('title'));
          }
        @endphp
        <li class="list-row">
          <div class="list-col-grow">
            <a class="link link-hover text-primary font-semibold"
               href="{{ Route::url($row->link(), false) }}">
              {{ $row->get('title', '') }}
            </a>
            <div class="text-sm text-base-content/60">
              @if($catid <= 0 && $row->get('ctitle'))
                <span>{!! Lang::txt('COM_KB_IN_CATEGORY', e($row->get('ctitle', '') ?? '') !!}</span>
              @endif
              <span>
                {{ Lang::txt('COM_KB_LAST_MODIFIED') }}
                {{ Lang::txt('COM_KB_DATETIME_AT') }}
                <time datetime="{{ $row->modified() }}">{{ $row->modified('time') }}</time>
                {{ Lang::txt('COM_KB_DATETIME_ON') }}
                <time datetime="{{ $row->modified() }}">{{ $row->modified('date') }}</time>
              </span>
            </div>
          </div>
          <span class="flex items-center gap-1 text-sm text-base-content/50"
                title="{{ Lang::txt('COM_KB_HELPFUL') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="w-4 h-4 shrink-0" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M6.633 10.25c.806 0 1.533-.446 2.031-1.08a9.041 9.041 0 0 1 2.861-2.4c.723-.384 1.35-.956 1.653-1.715a4.498 4.498 0 0 0 .322-1.672V3a.75.75 0 0 1 .75-.75 2.25 2.25 0 0 1 2.25 2.25c0 1.152-.26 2.243-.723 3.218-.266.558.107 1.282.725 1.282m0 0h3.126c1.026 0 1.945.694 2.054 1.715.045.422.068.85.068 1.285a11.95 11.95 0 0 1-2.649 7.521c-.388.482-.987.729-1.605.729H13.48c-.483 0-.964-.078-1.423-.23l-3.114-1.04a4.501 4.501 0 0 0-1.423-.23H5.904m7.723-9.132c.065.987.065 1.98 0 2.968" />
            </svg>
            {{ $row->get('helpful', 0) }}
          </span>
        </li>
      @endforeach
    </ul>

    {{-- Pagination --}}
    <nav aria-label="Page navigation" class="flex justify-center mt-8">
      @php
        $pageNav = $articles->pagination;
        $pageNav->setAdditionalUrlParam('search', $filters['search']);
        $pageNav->setAdditionalUrlParam('sort', $filters['sort']);
      @endphp
      {!! $pageNav !!}
    </nav>
  @else
    <x-empty-state
      :title="Lang::txt('COM_KB_NO_ARTICLES')"
      :message="$filters['search'] ? '' : Lang::txt('COM_KB_NO_ARTICLES_FOR_CATEGORY')"
    />
  @endif

</x-page-container>
