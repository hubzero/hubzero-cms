{{--
  Job application form.

  Variables from controller (applyTask):
    $title       — Page title string
    $config      — Component params (Registry)
    $option      — Component option string
    $emp         — Employer privileges flag
    $admin       — Admin privileges flag
    $job         — Job object
    $seeker      — Seeker object (null if no resume)
    $application — Application object
    $task        — Current task (apply or editapp)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Config;
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $dashboardUrl = Route::url('index.php?option=' . $option . '&task=dashboard');
  $shortlistUrl = Route::url('index.php?option=' . $option . '&task=resumes') . '?filterby=shortlisted';
  $browseUrl = Route::url('index.php?option=' . $option . '&task=browse');
  $loginUrl = Route::url('index.php?option=' . $option . '&task=view') . '?action=login';
  $resumeUrl = Route::url(
      'index.php?option=com_members&id=' . User::get('id') . '&active=resume'
  );
  $jobUrl = Route::url('index.php?option=' . $option . '&task=job&code=' . $job->code);
  $saveAppUrl = Route::url('index.php?option=' . $option . '&task=saveapp');

  $owner = (User::get('id') == $job->employerid || $admin) ? 1 : 0;
  $appid = ($application->status ?? null) != 2 ? ($application->id ?? 0) : 0;
  $isOwnAd = (!$admin && User::get('id') == $job->employerid)
      || ($admin && $job->employerid == 1);

  $submitLabel = ($task ?? '') == 'editapp'
      ? Lang::txt('COM_JOBS_ACTION_SAVE_CHANGES_APPLICATION')
      : Lang::txt('COM_JOBS_ACTION_APPLY_THIS_JOB');

  $job->title = trim(stripslashes($job->title));
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
      <a class="btn" href="{{ $dashboardUrl }}">{{ Lang::txt('COM_JOBS_EMPLOYER_DASHBOARD') }}</a>
      <a class="btn" href="{{ $shortlistUrl }}">{{ Lang::txt('COM_JOBS_SHORTLIST') }}</a>
    @elseif($admin)
      <a class="btn" href="{{ $dashboardUrl }}">{{ Lang::txt('COM_JOBS_ADMIN_DASHBOARD') }}</a>
    @else
      <a class="btn" href="{{ $browseUrl }}">{{ Lang::txt('COM_JOBS_ALL_JOBS') }}</a>
    @endif
  @endslot

  @if(!$seeker)
    <div role="alert" class="alert alert-warning">
      <span>
        {{ Lang::txt('COM_JOBS_APPLY_TO_APPLY') }}
        {{ Config::get('sitename') }}
        {{ Lang::txt('COM_JOBS_APPLY_NEED_RESUME') }}
        <a class="link link-primary" href="{{ $resumeUrl }}">
          {{ Lang::txt('COM_JOBS_ACTION_CREATE_PROFILE') }}
        </a>
      </span>
    </div>
  @else
    @if($isOwnAd)
      <div role="alert" class="alert alert-warning mb-4">
        <span>{{ Lang::txt('COM_JOBS_APPLY_WARNING_OWN_AD') }}</span>
      </div>
    @endif

    {{-- Job info header --}}
    <div class="card bg-base-200 p-4 mb-6">
      <h3 class="font-bold">
        {{ $job->title }} &mdash;
        @if(preg_match('/(.*)http/i', $job->companyWebsite))
          <a href="{{ $job->companyWebsite }}" class="link link-primary" rel="external">{{ $job->companyName }}</a>
        @else
          {{ $job->companyName }}
        @endif
      </h3>
      <p class="text-sm text-base-content/60">
        {{ $job->companyLocation }}, {{ $job->companyLocationCountry }}
        &mdash; {{ Lang::txt('COM_JOBS_JOB_REFERENCE_CODE') }}: {{ $job->code }}
      </p>
    </div>

    <form id="hubForm" method="post" action="{{ $saveAppUrl }}">
      <input type="hidden" name="task" value="saveapp" />
      <input type="hidden" name="code" value="{{ $job->code }}" />
      <input type="hidden" name="jid" value="{{ $job->id }}" />
      <input type="hidden" name="appid" value="{{ $appid }}" />
      <input type="hidden" name="uid" value="{{ User::get('id') }}" />

      <x-form-section :heading="Lang::txt('COM_JOBS_APPLY_MSG_TO_EMPLOYER')">
        <x-form-field name="cover"
                      :label="Lang::txt('COM_JOBS_APPLY_HINT_COVER_LETTER')"
                      :hint="Lang::txt('COM_JOBS_OPTIONAL')">
          <textarea class="textarea textarea-bordered w-full"
                    name="cover" id="cover"
                    rows="10">{{ $application->cover ?? '' }}</textarea>
        </x-form-field>
      </x-form-section>

      {{-- Seeker profile info --}}
      @php
        $out = Event::trigger(
            'members.showSeeker',
            [$seeker, $emp, $admin, 'com_members', 0]
        );
      @endphp
      @if(count($out) > 0)
        <div class="my-6">{!! implode("\n", $out) !!}</div>
      @endif

      <div class="form-actions">
        <button class="btn btn-primary" type="submit">
          {{ $submitLabel }}
        </button>
        <a class="btn btn-ghost" href="{{ $jobUrl }}">
          {{ Lang::txt('JCANCEL') }}
        </a>
      </div>
    </form>
  @endif
</x-page-container>
