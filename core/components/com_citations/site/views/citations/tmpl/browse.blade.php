{{--
  Citations — browse / search listing.

  Variables from controller (browseTask):
    $title          — Page title string
    $citations      — Paginated citation results
    $filters        — Array of active filter values
    $filter         — Array of filter labels (all, aff, nonaff)
    $sorts          — Array of sort options (key => label)
    $types          — Collection of citation type objects
    $defaultFormat  — Default citation Format model
    $config         — Component configuration (Registry)
    $openurl        — OpenURL resolver data (disabled)
    $allow_import   — 0/1/2
    $allow_bulk_import — 0/1/2
    $isAdmin        — Boolean admin flag

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
          Lang::txt('COM_CITATIONS'),
          'index.php?option=' . $option
      );
  }
  Pathway::append(
      Lang::txt('COM_CITATIONS_BROWSE'),
      'index.php?option=' . $option . '&task=browse'
  );

  Document::setTitle(
      Lang::txt('COM_CITATIONS') . ': ' . Lang::txt('COM_CITATIONS_BROWSE')
  );

  $browseBase = 'index.php?option=' . $option . '&task=browse';

  // Build query string preserving current filters (minus 'filter')
  $qs = '';
  foreach ($filters as $k => $v) {
      if ($k === 'filter' || $v === '' || $v === null) {
          continue;
      }
      if (is_array($v)) {
          foreach ($v as $k2 => $v2) {
              $qs .= '&' . $k . '[' . $k2 . ']=' . $v2;
          }
      } else {
          $qs .= '&' . $k . '=' . $v;
      }
  }
@endphp

<x-page-container :title="Lang::txt('COM_CITATIONS')">
  @slot('actions')
    <a class="btn"
       href="{{ Route::url('index.php?option=' . $option, false) }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round"
              d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
      </svg>
      {{ Lang::txt('COM_CITATIONS') }}
    </a>
    @if($allow_import == 1 || ($allow_import == 2 && $isAdmin))
      <a class="btn"
         href="{{ Route::url('index.php?option=' . $option . '&task=add', false) }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        {{ Lang::txt('COM_CITATIONS_SUBMIT_CITATION') }}
      </a>
    @endif
  @endslot

  @slot('sidebar')
    {{-- Filters --}}
    <x-sidebar-card :title="Lang::txt('COM_CITATIONS_TYPE')">
      <ul class="menu menu-sm">
        <li>
          <a class="{{ empty($filters['type']) ? 'active' : '' }}"
             href="{{ Route::url($browseBase . '&type=', false) }}">
            {{ Lang::txt('COM_CITATIONS_ALL') }}
          </a>
        </li>
        @foreach($types as $t)
          <li>
            <a class="{{ $filters['type'] == $t['id'] ? 'active' : '' }}"
               href="{{ Route::url($browseBase . '&type=' . $t['id'], false) }}">
              {{ $t['type_title'] }}
            </a>
          </li>
        @endforeach
      </ul>
    </x-sidebar-card>

    {{-- Year range --}}
    <x-sidebar-card :title="Lang::txt('COM_CITATIONS_YEAR')">
      <form method="get"
            action="{{ Route::url($browseBase, false) }}"
            class="space-y-2">
        @foreach($filters as $fk => $fv)
          @if(!in_array($fk, ['year_start', 'year_end', 'limitstart']))
            @if(is_array($fv))
              @foreach($fv as $fk2 => $fv2)
                <input type="hidden"
                       name="{{ $fk }}[{{ $fk2 }}]"
                       value="{{ $fv2 }}" />
              @endforeach
            @else
              <input type="hidden" name="{{ $fk }}" value="{{ $fv }}" />
            @endif
          @endif
        @endforeach
        <div class="flex items-center gap-2">
          <label for="year_start" class="sr-only">
            {{ Lang::txt('COM_CITATIONS_YEAR') }} (from)
          </label>
          <input type="text"
                 id="year_start"
                 name="year_start"
                 class="input input-bordered input-sm w-20"
                 value="{{ $filters['year_start'] }}"
                 placeholder="From" />
          <span class="text-base-content/50">&ndash;</span>
          <label for="year_end" class="sr-only">
            {{ Lang::txt('COM_CITATIONS_YEAR') }} (to)
          </label>
          <input type="text"
                 id="year_end"
                 name="year_end"
                 class="input input-bordered input-sm w-20"
                 value="{{ $filters['year_end'] }}"
                 placeholder="To" />
          <button type="submit" class="btn btn-sm btn-ghost">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="w-4 h-4" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round"
                    d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
          </button>
        </div>
      </form>
    </x-sidebar-card>

    {{-- Sort --}}
    <x-sidebar-card :title="Lang::txt('COM_CITATIONS_SORT_BY')">
      <ul class="menu menu-sm">
        @foreach($sorts as $sk => $sv)
          @if($isAdmin || $sv !== 'Date uploaded')
            <li>
              <a class="{{ $filters['sort'] == $sk ? 'active' : '' }}"
                 href="{{ Route::url($browseBase . $qs . '&sort=' . urlencode($sk) . '&filter=' . $filters['filter'], false) }}">
                {{ $sv }}
              </a>
            </li>
          @endif
        @endforeach
      </ul>
    </x-sidebar-card>
  @endslot

  <x-search-bar
      :action="Route::url($browseBase, false)"
      :query="$filters['search']"
      :placeholder="Lang::txt('COM_CITATIONS_SEARCH_CITATIONS_PLACEHOLDER')"
      :buttonLabel="Lang::txt('COM_CITATIONS_SEARCH')"
      :clearUrl="Route::url($browseBase, false)"
      :clearLabel="Lang::txt('COM_CITATIONS_CLEAR')"
  >
    <input type="hidden" name="task" value="browse" />
  </x-search-bar>

  {{-- Affiliation filter tabs --}}
  <div class="flex flex-wrap items-center gap-3 mb-4">
    <div role="tablist" class="tabs tabs-border">
      <a role="tab"
         class="tab {{ $filters['filter'] == '' ? 'tab-active' : '' }}"
         href="{{ Route::url($browseBase . $qs . '&filter=', false) }}">
        {{ Lang::txt('COM_CITATIONS_ALL') }}
      </a>
      <a role="tab"
         class="tab {{ $filters['filter'] == 'aff' ? 'tab-active' : '' }}"
         href="{{ Route::url($browseBase . $qs . '&filter=aff', false) }}">
        {{ Lang::txt('COM_CITATIONS_AFFILIATED') }}
      </a>
      <a role="tab"
         class="tab {{ $filters['filter'] == 'nonaff' ? 'tab-active' : '' }}"
         href="{{ Route::url($browseBase . $qs . '&filter=nonaff', false) }}">
        {{ Lang::txt('COM_CITATIONS_NONAFFILIATED') }}
      </a>
    </div>

    <span class="text-sm text-base-content/50 ml-auto">
      {{ $citations->count() }} {{ Lang::txt('COM_CITATIONS') }}
    </span>
  </div>

  {{-- Results --}}
  @if($citations->count() > 0)
    <ul class="list bg-base-100 rounded-box shadow-sm"
        aria-label="{{ Lang::txt('COM_CITATIONS') }}">
      @php $counter = intval($filters['limitstart'] ?? 0) + 1; @endphp
      @foreach($citations as $cite)
        @php
          // Find type name
          $typeName = '';
          foreach ($types as $t) {
              if ($t['id'] == $cite->type) {
                  $typeName = $t['type_title'];
                  break;
              }
          }
          $typeName = $typeName ?: 'Generic';

          $viewUrl = Route::url(
              'index.php?option=' . $option . '&task=view&id=' . $cite->id, false
          );
        @endphp
        <li class="list-row">
          <div class="list-col-grow">
            <a class="link link-hover text-primary font-semibold"
               href="{{ $viewUrl }}">
              {{ $cite->title }}
            </a>
            <div class="text-sm text-base-content/60 mt-1">
              @if($cite->author)
                <span>{{ Illuminate\Support\Str::limit($cite->author, 100) }}</span>
              @endif
              @if($cite->year)
                <span class="ml-1">({{ $cite->year }})</span>
              @endif
              @if($cite->journal)
                <span class="ml-1 italic">{{ $cite->journal }}</span>
              @endif
              @if($cite->booktitle && !$cite->journal)
                <span class="ml-1 italic">{{ $cite->booktitle }}</span>
              @endif
            </div>
            @if($cite->doi)
              <div class="text-xs text-base-content/40 mt-0.5">
                DOI:
                <a class="link link-hover"
                   href="https://doi.org/{{ $cite->doi }}"
                   rel="external">{{ $cite->doi }}</a>
              </div>
            @endif
          </div>
          <div class="flex flex-col items-end gap-1">
            <span class="badge badge-sm badge-ghost">{{ $typeName }}</span>
            @if($cite->affiliated)
              <span class="badge badge-sm badge-primary badge-outline">
                {{ Lang::txt('COM_CITATIONS_AFFILIATED') }}
              </span>
            @endif
          </div>
        </li>
        @php $counter++; @endphp
      @endforeach
    </ul>

    {{-- Pagination --}}
    <nav aria-label="Page navigation" class="flex justify-center mt-8">
      @php
        $pageNav = $citations->pagination;
        $pageNav->setAdditionalUrlParam('task', 'browse');
        foreach ($filters as $key => $value) {
            if (in_array($key, ['limit', 'idlist', 'start'])) {
                continue;
            }
            if (is_array($value)) {
                foreach ($value as $k => $v) {
                    $pageNav->setAdditionalUrlParam($key . '[' . $k . ']', $v);
                }
            } else {
                $pageNav->setAdditionalUrlParam($key, $value);
            }
        }
      @endphp
      {!! $pageNav !!}
    </nav>
  @else
    <x-empty-state
      :title="Lang::txt('COM_CITATIONS_NO_CITATIONS_FOUND')"
      :message="$filters['search'] ? '' : ''"
    >
      @if(!empty($filters['search']) || !empty($filters['type']) || !empty($filters['filter']))
        <a class="btn btn-ghost"
           href="{{ Route::url($browseBase, false) }}">
          {{ Lang::txt('COM_CITATIONS_CLEAR') }}
        </a>
      @endif
    </x-empty-state>
  @endif

</x-page-container>
