{{--
  Browse resumes/candidates — employer only.

  Variables from controller (resumesTask):
    $config      — Component params (Registry)
    $admin       — Admin flag
    $masterAdmin — Master admin flag
    $title       — Page title string
    $seekers     — Array of seeker objects
    $pageNav     — Paginator instance
    $cats        — Categories array
    $types       — Types array
    $filters     — Filter settings
    $emp         — Employer flag
    $option      — Component option string

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Plugin;
  use Hubzero\Facades\Route;

  $dashboardUrl = Route::url('index.php?option=' . $option . '&task=dashboard');
  $resumesUrl = Route::url('index.php?option=' . $option . '&task=resumes');
  $isShortlist = ($filters['filterby'] ?? '') == 'shortlisted';
@endphp

<x-page-container :title="$title">
  @slot('actions')
    @if($emp)
      <a class="btn" href="{{ $dashboardUrl }}">{{ Lang::txt('COM_JOBS_EMPLOYER_DASHBOARD') }}</a>
      @if($isShortlist)
        <a class="btn" href="{{ $resumesUrl }}">{{ Lang::txt('COM_JOBS_ALL_CANDIDATES') }}</a>
      @else
        <a class="btn" href="{{ $resumesUrl }}?filterby=shortlisted">
          {{ Lang::txt('COM_JOBS_SHORTLIST') }}
        </a>
      @endif
    @else
      <a class="btn" href="{{ $dashboardUrl }}">{{ Lang::txt('COM_JOBS_ADMIN_DASHBOARD') }}</a>
    @endif
  @endslot

  @slot('sidebar')
    @if(!$isShortlist)
      <x-sidebar-card :title="Lang::txt('COM_JOBS_ACTION_SEARCH')">
        <form method="post" action="{{ $resumesUrl }}">
          <div class="space-y-3">
            <div>
              <label class="label label-text text-xs" for="sort-lastupdate">
                {{ Lang::txt('COM_JOBS_SORTBY') }}
              </label>
              <div class="flex gap-3">
                <label class="label cursor-pointer gap-1 text-sm">
                  <input type="radio" name="sortby" value="lastupdate"
                         class="radio radio-sm"
                         {{ ($filters['sortby'] ?? '') != 'bestmatch' ? 'checked' : '' }} />
                  {{ Lang::txt('COM_JOBS_RESUMES_LAST_UPDATE') }}
                </label>
                <label class="label cursor-pointer gap-1 text-sm">
                  <input type="radio" name="sortby" value="bestmatch"
                         class="radio radio-sm"
                         {{ ($filters['sortby'] ?? '') == 'bestmatch' ? 'checked' : '' }}
                         {{ !($filters['match'] ?? false) ? 'disabled' : '' }} />
                  {{ Lang::txt('COM_JOBS_SORTBY_BEST_MATCH') }}
                </label>
              </div>
            </div>

            <x-form-field name="q" inputId="search-q"
                          :label="Lang::txt('COM_JOBS_SEARCH_KEYWORDS')">
              <input type="text" name="q" id="search-q" maxlength="250"
                     class="input input-bordered input-sm w-full"
                     value="{{ e($filters['search'] ?? '') }}" />
            </x-form-field>

            <x-form-field name="category" inputId="filter-category"
                          :label="Lang::txt('COM_JOBS_SEARCH_CATEGORY_SOUGHT')">
              {!! \Components\Jobs\Helpers\Html::formSelect(
                  'category', $cats, $filters['category'] ?? 0,
                  'select select-bordered select-sm w-full'
              ) !!}
            </x-form-field>

            <x-form-field name="type" inputId="filter-type"
                          :label="Lang::txt('COM_JOBS_SEARCH_TYPE_SOUGHT')">
              {!! \Components\Jobs\Helpers\Html::formSelect(
                  'type', $types, $filters['type'] ?? 0,
                  'select select-bordered select-sm w-full'
              ) !!}
            </x-form-field>

            <label class="label cursor-pointer justify-start gap-2 text-sm">
              <input type="checkbox" name="saveprefs" value="1"
                     class="checkbox checkbox-sm" checked />
              {{ Lang::txt('COM_JOBS_SEARCH_SAVE_PREFS') }}
            </label>

            <input type="hidden" name="performsearch" value="1" />
            <button type="submit" class="btn btn-primary btn-sm w-full">
              {{ Lang::txt('JSEARCH_FILTER_SUBMIT', 'Search') }}
            </button>
          </div>
        </form>
      </x-sidebar-card>
    @else
      <x-sidebar-card>
        <p class="text-sm text-base-content/60">
          {{ Lang::txt('COM_JOBS_SHORTLIST_EXPLANATION') }}
          <a href="{{ $resumesUrl }}" class="link link-primary">
            {{ Lang::txt('COM_JOBS_ALL_CANDIDATES') }}
          </a>
        </p>
      </x-sidebar-card>
    @endif
  @endslot

  @if($isShortlist)
    <h4 class="text-lg font-semibold mb-4">
      {{ Lang::txt('COM_JOBS_SHORTLIST') }}
    </h4>
  @endif

  @if(count($seekers) > 0)
    <p class="text-sm text-base-content/60 mb-4">
      {{ Lang::txt('COM_JOBS_NOTICE_DISPLAYING') }}
      @if(($filters['start'] ?? 0) == 0)
        {{ $pageNav->total > count($seekers)
            ? Lang::txt('COM_JOBS_NOTICE_TOP') . ' ' . count($seekers) . ' ' . Lang::txt('COM_JOBS_NOTICE_OUT_OF') . ' ' . $pageNav->total
            : Lang::txt('COM_JOBS_ALL') . ' ' . count($seekers) }}
      @else
        {{ ($filters['start'] + 1) }} - {{ $filters['start'] + count($seekers) }}
        {{ Lang::txt('COM_JOBS_NOTICE_OUT_OF') }} {{ $pageNav->total }}
      @endif
      @if($isShortlist)
        {{ strtolower(Lang::txt('COM_JOBS_SHORTLISTED')) }}
      @endif
      {{ strtolower(Lang::txt('COM_JOBS_CANDIDATES')) }}
    </p>

    <div class="space-y-3">
      @foreach($seekers as $seeker)
        @php
          $params = new \Hubzero\Config\Registry(
              Plugin::params('members', 'resume')
          );
        @endphp
        {!! $__view->view('_seeker')
            ->set('seeker', $seeker)
            ->set('emp', $emp)
            ->set('option', 'com_members')
            ->set('admin', $admin)
            ->set('params', $params)
            ->set('list', 1)
            ->loadTemplate() !!}
      @endforeach
    </div>
  @else
    <x-empty-state
        :title="$isShortlist
            ? Lang::txt('COM_JOBS_RESUMES_NONE_SHORTLISTED')
            : Lang::txt('COM_JOBS_RESUMES_NONE_FOUND')"
        :message="''"
    />
  @endif

  @php
    $pageNav->setAdditionalUrlParam('task', 'resumes');
    $pageNav->setAdditionalUrlParam('sortby', $filters['sortby'] ?? '');
    $pageNav->setAdditionalUrlParam('filterby', $filters['filterby'] ?? '');
    $pageNav->setAdditionalUrlParam('category', $filters['category'] ?? '');
    $pageNav->setAdditionalUrlParam('type', $filters['type'] ?? '');
    $pageNav->setAdditionalUrlParam('q', $filters['search'] ?? '');
  @endphp
  {!! $pageNav->render() !!}
</x-page-container>
