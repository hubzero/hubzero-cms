{{--
  Publications browse page — search, sort tabs, paginated results list with sidebar.

  Variables from controller (browseTask):
    $title      — page title
    $option     — component option string
    $config     — component params (Registry)
    $filters    — array: search, tag, category, sortby, limit, start, tag_ignored
    $categories — array of category objects
    $total      — total result count
    $results    — result set
    $pageNav    — Paginator instance

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $database = App::get('db');

  $__view->css();
  $__view->js();

  $browseBase = 'index.php?option=' . $option . '&task=browse';
  $actionUrl  = Route::url($browseBase);

  // Build query string for sort links (preserves search, category, tag)
  $qs  = ($filters['search']   ? '&search=' . e($filters['search'])     : '');
  $qs .= ($filters['category'] ? '&category=' . e($filters['category']) : '');
  $qs .= ($filters['tag']      ? '&tag=' . e($filters['tag'])           : '');

  // Sort options
  $sortbys = [];
  $sortbys['title'] = Lang::txt('COM_PUBLICATIONS_TITLE');
  $sortbys['date']  = Lang::txt('COM_PUBLICATIONS_PUBLISHED');
  if ($config->get('show_ranking')) {
      $sortbys['ranking'] = Lang::txt('COM_PUBLICATIONS_RANKING');
  }

  // Determine which date to show based on sort
  switch ($filters['sortby']) {
      case 'date_created':
          $showDate = 1;
          break;
      case 'date_modified':
          $showDate = 2;
          break;
      case 'date':
      default:
          $showDate = 3;
          break;
  }

  // Get version authors table
  $pa = new \Components\Publications\Tables\Author($database);
@endphp

<x-page-container :title="$title">
  @slot('sidebar')
    <x-sidebar-card :title="Lang::txt('COM_PUBLICATIONS_POPULAR_TAGS')">
      @php
        $rt = new \Components\Publications\Helpers\Tags($database);
      @endphp
      {!! $rt->getTopTagCloud(20, $filters['tag']) !!}
      <p class="text-sm text-base-content/70 mt-2">
        {{ Lang::txt('COM_PUBLICATIONS_CLICK_TAG_TO_FILTER') }}
      </p>
    </x-sidebar-card>
  @endslot

  {{-- Search bar --}}
  <x-search-bar
      :action="$actionUrl"
      :query="$filters['search']"
      :placeholder="Lang::txt('COM_PUBLICATIONS_ENTER_KEYWORD')"
      :label="Lang::txt('COM_PUBLICATIONS_ENTER_KEYWORD')"
      :buttonLabel="Lang::txt('COM_PUBLICATIONS_SEARCH')"
      :clearUrl="$actionUrl"
      name="search">
    <input type="hidden" name="sortby" value="{{ e($filters['sortby']) }}" />
    <input type="hidden" name="tag" value="{{ e($filters['tag']) }}" />
    <input type="hidden" name="category" value="{{ e($filters['category']) }}" />
  </x-search-bar>

  {{-- Applied tags --}}
  @if($filters['tag'])
    @php
      $rt = new \Components\Publications\Helpers\Tags($database);
      $tags = $rt->parseTopTags($filters['tag']);
      $tagBaseUrl = $browseBase;
      $tagBaseUrl .= ($filters['search']   ? '&search=' . e($filters['search'])     : '');
      $tagBaseUrl .= ($filters['sortby']   ? '&sortby=' . e($filters['sortby'])     : '');
      $tagBaseUrl .= ($filters['category'] ? '&category=' . e($filters['category']) : '');
    @endphp
    <div class="flex flex-wrap gap-2 mb-4">
      @foreach($tags as $tag)
        @php
          $remainingTags = $rt->parseTopTags($filters['tag'], $tag);
          $tagUrl = Route::url($tagBaseUrl . '&tag=' . implode(',', $remainingTags));
        @endphp
        <a class="badge badge-outline gap-1" href="{{ $tagUrl }}">
          {{ e(stripslashes($tag)) }}
          <span aria-label="{{ Lang::txt('JLIB_HTML_REMOVE_TAG') }}">&times;</span>
        </a>
      @endforeach
    </div>
  @endif

  {{-- Tag limit warning --}}
  @if(isset($filters['tag_ignored']) && count($filters['tag_ignored']) > 0)
    @php
      $ignoredBaseUrl = $browseBase;
      $ignoredBaseUrl .= ($filters['search']   ? '&search=' . e($filters['search'])     : '');
      $ignoredBaseUrl .= ($filters['sortby']   ? '&sortby=' . e($filters['sortby'])     : '');
      $ignoredBaseUrl .= ($filters['category'] ? '&category=' . e($filters['category']) : '');
    @endphp
    <div role="alert" class="alert alert-warning mb-4">
      <span>
        {{ Lang::txt('COM_PUBLICATIONS_TAGS_LIMIT_WARNING') }}
        @foreach($filters['tag_ignored'] as $ignoredTag)
          <a class="link" href="{{ Route::url($ignoredBaseUrl . '&tag=' . $ignoredTag) }}">
            {{ e(stripslashes($ignoredTag)) }}
          </a>
        @endforeach
      </span>
    </div>
  @endif

  {{-- Sort tabs + category filter --}}
  <nav class="flex flex-wrap items-center justify-between gap-4 mb-4"
       aria-label="{{ Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS') }}">
    <div role="tablist" class="tabs tabs-border">
      @foreach($sortbys as $sortKey => $sortLabel)
        @php
          $sortUrl = Route::url($browseBase . '&sortby=' . $sortKey . $qs);
          $isActive = ($filters['sortby'] == $sortKey);
        @endphp
        <a role="tab"
           class="tab {{ $isActive ? 'tab-active' : '' }}"
           href="{{ $sortUrl }}"
           aria-selected="{{ $isActive ? 'true' : 'false' }}">
          {{ $sortLabel }}
        </a>
      @endforeach
    </div>

    @if(count($categories) > 0)
      <div>
        <label for="filter-type" class="sr-only">{{ Lang::txt('COM_PUBLICATIONS_CATEGORY') }}</label>
        <select name="category" id="filter-type" class="select select-bordered select-sm">
          <option value=""
                  @selected(!$filters['category'])>
            {{ Lang::txt('COM_PUBLICATIONS_ALL_CATEGORIES') }}
          </option>
          @foreach($categories as $item)
            <option value="{{ $item->id }}"
                    @selected($filters['category'] == $item->id)>
              {{ e(stripslashes($item->name)) }}
            </option>
          @endforeach
        </select>
      </div>
    @endif
  </nav>

  {{-- Results --}}
  @if($results && count($results) > 0)
    <ul class="list bg-base-100 rounded-box shadow-sm" aria-label="{{ Lang::txt('COM_PUBLICATIONS') }}">
      @foreach($results as $line)
        @php
          // Merge item params with component config
          $params = clone($config);
          $rparams = new \Hubzero\Config\Registry($line->params);
          $params->merge($rparams);

          // Determine the display date
          switch ($showDate) {
              case 0:  $thedate = ''; break;
              case 1:  $thedate = $line->created(); break;
              case 2:  $thedate = $line->modified(); break;
              case 3:  $thedate = $line->published(); break;
              default: $thedate = ''; break;
          }

          // Get authors for this version
          $authors = $pa->getAuthors($line->version_id);

          // Access labels
          $accessLabels = [
              0 => Lang::txt('COM_PUBLICATIONS_ACCESS_PUBLIC'),
              1 => Lang::txt('COM_PUBLICATIONS_ACCESS_REGISTERED'),
              2 => Lang::txt('COM_PUBLICATIONS_ACCESS_PROTECTED'),
              3 => Lang::txt('COM_PUBLICATIONS_ACCESS_PRIVATE'),
          ];
          $accessLabel = $accessLabels[$line->get('master_access')]
              ?? Lang::txt('COM_PUBLICATIONS_ACCESS_PUBLIC');

          // Plugin extras
          $extras = Event::trigger('publications.onPublicationsList', [$line]);

          // Build info details
          $info = [];
          if ($thedate) {
              $info[] = $thedate;
          }
          if ($line->category && !intval($filters['category'])) {
              $info[] = e($line->cat_name);
          }
          if ($authors && $params->get('show_authors')) {
              $info[] = Lang::txt('COM_PUBLICATIONS_CONTRIBUTORS') . ': '
                  . \Components\Publications\Helpers\Html::showContributors($authors, false, true);
          }
          if ($line->doi) {
              $info[] = 'doi:' . e($line->doi);
          }

          // Description snippet
          $content = '';
          if ($line->get('abstract')) {
              $content = $line->get('abstract');
          } elseif ($line->get('description')) {
              $content = $line->get('description');
          }
          $snippet = \Hubzero\Utility\Str::truncate(stripslashes($content), 300);
        @endphp

        <li class="list-row">
          {{-- Thumbnail --}}
          @if($line->hasImage())
            <div class="shrink-0">
              <img class="size-12 rounded"
                   src="{{ Route::url($line->link('thumb')) }}"
                   alt="{{ e($line->title) }}" />
            </div>
          @endif

          <div class="list-col-grow">
            {{-- Title --}}
            <h3 class="text-base font-semibold">
              <a class="link link-hover text-primary"
                 href="{{ Route::url($line->link()) }}">
                {{ e(stripslashes($line->title)) }}
              </a>
            </h3>

            {{-- Plugin extras --}}
            @if(!empty($extras))
              {!! implode("\n", $extras) !!}
            @endif

            {{-- Ranking --}}
            @if($params->get('show_ranking') && $config->get('show_ranking'))
              @php
                $ranking = round(floatval($line->get('master_ranking', 1)));
                $r = 10 * $ranking;
              @endphp
              <div class="flex items-center gap-2 mt-1">
                <div class="w-24 bg-base-200 rounded-full h-2"
                     role="img"
                     aria-label="{{ Lang::txt('COM_PUBLICATIONS_RANKING') }}: {{ number_format($ranking, 1) }}">
                  <div class="bg-primary h-2 rounded-full"
                       style="width: {{ $r }}%"></div>
                </div>
                <span class="text-xs text-base-content/60">
                  {{ number_format($ranking, 1) }} {{ Lang::txt('COM_PUBLICATIONS_RANKING') }}
                </span>
              </div>
            @elseif($params->get('show_rating') && $config->get('show_rating'))
              @php
                $rating = $line->get('master_rating');
              @endphp
              <div class="mt-1"
                   role="img"
                   aria-label="{{ Lang::txt('COM_PUBLICATIONS_OUT_OF_5_STARS', $rating) }}">
                <span class="text-xs text-base-content/60">
                  {{ Lang::txt('COM_PUBLICATIONS_OUT_OF_5_STARS', $rating) }}
                </span>
              </div>
            @endif

            {{-- Details: date, category, contributors, DOI --}}
            @if(!empty($info))
              <div class="flex flex-wrap items-baseline gap-x-3 text-sm text-base-content/70">
                {!! implode(' <span class="opacity-40">|</span> ', $info) !!}
              </div>
            @endif

            {{-- Description --}}
            @if($snippet)
              <p class="text-sm text-base-content/70 mt-1 line-clamp-3">
                {{ $snippet }}
              </p>
            @endif
          </div>

          {{-- Access badge --}}
          @if($line->get('master_access') > 0)
            <div>
              <span class="badge badge-ghost badge-sm">
                {{ $accessLabel }}
              </span>
            </div>
          @endif
        </li>
      @endforeach
    </ul>

    {{-- Pagination --}}
    <nav aria-label="{{ Lang::txt('JGLOBAL_PAGINATION') }}" class="flex justify-center mt-8">
      @php
        $pageNav->setAdditionalUrlParam('tag', $filters['tag']);
        $pageNav->setAdditionalUrlParam('category', $filters['category']);
        $pageNav->setAdditionalUrlParam('sortby', $filters['sortby']);
      @endphp
      {!! $pageNav->render() !!}
    </nav>
  @else
    <x-empty-state
        :title="Lang::txt('COM_PUBLICATIONS_NO_RESULTS')"
        :message="Lang::txt('COM_PUBLICATIONS_NO_RESULTS')" />
  @endif

</x-page-container>
