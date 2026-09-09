{{--
  Member Contributions — categorized results with sort.

  Variables from plugin (onMembers):
    $cats    — array of categories (with optional _sub arrays)
    $results — array of results by category
    $active  — current active category
    $sort    — sort option (date/title/usage)
    $total   — total result count
    $member  — member profile object
    $limit   — pagination limit
    $start   — pagination start

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css()->js('resources', 'com_resources');

  // Build "all" category and prepend
  $all = [
      'category' => '',
      'title'    => Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_ALL_CATEGORIES'),
      'total'    => $total,
  ];
  array_unshift($cats, $all);

  // Build category links
  $links = [];
  foreach ($cats as $cat) {
      if ($cat['total'] <= 0) {
          continue;
      }
      $isActive = ($cat['category'] == $active);
      $blob     = $cat['category'] ?: '';
      $catUrl   = Route::url(
          $member->link() . '&active=contributions&area=' . urlencode(stripslashes($blob)) . '&sort=' . $sort
      );
      $catTitle = e(stripslashes($cat['title']));

      $link = '<li' . ($isActive ? ' class="active"' : '') . '>'
          . '<a href="' . $catUrl . '">'
          . $catTitle
          . ' <span class="badge badge-sm badge-ghost">' . e($cat['total']) . '</span></a>';

      // Sub-categories
      if (isset($cat['_sub']) && is_array($cat['_sub'])) {
          $subLinks = [];
          foreach ($cat['_sub'] as $subcat) {
              if ($subcat['total'] > 0) {
                  $subActive = ($subcat['category'] == $active);
                  $subBlob   = $subcat['category'] ?: '';
                  $subUrl    = Route::url(
                      $member->link() . '&active=contributions&area=' . urlencode(stripslashes($subBlob)) . '&sort=' . $sort
                  );
                  $subLinks[] = '<li' . ($subActive ? ' class="active"' : '') . '>'
                      . '<a href="' . $subUrl . '">'
                      . e(stripslashes($subcat['title']))
                      . ' <span class="badge badge-sm badge-ghost">' . e($subcat['total']) . '</span></a></li>';
              }
          }
          if (count($subLinks) > 0) {
              $link .= '<ul class="menu menu-sm">' . implode("\n", $subLinks) . '</ul>';
          }
      }
      $link .= '</li>';
      $links[] = $link;
  }

  // Sort options
  $activeArea = urlencode(stripslashes($active));
  $sortBase   = $member->link() . '&active=contributions&area=' . $activeArea;
  $sortOptions = [
      'date'  => [
          'label' => Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_SORT_DATE'),
          'title' => Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_SORT_BY_DATE'),
      ],
      'title' => [
          'label' => Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_SORT_TITLE'),
          'title' => Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_SORT_BY_TITLE'),
      ],
      'usage' => [
          'label' => Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_SORT_POPULARITY'),
          'title' => Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_SORT_BY_POPULARITY'),
      ],
  ];
@endphp

<h3 class="text-lg font-semibold mb-4">
  {{ Lang::txt('PLG_MEMBERS_CONTRIBUTIONS') }}
</h3>

<form method="get" action="{{ Route::url($member->link() . '&active=contributions') }}">
  <input type="hidden" name="area" value="{{ e($active) }}" />

  {{-- Filters --}}
  <nav class="mb-4 flex flex-wrap gap-4 justify-between"
       aria-label="{{ Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS') }}">

    {{-- Category dropdown --}}
    @if (count($links) > 0)
      <div class="dropdown">
        <div tabindex="0" role="button" class="btn btn-ghost btn-sm">
          {{ Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_CATEGORIES') }}
          <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
               stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
          </svg>
        </div>
        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box shadow-lg z-10 w-64 max-h-80 overflow-y-auto">
          {!! implode("\n", $links) !!}
        </ul>
      </div>
    @endif

    {{-- Sort tabs --}}
    <div role="tablist" class="tabs tabs-border">
      @foreach ($sortOptions as $sortKey => $sortData)
        @php
          $isSortActive = ($sort === $sortKey);
          $sortUrl = Route::url($sortBase . '&sort=' . $sortKey);
        @endphp
        <a role="tab" class="tab {{ $isSortActive ? 'tab-active' : '' }}"
           href="{{ $sortUrl }}"
           title="{{ $sortData['title'] }}"
           @if($isSortActive) aria-selected="true" @endif>
          {{ $sortData['label'] }}
        </a>
      @endforeach
    </div>
  </nav>

  {{-- Results --}}
  <div>
    @php
      $foundresults = false;
      $dopaging = false;
      $html = '';
      $k = 1;

      foreach ($results as $category) {
          $amt = count($category);

          if ($amt > 0 && isset($cats[$k])) {
              $foundresults = true;

              $name  = $cats[$k]['title'];
              $catTotal = $cats[$k]['total'];
              $divid = 'search' . $cats[$k]['category'];

              if (!$active || $active == $cats[$k]['category']) {
                  $name  = $cats[$k]['title'];
                  $catTotal = $cats[$k]['total'];
                  $divid = 'search' . $cats[$k]['category'];

                  if ($active == $cats[$k]['category']) {
                      $dopaging = true;
                  }
              } else {
                  if (isset($cats[$k]['_sub']) && is_array($cats[$k]['_sub'])) {
                      foreach ($cats[$k]['_sub'] as $sub) {
                          if ($active == $sub['category']) {
                              $name  = $sub['title'];
                              $catTotal = $sub['total'];
                              $divid = 'search' . $sub['category'];
                              $dopaging = true;
                              break;
                          }
                      }
                  }
              }
              $name = stripslashes($name);

              // Category-specific document setup
              $f = 'plgMembers' . ucfirst($cats[$k]['category']) . 'Doc';
              if (function_exists($f)) {
                  $f();
              }
              $catName = ucfirst($cats[$k]['category']);
              $obj = 'Plugins\\Members\\' . $catName . '\\' . $catName;
              if (method_exists($obj, 'documents')) {
                  $html .= call_user_func([$obj, 'documents']);
              }

              $ttl = ($catTotal > 5) ? 5 : $catTotal;
              if (!$dopaging) {
                  $num = '1-' . $ttl . ' of ';
              } else {
                  $stl = $start + 1;
                  $ttl = ($catTotal > $limit) ? $start + $limit : $start + $catTotal;
                  $ttl = ($catTotal > $ttl) ? $ttl : $catTotal;
                  $num = $stl . '-' . $ttl . ' of ';
              }

              // Category header
              $html .= '<h4 class="text-base font-semibold mt-6 mb-2" id="rel-' . $divid . '">';
              if (!$dopaging) {
                  $html .= '<a class="link link-hover" href="' . Route::url(
                      $member->link() . '&active=contributions&area='
                      . urlencode(stripslashes($cats[$k]['category']))
                  ) . '">';
              }
              $html .= e($name) . ' <span class="text-base-content/50 font-normal text-sm">(' . $num . $catTotal . ')</span>';
              if (!$dopaging) {
                  $html .= ' <span class="text-sm text-primary">' . Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_MORE') . '</span></a>';
              }
              $html .= '</h4>';
              $html .= '<div id="' . $divid . '">';

              // Before-content hook
              $func = 'plgMembers' . ucfirst($cats[$k]['category']) . 'Before';
              if (function_exists($func)) {
                  $html .= $func();
              }
              $catName = ucfirst($cats[$k]['category']);
              $obj = 'Plugins\\Members\\' . $catName . '\\' . $catName;
              if (method_exists($obj, 'before')) {
                  $html .= call_user_func([$obj, 'before']);
              }

              // Result list
              $html .= '<ul class="space-y-4">';
              foreach ($category as $row) {
                  $row->href = str_replace('&amp;', '&', $row->href ?: '');
                  $row->href = str_replace('&', '&amp;', $row->href);

                  $func = 'plgMembers' . ($row->section ? ucfirst($row->section) : '') . 'Out';
                  $catName = ucfirst($cats[$k]['category']);
                  $obj = 'Plugins\\Members\\' . $catName . '\\' . $catName;

                  if (function_exists($func)) {
                      $html .= $func($row);
                  } elseif (method_exists($obj, 'out')) {
                      $html .= call_user_func([$obj, 'out'], $row);
                  } else {
                      $html .= '<li>';
                      $html .= '<a class="link link-primary" href="' . $row->href . '">'
                          . e(stripslashes($row->title)) . '</a>';
                      if ($row->text) {
                          $html .= '<p class="text-sm text-base-content/60 mt-0.5">'
                              . \Hubzero\Utility\Str::truncate(stripslashes($row->text)) . '</p>';
                      }
                      $html .= '</li>';
                  }
              }
              $html .= '</ul>';

              if (!$dopaging) {
                  $html .= '<p class="text-sm text-base-content/60 mt-2">'
                      . Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_NUMBER_SHOWN', $amt);
                  if ($cats[$k]['total'] > 5) {
                      $html .= ' | <a class="link link-primary" href="'
                          . Route::url($member->link() . '&active=contributions&area='
                          . urlencode(strtolower($cats[$k]['category'])))
                          . '">' . Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_MORE') . '</a>';
                  }
                  $html .= '</p>';
              }
              $html .= '</div>';
          }
          $k++;
      }
    @endphp

    {!! $html !!}

    @if (!$foundresults)
      <div class="alert alert-warning">
        {{ Lang::txt('PLG_MEMBERS_CONTRIBUTIONS_NONE') }}
      </div>
    @endif
  </div>

  {{-- Pagination --}}
  @if ($dopaging)
    @php
      $pageNav = $__view->pagination($total, $start, $limit);
      $pageNav->setAdditionalUrlParam('id', $member->get('id'));
      $pageNav->setAdditionalUrlParam('active', 'contributions');
      $pageNav->setAdditionalUrlParam('area', urlencode(stripslashes($active)));
      $pageNav->setAdditionalUrlParam('sort', $sort);
    @endphp
    {!! $pageNav->render() !!}
  @endif
</form>
