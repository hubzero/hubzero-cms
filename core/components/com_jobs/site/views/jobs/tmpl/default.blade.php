{{--
  Job listing page — browse jobs with search and pagination.

  Variables from controller (displayTask):
    $title            — Page title string
    $config           — Component params (Registry)
    $option           — Component option string
    $emp              — Employer privileges flag
    $admin            — Admin privileges flag
    $total            — Total job count
    $pageNav          — Paginator instance
    $jobs             — Array of job objects
    $mini             — Mini mode flag
    $filters          — Current filter settings
    $subscriptionCode — Employer subscription code filter
    $employer         — Employer object (when filtering by code)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $pageTitle = $title;
  if ($subscriptionCode && $employer) {
      $pageTitle .= ' ' . Lang::txt('COM_JOBS_FROM') . ' ' . $employer->companyName;
  }

  $loginUrl = Route::url('index.php?option=' . $option . '&task=view') . '?action=login';
  $dashboardUrl = Route::url('index.php?option=' . $option . '&task=dashboard');
  $shortlistUrl = Route::url('index.php?option=' . $option . '&task=resumes') . '?filterby=shortlisted';
  $resumeUrl = Route::url('index.php?option=' . $option . '&task=addresume');
  $browseUrl = Route::url('index.php?option=' . $option . '&task=browse');
@endphp

<x-page-container :title="$pageTitle">
  @slot('actions')
    @if(User::isGuest())
      <span class="text-sm">
        {{ Lang::txt('COM_JOBS_PLEASE') }}
        <a class="link link-primary" href="{{ $loginUrl }}">{{ Lang::txt('COM_JOBS_ACTION_LOGIN') }}</a>
        {{ Lang::txt('COM_JOBS_ACTION_LOGIN_TO_VIEW_OPTIONS') }}
      </span>
    @elseif($emp && $config->get('allowsubscriptions', 0))
      <a class="btn" href="{{ $dashboardUrl }}">
        {{ Lang::txt('COM_JOBS_EMPLOYER_DASHBOARD') }}
      </a>
      <a class="btn" href="{{ $shortlistUrl }}">
        {{ Lang::txt('COM_JOBS_SHORTLIST') }}
      </a>
    @elseif($admin)
      <a class="btn" href="{{ $dashboardUrl }}">
        {{ Lang::txt('COM_JOBS_ADMIN_DASHBOARD') }}
      </a>
    @else
      <a class="btn" href="{{ $resumeUrl }}">
        {{ Lang::txt('COM_JOBS_MY_RESUME') }}
      </a>
    @endif
  @endslot

  @if(count($jobs) > 0)
    <x-search-bar
        :action="$browseUrl"
        :query="$filters['search']"
        :placeholder="Lang::txt('COM_JOBS_SEARCH_PLACEHOLDER')"
        :buttonLabel="Lang::txt('JSEARCH_FILTER_SUBMIT')"
        :clearUrl="$filters['search'] ? $browseUrl : ''"
    >
      <input type="hidden" name="limitstart" value="0" />
      <input type="hidden" name="performsearch" value="1" />
    </x-search-bar>

    {!! $__view->view('_list')
        ->set('option', $option)
        ->set('filters', $filters)
        ->set('config', $config)
        ->set('task', $task ?? 'browse')
        ->set('emp', $emp)
        ->set('mini', $mini)
        ->set('jobs', $jobs)
        ->set('admin', $admin)
        ->loadTemplate() !!}
  @else
    <x-empty-state
        :title="Lang::txt('COM_JOBS_NO_JOBS_FOUND')"
    >
      @if($subscriptionCode)
        <p class="text-sm text-base-content/60">
          @if($employer)
            {{ Lang::txt('COM_JOBS_FROM') }} {{ Lang::txt('COM_JOBS_EMPLOYER') }}
            {{ $employer->companyName }} ({{ $subscriptionCode }})
          @else
            {{ Lang::txt('COM_JOBS_FROM') }} {{ Lang::txt('COM_JOBS_REQUESTED_EMPLOYER') }}
            ({{ $subscriptionCode }})
          @endif
        </p>
        <a class="btn btn-ghost btn-sm" href="{{ $browseUrl }}">
          {{ Lang::txt('COM_JOBS_ACTION_BROWSE_ALL_JOBS') }}
        </a>
      @endif
    </x-empty-state>
  @endif

  @php
    $pageNav->setAdditionalUrlParam('sortby', $filters['sortby']);
  @endphp
  {!! $pageNav->render() !!}
</x-page-container>
