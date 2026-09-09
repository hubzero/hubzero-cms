{{--
  Job posting detail view.

  Variables from controller (jobTask):
    $title   — Page title string
    $config  — Component params (Registry)
    $option  — Component option string
    $emp     — Employer privileges flag
    $admin   — Admin privileges flag
    $job     — Job object with applications, withdrawnlist

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Event;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $job->cat = $job->cat ?: Lang::txt('COM_JOBS_NOTICE_UNSPECIFIED');
  $job->type = $job->type ?: Lang::txt('COM_JOBS_NOTICE_UNSPECIFIED');

  $startdate = ($job->startdate && $job->startdate != '0000-00-00 00:00:00')
      ? Date::of($job->startdate)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
      : Lang::txt('COM_JOBS_NOTICE_UNSPECIFIED');
  $closedate = ($job->closedate && $job->closedate != '0000-00-00 00:00:00')
      ? Date::of($job->closedate)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
      : Lang::txt('COM_JOBS_NOTICE_UNSPECIFIED');

  $model = new \Components\Jobs\Models\Job($job);
  $maintext = $model->content('parsed');
  $owner = (User::get('id') == $job->employerid || $admin) ? 1 : 0;

  $dashboardUrl = Route::url('index.php?option=' . $option . '&task=dashboard');
  $shortlistUrl = Route::url('index.php?option=' . $option . '&task=resumes') . '?filterby=shortlisted';
  $addjobUrl = Route::url('index.php?option=' . $option . '&task=addjob');
  $resumeUrl = Route::url('index.php?option=' . $option . '&task=addresume');
  $loginUrl = Route::url('index.php?option=' . $option . '&task=view') . '?action=login';
  $applyUrl = Route::url('index.php?option=' . $option . '&task=apply&code=' . $job->code);
  $editappUrl = Route::url('index.php?option=' . $option . '&task=editapp&code=' . $job->code);
  $withdrawUrl = Route::url('index.php?option=' . $option . '&task=withdraw&code=' . $job->code);
  $editjobUrl = Route::url('index.php?option=' . $option . '&task=editjob&code=' . $job->code);
  $unpublishUrl = Route::url('index.php?option=' . $option . '&task=unpublish&code=' . $job->code);
  $reopenUrl = Route::url('index.php?option=' . $option . '&task=reopen&code=' . $job->code);
  $removeUrl = Route::url('index.php?option=' . $option . '&task=remove&code=' . $job->code);
  $confirmUrl = Route::url('index.php?option=' . $option . '&task=confirmjob&code=' . $job->code);

  $job->title = trim(stripslashes($job->title));
  $job->description = trim(stripslashes($job->description));
  $job->description = preg_replace('/<br\\s*?\/??>/i', '', $job->description);
  $job->description = \Components\Jobs\Helpers\Html::txt_unpee($job->description);
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
      <a class="btn" href="{{ $addjobUrl }}">{{ Lang::txt('COM_JOBS_ADD_ANOTHER_JOB') }}</a>
    @elseif($admin)
      <a class="btn" href="{{ $dashboardUrl }}">{{ Lang::txt('COM_JOBS_ADMIN_DASHBOARD') }}</a>
      <a class="btn" href="{{ $addjobUrl }}">{{ Lang::txt('COM_JOBS_ADD_ANOTHER_JOB') }}</a>
    @else
      <a class="btn" href="{{ $resumeUrl }}">{{ Lang::txt('COM_JOBS_MY_RESUME') }}</a>
    @endif
  @endslot

  @slot('sidebar')
    {{-- Application actions --}}
    <x-sidebar-card :title="Lang::txt('COM_JOBS_ACTIONS')">
      <div class="space-y-2">
        @if(!$job->applied && !$job->withdrawn && $job->status == 1)
          <a href="{{ $applyUrl }}" class="btn btn-primary btn-sm w-full">
            {{ Lang::txt('COM_JOBS_APPLY_NOW') }}
          </a>
        @endif
        @if($job->withdrawn && $job->status == 1)
          <a href="{{ $applyUrl }}" class="btn btn-primary btn-sm w-full">
            {{ Lang::txt('COM_JOBS_ACTION_REAPPLY') }}
          </a>
        @endif
        @if($job->applied)
          <a href="{{ $editappUrl }}" class="btn btn-sm w-full">
            {{ Lang::txt('COM_JOBS_ACTION_EDIT_APPLICATION') }}
          </a>
          <a href="{{ $withdrawUrl }}" class="btn btn-error btn-outline btn-sm w-full">
            {{ Lang::txt('COM_JOBS_ACTION_WITHDRAW_APPLICATION') }}
          </a>
        @endif
        @if($owner && ($job->status == 1 || $job->status == 3))
          <a href="{{ $editjobUrl }}" class="btn btn-sm w-full">
            {{ Lang::txt('COM_JOBS_ACTION_EDIT_JOB') }}
          </a>
        @endif
        @if($job->status == 1 && $owner)
          <a href="{{ $unpublishUrl }}" class="btn btn-sm w-full"
             title="{{ Lang::txt('COM_JOBS_NOTICE_ACCESS_PRESERVED') }}">
            {{ Lang::txt('COM_JOBS_ACTION_UNPUBLISH_THIS_JOB') }}
          </a>
        @endif
        @if($job->status == 3)
          <a href="{{ $reopenUrl }}" class="btn btn-sm w-full">
            {{ Lang::txt('COM_JOBS_ACTION_REOPEN_THIS') }}
          </a>
          <a href="{{ $removeUrl }}" class="btn btn-error btn-outline btn-sm w-full">
            {{ Lang::txt('COM_JOBS_ACTION_DELETE_THIS_JOB') }}
          </a>
        @endif
        @if($owner && $job->status == 4)
          <a href="{{ $confirmUrl }}" class="btn btn-primary btn-sm w-full">
            {{ Lang::txt('COM_JOBS_ACTION_PUBLISH_AD') }}
          </a>
          <a href="{{ $editjobUrl }}" class="btn btn-sm w-full">
            {{ Lang::txt('COM_JOBS_ACTION_MAKE_CHANGES') }}
          </a>
          <a href="{{ $removeUrl }}" class="btn btn-error btn-outline btn-sm w-full">
            {{ Lang::txt('COM_JOBS_ACTION_REMOVE_AD') }}
          </a>
        @endif
      </div>
    </x-sidebar-card>

    {{-- Job metadata --}}
    <x-sidebar-card :title="Lang::txt('COM_JOBS_EDITJOB_JOB_SPECIFICS')">
      <dl class="space-y-2 text-sm">
        <dt class="font-semibold">{{ Lang::txt('COM_JOBS_TABLE_CATEGORY') }}</dt>
        <dd>{{ $job->cat }}</dd>
        <dt class="font-semibold">{{ Lang::txt('COM_JOBS_TABLE_TYPE') }}</dt>
        <dd>{{ $job->type }}</dd>
        <dt class="font-semibold">{{ Lang::txt('COM_JOBS_TABLE_START_DATE') }}</dt>
        <dd>{{ $startdate }}</dd>
        <dt class="font-semibold">{{ Lang::txt('COM_JOBS_TABLE_EXPIRES') }}</dt>
        <dd>{{ $closedate }}</dd>
      </dl>
    </x-sidebar-card>
  @endslot

  {{-- Application status --}}
  @if($job->applied)
    <div role="alert" class="alert alert-success mb-4">
      <span>
        {{ Lang::txt('COM_JOBS_JOB_APPLIED_ON') }}
        {{ Date::of($job->applied)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
      </span>
    </div>
  @elseif($job->withdrawn)
    <div role="alert" class="alert alert-warning mb-4">
      <span>
        {{ Lang::txt('COM_JOBS_JOB_WITHDREW_ON') }}
        {{ Date::of($job->withdrawn)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
      </span>
    </div>
  @endif

  {{-- Job header --}}
  <div class="mb-6">
    <h3 class="text-xl font-bold">{{ $job->title }}</h3>
    <p class="text-base-content/70 mt-1">
      @if(preg_match('/(.*)http/i', $job->companyWebsite))
        <a href="{{ $job->companyWebsite }}" class="link link-primary" rel="external">{{ $job->companyName }}</a>
      @else
        {{ $job->companyName }}
      @endif
      &mdash; {{ $job->companyLocation }}@if($job->companyLocationCountry), {{ strtoupper($job->companyLocationCountry) }}@endif
    </p>
    <p class="text-sm text-base-content/50 mt-1">
      {{ Lang::txt('COM_JOBS_JOB_REFERENCE_CODE') }}: {{ $job->code }}
    </p>
  </div>

  {{-- Job description --}}
  <div class="prose max-w-none mb-8">
    {!! $maintext !!}
  </div>

  {{-- Contact info --}}
  @if($job->contactName)
    <div class="card bg-base-200 p-4 mb-8">
      <h4 class="font-semibold mb-2">{{ Lang::txt('COM_JOBS_JOB_INFO_CONTACT') }}</h4>
      <p class="text-sm">
        {{ $job->contactName }}
        @if($job->contactPhone)
          <br>{{ Lang::txt('COM_JOBS_JOB_TABLE_TEL') }}: {{ $job->contactPhone }}
        @endif
        @if($job->contactEmail)
          <br>{{ Lang::txt('COM_JOBS_JOB_TABLE_EMAIL') }}: {{ $job->contactEmail }}
        @endif
      </p>
    </div>
  @endif

  {{-- Applications (owner only) --}}
  @if($owner)
    <div class="divider"></div>

    <div class="flex items-center justify-between mb-4">
      <h3 class="text-lg font-semibold">
        {{ Lang::txt('COM_JOBS_APPLICATIONS') }}
        ({{ count($job->applications) }} {{ Lang::txt('COM_JOBS_TOTAL') }})
      </h3>
      @php
        $applicantsUrl = Route::url('index.php?option=com_jobs&task=resumes?filterby=applied');
      @endphp
      <a href="{{ $applicantsUrl }}" class="btn btn-sm">
        {{ Lang::txt('COM_JOBS_REVIEW_APPLICANTS') }}
      </a>
    </div>

    @if(count($job->applications) <= 0)
      <p class="text-base-content/60">{{ Lang::txt('COM_JOBS_NOTICE_APPLICATIONS_NONE') }}</p>
    @else
      <div class="space-y-4">
        @php $k = 1; @endphp
        @foreach($job->applications as $application)
          @if($application->seeker && $application->status != 2)
            @php
              $applied = ($application->applied && $application->applied != '0000-00-00 00:00:00')
                  ? Date::of($application->applied)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
                  : Lang::txt('N/A');
              $applicantUrl = Route::url('members/' . $application->uid . '/resume');
            @endphp
            <div class="card bg-base-100 shadow-sm">
              <div class="card-body p-4">
                <div class="flex items-start justify-between">
                  <div>
                    <span class="text-base-content/50 text-sm">{{ $k }}.</span>
                    <a href="{{ $applicantUrl }}" class="link link-primary font-medium">
                      {{ $application->seeker->name }}
                    </a>
                    <span class="text-sm text-base-content/60">
                      &mdash; {{ Lang::txt('COM_JOBS_APPLIED') }} {{ $applied }}
                    </span>
                  </div>
                </div>
                @if($application->cover)
                  <blockquote class="border-l-2 border-base-300 pl-4 mt-2 text-sm text-base-content/70 italic">
                    {{ trim(stripslashes($application->cover)) }}
                  </blockquote>
                @endif
                @php
                  $out = Event::trigger(
                      'members.showSeeker',
                      [$application->seeker, $emp, $admin, 'com_members', 0]
                  );
                @endphp
                @if(count($out) > 0)
                  <div class="mt-2">{!! $out[0] !!}</div>
                @endif
              </div>
            </div>
            @php $k++; @endphp
          @endif
        @endforeach
      </div>

      @if(count($job->withdrawnlist) > 0)
        <p class="text-sm text-base-content/60 mt-4">
          {{ count($job->withdrawnlist) }} {{ Lang::txt('COM_JOBS_NOTICE_CANDIDATES_WITHDREW') }}
        </p>
      @endif
    @endif
  @endif
</x-page-container>
