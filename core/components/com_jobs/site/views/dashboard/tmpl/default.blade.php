{{--
  Employer dashboard — stats, job management, subscription details.

  Variables from controller (dashboardTask):
    $title        — Page title string
    $option       — Component option string
    $subscription — Subscription object
    $employer     — Employer object
    $service      — Service object
    $myjobs       — Array of employer's job objects
    $activejobs   — Count of active jobs
    $stats        — Stats array (total_resumes, shortlisted, applied)
    $emp          — Employer privileges flag
    $admin        — Admin privileges flag
    $masterAdmin  — Master admin flag
    $uid          — Current user ID
    $login        — Username string

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $allowed_ads = max(0, $service->maxads - $activejobs);

  $statusClass = 'badge-error';
  switch ($subscription->status) {
      case '0':
          $status = Lang::txt('COM_JOBS_JOB_STATUS_PENDING');
          $statusClass = 'badge-warning';
          break;
      case '1':
          $status = Lang::txt('COM_JOBS_JOB_STATUS_ACTIVE');
          $statusClass = 'badge-success';
          break;
      case '2':
          $status = Lang::txt('COM_JOBS_JOB_STATUS_CANCELLED');
          break;
      default:
          $status = Lang::txt('N/A');
          break;
  }

  $today = date('Y-m-d');
  $isExpired = $subscription->expires < $today && $subscription->status == 1;
  if ($isExpired) {
      $status = Lang::txt('COM_JOBS_SUBSCRIPTION_STATUS_EXPIRED');
      $statusClass = 'badge-error';
  }

  $length = $subscription->status == 0
      ? $subscription->pendingunits
      : $subscription->units;

  $expiredate = $subscription->expires
      ? Date::of($subscription->expires)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
      : Lang::txt('N/A');

  if ($masterAdmin) {
      $subscription->code = Lang::txt('N/A');
      $service->title = Lang::txt('COM_JOBS_NOTICE_ADMIN_UNLIMITED_ACCESS');
      $statusClass = 'badge-success';
      $status = Lang::txt('COM_JOBS_SUBSCRIPTION_STATUS_ACTIVE_ADMIN');
  }

  $shortlistUrl = Route::url('index.php?option=' . $option . '&task=resumes') . '?filterby=shortlisted';
  $resumesUrl = Route::url('index.php?option=' . $option . '&task=resumes');
  $batchUrl = Route::url('index.php?option=' . $option . '&task=batch');
  $subscribeUrl = Route::url('index.php?option=' . $option . '&task=subscribe');
  $addjobUrl = Route::url('index.php?option=' . $option . '&task=addjob');
@endphp

<x-page-container :title="$title">
  @slot('actions')
    @if($emp && !$masterAdmin)
      <a class="btn" href="{{ $shortlistUrl }}">{{ Lang::txt('COM_JOBS_SHORTLIST') }}</a>
    @endif
  @endslot

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Activities column --}}
    <div class="space-y-6">
      <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
          <h3 class="card-title">{{ Lang::txt('COM_JOBS_DASHBOARD_ACTIVITIES') }}</h3>

          {{-- Resume stats --}}
          <div class="flex items-center justify-between py-2 border-b border-base-200">
            <span class="font-medium">
              <a href="{{ $resumesUrl }}" class="link link-hover">
                {{ Lang::txt('COM_JOBS_ACTION_BROWSE_RESUMES') }}
                ({{ $stats['total_resumes'] }})
              </a>
            </span>
          </div>

          <div class="flex items-center justify-between py-2 border-b border-base-200">
            <span class="text-sm">{{ Lang::txt('COM_JOBS_DASHBOARD_SHORTLISTED') }}</span>
            <span class="flex items-center gap-2">
              <span class="font-semibold">{{ $stats['shortlisted'] }}</span>
              @if($stats['shortlisted'] > 0)
                <a href="{{ $batchUrl }}?pile=shortlisted" class="link link-primary text-xs">
                  {{ Lang::txt('COM_JOBS_DASHBOARD_DOWNLOAD') }}
                </a>
              @endif
              <a href="{{ $resumesUrl }}?filterby=shortlisted" class="link link-primary text-xs">
                {{ Lang::txt('COM_JOBS_DASHBOARD_VIEW') }}
              </a>
            </span>
          </div>

          <div class="flex items-center justify-between py-2">
            <span class="text-sm">{{ Lang::txt('COM_JOBS_DASHBOARD_APPLIED_TO_ADS') }}</span>
            <span class="flex items-center gap-2">
              <span class="font-semibold">{{ $stats['applied'] }}</span>
              @if($stats['applied'] > 0)
                <a href="{{ $batchUrl }}?pile=applied" class="link link-primary text-xs">
                  {{ Lang::txt('COM_JOBS_DASHBOARD_DOWNLOAD') }}
                </a>
              @endif
              <a href="{{ $resumesUrl }}?filterby=applied" class="link link-primary text-xs">
                {{ Lang::txt('COM_JOBS_DASHBOARD_VIEW') }}
              </a>
            </span>
          </div>
        </div>
      </div>

      {{-- Job management --}}
      <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
          <h3 class="card-title">
            {{ Lang::txt('COM_JOBS_DASHBOARD_MANAGE_ADS') }}
            ({{ count($myjobs) }})
          </h3>

          <p class="text-sm text-base-content/70">
            {{ Lang::txt('COM_JOBS_DASHBOARD_YOU_HAVE_CURRENTLY') }}
            {{ $activejobs }}
            {{ Lang::txt('COM_JOBS_DASHBOARD_PUBLISHED_ADS') }}
            @if(!$masterAdmin)
              <br>{{ $allowed_ads }} {{ Lang::txt('COM_JOBS_DASHBOARD_NUMBER_ADS_STILL_ALLOWED') }}
            @endif
          </p>

          @if(count($myjobs) > 0)
            <div class="space-y-2 mt-2">
              @foreach($myjobs as $mj)
                @php
                  $jobUrl = Route::url('index.php?option=' . $option . '&task=job&code=' . $mj->code);
                  $truncTitle = \Hubzero\Utility\Str::truncate($mj->title, 50);

                  $jobStatusLabel = '';
                  $jobBadge = '';
                  switch ($mj->status) {
                      case 0:
                          $jobStatusLabel = Lang::txt('COM_JOBS_JOB_STATUS_PENDING');
                          $jobBadge = 'badge-warning';
                          break;
                      case 1:
                          $jobStatusLabel = $mj->applications . ' ' . Lang::txt('COM_JOBS_DASHBOARD_APPLICATIONS');
                          $jobBadge = 'badge-success';
                          break;
                      case 3:
                          $jobStatusLabel = Lang::txt('COM_JOBS_JOB_STATUS_INACTIVE');
                          $jobBadge = 'badge-ghost';
                          break;
                      case 4:
                          $jobStatusLabel = Lang::txt('COM_JOBS_JOB_STATUS_DRAFT');
                          $jobBadge = 'badge-info';
                          break;
                  }
                @endphp
                <div class="flex items-center justify-between text-sm py-1 border-b border-base-200 last:border-0">
                  <div>
                    <span class="text-base-content/50 font-mono text-xs">{{ $mj->code }}</span>
                    <a href="{{ $jobUrl }}" class="link link-hover ml-1">{{ $truncTitle }}</a>
                  </div>
                  <span class="badge badge-sm {{ $jobBadge }}">{{ $jobStatusLabel }}</span>
                </div>
              @endforeach
            </div>
          @endif

          @if($subscription->status == 1 || $masterAdmin)
            <div class="mt-4">
              <a class="btn btn-primary btn-sm" href="{{ $addjobUrl }}">
                {{ Lang::txt('COM_JOBS_DASHBOARD_AD_NEW_JOB') }}
              </a>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Subscription details column --}}
    <div>
      <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
          <h3 class="card-title">
            {{ Lang::txt('COM_JOBS_SUBSCRIPTION_DETAILS') }}
          </h3>
          <p class="text-xs text-base-content/50">
            {{ Lang::txt('COM_JOBS_JOB_REFERENCE_CODE') }}: {{ $subscription->code }}
          </p>

          <dl class="space-y-3 mt-4 text-sm">
            <div>
              <dt class="font-semibold">{{ Lang::txt('COM_JOBS_SUBSCRIPTION_SERVICE') }}</dt>
              <dd>{{ $service->title }}</dd>
            </div>
            <div>
              <dt class="font-semibold">{{ Lang::txt('COM_JOBS_TABLE_STATUS') }}</dt>
              <dd><span class="badge {{ $statusClass }}">{{ $status }}</span></dd>
            </div>

            @if(!$masterAdmin)
              <div>
                <dt class="font-semibold">{{ Lang::txt('COM_JOBS_SUBSCRIPTION_LENGTH') }}</dt>
                <dd>
                  {{ $length }}-{{ $service->unitmeasure }}
                  @if($subscription->pendingunits && $subscription->status == 1)
                    <span class="badge badge-warning badge-sm ml-1">
                      {{ $subscription->pendingunits }}
                      {{ Lang::txt('COM_JOBS_ADDITIONAL') }}
                      {{ $service->unitmeasure }}(s)
                      {{ Lang::txt('COM_JOBS_MONTHS_PENDING') }}
                    </span>
                  @endif
                </dd>
              </div>
              <div>
                <dt class="font-semibold">{{ Lang::txt('COM_JOBS_SUBSCRIPTION_EXPIRE_DATE') }}</dt>
                <dd>{{ $expiredate }}</dd>
              </div>
            @endif
          </dl>

          @if(!$masterAdmin)
            <div class="mt-4">
              <a href="{{ $subscribeUrl }}" class="link link-primary text-sm">
                {{ Lang::txt('COM_JOBS_SUBSCRIPTION_EXTEND_OR_RENEW_OR_CANCEL') }}
              </a>
            </div>

            <div class="divider"></div>

            <h4 class="font-semibold text-sm">
              {{ Lang::txt('COM_JOBS_SUBSCRIPTION_EMPLOYER_INFORMATION') }}
            </h4>
            <p class="text-xs text-base-content/50 mb-2">
              {{ Lang::txt('COM_JOBS_EMPLOYER_USERNAME') }}: {{ $login }}
            </p>

            @php $unspec = Lang::txt('COM_JOBS_NOTICE_UNSPECIFIED'); @endphp
            <dl class="space-y-2 text-sm">
              <div>
                <dt class="font-semibold">{{ Lang::txt('COM_JOBS_EMPLOYER_COMPANY') }}</dt>
                <dd>{{ $employer->companyName ?: $unspec }}</dd>
              </div>
              <div>
                <dt class="font-semibold">{{ Lang::txt('COM_JOBS_EMPLOYER_LOCATION') }}</dt>
                <dd>{{ $employer->companyLocation ?: $unspec }}</dd>
              </div>
              <div>
                <dt class="font-semibold">{{ Lang::txt('COM_JOBS_EMPLOYER_WEBSITE') }}</dt>
                <dd>{{ $employer->companyWebsite ?: $unspec }}</dd>
              </div>
            </dl>
            <div class="mt-2">
              <a href="{{ $subscribeUrl }}" class="link link-primary text-sm">
                {{ Lang::txt('COM_JOBS_EMPLOYER_EDIT_INFO') }}
              </a>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</x-page-container>
