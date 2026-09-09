{{--
  What's New — results listing by category and time period.

  Variables from controller (displayTask):
    $title      — Page title string
    $cats       — Array of category data [{category, title, total, _sub?}]
    $results    — Array of result arrays (parallel to $cats)
    $totals     — Raw totals from plugin events
    $total      — Sum of all category totals
    $period     — Current time period string
    $periodlist — Select option objects for period dropdown
    $active     — Active category filter string
    $start      — Pagination start offset
    $limit      — Results per page

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  Document::setTitle($title);

  // Prepend "All Categories" entry
  $allCat = [
      'category' => '',
      'title'    => Lang::txt('COM_WHATSNEW_ALL_CATEGORIES'),
      'total'    => $total,
  ];
  array_unshift($cats, $allCat);

  // Build category links for sidebar
  $catLinks = [];
  foreach ($cats as $cat) {
      if ($cat['total'] > 0) {
          $blob = $cat['category']
              ? $cat['category'] . ':' . $period
              : $period;

          $isActive = ($cat['category'] == $active);
          if ($isActive) {
              Pathway::append(
                  $cat['title'],
                  'index.php?option=' . $option . '&period='
                  . urlencode(stripslashes($blob))
              );
          }

          $catUrl = Route::url(
              'index.php?option=' . $option . '&period='
              . urlencode(stripslashes($blob)),
              false
          );

          $entry = [
              'title'    => $cat['title'],
              'total'    => $cat['total'],
              'url'      => $catUrl,
              'active'   => $isActive,
              'children' => [],
          ];

          // Sub-categories
          if (isset($cat['_sub']) && is_array($cat['_sub'])) {
              foreach ($cat['_sub'] as $sub) {
                  if ($sub['total'] > 0) {
                      $subBlob = $sub['category']
                          ? $sub['category'] . ':' . $period
                          : $period;

                      $subActive = ($sub['category'] == $active);
                      if ($subActive) {
                          Pathway::append(
                              $sub['title'],
                              'index.php?option=' . $option . '&period='
                              . urlencode(stripslashes($subBlob))
                          );
                      }

                      $subUrl = Route::url(
                          'index.php?option=' . $option . '&period='
                          . urlencode(stripslashes($subBlob)),
                          false
                      );

                      $entry['children'][] = [
                          'title'  => $sub['title'],
                          'total'  => $sub['total'],
                          'url'    => $subUrl,
                          'active' => $subActive,
                      ];
                  }
              }
          }

          $catLinks[] = $entry;
      }
  }

  $formAction = Route::url('index.php?option=' . $option, false);
@endphp

<x-page-container :title="$title">

  @slot('sidebar')
    {{-- Period filter --}}
    <x-sidebar-card :title="Lang::txt('COM_WHATSNEW_FILTER')">
      <form method="get" action="{{ $formAction }}">
        <div class="form-control w-full">
          <label class="label" for="period-select">
            <span class="label-text">
              {{ Lang::txt('COM_WHATSNEW_TIME_PERIOD') }}
            </span>
          </label>
          {!! Html::select(
              'genericlist',
              $periodlist,
              'period',
              'class="select select-bordered select-sm w-full"
               id="period-select"',
              'value',
              'text',
              $period
          ) !!}
        </div>
        <input type="hidden" name="category"
               value="{{ e($active) }}" />
        <button type="submit"
                class="btn btn-primary btn-sm w-full mt-3">
          {{ Lang::txt('COM_WHATSNEW_GO') }}
        </button>
      </form>
    </x-sidebar-card>

    {{-- Category navigation --}}
    @if(count($catLinks) > 0)
      <x-sidebar-card :title="Lang::txt('COM_WHATSNEW_CATEGORY')">
        <ul class="menu menu-sm">
          @foreach($catLinks as $link)
            <li>
              <a class="{{ $link['active'] ? 'active' : '' }}"
                 href="{{ $link['url'] }}">
                {{ e($link['title']) }}
                <span class="badge badge-sm badge-ghost ml-auto">
                  {{ $link['total'] }}
                </span>
              </a>
              @if(!empty($link['children']))
                <ul>
                  @foreach($link['children'] as $child)
                    <li>
                      <a class="{{ $child['active'] ? 'active' : '' }}"
                         href="{{ $child['url'] }}">
                        {{ e($child['title']) }}
                        <span class="badge badge-sm badge-ghost ml-auto">
                          {{ $child['total'] }}
                        </span>
                      </a>
                    </li>
                  @endforeach
                </ul>
              @endif
            </li>
          @endforeach
        </ul>
      </x-sidebar-card>
    @endif
  @endslot

  @php
    $foundResults = false;
  @endphp

  @foreach($results as $k => $category)
    @php
      $ci = $k + 1; // offset by "all" at position 0
      $amt = count($category);

      if ($amt <= 0 || !isset($cats[$ci])) {
          continue;
      }

      $foundResults = true;
      $doPaging = false;

      // Determine the active display category
      $name  = '';
      $catTotal = 0;

      if (!$active || $active == $cats[$ci]['category']) {
          $name     = $cats[$ci]['title'];
          $catTotal = $cats[$ci]['total'];

          if ($active == $cats[$ci]['category']) {
              $doPaging = true;
          }
      } elseif (isset($cats[$ci]['_sub']) && is_array($cats[$ci]['_sub'])) {
          foreach ($cats[$ci]['_sub'] as $sub) {
              if ($active == $sub['category']) {
                  $name     = $sub['title'];
                  $catTotal = $sub['total'];
                  $doPaging = true;
                  break;
              }
          }
      }

      if (!$name) {
          continue;
      }

      $resultLabel = ($catTotal > 1)
          ? Lang::txt('COM_WHATSNEW_RESULTS', $catTotal)
          : Lang::txt('COM_WHATSNEW_RESULT', $catTotal);

      // Feed URL
      $act = $active ?: $cats[$ci]['category'];
      $feedPeriod = urlencode(
          strtolower($act) . ':' . stripslashes($period)
      );
      $feedUrl = Route::url(
          'index.php?option=' . $option . '&task=feed.rss&period='
          . $feedPeriod,
          false
      );
      if (substr($feedUrl, 0, 4) != 'http') {
          $feedUrl = rtrim(Request::getSchemeAndHttpHost(), '/')
              . '/' . ltrim($feedUrl, '/');
      }

      // Plugin documents callback
      $catName = ucfirst($cats[$ci]['category']);
      $pluginClass = 'Plugins\\Whatsnew\\' . $catName . '\\' . $catName;
    @endphp

    {{-- Plugin documents() callback --}}
    @if(method_exists($pluginClass, 'documents'))
      {!! call_user_func([$pluginClass, 'documents']) !!}
    @endif

    <div class="card bg-base-100 shadow-sm mb-6">
      <div class="card-body">
        <div class="flex items-center justify-between mb-4">
          <h2 class="card-title text-lg">
            {{ e($name) }}
            <span class="text-sm font-normal text-base-content/50">
              {{ $resultLabel }}
            </span>
          </h2>
          <a class="btn btn-ghost btn-sm gap-1"
             href="{{ $feedUrl }}"
             title="{{ Lang::txt('COM_WHATSNEW_FEED') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                 viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="w-4 h-4"
                 aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="M12.75 19.5v-.75a7.5 7.5 0 0 0-7.5-7.5H4.5m0-6.75h.75c7.87 0 14.25 6.38 14.25 14.25v.75M6 18.75a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z" />
            </svg>
            {{ Lang::txt('COM_WHATSNEW_FEED') }}
          </a>
        </div>

        {{-- Plugin before() callback --}}
        @if(method_exists($pluginClass, 'before'))
          {!! call_user_func([$pluginClass, 'before'], $period) !!}
        @endif

        <ul class="list">
          @foreach($category as $row)
            @php
              $row->href = str_replace('&amp;', '&', $row->href);
              $row->href = str_replace('&', '&amp;', $row->href);

              // Check for plugin custom output
              $hasPluginOut = method_exists($pluginClass, 'out');
              $hasLegacyOut = function_exists(
                  'plgWhatsnew'
                  . ucfirst($row->section ?? '')
                  . 'Out'
              );
            @endphp

            @if($hasLegacyOut)
              {!! call_user_func(
                  'plgWhatsnew' . ucfirst($row->section) . 'Out',
                  $row, $period
              ) !!}
            @elseif($hasPluginOut)
              {!! call_user_func([$pluginClass, 'out'], $row, $period) !!}
            @else
              @php
                $itemUrl = strstr($row->href, 'index.php')
                    ? Route::url($row->href, false)
                    : $row->href;
              @endphp
              <li class="list-row">
                <div class="list-col-grow">
                  <a class="link link-hover text-primary font-semibold"
                     href="{{ $itemUrl }}">
                    {{ stripslashes($row->title) }}
                  </a>
                  @if(!empty($row->text))
                    <p class="text-sm text-base-content/60 mt-1 line-clamp-2">
                      {{ \Hubzero\Utility\Str::truncate(
                          strip_tags(
                              \Hubzero\Utility\Sanitize::stripAll(
                                  stripslashes($row->text)
                              )
                          ),
                          200
                      ) }}
                    </p>
                  @endif
                </div>
              </li>
            @endif
          @endforeach
        </ul>

        {{-- Pagination or "top N shown" --}}
        @if($doPaging)
          @php
            $pageNav = $__view->pagination(
                $catTotal,
                $start,
                $limit
            );
            $pageNav->setAdditionalUrlParam(
                'category', urlencode(strtolower($active))
            );
            $pageNav->setAdditionalUrlParam('period', $period);
          @endphp
          <nav aria-label="Page navigation" class="flex justify-center mt-4">
            {!! $pageNav !!}
          </nav>
        @else
          @php
            $ttl = 0;
            if (isset($totals[$k])) {
                if (is_array($totals[$k])) {
                    foreach ($totals[$k] as $t) {
                        $ttl += $t;
                    }
                } else {
                    $ttl = $totals[$k];
                }
            }

            $morePeriod = urlencode(
                strtolower($cats[$ci]['category']) . ':'
                . stripslashes($period)
            );
            $moreUrl = Route::url(
                'index.php?option=' . $option . '&period='
                . $morePeriod,
                false
            );
          @endphp
          <div class="text-sm text-base-content/50 mt-4 flex items-center gap-2">
            <span>{{ Lang::txt('COM_WHATSNEW_TOP_SHOWN', $amt) }}</span>
            @if($ttl > 5)
              <a class="link link-hover link-primary"
                 href="{{ $moreUrl }}">
                {{ Lang::txt('COM_WHATSNEW_SEE_MORE_RESULTS') }}
              </a>
            @endif
          </div>
        @endif

        {{-- Plugin after() callback --}}
        @if(method_exists($pluginClass, 'after'))
          {!! call_user_func([$pluginClass, 'after'], $period) !!}
        @endif
      </div>
    </div>
  @endforeach

  @if(!$foundResults)
    <x-empty-state
      :title="Lang::txt('COM_WHATSNEW_NO_RESULTS')"
      message=""
    />
  @endif

</x-page-container>
