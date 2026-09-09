{{--
  Member Resume — resume management and job seeker profile.

  Variables from plugin (onMembers):
    $member    — member profile object
    $self      — boolean, viewing own profile
    $file      — resume file exists
    $resume    — resume record object
    $js        — job seeker record
    $emp       — employer flag
    $editpref  — edit preferences mode (0/1/2)
    $edittitle — edit title mode
    $path      — upload path
    $jt        — job types table
    $jc        — job categories table
    $config    — component config
    $params    — plugin params
    $option    — component option
    $stats     — view stats array

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css('jobs', 'com_jobs');
@endphp

@if ($__view->getError())
  <div class="alert alert-error mb-4" role="alert">{!! implode('<br />', $__view->getErrors()) !!}</div>
@endif

{{-- Inclusion preference --}}
@if ($self && $file)
  <div class="card bg-base-100 shadow-sm mb-6">
    <div class="card-body">
      @if ($js->active && $file)
        <p class="text-success">{{ Lang::txt('PLG_MEMBERS_RESUME_PROFILE_INCLUDED') }}</p>
      @elseif ($file)
        <p class="text-base-content/70">{{ Lang::txt('PLG_MEMBERS_RESUME_PROFILE_NOT_INCLUDED') }}</p>
      @endif

      @if (!$editpref)
        @php
          $activateOn = ($js->active && $file) ? 0 : 1;
          $activateUrl = Route::url($member->link() . '&active=resume&action=activate&on=' . $activateOn);
        @endphp
        <a class="btn btn-sm {{ $js->active && $file ? 'btn-ghost' : 'btn-primary' }} mt-2"
           href="{{ $activateUrl }}">
          @if ($js->active && $file)
            {{ Lang::txt('PLG_MEMBERS_RESUME_ACTION_HIDE') }}
          @else
            {{ Lang::txt('PLG_MEMBERS_RESUME_ACTION_INCLUDE') }}
          @endif
        </a>
      @else
        {{-- Edit preferences form --}}
        <form id="prefsForm" method="post"
              action="{{ Route::url($member->link() . '&active=resume') }}" class="space-y-4 mt-4">
          <fieldset>
            <legend class="text-sm font-medium mb-2">
              {{ $editpref == 1
                  ? Lang::txt('PLG_MEMBERS_RESUME_ACTION_INCLUDE_WITH_INFO')
                  : Lang::txt('PLG_MEMBERS_RESUME_ACTION_EDIT_PREFS') }}
            </legend>

            <div class="form-control w-full">
              <label class="label" for="tagline-men">
                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_RESUME_PERSONAL_TAGLINE') }}</span>
              </label>
              <textarea name="tagline" id="tagline-men" rows="4"
                        class="textarea textarea-bordered w-full">{{ stripslashes($js->tagline) }}</textarea>
              <div class="label">
                <span class="label-text-alt"><span id="counter_number_tagline"></span> {{ Lang::txt('PLG_MEMBERS_RESUME_CHARS_LEFT') }}</span>
              </div>
            </div>

            <div class="form-control w-full">
              <label class="label" for="lookingfor-men">
                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_RESUME_LOOKING_FOR') }}</span>
              </label>
              <textarea name="lookingfor" id="lookingfor-men" rows="4"
                        class="textarea textarea-bordered w-full">{{ stripslashes($js->lookingfor) }}</textarea>
              <div class="label">
                <span class="label-text-alt"><span id="counter_number_lookingfor"></span> {{ Lang::txt('PLG_MEMBERS_RESUME_CHARS_LEFT') }}</span>
              </div>
            </div>

            <div class="form-control w-full">
              <label class="label" for="pref-url">
                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_RESUME_WEBSITE') }}</span>
              </label>
              <input type="text" id="pref-url" name="url" class="input input-bordered w-full"
                     maxlength="190" placeholder="http://"
                     value="{{ $js->url ?: $member->get('url') }}" />
            </div>

            <div class="form-control w-full">
              <label class="label" for="pref-linkedin">
                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_RESUME_LINKEDIN') }}</span>
              </label>
              <input type="text" id="pref-linkedin" name="linkedin" class="input input-bordered w-full"
                     maxlength="190" placeholder="http://"
                     value="{{ $js->linkedin }}" />
            </div>

            @php
              $types = $jt->getTypes();
              $types[0] = Lang::txt('PLG_MEMBERS_RESUME_TYPE_ANY');
              $cats = $jc->getCats();
              $cats[0] = Lang::txt('PLG_MEMBERS_RESUME_CATEGORY_ANY');
            @endphp

            <div class="form-control w-full">
              <label class="label">
                <span class="label-text">{{ Lang::txt('PLG_MEMBERS_RESUME_POSITION_SOUGHT') }}</span>
              </label>
              <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <select name="sought_type" id="sought_type" class="select select-bordered w-full">
                  @foreach ($types as $avalue => $alabel)
                    <option value="{{ e($avalue) }}"
                      {{ ($avalue == $js->sought_type || $alabel == $js->sought_type) ? 'selected' : '' }}>
                      {{ e($alabel) }}
                    </option>
                  @endforeach
                </select>
                <select name="sought_cid" id="sought_cid" class="select select-bordered w-full">
                  @foreach ($cats as $avalue => $alabel)
                    <option value="{{ e($avalue) }}"
                      {{ ($avalue == $js->sought_cid || $alabel == $js->sought_cid) ? 'selected' : '' }}>
                      {{ e($alabel) }}
                    </option>
                  @endforeach
                </select>
              </div>
            </div>

            <div class="flex gap-2">
              <button type="submit" class="btn btn-primary">
                {{ $editpref == 1
                    ? Lang::txt('PLG_MEMBERS_RESUME_ACTION_SAVE_AND_INCLUDE')
                    : Lang::txt('PLG_MEMBERS_RESUME_ACTION_SAVE') }}
              </button>
              <a class="btn btn-ghost" href="{{ Route::url($member->link() . '&active=resume') }}">
                {{ Lang::txt('JCANCEL') }}
              </a>
            </div>

            <input type="hidden" name="activeres" value="{{ $editpref == 1 ? 1 : $js->active }}" />
            <input type="hidden" name="action" value="saveprefs" />
          </fieldset>
        </form>
      @endif
    </div>
  </div>
@endif

{{-- Seeker profile card --}}
@if ($js->active && $file)
  @php
    $seeker = $js->getSeeker($member->get('id'), User::get('id'));
  @endphp
  @if (!$seeker || count($seeker) == 0)
    <div class="alert alert-error mb-4" role="alert">{{ Lang::txt('PLG_MEMBERS_RESUME_ERROR_RETRIEVING_PROFILE') }}</div>
  @else
    {!! $__view->view('seeker')
        ->set('seeker', $seeker[0])
        ->set('emp', $emp)
        ->set('admin', 0)
        ->set('option', $option)
        ->set('params', $params)
        ->set('list', 0)
        ->loadTemplate() !!}
  @endif
@endif

{{-- Resume file table --}}
@if ($resume->id && $file && $self)
  <div class="overflow-x-auto mb-6">
    <table class="table table-zebra w-full">
      <thead>
        <tr>
          <th>{{ ucfirst(Lang::txt('PLG_MEMBERS_RESUME_RESUME')) }}</th>
          <th>{{ Lang::txt('PLG_MEMBERS_RESUME_LAST_UPDATED') }}</th>
          @if ($self)
            <th>{{ Lang::txt('PLG_MEMBERS_RESUME_OPTIONS') }}</th>
          @endif
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            @php
              $title = $resume->title ? stripslashes($resume->title) : $resume->filename;
              $defaultTitle = $member->get('firstname')
                  ? $member->get('firstname') . ' ' . $member->get('lastname') . ' ' . Lang::txt('PLG_MEMBERS_RESUME')
                  : $member->get('name') . ' ' . Lang::txt('PLG_MEMBERS_RESUME');
            @endphp
            @if ($edittitle && $self)
              <form id="editTitleForm" method="post"
                    action="{{ Route::url($member->link() . '&active=resume&action=savetitle') }}"
                    class="flex gap-2 items-center">
                <input type="text" name="title" value="{{ e($title) }}" class="input input-bordered input-sm" maxlength="40" />
                <input type="hidden" name="author" value="{{ $member->get('id') }}" />
                <button type="submit" class="btn btn-primary btn-sm">{{ Lang::txt('PLG_MEMBERS_RESUME_ACTION_SAVE') }}</button>
              </form>
            @else
              <a class="link link-hover" href="{{ Route::url($member->link() . '&active=resume&action=download') }}">
                {{ e($title) }}
              </a>
            @endif
          </td>
          <td>
            <time datetime="{{ $resume->created }}">
              {{ Date::of($resume->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
            </time>
          </td>
          <td>
            <a class="btn btn-error btn-xs"
               href="{{ Route::url($member->link() . '&active=resume&action=deleteresume') }}"
               title="{{ Lang::txt('PLG_MEMBERS_RESUME_ACTION_DELETE_THIS_RESUME') }}">
              {{ Lang::txt('PLG_MEMBERS_RESUME_ACTION_DELETE') }}
            </a>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
@elseif (!$js->active)
  <p class="text-base-content/60 mb-4">
    {{ !$self
        ? Lang::txt('PLG_MEMBERS_RESUME_USER_HAS_NO_RESUME')
        : Lang::txt('PLG_MEMBERS_RESUME_YOU_HAVE_NO_RESUME') }}
  </p>
@endif

{{-- Upload form --}}
@if ($self)
  <div class="card bg-base-100 shadow-sm mb-6">
    <div class="card-body">
      <form method="post" action="{{ Route::url($member->link() . '&active=resume') }}"
            enctype="multipart/form-data" class="space-y-4">
        <h4 class="card-title text-base">
          @if ($resume->id && $file)
            {!! Lang::txt('PLG_MEMBERS_RESUME_ACTION_UPLOAD_NEW_RESUME') !!}
            <span class="text-sm text-base-content/50">({{ Lang::txt('PLG_MEMBERS_RESUME_WILL_BE_REPLACED') }})</span>
          @else
            {{ Lang::txt('PLG_MEMBERS_RESUME_ACTION_UPLOAD_A_RESUME') }}
          @endif
        </h4>

        <div class="form-control w-full">
          <label class="label" for="uploadres">
            <span class="label-text">{{ Lang::txt('PLG_MEMBERS_RESUME_ACTION_ATTACH_FILE') }}</span>
          </label>
          <input type="file" name="uploadres" id="uploadres" class="file-input file-input-bordered w-full" />
        </div>

        {!! Html::input('token') !!}
        <input type="hidden" name="action" value="uploadresume" />
        <input type="hidden" name="path" value="{{ e($path) }}" />
        <input type="hidden" name="emp" value="{{ e($emp) }}" />

        <button type="submit" class="btn btn-primary">
          {{ Lang::txt('PLG_MEMBERS_RESUME_ACTION_UPLOAD') }}
        </button>
      </form>
    </div>
  </div>
@endif

{{-- Sidebar content --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
  <div class="md:col-span-2"></div>
  <div class="space-y-4">
    <div class="card bg-base-100 shadow-sm">
      <div class="card-body">
        <p class="text-base-content/70">
          {{ $self
              ? Lang::txt('PLG_MEMBERS_RESUME_HUB_OFFERS')
              : Lang::txt('PLG_MEMBERS_RESUME_NOTICE_YOU_ARE_EMPLOYER') }}
        </p>
        @php
          $industry = $config->get('industry');
          $jobsLabel = $industry
              ? Lang::txt('PLG_MEMBERS_RESUME_VIEW_JOBS_IN', $industry)
              : Lang::txt('PLG_MEMBERS_RESUME_VIEW_JOBS');
        @endphp
        <a class="btn btn-primary btn-sm mt-2" href="{{ Route::url('index.php?option=com_jobs') }}">
          {{ $jobsLabel }}
        </a>
      </div>
    </div>

    @if ($self && $js->active)
      <div class="card bg-base-100 shadow-sm">
        <div class="card-body">
          <h4 class="card-title text-sm">{{ Lang::txt('PLG_MEMBERS_RESUME_YOUR_STATS') }}</h4>
          <div class="overflow-x-auto">
            <table class="table table-sm w-full">
              <tbody>
                <tr>
                  <th scope="row">{{ Lang::txt('PLG_MEMBERS_RESUME_TOTAL_VIEWED') }}</th>
                  <td class="text-right">{{ $stats['totalviewed'] }}</td>
                </tr>
                <tr>
                  <th scope="row">{{ Lang::txt('PLG_MEMBERS_RESUME_VIEWED_PAST_30_DAYS') }}</th>
                  <td class="text-right">{{ $stats['viewed_thismonth'] }}</td>
                </tr>
                <tr>
                  <th scope="row">{{ Lang::txt('PLG_MEMBERS_RESUME_VIEWED_PAST_7_DAYS') }}</th>
                  <td class="text-right">{{ $stats['viewed_thisweek'] }}</td>
                </tr>
                <tr>
                  <th scope="row">{{ Lang::txt('PLG_MEMBERS_RESUME_VIEWED_PAST_24_HOURS') }}</th>
                  <td class="text-right">{{ $stats['viewed_today'] }}</td>
                </tr>
                <tr>
                  <th scope="row">{{ Lang::txt('PLG_MEMBERS_RESUME_PROFILE_SHORTLISTED') }}</th>
                  <td class="text-right">{{ $stats['shortlisted'] }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    @endif
  </div>
</div>
