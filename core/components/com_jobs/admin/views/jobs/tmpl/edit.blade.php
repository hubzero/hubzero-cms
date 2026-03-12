{{--
  Job — Admin edit view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;

  $canDo = \Components\Jobs\Helpers\Permissions::getActions('job');

  $text = ($task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

  $now = Date::toSql();

  $usonly = $config->get('usonly');
  $row->companyLocationCountry = !$isnew
      ? $row->companyLocationCountry
      : Lang::txt('COM_JOBS_USA');
  $row->code = !$isnew
      ? $row->code
      : Lang::txt('COM_JOBS_ISNEW');

  $startdate = ($row->startdate && $row->startdate != '0000-00-00 00:00:00')
      ? Date::of($row->startdate)->toLocal('Y-m-d')
      : '';
  $closedate = ($row->closedate && $row->closedate != '0000-00-00 00:00:00')
      ? Date::of($row->closedate)->toLocal('Y-m-d')
      : '';
  $expiredate = ($row->expiredate && $row->expiredate != '0000-00-00 00:00:00')
      ? Date::of($row->expiredate)->toLocal('Y-m-d')
      : '';
  $opendate = ($row->opendate && $row->opendate != '0000-00-00 00:00:00')
      ? Date::of($row->opendate)->toLocal('Y-m-d')
      : '';

  $status = (!$isnew) ? $row->status : 4;
  $employerid = ($task != 'edit') ? 1 : $job->employerid;

  $expired = $subscription->expires && $subscription->expires < $now ? 1 : 0;

  // Status text
  switch ($row->status) {
      case 0:
          $statusText  = Lang::txt('COM_JOBS_STATUS_PENDING');
          $statusClass = 'badge-warning';
          break;
      case 1:
          $statusText  = $expired
              ? Lang::txt('COM_JOBS_STATUS_EXPIRED')
              : Lang::txt('COM_JOBS_STATUS_ACTIVE');
          $statusClass = $expired ? 'badge-error' : 'badge-success';
          break;
      case 2:
          $statusText  = Lang::txt('COM_JOBS_STATUS_DELETED');
          $statusClass = 'badge-ghost';
          break;
      case 3:
          $statusText  = Lang::txt('COM_JOBS_STATUS_INACTIVE');
          $statusClass = 'badge-ghost';
          break;
      case 4:
          $statusText  = Lang::txt('COM_JOBS_STATUS_DRAFT');
          $statusClass = 'badge-info';
          break;
      default:
          $statusText  = '-';
          $statusClass = '';
          break;
  }
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_JOBS') }}: {{ $text }}"
    icon="job"
    :canDo="$canDo"
    option="{{ $option }}"
    :edit="true"
/>

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  {{-- Company fieldset --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_JOBS_FIELDSET_COMPANY') }}">

      <div class="admin-field">
        <label for="companyName" class="label">
          {{ Lang::txt('COM_JOBS_FIELD_NAME') }} <span class="text-error">*</span>
        </label>
        <input type="text"
               name="companyName"
               id="companyName"
               class="input input-bordered w-full"
               maxlength="200"
               required
               value="{{ $row->companyName }}" />
      </div>

      <div class="admin-field">
        <label for="companyWebsite" class="label">
          {{ Lang::txt('COM_JOBS_FIELD_URL') }}
        </label>
        <input type="text"
               name="companyWebsite"
               id="companyWebsite"
               class="input input-bordered w-full"
               maxlength="200"
               value="{{ $row->companyWebsite }}" />
      </div>

      <div class="admin-field">
        <label for="companyLocation" class="label">
          {{ Lang::txt('COM_JOBS_FIELD_LOCATION') }} <span class="text-error">*</span>
        </label>
        <input type="text"
               name="companyLocation"
               id="companyLocation"
               class="input input-bordered w-full"
               maxlength="200"
               required
               value="{{ $row->companyLocation }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_JOBS_FIELD_LOCATION_HINT') }}</p>
      </div>

  </x-admin-fieldset>

  {{-- Job Details fieldset --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_JOBS_FIELDSET_JOB') }}">

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="admin-field">
          <label for="cid" class="label">{{ Lang::txt('COM_JOBS_FIELD_CATEGORY') }}</label>
          <select name="cid" id="cid" class="select select-bordered w-full">
            @foreach($cats as $catId => $catLabel)
              <option value="{{ $catId }}" @selected($catId == $row->cid)>
                {{ $catLabel }}
              </option>
            @endforeach
          </select>
        </div>

        <div class="admin-field">
          <label for="type" class="label">{{ Lang::txt('COM_JOBS_FIELD_TYPE') }}</label>
          <select name="type" id="type" class="select select-bordered w-full">
            @foreach($types as $typeId => $typeLabel)
              <option value="{{ $typeId }}" @selected($typeId == $row->type)>
                {{ $typeLabel }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="admin-field">
        <label for="companyLocationCountry" class="label">
          {{ Lang::txt('COM_JOBS_FIELD_COUNTRY') }}
        </label>
        @if($usonly)
          <p class="text-sm">{{ Lang::txt('COM_JOBS_USA') }}</p>
          <p class="text-xs text-muted-foreground">{{ Lang::txt('COM_JOBS_USA_HINT') }}</p>
          <input type="hidden" id="companyLocationCountry" name="companyLocationCountry" value="us" />
        @else
          <select name="companyLocationCountry"
                  id="companyLocationCountry"
                  class="select select-bordered w-full">
            <option value="">{{ Lang::txt('COM_JOBS_SELECT') }}</option>
            @foreach(\Hubzero\Geocode\Geocode::countries() as $country)
              <option value="{{ $country->name }}"
                      @selected($country->name == $row->companyLocationCountry)>
                {{ $country->name }}
              </option>
            @endforeach
          </select>
        @endif
      </div>

      <div class="admin-field">
        <label for="title" class="label">
          {{ Lang::txt('COM_JOBS_FIELD_TITLE') }} <span class="text-error">*</span>
        </label>
        <input type="text"
               name="title"
               id="title"
               class="input input-bordered w-full"
               maxlength="200"
               required
               value="{{ $row->title }}" />
      </div>

      <div class="admin-field">
        <label for="description" class="label">
          {{ Lang::txt('COM_JOBS_FIELD_DESCRIPTION') }}
        </label>
        {!! $__view->editor('description', e($row->description), 50, 30, 'description') !!}
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_JOBS_FIELD_DESCRIPTION_HINT') }}</p>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="admin-field">
          <label for="startdate" class="label">{{ Lang::txt('COM_JOBS_FIELD_STARTDATE') }}</label>
          <input type="date"
                 name="startdate"
                 id="startdate"
                 class="input input-bordered w-full"
                 value="{{ $startdate }}" />
        </div>

        <div class="admin-field">
          <label for="closedate" class="label">{{ Lang::txt('COM_JOBS_FIELD_DUEDATE') }}</label>
          <input type="date"
                 name="closedate"
                 id="closedate"
                 class="input input-bordered w-full"
                 value="{{ $closedate }}" />
        </div>

        <div class="admin-field">
          <label for="expiredate" class="label">{{ Lang::txt('COM_JOBS_FIELD_EXPIREDATE') }}</label>
          <input type="date"
                 name="expiredate"
                 id="expiredate"
                 class="input input-bordered w-full"
                 value="{{ $expiredate }}" />
        </div>
      </div>

      <div class="admin-field">
        <label for="applyExternalUrl" class="label">
          {{ Lang::txt('COM_JOBS_FIELD_EXTERNAL_URL') }}
        </label>
        <input type="text"
               name="applyExternalUrl"
               id="applyExternalUrl"
               class="input input-bordered w-full"
               maxlength="100"
               value="{{ $row->applyExternalUrl }}" />
        <p class="text-xs text-muted-foreground mt-1">{{ Lang::txt('COM_JOBS_FIELD_EXTERNAL_URL_HINT') }}</p>
      </div>

      <div class="admin-field">
        <label class="label cursor-pointer justify-start gap-3">
          <input type="checkbox"
                 name="applyInternal"
                 id="applyInternal"
                 class="checkbox checkbox-sm"
                 value="1"
                 @checked($row->applyInternal) />
          <span>{{ Lang::txt('COM_JOBS_FIELD_APPLY_INTERNAL') }}</span>
        </label>
      </div>

  </x-admin-fieldset>

  {{-- Contact Info fieldset --}}
  <x-admin-fieldset legend="{{ Lang::txt('COM_JOBS_FIELDSET_CONTACT_INFO') }}">

      <div class="admin-field">
        <label for="contactName" class="label">{{ Lang::txt('COM_JOBS_FIELD_CONTACT_NAME') }}</label>
        <input type="text"
               name="contactName"
               id="contactName"
               class="input input-bordered w-full"
               maxlength="100"
               value="{{ $row->contactName }}" />
      </div>

      <div class="admin-field">
        <label for="contactEmail" class="label">{{ Lang::txt('COM_JOBS_FIELD_CONTACT_EMAIL') }}</label>
        <input type="text"
               name="contactEmail"
               id="contactEmail"
               class="input input-bordered w-full"
               maxlength="100"
               value="{{ $row->contactEmail }}" />
      </div>

      <div class="admin-field">
        <label for="contactPhone" class="label">{{ Lang::txt('COM_JOBS_FIELD_CONTACT_PHONE') }}</label>
        <input type="text"
               name="contactPhone"
               id="contactPhone"
               class="input input-bordered w-full"
               maxlength="100"
               value="{{ $row->contactPhone }}" />
      </div>

  </x-admin-fieldset>

  @slot('sidebar')
    {{-- Metadata --}}
    @if($row->id)
      <x-admin-fieldset legend="{{ Lang::txt('JDETAILS') }}">
          <table class="admin-meta">
            <tbody>
              <tr>
                <td>{{ Lang::txt('COM_JOBS_FIELD_CREATED') }}</td>
                <td>{{ $row->added }}</td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_JOBS_FIELD_CREATOR') }}</td>
                <td>
                  {{ $row->addedBy }}
                  @if($job->employerid == 1)
                    <span class="badge badge-sm badge-primary">{{ Lang::txt('COM_JOBS_ADMIN') }}</span>
                  @endif
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_JOBS_FIELD_MODIFIED') }}</td>
                <td>
                  {{ ($job->edited && $job->edited != '0000-00-00 00:00:00')
                      ? $job->edited
                      : Lang::txt('COM_JOBS_NOT_APPLICABLE') }}
                </td>
              </tr>
              <tr>
                <td>{{ Lang::txt('COM_JOBS_FIELD_MODIFIER') }}</td>
                <td>
                  {{ $job->editedBy ?: Lang::txt('COM_JOBS_NOT_APPLICABLE') }}
                </td>
              </tr>
              @if(isset($subscription->id))
                <tr>
                  <td>{{ Lang::txt('COM_JOBS_FIELD_USER_SUBSCRIPTION') }}</td>
                  <td>
                    {{ $subscription->code }}
                    @if(!$job->inactive)
                      {{ Lang::txt('COM_JOBS_FIELD_USER_SUBSCRIPTION_EXPIRES', $subscription->expires) }}
                    @endif
                  </td>
                </tr>
              @endif
              <tr>
                <td>{{ Lang::txt('COM_JOBS_FIELD_STATUS') }}</td>
                <td><span class="badge badge-sm {{ $statusClass }}">{{ $statusText }}</span></td>
              </tr>
              @if($opendate)
                <tr>
                  <td>{{ Lang::txt('COM_JOBS_FIELD_AD_PUBLISHED') }}</td>
                  <td>{{ $row->opendate }}</td>
                </tr>
              @endif
            </tbody>
          </table>
      </x-admin-fieldset>
    @endif

    {{-- Manage --}}
    <x-admin-fieldset legend="{{ Lang::txt('COM_JOBS_FIELDSET_MANAGE') }}">
        @if(!$isnew)
          <fieldset class="space-y-2 mb-4">
            <legend class="text-sm font-semibold mb-2">{{ Lang::txt('COM_JOBS_FIELDSET_TAKE_ACTION') }}</legend>

            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" name="action" value="message" class="radio radio-sm" checked />
              <span class="text-sm">{{ Lang::txt('COM_JOBS_FIELD_ACTION_NONE') }}</span>
            </label>

            @if($row->status != 1)
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="action" value="publish" class="radio radio-sm" />
                <span class="text-sm">{{ Lang::txt('COM_JOBS_FIELD_ACTION_PUBLISH') }}</span>
              </label>
            @else
              <label class="flex items-center gap-2 cursor-pointer">
                <input type="radio" name="action" value="unpublish" class="radio radio-sm" />
                <span class="text-sm">{{ Lang::txt('COM_JOBS_FIELD_ACTION_UNPUBLISH') }}</span>
              </label>
            @endif

            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" name="action" value="delete" class="radio radio-sm" />
              <span class="text-sm">{{ Lang::txt('COM_JOBS_FIELD_ACTION_DELETE') }}</span>
            </label>
          </fieldset>

          <div class="admin-field">
            <label for="message" class="label">{{ Lang::txt('COM_JOBS_FIELD_MESSAGE') }}</label>
            <textarea name="message"
                      id="message"
                      class="textarea textarea-bordered w-full"
                      rows="5"></textarea>
          </div>
        @else
          <p class="text-sm text-muted-foreground">{{ Lang::txt('COM_JOBS_WARNING_MUST_SAVE_FIRST') }}</p>
        @endif
    </x-admin-fieldset>
  @endslot

  <input type="hidden" name="id" value="{{ $row->id }}" />
  <input type="hidden" name="isnew" value="{{ $isnew }}" />
  <input type="hidden" name="employerid" value="{{ $employerid }}" />
  <input type="hidden" name="status" value="{{ $status }}" />
</x-admin-edit>
