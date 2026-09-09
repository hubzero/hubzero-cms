{{--
  Member Resume — job seeker profile card.

  Variables (set by parent view):
    $seeker  — seeker record object
    $emp     — employer flag
    $admin   — admin flag
    $option  — component option
    $params  — plugin params
    $list    — list mode flag

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Filesystem;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;
  use Plugins\Members\Resume\Resume as plgMembersResume;

  $database = App::get('db');
  $jt = new \Components\Jobs\Tables\Type($database);
  $jc = new \Components\Jobs\Tables\Category($database);

  $profile = User::getInstance($seeker->uid);
  $jobtype = $jt->getType($seeker->sought_type, strtolower(Lang::txt('PLG_MEMBERS_RESUME_TYPE_ANY')));
  $jobcat = $jc->getCat($seeker->sought_cid, strtolower(Lang::txt('PLG_MEMBERS_RESUME_CATEGORY_ANY')));

  $downloadTitle = Lang::txt('PLG_MEMBERS_RESUME_ACTION_DOWNLOAD') . ' '
      . $seeker->name . ' ' . ucfirst(Lang::txt('PLG_MEMBERS_RESUME_RESUME'));

  $basePath = DS . trim($params->get('webpath', '/site/members'), DS);
  $path = $basePath . DS . \Hubzero\Utility\Str::pad($seeker->uid);

  if (!is_dir(PATH_APP . $path)) {
      if (!Filesystem::makeDirectory(PATH_APP . $path)) {
          $path = '';
      }
  }

  $resume = is_file(PATH_APP . $path . DS . $seeker->filename)
      ? $path . DS . $seeker->filename : '';
@endphp

<div class="card bg-base-100 shadow-sm mb-6 {{ $seeker->mine && $list ? 'ring-2 ring-primary' : '' }} {{ isset($seeker->shortlisted) && $seeker->shortlisted ? 'ring-2 ring-warning' : '' }}">
  <div class="card-body">
    <div class="flex gap-4">
      {{-- Avatar --}}
      <div class="shrink-0">
        <img src="{{ $profile->picture() }}" alt="{{ $seeker->name }}"
             class="size-16 rounded-full object-cover" />
      </div>

      {{-- Info --}}
      <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-2">
          <div>
            <h4 class="font-semibold text-lg">
              @if ($list)
                <a class="link link-hover"
                   href="{{ Route::url('index.php?option=' . $option . '&id=' . $seeker->uid . '&active=resume') }}">
                  {{ $seeker->name }}
                </a>
              @else
                {{ $seeker->name }}
              @endif
              @if ($seeker->countryresident)
                <span class="text-sm text-base-content/50 font-normal">, {{ e($seeker->countryresident) }}</span>
              @endif
            </h4>
            @if ($seeker->tagline)
              <blockquote class="text-sm text-base-content/70 italic mt-1">
                {{ stripslashes($seeker->tagline) }}
              </blockquote>
            @endif
          </div>

          {{-- Action buttons --}}
          @if ($seeker->mine)
            <a class="btn btn-ghost btn-sm"
               href="{{ Route::url('index.php?option=' . $option . '&id=' . $seeker->uid . '&active=resume&action=editprefs') }}"
               title="{{ Lang::txt('PLG_MEMBERS_RESUME_ACTION_EDIT_MY_PROFILE') }}">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                   stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
              </svg>
              {{ Lang::txt('JACTION_EDIT') }}
            </a>
          @elseif ($emp || $admin)
            @php
              $isShortlisted = isset($seeker->shortlisted) && $seeker->shortlisted;
              $shortlistUrl = Route::url('index.php?option=com_jobs&oid=' . $seeker->uid . '&task=shortlist');
              $shortlistTitle = $isShortlisted
                  ? Lang::txt('PLG_MEMBERS_RESUME_ACTION_REMOVE_FROM_SHORTLIST')
                  : Lang::txt('PLG_MEMBERS_RESUME_ACTION_ADD_TO_SHORTLIST');
            @endphp
            <a id="o{{ $seeker->uid }}" class="btn btn-sm {{ $isShortlisted ? 'btn-warning' : 'btn-ghost' }}"
               href="{{ $shortlistUrl }}" title="{{ $shortlistTitle }}">
              {{ $shortlistTitle }}
            </a>
          @endif
        </div>

        {{-- Looking for --}}
        <div class="mt-3">
          <span class="text-sm font-medium">{{ Lang::txt('PLG_MEMBERS_RESUME_LOOKING_FOR') }}</span>
          <div class="flex flex-wrap gap-2 mt-1">
            @if ($jobtype)
              <span class="badge badge-ghost badge-sm">{{ $jobtype }}</span>
            @endif
            @if ($jobcat)
              <span class="badge badge-ghost badge-sm">{{ $jobcat }}</span>
            @endif
          </div>
          @if ($seeker->lookingfor)
            <p class="text-sm text-base-content/70 mt-1">{{ stripslashes($seeker->lookingfor) }}</p>
          @endif
        </div>

        {{-- Links --}}
        <div class="mt-3 flex flex-wrap gap-3 text-sm">
          @if ($resume)
            <a class="link link-hover"
               href="{{ Route::url('index.php?option=' . $option . '&id=' . $seeker->uid . '&active=resume&action=download') }}"
               title="{{ $downloadTitle }}">
              {{ ucfirst(Lang::txt('PLG_MEMBERS_RESUME_RESUME')) }}
            </a>
            <span class="text-base-content/40">
              {{ Lang::txt('PLG_MEMBERS_RESUME_LAST_UPDATE') }}: {{ plgMembersResume::nicetime($seeker->created) }}
            </span>
            @if ($seeker->url)
              @php
                $url = (strpos($seeker->url, 'http://') === false && strpos($seeker->url, 'https://') === false)
                    ? 'http://' . $seeker->url : $seeker->url;
              @endphp
              <a class="link link-hover" href="{{ $url }}" rel="external"
                 title="{{ Lang::txt('PLG_MEMBERS_RESUME_MEMBER_WEBSITE') }}: {{ $seeker->url }}">
                {{ Lang::txt('PLG_MEMBERS_RESUME_WEBSITE') }}
              </a>
            @endif
            @if ($seeker->linkedin)
              <a class="link link-hover" href="{{ $seeker->linkedin }}" rel="external"
                 title="{{ Lang::txt('PLG_MEMBERS_RESUME_MEMBER_LINKEDIN') }}">
                {{ Lang::txt('PLG_MEMBERS_RESUME_LINKEDIN') }}
              </a>
            @endif
          @else
            <span class="text-base-content/40">{{ Lang::txt('PLG_MEMBERS_RESUME_ACTION_DOWNLOAD') }}</span>
          @endif
        </div>
      </div>
    </div>
  </div>
</div>
