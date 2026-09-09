{{--
  Post new job / edit existing job form.

  Variables from controller (editjobTask):
    $title    — Page title string
    $config   — Component params (Registry)
    $option   — Component option string
    $emp      — Employer privileges flag
    $admin    — Admin privileges flag
    $job      — Job object
    $jobid    — Job ID (0 for new)
    $uid      — Current user ID
    $employer — Employer object
    $profile  — User profile object
    $task     — Current task (addjob or editjob)
    $cats     — Categories array [id => name]
    $types    — Types array [id => name]

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $model = new \Components\Jobs\Models\Job($job);

  $job->title = trim(stripslashes($job->title));
  $job->description = $model->content('raw');
  $job->companyLocation = $jobid ? $job->companyLocation : $employer->companyLocation;

  $hubzero_Geo = new \Hubzero\Geocode\Geocode();
  $countries = $hubzero_Geo->countries();

  $job->companyLocationCountry = $jobid
      ? $job->companyLocationCountry
      : e($hubzero_Geo->getcountry($profile->get('countryresident')));
  $job->companyName = $jobid ? $job->companyName : $employer->companyName;
  $job->companyWebsite = $jobid ? $job->companyWebsite : $employer->companyWebsite;

  $usonly = $config->get('usonly', 0);

  $startdate = ($job->startdate && $job->startdate != '0000-00-00 00:00:00')
      ? Date::of($job->startdate)->toLocal('Y-m-d 00:00:00')
      : '';
  $closedate = ($job->closedate && $job->closedate != '0000-00-00 00:00:00')
      ? Date::of($job->closedate)->toLocal('Y-m-d 00:00:00')
      : '';
  $defaultExpire = $config->get('expiry', 0)
      ? Date::of(strtotime('180 days'))->toLocal('Y-m-d 00:00:00')
      : '';
  $expiredate = ($job->expiredate && $job->expiredate != '0000-00-00 00:00:00')
      ? Date::of($job->expiredate)->toLocal('Y-m-d 00:00:00')
      : $defaultExpire;

  $status = ($task ?? '') != 'addjob' ? $job->status : 4;

  $dashboardUrl = Route::url('index.php?option=' . $option . '&task=dashboard');
  $shortlistUrl = Route::url('index.php?option=' . $option . '&task=resumes&filterby=shortlisted');
  $formUrl = Route::url('index.php?option=' . $option);

  $submitLabel = (($task ?? '') == 'addjob' || $job->status == 4)
      ? Lang::txt('COM_JOBS_ACTION_SAVE_PREVIEW')
      : Lang::txt('COM_JOBS_ACTION_SAVE');
@endphp

<x-page-container :title="$title" bodyClass="edit-form">
  @slot('actions')
    @if($emp)
      <a class="btn" href="{{ $dashboardUrl }}">{{ Lang::txt('COM_JOBS_EMPLOYER_DASHBOARD') }}</a>
      <a class="btn" href="{{ $shortlistUrl }}">{{ Lang::txt('COM_JOBS_SHORTLIST') }}</a>
    @else
      <a class="btn" href="{{ $dashboardUrl }}">{{ Lang::txt('COM_JOBS_ADMIN_DASHBOARD') }}</a>
    @endif
  @endslot

  @if($__view->getError())
    <div role="alert" class="alert alert-error mb-4">
      <span>{{ $__view->getError() }}</span>
    </div>
  @endif

  <form id="hubForm" method="post" action="{{ $formUrl }}">
    <input type="hidden" name="task" value="savejob" />
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="code" value="{{ $job->code }}" />
    <input type="hidden" name="id" value="{{ $jobid }}" />
    <input type="hidden" name="status" value="{{ $status }}" />
    <input type="hidden" name="employerid" value="{{ $uid }}" />

    <x-form-section :heading="Lang::txt('COM_JOBS_EDITJOB_JOB_OVERVIEW')">
      <x-form-field name="title"
                    :label="Lang::txt('COM_JOBS_EDITJOB_JOB_TITLE')"
                    :required="true">
        <input type="text" class="input input-bordered w-full"
               name="title" id="title" maxlength="190"
               value="{{ e($job->title) }}" required />
      </x-form-field>

      <x-form-field name="companyLocation"
                    :label="Lang::txt('COM_JOBS_EDITJOB_JOB_LOCATION')"
                    :required="true">
        <input type="text" class="input input-bordered w-full"
               name="companyLocation" id="companyLocation" maxlength="190"
               value="{{ e(stripslashes($job->companyLocation)) }}" required />
      </x-form-field>

      @if($usonly == 0 && !empty($countries))
        <x-form-field name="companyLocationCountry"
                      :label="Lang::txt('COM_JOBS_EDITJOB_COUNTRY')"
                      :required="true">
          <select name="companyLocationCountry" id="companyLocationCountry"
                  class="select select-bordered w-full" required>
            <option value="">{{ Lang::txt('COM_JOBS_OPTION_SELECT_FROM_LIST') }}</option>
            @php
              $selectedCountry = $job->companyLocationCountry ?: 'United States';
            @endphp
            @foreach($countries as $country)
              <option value="{{ e($country->name) }}"
                      {{ strtoupper($country->name) == strtoupper($selectedCountry) ? 'selected' : '' }}>
                {{ e($country->name) }}
              </option>
            @endforeach
          </select>
        </x-form-field>
      @else
        <div role="alert" class="alert alert-warning mb-2">
          <span>{{ Lang::txt('COM_JOBS_EDITJOB_US_ONLY') }}</span>
        </div>
        <input type="hidden" name="companyLocationCountry" value="us" />
      @endif

      <x-form-field name="companyName"
                    :label="Lang::txt('COM_JOBS_EMPLOYER_COMPANY_NAME')"
                    :required="true">
        <input type="text" class="input input-bordered w-full"
               name="companyName" id="companyName" maxlength="120"
               value="{{ e(stripslashes($job->companyName)) }}" required />
      </x-form-field>

      <x-form-field name="companyWebsite"
                    :label="Lang::txt('COM_JOBS_EMPLOYER_COMPANY_WEBSITE')"
                    :hint="Lang::txt('COM_JOBS_EDITJOB_HINT_COMPANY')">
        <input type="text" class="input input-bordered w-full"
               name="companyWebsite" id="companyWebsite" maxlength="190"
               value="{{ e(stripslashes($job->companyWebsite)) }}" />
      </x-form-field>
    </x-form-section>

    <x-form-section :heading="Lang::txt('COM_JOBS_EDITJOB_JOB_DESCRIPTION')">
      <x-form-field name="description"
                    :label="Lang::txt('COM_JOBS_EDITJOB_JOB_DESCRIPTION')"
                    :required="true"
                    :hint="Lang::txt('COM_JOBS_EDITJOB_DESC_INFO')">
        {!! $__view->editor('description', e($job->description), 50, 25, 'description') !!}
      </x-form-field>
    </x-form-section>

    <x-form-section :heading="Lang::txt('COM_JOBS_EDITJOB_JOB_SPECIFICS')">
      <x-form-field name="cid" :label="Lang::txt('COM_JOBS_EDITJOB_CATEGORY')">
        {!! \Components\Jobs\Helpers\Html::formSelect('cid', $cats, $job->cid, 'select select-bordered w-full') !!}
      </x-form-field>

      <x-form-field name="type" :label="Lang::txt('COM_JOBS_EDITJOB_TYPE')">
        {!! \Components\Jobs\Helpers\Html::formSelect('type', $types, $job->type, 'select select-bordered w-full') !!}
      </x-form-field>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-form-field name="startdate"
                      :label="Lang::txt('COM_JOBS_EDITJOB_START_DATE')"
                      :hint="Lang::txt('COM_JOBS_EDITJOB_HINT_DATE_FORMAT')">
          <input type="text" class="input input-bordered w-full"
                 name="startdate" id="startdate" maxlength="10"
                 value="{{ $startdate }}" />
        </x-form-field>

        <x-form-field name="closedate"
                      :label="Lang::txt('COM_JOBS_EDITJOB_CLOSE_DATE')"
                      :hint="Lang::txt('COM_JOBS_EDITJOB_HINT_DATE_FORMAT')">
          <input type="text" class="input input-bordered w-full"
                 name="closedate" id="closedate" maxlength="10"
                 value="{{ $closedate }}" />
        </x-form-field>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <x-form-field name="expiredate"
                      :label="Lang::txt('COM_JOBS_EDITJOB_EXPIRE_DATE')"
                      :required="true"
                      :hint="Lang::txt('COM_JOBS_EDITJOB_HINT_MAX_DATE')">
          <input type="text" class="input input-bordered w-full"
                 name="expiredate" id="expiredate" maxlength="10"
                 value="{{ $expiredate }}" required />
        </x-form-field>

        <div>
          <x-form-field name="applyExternalUrl"
                        :label="Lang::txt('COM_JOBS_EDITJOB_EXTERNAL_URL')">
            <input type="text" class="input input-bordered w-full"
                   name="applyExternalUrl" id="applyExternalUrl" maxlength="250"
                   value="{{ e(stripslashes($job->applyExternalUrl)) }}" />
          </x-form-field>

          <x-form-field name="applyInternal"
                        :label="Lang::txt('COM_JOBS_EDITJOB_ALLOW_INTERNAL_APPLICATION')"
                        type="checkbox">
            <input type="checkbox" class="checkbox checkbox-sm"
                   name="applyInternal" id="applyInternal" value="1"
                   {{ $job->applyInternal ? 'checked' : '' }} />
          </x-form-field>
        </div>
      </div>
    </x-form-section>

    <x-form-section :heading="Lang::txt('COM_JOBS_EDITJOB_CONTACT_INFO') . ' (' . Lang::txt('COM_JOBS_OPTIONAL') . ')'">
      @php
        $contactNameVal = $job->contactName
            ? e(stripslashes($job->contactName))
            : e(stripslashes($profile->get('name')));
        $contactEmailVal = $job->contactEmail
            ? e(stripslashes($job->contactEmail))
            : e(stripslashes($profile->get('email')));
        $contactPhoneVal = $job->contactPhone
            ? e(stripslashes($job->contactPhone))
            : e(stripslashes($profile->get('phone')));
      @endphp
      <x-form-field name="contactName" :label="Lang::txt('COM_JOBS_EDITJOB_CONTACT_NAME')">
        <input type="text" class="input input-bordered w-full"
               name="contactName" id="contactName" maxlength="100"
               value="{{ $contactNameVal }}" />
      </x-form-field>

      <x-form-field name="contactEmail" :label="Lang::txt('COM_JOBS_EDITJOB_CONTACT_EMAIL')">
        <input type="text" class="input input-bordered w-full"
               name="contactEmail" id="contactEmail" maxlength="100"
               value="{{ $contactEmailVal }}" />
      </x-form-field>

      <x-form-field name="contactPhone" :label="Lang::txt('COM_JOBS_EDITJOB_CONTACT_PHONE')">
        <input type="text" class="input input-bordered w-full"
               name="contactPhone" id="contactPhone" maxlength="100"
               value="{{ $contactPhoneVal }}" />
      </x-form-field>
    </x-form-section>

    <div class="form-actions">
      <button class="btn btn-primary" type="submit">{{ $submitLabel }}</button>
      <a class="btn btn-ghost" href="{{ $dashboardUrl }}">{{ Lang::txt('JCANCEL') }}</a>
    </div>
  </form>
</x-page-container>
