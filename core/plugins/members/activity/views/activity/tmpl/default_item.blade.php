{{--
  Member Activity — single activity item.

  Variables (set by parent view):
    $member — member profile object
    $row    — activity recipient row
    $online — array of online user IDs

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\User;

  $status = '';
  if (!$row->wasViewed()) {
      $status = 'new';
      $row->markAsViewed();
  }
  if ($row->get('starred')) {
      $status .= ' starred';
  }

  $creator = User::getInstance($row->log->get('created_by'));
  $name = Lang::txt('JANONYMOUS');
  $isOnline = false;

  if ($row->log->get('created_by') == User::get('id')) {
      $name = 'You';
      $isOnline = true;
  } elseif (!$row->log->get('anonymous')) {
      $name = e(stripslashes($creator->get('name', Lang::txt('PLG_MEMBERS_ACTIVITY_UNKNOWN'))));
      if (in_array($creator->get('access'), User::getAuthorisedViewLevels())) {
          $name = '<a href="' . Route::url($creator->link()) . '">' . $name . '</a>';
      }
      if (isset($online) && !$row->log->get('anonymous') && in_array($row->log->get('created_by'), $online)) {
          $isOnline = true;
      }
  }

  $base = 'index.php?option=com_members&id=' . $member->get('id') . '&active=activity';

  // Time display
  $dt = Date::of($row->get('created'));
  $ct = Date::of('now');
  $lapsed = $ct->toUnix() - $dt->toUnix();

  if ($lapsed < 30) {
      $timeDisplay = Lang::txt('PLG_MEMBERS_ACTIVITY_JUST_NOW');
  } elseif ($lapsed > 86400 && $ct->format('Y') != $dt->format('Y')) {
      $timeDisplay = $dt->toLocal('M j, Y');
  } elseif ($lapsed > 86400) {
      $timeDisplay = $dt->toLocal('M j') . ' @ ' . $dt->toLocal('g:i a');
  } else {
      $timeDisplay = $dt->relative();
  }

  // Scope context
  $scope = explode('.', $row->log->get('scope'));
  $context = e($scope[0]);
  if (in_array('comment', $scope)) {
      $context = $row->log->get('parent') ? 'reply' : 'comment';
  }

  // Content truncation
  $content = $row->log->get('description');
  $short = (strlen(strip_tags($content)) > 150)
      ? Hubzero\Utility\Str::truncate($content, 150, ['html' => true])
      : null;
@endphp

<li data-time="{{ $row->get('created') }}" data-id="{{ $row->get('id') }}"
    data-log_id="{{ $row->get('log_id') }}" data-context="{{ $row->log->get('scope') }}"
    data-action="{{ $row->log->get('action') }}" id="activity{{ $row->get('id') }}"
    class="activity card bg-base-100 shadow-sm {{ trim($status) }} {{ $row->get('starred') ? 'border-l-4 border-warning' : '' }}">

  <div class="card-body p-4">
    <div class="flex gap-3">
      {{-- Avatar --}}
      <div class="shrink-0 relative">
        @if ($row->log->get('created_by') == User::get('id'))
          <div class="size-10 rounded-full bg-base-200 flex items-center justify-center">
            <span class="text-xs font-medium text-base-content/60">
              {{ $context }} {{ $row->log->get('action') }}
            </span>
          </div>
        @elseif ($creator->get('public'))
          <a href="{{ Route::url($creator->link()) }}" title="{{ strip_tags($name) }}">
            <img src="{{ $creator->picture() }}" class="size-10 rounded-full object-cover"
                 alt="{{ Lang::txt('PLG_MEMBERS_ACTIVITY_PROFILE_PICTURE', strip_tags($name)) }}" />
          </a>
        @else
          <img src="{{ $creator->picture() }}" class="size-10 rounded-full object-cover"
               alt="{{ Lang::txt('PLG_MEMBERS_ACTIVITY_PROFILE_PICTURE', strip_tags($name)) }}" />
        @endif
        @if ($isOnline)
          <span class="absolute bottom-0 right-0 size-3 rounded-full bg-success border-2 border-base-100"
                title="{{ Lang::txt('PLG_MEMBERS_ACTIVITY_ONLINE') }}"></span>
        @endif
      </div>

      {{-- Content --}}
      <div class="flex-1 min-w-0">
        <div class="flex items-start justify-between gap-2">
          <div class="text-sm">
            <span class="font-medium">{!! $name !!}</span>
            <span class="text-base-content/60">{{ e($row->log->get('action')) }}</span>
            <span class="text-base-content/40">{{ e($scope[0]) }}</span>
            <span class="text-base-content/40">&middot;</span>
            <time class="text-base-content/40"
                  datetime="{{ Date::of($row->get('created'))->format('Y-m-d\TH:i:s\Z') }}">
              {{ $timeDisplay }}
            </time>
          </div>

          {{-- Actions --}}
          <div class="flex gap-1 shrink-0">
            @if (!$row->log->get('parent'))
              @php
                $rowId = $row->get('id');
                $starred = $row->get('starred');
                $starAction = ($starred ? 'un' : '') . 'star';
                $starHref = Route::url($base . '&action=' . $starAction . '&activity=' . $rowId);
                $starTitle = $starred
                    ? Lang::txt('PLG_MEMBERS_ACTIVITY_UNSTAR')
                    : Lang::txt('PLG_MEMBERS_ACTIVITY_STAR');
              @endphp
              <a class="btn btn-ghost btn-xs" href="{{ $starHref }}"
                 data-id="activity{{ $rowId }}"
                 data-hrf-active="{{ Route::url($base . '&action=unstar&activity=' . $rowId) }}"
                 data-hrf-inactive="{{ Route::url($base . '&action=star&activity=' . $rowId) }}"
                 data-txt-active="{{ Lang::txt('PLG_MEMBERS_ACTIVITY_UNSTAR') }}"
                 data-txt-inactive="{{ Lang::txt('PLG_MEMBERS_ACTIVITY_STAR') }}"
                 title="{{ $starTitle }}">
                @if ($starred)
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"
                       class="size-4 text-warning" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10.788 3.21c.448-1.077 1.976-1.077 2.424 0l2.082 5.006 5.404.434c1.164.093 1.636 1.545.749 2.305l-4.117 3.527 1.257 5.273c.271 1.136-.964 2.033-1.96 1.425L12 18.354 7.373 21.18c-.996.608-2.231-.29-1.96-1.425l1.257-5.273-4.117-3.527c-.887-.76-.415-2.212.749-2.305l5.404-.434 2.082-5.005Z" clip-rule="evenodd" />
                  </svg>
                @else
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                       stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 0 1 1.04 0l2.125 5.111a.563.563 0 0 0 .475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 0 0-.182.557l1.285 5.385a.562.562 0 0 1-.84.61l-4.725-2.885a.562.562 0 0 0-.586 0L6.982 20.54a.562.562 0 0 1-.84-.61l1.285-5.386a.562.562 0 0 0-.182-.557l-4.204-3.602a.562.562 0 0 1 .321-.988l5.518-.442a.563.563 0 0 0 .475-.345L11.48 3.5Z" />
                  </svg>
                @endif
              </a>
            @endif

            <a class="btn btn-ghost btn-xs text-error" data-id="activity{{ $row->get('id') }}"
               href="{{ Route::url($base . '&action=remove&activity=' . $row->get('id') . '&' . Session::getFormToken() . '=1') }}"
               title="{{ Lang::txt('PLG_MEMBERS_ACTIVITY_DELETE') }}"
               data-txt-confirm="{{ Lang::txt('PLG_MEMBERS_ACTIVITY_CONFIRM_DELETE') }}">
              <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                   stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
              </svg>
            </a>
          </div>
        </div>

        {{-- Source context (group/project) --}}
        @if ($row->log->get('scope') === 'activity.comment')
          @php
            $group = Hubzero\User\Group::getInstance($row->log->get('scope_id'));
          @endphp
          @if ($group)
            <div class="text-xs text-base-content/50 mt-1">
              <a class="link link-hover" href="{{ Route::url('index.php?option=com_groups&cn=' . $group->get('cn') . '&active=activity') }}">
                {{ $group->get('description') }}
              </a>
            </div>
          @endif
        @elseif (str_starts_with($row->log->get('scope'), 'project'))
          @php
            $project = new \Components\Projects\Models\Project($row->log->get('scope_id'));
          @endphp
          @if ($project)
            <div class="text-xs text-base-content/50 mt-1">
              <a class="link link-hover" href="{{ Route::url('index.php?option=com_projects&alias=' . $project->get('alias')) }}">
                {{ $project->get('title') }}
              </a>
            </div>
          @endif
        @endif

        {{-- Event content --}}
        @if ($short)
          <div class="activity-event-preview mt-2 text-sm text-base-content/80">
            {!! $short !!}
            <a class="link link-hover text-xs more-content"
               href="#activity-event-content{{ $row->get('id') }}">
              {{ Lang::txt('PLG_MEMBERS_ACTIVITY_MORE') }}
            </a>
          </div>
        @endif
        <div class="activity-event-content mt-2 text-sm text-base-content/80 {{ $short ? 'hide' : '' }}"
             id="activity-event-content{{ $row->get('id') }}">
          {!! $content !!}
        </div>

        {{-- Duplicate actions --}}
        @if (in_array($row->log->get('action'), ['updated', 'emailed', 'downloaded', 'uploaded', 'denied', 'voted', 'shared']))
          @php
            $recipient = Hubzero\Activity\Recipient::all();
            $r = $recipient->getTableName();
            $l = Hubzero\Activity\Log::blank()->getTableName();

            $duplicates = $recipient
                ->select($r . '.*')
                ->including('log')
                ->join($l, $l . '.id', $r . '.log_id')
                ->whereEquals($r . '.scope', 'user')
                ->whereEquals($r . '.scope_id', $member->get('id'))
                ->whereEquals($r . '.state', Hubzero\Activity\Recipient::STATE_UNPUBLISHED)
                ->whereEquals($l . '.action', $row->log->get('action'))
                ->whereEquals($l . '.scope', $row->log->get('scope'))
                ->whereEquals($l . '.scope_id', $row->log->get('scope_id'))
                ->whereEquals($l . '.created_by', $row->log->get('created_by'))
                ->whereEquals($l . '.description', $row->log->get('description'))
                ->where('created', '>=', Date::of($row->log->get('created'))->modify('-1 hour')->toSql())
                ->where('created', '<', $row->log->get('created'))
                ->order($r . '.created', 'desc')
                ->rows();
          @endphp
          @if ($duplicates->count() > 0)
            <ul class="mt-2 text-xs text-base-content/40 space-y-1">
              @foreach ($duplicates as $dup)
                <li>
                  <span>{{ $dup->log->get('action') }}</span>
                  <time datetime="{{ Date::of($dup->log->get('created'))->format('Y-m-d\TH:i:s\Z') }}">
                    {{ Date::of($dup->log->get('created'))->toLocal('M j') }} @ {{ Date::of($dup->log->get('created'))->toLocal('g:i a') }}
                  </time>
                </li>
              @endforeach
            </ul>
          @endif
        @endif
      </div>
    </div>

    <div class="activity-processor hidden">
      <div class="loading loading-spinner loading-sm"></div>
      <div class="msg"></div>
    </div>
  </div>
</li>
