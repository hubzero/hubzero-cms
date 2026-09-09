{{--
  Resources browse page — search, sort tabs, paginated results list with sidebar.

  Variables from controller (browseTask):
    $title     — page title
    $option    — component option string
    $config    — component params (Registry)
    $filters   — array: search, tag, type, sortby, limit, start
    $results   — paginated result set
    $types     — collection of Type models
    $authorized — bool

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Component;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css();
  $__view->js();

  $browseBase = 'index.php?option=' . $option . '&task=browse';
  $submitUrl  = Route::url('index.php?option=' . $option . '&task=new');
  $actionUrl  = Route::url($browseBase);

  // Build query string for sort links (preserves search, type, tag)
  $qs  = ($filters['search'] ? '&search=' . e($filters['search']) : '');
  $qs .= ($filters['type']   ? '&type=' . e($filters['type'])     : '');
  $qs .= ($filters['tag']    ? '&tag=' . e($filters['tag'])       : '');

  // Sort options
  $sortbys = [];
  if ($config->get('show_ranking')) {
      $sortbys['ranking'] = Lang::txt('COM_RESOURCES_RANKING');
  }
  $sortbys['date']          = Lang::txt('COM_RESOURCES_DATE_PUBLISHED');
  $sortbys['date_modified'] = Lang::txt('COM_RESOURCES_DATE_MODIFIED');
  $sortbys['title']         = Lang::txt('COM_RESOURCES_TITLE');
@endphp

<x-page-container :title="$title">
  @slot('actions')
    <a class="btn btn-primary" href="{{ $submitUrl }}">
      {{ Lang::txt('COM_RESOURCES_SUBMIT_A_RESOURCE') }}
    </a>
  @endslot

  @slot('sidebar')
    <x-sidebar-card :title="Lang::txt('COM_RESOURCES_FIND_RESOURCE')">
      <p class="text-sm text-base-content/70">
        {{ Lang::txt('COM_RESOURCES_FIND_RESOURCE_DETAILS') }}
      </p>
    </x-sidebar-card>

    <x-sidebar-card :title="Lang::txt('COM_RESOURCES_POPULAR_TAGS')">
      @php
        $rt = new \Components\Resources\Helpers\Tags(0);
      @endphp
      {!! $rt->getTopTagCloud(20, $filters['tag']) !!}
      <p class="text-sm text-base-content/70 mt-2">
        {{ Lang::txt('COM_RESOURCES_POPULAR_TAGS_HINT') }}
      </p>
    </x-sidebar-card>
  @endslot

  {{-- Search bar --}}
  <x-search-bar
      :action="$actionUrl"
      :query="$filters['search']"
      :placeholder="Lang::txt('COM_RESOURCES_SEARCH_LABEL')"
      :label="Lang::txt('COM_RESOURCES_SEARCH_LABEL')"
      :buttonLabel="Lang::txt('COM_RESOURCES_SEARCH')"
      :clearUrl="$actionUrl"
      name="search">
    <input type="hidden" name="sortby" value="{{ e($filters['sortby']) }}" />
    <input type="hidden" name="tag" value="{{ e($filters['tag']) }}" />
  </x-search-bar>

  {{-- Applied tags --}}
  @if($filters['tag'])
    @php
      $rt = new \Components\Resources\Helpers\Tags(0);
      $tags = $rt->parseTopTags($filters['tag']);
      $tagBaseUrl = $browseBase;
      $tagBaseUrl .= ($filters['search'] ? '&search=' . e($filters['search']) : '');
      $tagBaseUrl .= ($filters['sortby'] ? '&sortby=' . e($filters['sortby']) : '');
      $tagBaseUrl .= ($filters['type'] ? '&type=' . e($filters['type']) : '');
    @endphp
    <div class="flex flex-wrap gap-2 mb-4">
      @foreach($tags as $tag)
        @php
          $remainingTags = $rt->parseTopTags($filters['tag'], $tag);
          $tagUrl = Route::url($tagBaseUrl . '&tag=' . implode(',', $remainingTags));
        @endphp
        <a class="badge badge-outline gap-1" href="{{ $tagUrl }}">
          {{ e(stripslashes($tag)) }}
          <span aria-label="{{ Lang::txt('COM_RESOURCES_REMOVE_TAG') }}">&times;</span>
        </a>
      @endforeach
    </div>
  @endif

  {{-- Tag limit warning --}}
  @if(isset($filters['tag_ignored']) && count($filters['tag_ignored']) > 0)
    @php
      $ignoredBaseUrl = $browseBase;
      $ignoredBaseUrl .= ($filters['search'] ? '&search=' . e($filters['search']) : '');
      $ignoredBaseUrl .= ($filters['sortby'] ? '&sortby=' . e($filters['sortby']) : '');
      $ignoredBaseUrl .= ($filters['type'] ? '&type=' . e($filters['type']) : '');
    @endphp
    <div role="alert" class="alert alert-warning mb-4">
      <span>
        {{ Lang::txt('COM_RESOURCES_SEARCH_TAG_LIMIT_REACHED') }}
        @foreach($filters['tag_ignored'] as $ignoredTag)
          <a class="link" href="{{ Route::url($ignoredBaseUrl . '&tag=' . $ignoredTag) }}">
            {{ e(stripslashes($ignoredTag)) }}
          </a>
        @endforeach
      </span>
    </div>
  @endif

  {{-- Sort tabs + type filter --}}
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

    @if(count($types) > 0)
      <div>
        <label for="filter-type" class="sr-only">{{ Lang::txt('COM_RESOURCES_TYPE') }}</label>
        <select name="type" id="filter-type" class="select select-bordered select-sm">
          <option value=""
                  @selected(!$filters['type'])>
            {{ Lang::txt('COM_RESOURCES_ALL_TYPES') }}
          </option>
          @foreach($types as $item)
            @if(!$item->state)
              @continue
            @endif
            @if($item->isForTools() && !Component::isEnabled('com_tools', true))
              @continue
            @endif
            <option value="{{ $item->id }}"
                    @selected($filters['type'] == $item->id)>
              {{ e(stripslashes($item->type)) }}
            </option>
          @endforeach
        </select>
      </div>
    @endif
  </nav>

  {{-- Results --}}
  @if($results->count())
    @php
      $supported = [];
      if ($supportedTag = $config->get('supportedtag')) {
          $rt = new \Components\Resources\Helpers\Tags(0);
          $supported = $rt->getTagUsage($supportedTag, 'id');
      }
    @endphp

    <ul class="list bg-base-100 rounded-box shadow-sm" aria-label="{{ Lang::txt('COM_RESOURCES') }}">
      @foreach($results as $line)
        @php
          $params = $line->params;
          $extras = \Hubzero\Facades\Event::trigger('resources.onResourcesList', [$line]);
          $isSupported = ($params->get('supportedtag') && in_array($line->id, $supported));
        @endphp
        <li class="list-row">
          <div class="list-col-grow">
            {{-- Title --}}
            <h3 class="text-base font-semibold">
              <a class="link link-hover text-primary" href="{{ Route::url($line->link()) }}">
                {{ e(stripslashes($line->title)) }}
              </a>
              @if($isSupported)
                <span class="badge badge-success badge-sm ml-1">
                  {{ Lang::txt('COM_RESOURCES_SUPPORTED') }}
                </span>
              @endif
            </h3>

            {{-- Plugin extras --}}
            @if(!empty($extras))
              {!! implode("\n", $extras) !!}
            @endif

            {{-- Details: type, date, authors --}}
            <div class="flex flex-wrap items-baseline gap-x-3 text-sm text-base-content/70">
              @if($params->get('show_type'))
                <strong>{{ stripslashes($line->type->get('type')) }}</strong>
              @endif
              @if($thedate = $line->date)
                <span>{{ $thedate }}</span>
              @endif
              @if($line->authors->count() && $params->get('show_authors'))
                @php $authors = $line->authorsList(); @endphp
                @if(trim($authors))
                  <span>{!! Lang::txt('COM_RESOURCES_CONTRIBUTORS') . ': ' . $authors !!}</span>
                @endif
              @endif
            </div>

            {{-- Ranking --}}
            @if($params->get('show_ranking'))
              @php
                $ranking = round($line->get('ranking'), 1);
                $r = 10 * $ranking;
              @endphp
              <div class="flex items-center gap-2 mt-1">
                <div class="w-24 bg-base-200 rounded-full h-2"
                     role="img"
                     aria-label="{{ Lang::txt('COM_RESOURCES_RANKING') }}: {{ number_format($ranking, 1) }}">
                  <div class="bg-primary h-2 rounded-full"
                       style="width: {{ $r }}%"></div>
                </div>
                <span class="text-xs text-base-content/60">
                  {{ number_format($ranking, 1) }} {{ Lang::txt('COM_RESOURCES_RANKING') }}
                </span>
              </div>
            @elseif($params->get('show_rating'))
              <div class="mt-1"
                   role="img"
                   aria-label="{{ Lang::txt('COM_RESOURCES_OUT_OF_5_STARS', $line->get('rating')) }}">
                <span class="text-xs text-base-content/60">
                  {{ Lang::txt('COM_RESOURCES_OUT_OF_5_STARS', $line->get('rating')) }}
                </span>
              </div>
            @endif

            {{-- Description --}}
            @php
              $content = '';
              if ($line->get('introtext')) {
                  $content = $line->get('introtext');
              } elseif ($line->get('fulltxt')) {
                  $content = $line->get('fulltxt');
                  $content = preg_replace('#<nb:(.*?)>(.*?)</nb:(.*?)>#s', '', $content);
                  $content = trim($content);
              }
              $snippet = \Hubzero\Utility\Str::truncate(
                  strip_tags(\Hubzero\Utility\Sanitize::stripAll(stripslashes($content))),
                  300
              );
            @endphp
            <p class="text-sm text-base-content/70 mt-1 line-clamp-3">
              {{ $snippet }}
            </p>

            {{-- Tag cloud --}}
            @php $tc = $line->tags('cloud'); @endphp
            @if($tc)
              <div class="mt-1">
                {!! $tc->render() !!}
              </div>
            @endif
          </div>

          {{-- Access badge --}}
          @php
            $accessLabels = [
                0 => 'COM_RESOURCES_ACCESS_PUBLIC',
                1 => 'COM_RESOURCES_ACCESS_REGISTERED',
                2 => 'COM_RESOURCES_ACCESS_SPECIAL',
                3 => 'COM_RESOURCES_ACCESS_PROTECTED',
                4 => 'COM_RESOURCES_ACCESS_PRIVATE',
            ];
            $accessKey = $accessLabels[$line->access] ?? $accessLabels[0];
          @endphp
          @if($line->access > 0)
            <div>
              <span class="badge badge-ghost badge-sm">
                {{ Lang::txt($accessKey) }}
              </span>
            </div>
          @endif
        </li>
      @endforeach
    </ul>

    {{-- Pagination --}}
    <nav aria-label="{{ Lang::txt('JGLOBAL_PAGINATION') }}" class="flex justify-center mt-8">
      @php
        $pageNav = $results->pagination;
        $pageNav->setAdditionalUrlParam('tag', $filters['tag']);
        $pageNav->setAdditionalUrlParam('type', $filters['type']);
        $pageNav->setAdditionalUrlParam('sortby', $filters['sortby']);
      @endphp
      {!! $pageNav->render() !!}
    </nav>
  @else
    <x-empty-state
        :title="Lang::txt('COM_RESOURCES_NO_RESULTS')"
        :message="Lang::txt('COM_RESOURCES_NO_RESULTS')" />
  @endif

</x-page-container>
