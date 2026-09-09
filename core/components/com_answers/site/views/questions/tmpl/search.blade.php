{{--
  Questions browse/search — list of questions with filters and sidebar help.

  Variables from controller (searchTask):
    $results  — Paginated Question collection
    $filters  — array: limit, start, tag, search, filterby, sortby, sort_Dir, area
    $config   — Component params (Registry)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Config;
  use Hubzero\Facades\Document;
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Pathway;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  if (Pathway::count() <= 0) {
      Pathway::append(
          Lang::txt(strtoupper($option)),
          'index.php?option=' . $option
      );
  }

  Document::setTitle(Lang::txt('COM_ANSWERS'));

  if (!$filters['filterby'] || $filters['filterby'] == 'none') {
      $filters['filterby'] = 'all';
  }

  $filterby = urlencode($filters['filterby']);
  $sortby   = urlencode($filters['sortby']);
  $area     = urlencode($filters['area']);
  $sortdir  = ($filters['sort_Dir'] == 'DESC') ? 'ASC' : 'DESC';

  $baseUrl = 'index.php?option=' . $option . '&task=search';
@endphp

<x-page-container :title="Lang::txt('COM_ANSWERS')">
  @if(User::authorise('core.create', $option))
    @slot('actions')
      <a class="btn btn-primary"
         href="{{ Route::url('index.php?option=' . $option . '&task=new', false) }}">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
        </svg>
        {{ Lang::txt('COM_ANSWERS_NEW_QUESTION') }}
      </a>
    @endslot
  @endif

  @slot('sidebar')
    {{-- Knowledge Base --}}
    <x-sidebar-card :title="Lang::txt('COM_ANSWERS_NEED_AN_ANSWER')">
        <p class="text-sm text-base-content/60">
          {!! Lang::txt(
              'COM_ANSWERS_CANT_FIND_ANSWER',
              '<a class="link" href="' . Route::url('index.php?option=com_kb', false) . '">'
                  . Lang::txt('COM_ANSWERS_KNOWLEDGE_BASE') . '</a>',
              Config::get('sitename')
          ) !!}
        </p>
    </x-sidebar-card>

    {{-- Get started --}}
    <x-sidebar-card :title="Lang::txt('COM_ANSWERS_GET_STARTED')">
        <p class="text-sm text-base-content/60">
          {!! Lang::txt(
              'COM_ANSWERS_GET_STARTED_HELP',
              Route::url('index.php?option=com_help&component=answers&page=index', false)
          ) !!}
        </p>
    </x-sidebar-card>

    {{-- Earn points --}}
    @if($config->get('banking'))
      <x-sidebar-card :title="Lang::txt('COM_ANSWERS_EARN_POINTS')">
          <p class="text-sm text-base-content/60">
            {{ Lang::txt('COM_ANSWERS_START_EARNING_POINTS') }}
            <a class="link" href="{{ $config->get('infolink') }}">
              {{ Lang::txt('COM_ANSWERS_LEARN_MORE') }}
            </a>.
          </p>
      </x-sidebar-card>
    @endif
  @endslot

  {{-- Search --}}
  <x-search-bar
    :action="Route::url('index.php?option=' . $option, false)"
    name="q"
    :query="$filters['search']"
    :placeholder="Lang::txt('COM_ANSWERS_SEARCH_PLACEHOLDER')"
  >
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="task" value="search" />
    <input type="hidden" name="area" value="{{ $filters['area'] }}" />
    <input type="hidden" name="sortby" value="{{ $filters['sortby'] }}" />
    <input type="hidden" name="sortdir" value="{{ $filters['sort_Dir'] }}" />
    <input type="hidden" name="filterby" value="{{ $filters['filterby'] }}" />
  </x-search-bar>

  {{-- Filters --}}
  @php
    // Build filter URLs as JS data for select-driven navigation
    // Strip arrow entity prefixes from sort labels — the select has its own indicator
    $cleanLabel = function ($key) {
        return trim(preg_replace('/^&[a-z]+;\s*/i', '', Lang::txt($key)));
    };
    $sortOptions = [];
    if ($config->get('banking')) {
        $sortOptions['rewards'] = $cleanLabel('COM_ANSWERS_SORT_REWARDS');
    }
    $sortOptions['votes'] = $cleanLabel('COM_ANSWERS_SORT_POPULAR');
    $sortOptions['date']  = $cleanLabel('COM_ANSWERS_SORT_RECENT');

    $statusOptions = [
        'all'    => $cleanLabel('COM_ANSWERS_FILTER_ALL'),
        'open'   => $cleanLabel('COM_ANSWERS_FILTER_OPEN'),
        'closed' => $cleanLabel('COM_ANSWERS_FILTER_CLOSED'),
    ];
  @endphp
  <div class="flex flex-wrap items-center gap-3 mb-4">
    {{-- Area filter (logged-in only) --}}
    @if(!User::isGuest())
      @php
        $areaOptions = [
            ''         => Lang::txt('COM_ANSWERS_FILTER_EVERYTHING'),
            'mine'     => Lang::txt('COM_ANSWERS_QUESTIONS_I_ASKED'),
            'assigned' => Lang::txt('COM_ANSWERS_QUESTIONS_RELATED_TO_CONTRIBUTIONS'),
        ];
        foreach (Event::trigger('answers.onQuestionsFilters') as $opt) {
            if (!empty($opt)) {
                $areaOptions[$opt['value']] = $opt['label'];
            }
        }
      @endphp
      <div role="tablist" class="tabs tabs-border">
        @foreach($areaOptions as $val => $label)
          <a role="tab"
             class="tab {{ $filters['area'] == $val ? 'tab-active' : '' }}"
             href="{{ Route::url($baseUrl . '&area=' . $val . '&filterby=' . $filterby . '&sortby=' . $sortby, false) }}">
            {{ $label }}
          </a>
        @endforeach
      </div>
    @endif

    {{-- Sort + status selects (form submits on change, works without JS) --}}
    <form method="get"
          action="{{ Route::url('index.php?option=' . $option, false) }}"
          class="flex items-center gap-2 ml-auto"
          data-submit-on-change>
      <input type="hidden" name="option" value="{{ $option }}" />
      <input type="hidden" name="task" value="search" />
      <input type="hidden" name="area" value="{{ $filters['area'] }}" />
      <input type="hidden" name="q" value="{{ $filters['search'] }}" />

      <label for="sort-select" class="text-sm text-base-content/60">{{ Lang::txt('COM_ANSWERS_SORT') }}:</label>
      <select id="sort-select" name="sortby" class="select select-bordered select-sm">
        @foreach($sortOptions as $val => $label)
          <option value="{{ $val }}" {{ $filters['sortby'] == $val ? 'selected' : '' }}>
            {{ $label }}
          </option>
        @endforeach
      </select>

      <label for="filter-select" class="text-sm text-base-content/60">{{ Lang::txt('COM_ANSWERS_FILTER') }}:</label>
      <select id="filter-select" name="filterby" class="select select-bordered select-sm">
        @foreach($statusOptions as $val => $label)
          <option value="{{ $val }}" {{ $filters['filterby'] == $val || ($val == 'all' && !$filters['filterby']) ? 'selected' : '' }}>
            {{ $label }}
          </option>
        @endforeach
      </select>

      <noscript><button class="btn btn-sm" type="submit">Go</button></noscript>
    </form>
  </div>

  {{-- Results heading --}}
  @php
    $total = $results->count();
    $resultsTitle = $filters['search']
        ? Lang::txt('COM_ANSWERS_SEARCH_FOR', e($filters['search']), Lang::txt('COM_ANSWERS_FILTER_' . strtoupper($filters['filterby'])))
        : Lang::txt('COM_ANSWERS_FILTER_' . strtoupper($filters['filterby']));
  @endphp
  <x-results-heading :title="$resultsTitle" :count="$total" />

  @if($total > 0)
    <ul class="list bg-base-100 rounded-box shadow-sm" aria-label="{{ Lang::txt('COM_ANSWERS') }}">
      @foreach($results as $row)
        @php
          $isClosed   = $row->isClosed();
          $isReported = $row->isReported();
          $isMine     = $row->get('created_by') == User::get('id');

          // Author name
          $authorName = Lang::txt('JANONYMOUS');
          $authorUrl  = '';
          if (!$row->get('anonymous')) {
              $authorName = $row->creator->get('name', $authorName);
              $viewLevels = User::getAuthorisedViewLevels();
              if (
                  in_array($row->creator->get('access'), $viewLevels)
                  && !$row->creator->get('block')
                  && $row->creator->get('approved')
              ) {
                  $authorUrl = Route::url($row->creator->link(), false);
              }
          }

          // Vote data
          $ballot  = $row->ballot();
          $voteVal = $ballot->get('vote', null);
          $voteStr = '';
          if ($voteVal == 1)  { $voteStr = 'like'; }
          if ($voteVal == -1) { $voteStr = 'dislike'; }

          $canVote = !User::isGuest() && User::get('id') != $row->get('created_by') && !$voteVal;
          $likeUrl = $canVote
              ? Route::url('index.php?option=' . $option . '&task=vote&category=question&id=' . $row->get('id') . '&vote=yes', false)
              : '';
          $dislikeUrl = $canVote
              ? Route::url('index.php?option=' . $option . '&task=vote&category=question&id=' . $row->get('id') . '&vote=no', false)
              : '';
        @endphp
        <li class="list-row" id="q{{ $row->get('id') }}">
          <div class="list-col-grow">
            @if($isReported)
              <span class="font-semibold text-base-content/40 italic">
                {{ Lang::txt('COM_ANSWERS_QUESTION_UNDER_REVIEW') }}
              </span>
            @else
              <h3 class="text-base font-semibold">
                <a class="link link-hover text-primary"
                   href="{{ Route::url($row->link(), false) }}">
                  {{ strip_tags($row->get('subject')) }}
                </a>
              </h3>
            @endif
            <div class="flex items-baseline gap-x-3 text-sm text-base-content/60">
              <span>
                @if($authorUrl)
                  {!! Lang::txt(
                      'COM_ANSWERS_ASKED_BY',
                      '<a class="link link-hover" href="' . $authorUrl . '">' . e($authorName) . '</a>'
                  ) !!}
                @else
                  {{ Lang::txt('COM_ANSWERS_ASKED_BY', $authorName) }}
                @endif
              </span>
              <time datetime="{{ $row->created() }}">
                {{ $row->created('date') }}
              </time>
              <span class="badge badge-sm {{ $isClosed ? 'badge-ghost' : 'badge-success' }}">
                {{ $isClosed ? Lang::txt('COM_ANSWERS_STATE_CLOSED') : Lang::txt('COM_ANSWERS_STATE_OPEN') }}
              </span>
              <a class="link link-hover" href="{{ Route::url($row->link() . '#answers', false) }}">
                {{ $row->responses->count() }}
                {{ Lang::txt('COM_ANSWERS_RESPONSES') }}
              </a>
            </div>
          </div>
          @if($config->get('banking') && $row->get('reward'))
            <div class="text-sm font-medium text-warning">
              {{ $row->get('points') }} {{ Lang::txt('COM_ANSWERS_POINTS') }}
            </div>
          @endif
          <div>
            <x-vote-widget
              :likes="$row->get('helpful', 0)"
              :dislikes="$row->get('nothelpful', 0)"
              :vote="$voteStr"
              :likeUrl="$likeUrl"
              :dislikeUrl="$dislikeUrl"
              :disabled="User::isGuest() || User::get('id') == $row->get('created_by')"
            />
          </div>
        </li>
      @endforeach
    </ul>

    {{-- Pagination --}}
    <nav aria-label="Page navigation" class="flex justify-center mt-8">
      @php
        $pageNav = $results->pagination;
        $pageNav->setAdditionalUrlParam('q', $filters['search']);
        $pageNav->setAdditionalUrlParam('filterby', $filters['filterby']);
        $pageNav->setAdditionalUrlParam('sortby', $filters['sortby']);
        $pageNav->setAdditionalUrlParam('area', $filters['area']);
        $pageNav->setAdditionalUrlParam('sortdir', $filters['sort_Dir']);
      @endphp
      {!! $pageNav !!}
    </nav>
  @else
    <x-empty-state
      :title="Lang::txt('COM_ANSWERS_NO_RESULTS')"
      :message="$filters['search'] ? Lang::txt('COM_ANSWERS_TRY_DIFFERENT_SEARCH') : ''"
    >
      @if(User::authorise('core.create', $option))
        <a class="btn btn-primary"
           href="{{ Route::url('index.php?option=' . $option . '&task=new', false) }}">
          {{ Lang::txt('COM_ANSWERS_NEW_QUESTION') }}
        </a>
      @endif
    </x-empty-state>
  @endif

</x-page-container>
