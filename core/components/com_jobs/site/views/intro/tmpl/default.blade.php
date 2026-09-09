{{--
  Jobs introduction page — landing with role-based actions and feature columns.

  Variables from controller (displayTask):
    $title   — Page title string
    $config  — Component params (Registry)
    $option  — Component option string
    $emp     — Employer privileges flag
    $admin   — Admin privileges flag
    $pageNav — Paginator instance
    $msg     — Optional help message

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Config;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $loginUrl = Route::url('index.php?option=' . $option . '&task=view') . '?action=login';
  $dashboardUrl = Route::url('index.php?option=' . $option . '&task=dashboard');
  $shortlistUrl = Route::url('index.php?option=' . $option . '&task=resumes') . '?filterby=shortlisted';
  $resumeUrl = Route::url('index.php?option=' . $option . '&task=addresume');
  $resumesUrl = Route::url('index.php?option=' . $option . '&task=resumes');
  $addjobUrl = Route::url('index.php?option=' . $option . '&task=addjob');
  $browseUrl = Route::url('index.php?option=' . $option . '&task=browse');
@endphp

<x-page-container :title="$title">
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

  @if($msg)
    <div role="alert" class="alert alert-info mb-6">
      <span>{{ $msg }}</span>
    </div>
  @endif

  @if($config->get('allowsubscriptions', 0))
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <div>
        <p class="text-base-content/70">
          {{ Lang::txt('COM_JOBS_TIP_ENJOY_COMMUNITY_EXPOSURE', Config::get('sitename')) }}
        </p>
      </div>

      <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
          <h3 class="card-title text-base">{{ Lang::txt('COM_JOBS_EMPLOYERS') }}</h3>
          <ul class="menu menu-sm p-0">
            <li><a href="{{ $resumesUrl }}">{{ Lang::txt('COM_JOBS_ACTION_BROWSE_RESUMES') }}</a></li>
            <li><a href="{{ $addjobUrl }}">{{ Lang::txt('COM_JOBS_ACTION_POST_JOB') }}</a></li>
          </ul>
        </div>
      </div>

      <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
          <h3 class="card-title text-base">{{ Lang::txt('COM_JOBS_SEEKERS') }}</h3>
          <ul class="menu menu-sm p-0">
            <li><a href="{{ $browseUrl }}">{{ Lang::txt('COM_JOBS_ACTION_BROWSE_JOBS') }}</a></li>
            <li><a href="{{ $resumeUrl }}">{{ Lang::txt('COM_JOBS_ACTION_POST_RESUME') }}</a></li>
          </ul>
        </div>
      </div>
    </div>
  @endif
</x-page-container>
