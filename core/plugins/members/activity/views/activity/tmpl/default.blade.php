{{--
  Member Activity — activity feed with filters and pagination.

  Variables from plugin (onMembers):
    $member     — member profile object
    $rows       — activity recipient rows
    $total      — total activity count
    $filters    — filters array (filter, search, scope, created_by, start, limit)
    $categories — scope categories array
    $digests    — digest settings available

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $__view->css()
      ->js('jquery.infinitescroll', 'com_collections')
      ->js();

  $no_html = Request::getInt('no_html', 0);

  // Get online users
  $online = [];
  $sessions = Hubzero\Session\Helper::getAllSessions(['guest' => 0, 'distinct' => 1]);
  if ($sessions) {
      foreach ($sessions as $session) {
          $online[] = $session->userid;
      }
  }

  $qsfilters  = ($filters['scope'] ? '&scope=' . $filters['scope'] : '');
  $qsfilters .= ($filters['created_by'] ? '&created_by=' . $filters['created_by'] : '');
  $baseUrl = $member->link() . '&active=activity';
@endphp

@if (!$no_html)
<div class="space-y-4">
  <form action="{{ Route::url($baseUrl) }}" method="get">
    {{-- Toolbar --}}
    <div class="flex justify-between items-center mb-4">
      <div class="flex gap-2">
        @if ($filters['filter'] === 'starred')
          <a class="btn btn-ghost btn-sm" href="{{ Route::url($baseUrl . $qsfilters) }}"
             title="{{ Lang::txt('PLG_MEMBERS_ACTIVITY_FILTER_ALL') }}">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                 class="size-4 text-warning" aria-hidden="true">
              <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
            </svg>
            {{ Lang::txt('PLG_MEMBERS_ACTIVITY_FILTER_ALL') }}
          </a>
        @else
          <a class="btn btn-ghost btn-sm" href="{{ Route::url($baseUrl . '&filter=starred' . $qsfilters) }}"
             title="{{ Lang::txt('PLG_MEMBERS_ACTIVITY_FILTER_STARRED') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
            </svg>
            {{ Lang::txt('PLG_MEMBERS_ACTIVITY_FILTER_STARRED') }}
          </a>
        @endif
        @if ($digests)
          <a class="btn btn-ghost btn-sm"
             href="{{ Route::url($baseUrl . '&action=settings') }}"
             title="{{ Lang::txt('PLG_MEMBERS_ACTIVITY_SETTINGS') }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
              <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.431.992a7.723 7.723 0 0 1 0 .255c-.007.378.138.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>
            {{ Lang::txt('PLG_MEMBERS_ACTIVITY_SETTINGS') }}
          </a>
        @endif
      </div>
    </div>

    {{-- Filters --}}
    <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-6">
      <div class="md:col-span-4">
        <div class="form-control w-full">
          <label class="label" for="filter-search">
            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_ACTIVITY_SEARCH') }}</span>
          </label>
          <input type="text" name="q" id="filter-search" class="input input-bordered w-full"
                 value="{{ e($filters['search']) }}"
                 placeholder="{{ Lang::txt('PLG_MEMBERS_ACTIVITY_SEARCH_PLACEHOLDER') }}" />
        </div>
      </div>
      <div class="md:col-span-3">
        <div class="form-control w-full">
          <label class="label" for="filter-scope">
            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_ACTIVITY_FILTER_SCOPE') }}</span>
          </label>
          <select name="scope" id="filter-scope" class="select select-bordered w-full">
            <option value="">{{ Lang::txt('PLG_MEMBERS_ACTIVITY_FILTER_SCOPE_ALL') }}</option>
            @foreach ($categories as $category)
              <option value="{{ e($category) }}" {{ $filters['scope'] == $category ? 'selected' : '' }}>
                {{ e($category) }}
              </option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="md:col-span-3">
        <div class="form-control w-full">
          <label class="label" for="filter-created_by">
            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_ACTIVITY_FILTER_CREATED_BY') }}</span>
          </label>
          <select name="created_by" id="filter-created_by" class="select select-bordered w-full">
            <option value="">{{ Lang::txt('PLG_MEMBERS_ACTIVITY_FILTER_CREATED_BY_ALL') }}</option>
            <option value="me" {{ $filters['created_by'] == 'me' ? 'selected' : '' }}>
              {{ Lang::txt('PLG_MEMBERS_ACTIVITY_FILTER_CREATED_BY_ME') }}
            </option>
            <option value="notme" {{ $filters['created_by'] == 'notme' ? 'selected' : '' }}>
              {{ Lang::txt('PLG_MEMBERS_ACTIVITY_FILTER_CREATED_BY_NOTME') }}
            </option>
          </select>
        </div>
      </div>
      <div class="md:col-span-2 flex items-end">
        <button type="submit" class="btn btn-primary w-full">
          {{ Lang::txt('PLG_MEMBERS_ACTIVITY_SEARCH') }}
        </button>
      </div>
    </div>
@endif

    @if ($rows->count())
      <ul class="activity-feed space-y-4" data-url="{{ Route::url($baseUrl) }}">
        @foreach ($rows as $row)
          {!! $__view->view('default_item')
              ->set('member', $member)
              ->set('row', $row)
              ->set('online', $online)
              ->loadTemplate() !!}
        @endforeach
      </ul>

      @php
        $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
        $pageNav->setAdditionalUrlParam('id', $member->get('id'));
        $pageNav->setAdditionalUrlParam('active', 'activity');
        if ($filters['filter']) {
            $pageNav->setAdditionalUrlParam('filter', $filters['filter']);
        }
        if ($filters['search']) {
            $pageNav->setAdditionalUrlParam('search', $filters['search']);
        }
        if ($filters['created_by']) {
            $pageNav->setAdditionalUrlParam('created_by', $filters['created_by']);
        }
      @endphp
      {!! $pageNav !!}
    @else
      <div class="text-center py-8">
        <p class="text-base-content/70 mb-4">{{ Lang::txt('PLG_MEMBERS_ACTIVITY_NO_RESULTS') }}</p>
        <p class="mb-2"><strong>{{ Lang::txt('PLG_MEMBERS_ACTIVITY_ABOUT_TITLE') }}</strong></p>
        <p class="text-base-content/70">{{ Lang::txt('PLG_MEMBERS_ACTIVITY_ABOUT') }}</p>
      </div>
    @endif

@if (!$no_html)
  </form>
</div>
@endif
