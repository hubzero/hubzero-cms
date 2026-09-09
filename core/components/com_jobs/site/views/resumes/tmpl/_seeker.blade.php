{{--
  Individual seeker/resume card partial.

  Variables:
    $seeker — Seeker object
    $emp    — Employer flag
    $admin  — Admin flag
    $option — Component option (com_members)
    $params — Plugin params (Registry)
    $list   — 1 if in list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Filesystem;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $database = App::get('db');
  $jt = new \Components\Jobs\Tables\Type($database);
  $jc = new \Components\Jobs\Tables\Category($database);
  $profile = \Components\Members\Models\Member::oneOrNew($seeker->uid);

  $jobtype = $jt->getType($seeker->sought_type, strtolower(Lang::txt('COM_JOBS_TYPE_ANY')));
  $jobcat = $jc->getCat($seeker->sought_cid, strtolower(Lang::txt('COM_JOBS_CATEGORY_ANY')));

  $title = Lang::txt('COM_JOBS_ACTION_DOWNLOAD') . ' ' . $seeker->name . ' ' . ucfirst(Lang::txt('COM_JOBS_RESUME'));

  $base_path = DS . trim($params->get('webpath', '/site/members'), DS);
  $path = $base_path . DS . \Hubzero\Utility\Str::pad($seeker->uid);
  if (!is_dir(PATH_APP . $path)) {
      if (!Filesystem::makeDirectory(PATH_APP . $path)) {
          $path = '';
      }
  }
  $resume = is_file(PATH_APP . $path . DS . $seeker->filename)
      ? $path . DS . $seeker->filename
      : '';

  $profileUrl = Route::url('index.php?option=' . $option . '&id=' . $seeker->uid . '&active=resume');
  $isShortlisted = isset($seeker->shortlisted) && $seeker->shortlisted;
@endphp

<div @class([
    'card bg-base-100 shadow-sm',
    'border-l-4 border-primary' => $seeker->mine ?? false,
    'border-l-4 border-warning' => $isShortlisted && !($seeker->mine ?? false),
])>
  <div class="card-body p-4">
    <div class="flex gap-4">
      {{-- Avatar --}}
      <div class="avatar shrink-0">
        <div class="w-12 h-12 rounded-full">
          <img src="{{ $profile->picture() }}" alt="{{ $seeker->name }}" />
        </div>
      </div>

      {{-- Info --}}
      <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-start justify-between gap-2">
          <div>
            @if($list)
              <a href="{{ $profileUrl }}" class="link link-hover font-semibold">{{ $seeker->name }}</a>
            @else
              <span class="font-semibold">{{ $seeker->name }}</span>
            @endif
            @if($seeker->countryresident)
              <span class="text-sm text-base-content/60">, {{ e($seeker->countryresident) }}</span>
            @endif
          </div>

          {{-- Shortlist toggle (employer/admin) --}}
          @if(!($seeker->mine ?? false) && ($emp || $admin))
            @php
              $shortlistUrl = Route::url('index.php?option=com_jobs&oid=' . $seeker->uid . '&task=shortlist');
              $shortlistTitle = $isShortlisted
                  ? Lang::txt('COM_JOBS_ACTION_REMOVE_FROM_SHORTLIST')
                  : Lang::txt('COM_JOBS_ACTION_ADD_TO_SHORTLIST');
            @endphp
            <a href="{{ $shortlistUrl }}" class="btn btn-xs btn-outline"
               title="{{ $shortlistTitle }}">
              {{ $shortlistTitle }}
            </a>
          @endif
        </div>

        @if($seeker->tagline)
          <p class="text-sm text-base-content/70 italic mt-1">{{ stripslashes($seeker->tagline) }}</p>
        @endif

        {{-- Looking for --}}
        <div class="mt-2">
          <span class="text-xs font-semibold text-base-content/50 uppercase">
            {{ Lang::txt('COM_JOBS_LOOKING_FOR') }}
          </span>
          <div class="text-sm">
            @if($jobtype)
              <span class="badge badge-sm badge-outline">{{ $jobtype }}</span>
            @endif
            @if($jobcat)
              <span class="badge badge-sm badge-outline">{{ $jobcat }}</span>
            @endif
          </div>
          @if($seeker->lookingfor)
            <p class="text-sm text-base-content/70 mt-1">{{ stripslashes($seeker->lookingfor) }}</p>
          @endif
        </div>

        {{-- Resume links --}}
        <div class="flex flex-wrap items-center gap-3 mt-3 text-sm">
          @if($resume)
            @php
              $downloadUrl = Route::url('index.php?option=' . $option . '&id=' . $seeker->uid . '&active=resume&action=download');
            @endphp
            <a href="{{ $downloadUrl }}" class="link link-primary" title="{{ $title }}">
              {{ ucfirst(Lang::txt('COM_JOBS_RESUME')) }}
            </a>
            <span class="text-base-content/50 text-xs">
              {{ Lang::txt('COM_JOBS_LAST_UPDATE') }}: {{ $seeker->created }}
            </span>
            @if($seeker->url)
              @php
                $url = (strpos($seeker->url, 'http://') === false && strpos($seeker->url, 'https://') === false)
                    ? 'http://' . $seeker->url
                    : $seeker->url;
              @endphp
              <a href="{{ $url }}" class="link link-primary text-xs" rel="external">
                {{ Lang::txt('COM_JOBS_WEBSITE') }}
              </a>
            @endif
            @if($seeker->linkedin)
              <a href="{{ $seeker->linkedin }}" class="link link-primary text-xs" rel="external">
                {{ Lang::txt('COM_JOBS_LINKEDIN') }}
              </a>
            @endif
          @else
            <span class="text-base-content/40">{{ Lang::txt('COM_JOBS_ACTION_DOWNLOAD') }}</span>
          @endif
        </div>

        {{-- Edit prefs (own profile) --}}
        @if($seeker->mine ?? false)
          @php
            $editPrefsUrl = Route::url('index.php?option=' . $option . '&id=' . $seeker->uid . '&active=resume&action=editprefs');
          @endphp
          <a href="{{ $editPrefsUrl }}" class="link link-primary text-xs mt-2 inline-block">
            {{ Lang::txt('COM_JOBS_ACTION_EDIT_MY_PROFILE') }}
          </a>
        @endif
      </div>
    </div>
  </div>
</div>
