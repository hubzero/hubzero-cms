{{--
  Pagination — daisyUI layout.

  Uses the join + btn pattern for a compact, accessible pagination bar.

  Variables (set by Paginator::render()):
    $limit    — items per page
    $start    — current offset
    $total    — total item count
    $pages    — data object (->start, ->previous, ->next, ->end, ->pages[],
                ->prefix, ->i, ->ellipsis, ->total, ->startloop, ->stoploop, ->current)
    $viewall  — bool
    $limits   — array of limit options
    $prefix   — form field prefix

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $fromResult = $start + 1;
  $toResult   = ($start + $limit < $total) ? $start + $limit : $total;
@endphp

<nav class="pagination flex flex-col sm:flex-row items-center justify-between gap-4 my-6"
     aria-label="{{ Lang::txt('JGLOBAL_PAGINATION_LABEL') }}">

  {{-- Results counter + per-page selector --}}
  <div class="flex items-center gap-3 text-sm text-base-content/70">
    @if($total > 0)
      <span>{{ Lang::txt('JLIB_HTML_RESULTS_OF', $fromResult, $toResult, $total) }}</span>
    @else
      <span>{{ Lang::txt('JLIB_HTML_NO_RECORDS_FOUND') }}</span>
    @endif

    @if(!empty($limits))
      <label for="{{ $prefix }}limit" class="sr-only">{{ Lang::txt('JGLOBAL_DISPLAY_NUM') }}</label>
      <select id="{{ $prefix }}limit"
              name="{{ $pages->prefix }}limit"
              class="select select-sm select-bordered w-auto"
              onchange="this.form.submit()">
        @foreach($limits as $val)
          <option value="{{ $val }}" @selected($val == $limit && !$viewall)>{{ $val }}</option>
        @endforeach
      </select>
    @endif
  </div>

  {{-- Page buttons --}}
  @if($pages->total > 1)
    <div class="join" role="group" aria-label="Page navigation">
      {{-- Start --}}
      @if($pages->start->base !== null)
        <a href="{{ $pages->start->link }}" class="join-item btn btn-sm" title="{{ $pages->start->text }}">
          &laquo;
        </a>
      @else
        <span class="join-item btn btn-sm btn-disabled" aria-disabled="true">&laquo;</span>
      @endif

      {{-- Previous --}}
      @if($pages->previous->base !== null)
        <a href="{{ $pages->previous->link }}" class="join-item btn btn-sm" rel="prev" title="{{ $pages->previous->text }}">
          &lsaquo;
        </a>
      @else
        <span class="join-item btn btn-sm btn-disabled" aria-disabled="true">&lsaquo;</span>
      @endif

      {{-- Leading ellipsis --}}
      @if($pages->ellipsis && $pages->i > 1)
        <span class="join-item btn btn-sm btn-disabled">&hellip;</span>
      @endif

      {{-- Page numbers --}}
      @for($i = $pages->i; $i <= $pages->stoploop && $i <= $pages->total; $i++)
        @if(isset($pages->pages[$i]))
          @php $page = $pages->pages[$i]; @endphp
          @if($page->base !== null)
            <a href="{{ $page->link }}"
               class="join-item btn btn-sm"
               @if($page->rel) rel="{{ $page->rel }}" @endif>
              {{ $page->text }}
            </a>
          @else
            <span class="join-item btn btn-sm btn-active" aria-current="page">{{ $page->text }}</span>
          @endif
        @endif
      @endfor

      {{-- Trailing ellipsis --}}
      @if($pages->ellipsis && ($pages->i - 1) < $pages->total)
        <span class="join-item btn btn-sm btn-disabled">&hellip;</span>
      @endif

      {{-- Next --}}
      @if($pages->next->base !== null)
        <a href="{{ $pages->next->link }}" class="join-item btn btn-sm" rel="next" title="{{ $pages->next->text }}">
          &rsaquo;
        </a>
      @else
        <span class="join-item btn btn-sm btn-disabled" aria-disabled="true">&rsaquo;</span>
      @endif

      {{-- End --}}
      @if($pages->end->base !== null)
        <a href="{{ $pages->end->link }}" class="join-item btn btn-sm" title="{{ $pages->end->text }}">
          &raquo;
        </a>
      @else
        <span class="join-item btn btn-sm btn-disabled" aria-disabled="true">&raquo;</span>
      @endif
    </div>
  @endif

  <input type="hidden" name="{{ $prefix }}limitstart" value="{{ $start }}" />
</nav>
