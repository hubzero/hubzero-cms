{{--
  Job listing table partial — used by both default and latest layouts.

  Variables:
    $jobs    — Array of job objects
    $filters — Filter/sort settings
    $config  — Component params
    $option  — Component option string
    $admin   — Admin flag
    $emp     — Employer flag
    $mini    — Mini mode (latest postings, no admin columns)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $database = App::get('db');
  $jt = new \Components\Jobs\Tables\Type($database);
  $jc = new \Components\Jobs\Tables\Category($database);

  $maxscore = ($filters['search'] ?? false) && isset($jobs[0]) && $jobs[0]->keywords > 0
      ? $jobs[0]->keywords
      : 1;
@endphp

<div class="overflow-x-auto">
  <table class="table table-zebra w-full">
    <thead>
      <tr>
        <th>{{ Lang::txt('COM_JOBS_TABLE_JOB_TITLE') }}</th>
        @if($admin && !$emp && !$mini)
          <th>{{ Lang::txt('COM_JOBS_TABLE_STATUS') }}</th>
        @endif
        <th>{{ Lang::txt('COM_JOBS_TABLE_COMPANY') }}</th>
        <th class="hidden md:table-cell">{{ Lang::txt('COM_JOBS_TABLE_LOCATION') }}</th>
        <th class="hidden lg:table-cell">{{ Lang::txt('COM_JOBS_TABLE_CATEGORY') }}</th>
        <th class="hidden lg:table-cell">{{ Lang::txt('COM_JOBS_TABLE_TYPE') }}</th>
        <th class="hidden sm:table-cell">{{ Lang::txt('COM_JOBS_TABLE_POSTED') }}</th>
        <th>{{ Lang::txt('COM_JOBS_TABLE_APPLY_BY') }}</th>
        @if($filters['search'] ?? false)
          <th>{{ Lang::txt('COM_JOBS_TABLE_RELEVANCE') }}</th>
        @endif
      </tr>
    </thead>
    <tbody>
      @foreach($jobs as $job)
        @php
          $model = new \Components\Jobs\Models\Job($job);

          $jobClose = $job->closedate;
          $hasCloseDate = ($jobClose && $jobClose != '0000-00-00 00:00:00');
          $closedate = $hasCloseDate
              ? Date::of($jobClose)->toLocal('d M y')
              : 'ASAP';
          if ($hasCloseDate && $jobClose < Date::toSql()) {
              $closedate = Lang::txt('COM_JOBS_EXPIRED');
          }

          $curtype = $jt->getType($job->type);
          $curcat = $jc->getCat($job->cid);

          $relscore = ($filters['search'] ?? false) && $job->keywords > 0
              ? floor(($job->keywords * 100) / $maxscore)
              : 0;

          $jobUrl = Route::url(
              'index.php?option=' . $option . '&task=job&code=' . $job->code
          );

          // Status for admin view
          $statusLabel = '';
          $statusBadge = '';
          if ($admin && !$emp && !$mini) {
              switch ($job->status) {
                  case 0:
                      $statusLabel = Lang::txt('COM_JOBS_JOB_STATUS_PENDING');
                      $statusBadge = 'badge-warning';
                      break;
                  case 1:
                      $isInvalid = $job->inactive && $job->inactive < Date::toSql();
                      $statusLabel = $isInvalid
                          ? Lang::txt('COM_JOBS_JOB_STATUS_INVALID')
                          : Lang::txt('COM_JOBS_JOB_STATUS_ACTIVE');
                      $statusBadge = $isInvalid ? 'badge-error' : 'badge-success';
                      break;
                  case 3:
                      $statusLabel = Lang::txt('COM_JOBS_JOB_STATUS_INACTIVE');
                      $statusBadge = 'badge-ghost';
                      break;
                  case 4:
                      $statusLabel = Lang::txt('COM_JOBS_JOB_STATUS_DRAFT');
                      $statusBadge = 'badge-info';
                      break;
              }
          }
        @endphp
        <tr>
          <td>
            <a href="{{ $jobUrl }}" class="link link-hover font-medium"
               title="{{ $model->content('clean', 250) }}">
              {{ $job->title }}
            </a>
          </td>
          @if($admin && !$emp && !$mini)
            <td>
              <span class="badge badge-sm {{ $statusBadge }}">{{ $statusLabel }}</span>
            </td>
          @endif
          <td>{{ $job->companyName }}</td>
          <td class="hidden md:table-cell">
            {{ $job->companyLocation }}@if($job->companyLocationCountry), {{ $job->companyLocationCountry }}@endif
          </td>
          <td class="hidden lg:table-cell text-base-content/60">{{ $curcat }}</td>
          <td class="hidden lg:table-cell text-base-content/60">{{ $curtype }}</td>
          <td class="hidden sm:table-cell text-base-content/60">
            {{ Date::of($job->added)->toLocal('d M y') }}
          </td>
          <td>
            @if($job->applied)
              <span class="badge badge-success badge-sm">
                {{ Lang::txt('COM_JOBS_JOB_APPLIED_ON') }}
                {{ Date::of($job->applied)->toLocal('d M y') }}
              </span>
            @elseif($job->withdrawn)
              <span class="badge badge-warning badge-sm">
                {{ Lang::txt('COM_JOBS_JOB_WITHDREW_ON') }}
                {{ Date::of($job->withdrawn)->toLocal('d M y') }}
              </span>
            @else
              {{ $closedate }}
            @endif
          </td>
          @if($filters['search'] ?? false)
            <td>
              <span @class(['font-semibold', 'text-success' => $relscore > 0])>
                {{ $relscore }} %
              </span>
            </td>
          @endif
        </tr>
      @endforeach
    </tbody>
  </table>
</div>
