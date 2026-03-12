{{--
  Pagination view for Blade/daisyUI admin shell.

  Loaded automatically by View::loadTemplate() when engine is 'blade'.
  Receives local variables: $start, $limit, $total, $pages, $viewall,
  $limits (raw array), $prefix.

  @package    framework
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Lang;
  use Hubzero\Html\Builder\Select;

  // Build option objects from raw limits array
  $limitOptions = [];
  if ($limits) {
      foreach ($limits as $val) {
          $limitOptions[] = Select::option($val);
      }
  }

  $fromResult = $start + 1;
  $toResult   = ($start + $limit < $total) ? $start + $limit : $total;

  $selected = $viewall ? 0 : $limit;
  $selectAttr = App::isAdmin()
      ? 'class="select select-bordered select-sm w-auto" size="1" aria-label="' . Lang::txt('JGLOBAL_DISPLAY_NUM') . '"'
      : 'class="select select-bordered select-sm w-auto" size="1" aria-label="' . Lang::txt('JGLOBAL_DISPLAY_NUM') . '"'
        . ' data-submit-on-change';

  /**
   * Render an active page link for admin (data-prefix/data-start) or
   * front-end (href).
   */
  $pageLink = function ($item) use ($prefix) {
      if (App::isAdmin()) {
          return '<a class="join-item btn btn-sm"'
              . ' title="' . $item->text . '"'
              . ' data-prefix="' . $prefix . '"'
              . ' data-start="' . ($item->base > 0 ? $item->base : 0) . '"'
              . '>' . $item->text . '</a>';
      }
      return '<a class="join-item btn btn-sm"'
          . ' title="' . $item->text . '"'
          . ' href="' . $item->link . '"'
          . ($item->rel ? ' rel="' . $item->rel . '"' : '')
          . '>' . $item->text . '</a>';
  };
@endphp

<nav class="pagination admin-pagination"
     aria-label="{{ Lang::txt('JGLOBAL_PAGINATION_LABEL') }}">
  <div class="flex flex-wrap items-center gap-4 w-full text-sm">
    {{-- Results counter --}}
    <span class="text-muted-foreground whitespace-nowrap">
      @if($total > 0)
        {{ Lang::txt('JLIB_HTML_RESULTS_OF', $fromResult, $toResult, $total) }}
      @else
        {{ Lang::txt('JLIB_HTML_NO_RECORDS_FOUND') }}
      @endif
    </span>

    {{-- Per-page limit --}}
    <span class="whitespace-nowrap">
      {!! Select::genericlist(
          $limitOptions,
          $pages->prefix . 'limit',
          $selectAttr,
          'value',
          'text',
          $selected
      ) !!}
    </span>

    {{-- Page buttons (only show when more than one page) --}}
    @if($pages->total > 1)
      <div class="join ml-auto">
        @php
        // Start
        if ($pages->start->base !== null) {
            echo $pageLink($pages->start);
        } else {
            echo '<span class="join-item btn btn-sm btn-disabled">'
                . $pages->start->text . '</span>';
        }

        // Previous
        if ($pages->previous->base !== null) {
            echo $pageLink($pages->previous);
        } else {
            echo '<span class="join-item btn btn-sm btn-disabled">'
                . $pages->previous->text . '</span>';
        }

        // Leading ellipsis
        if ($pages->ellipsis && $pages->i > 1) {
            echo '<span class="join-item btn btn-sm btn-disabled">…</span>';
        }

        // Page numbers
        for (
            ;
            $pages->i <= $pages->stoploop
            && $pages->i <= $pages->total;
            $pages->i++
        ) {
            if (isset($pages->pages[$pages->i])) {
                $page = $pages->pages[$pages->i];
                if ($page->base !== null) {
                    echo $pageLink($page);
                } else {
                    echo '<span class="join-item btn btn-sm btn-primary">'
                        . $page->text . '</span>';
                }
            }
        }

        // Trailing ellipsis
        if (
            $pages->ellipsis
            && ($pages->i - 1) < $pages->total
        ) {
            echo '<span class="join-item btn btn-sm btn-disabled">…</span>';
        }

        // Next
        if ($pages->next->base !== null) {
            echo $pageLink($pages->next);
        } else {
            echo '<span class="join-item btn btn-sm btn-disabled">'
                . $pages->next->text . '</span>';
        }

        // End
        if ($pages->end->base !== null) {
            echo $pageLink($pages->end);
        } else {
            echo '<span class="join-item btn btn-sm btn-disabled">'
                . $pages->end->text . '</span>';
        }
        @endphp
      </div>
    @endif
  </div>
  <input type="hidden"
         name="{{ $prefix }}limitstart"
         value="{{ $start }}" />
</nav>
