{{--
  Group card partial — used in browse, display, and other listing views.

  Variables (set by caller):
    $group   — Group object (gidNumber, cn, description, etc.)
    $option  — string: component option

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $group = \Hubzero\User\Group::getInstance($group->gidNumber);

  // Determine membership status
  $status = '';
  $isManager = false;
  $members = [];

  if ($group->get('published') == 1 && !User::isGuest()) {
      $members = $group->get('members');

      if (in_array(User::get('id'), $members)) {
          $status = 'member';
          $managers = $group->get('managers');
          if (in_array(User::get('id'), $managers)) {
              $status = 'manager';
              $isManager = true;
          }
      } else {
          $invitees   = $group->get('invitees');
          $applicants = $group->get('applicants');

          if (in_array(User::get('id'), $invitees)) {
              $status = 'invitee';
          } elseif (in_array(User::get('id'), $applicants)) {
              $status = 'pending';
          }
      }
  }

  $published = (bool) $group->get('published');
  $isArchived = $group->get('published') == 2;
  $cn = $group->get('cn');
  $groupUrl = Route::url('index.php?option=' . $option . '&cn=' . $cn);
  $desc = \Hubzero\Utility\Str::truncate(stripslashes($group->get('description')), 60);

  $statusClass = !$published ? 'notpublished' : ($isArchived ? 'archived' : 'published');

  // Logo
  $logoPath = PATH_APP . '/site/groups/' . $group->get('gidNumber') . '/uploads/' . $group->get('logo');
  $hasLogo = $group->get('logo') && is_file($logoPath);
  $logoUrl = $hasLogo
      ? with(new \Hubzero\Content\Moderator($logoPath))->getUrl()
      : '';
  $logoAlt = stripslashes($group->get('description'));
@endphp

<div class="card bg-base-100 shadow-sm group-card {{ $statusClass }}"
     id="group{{ $group->get('gidNumber') }}"
     data-id="{{ $group->get('gidNumber') }}"
     data-status="{{ e($status) }}"
     data-title="{{ e(stripslashes($group->get('description')) . ' ' . $cn) }}">
  <div class="card-body flex-row gap-4 p-4">
    {{-- Logo / Identity --}}
    <div class="shrink-0 w-16 h-16">
      @if($published)
        <a href="{{ $groupUrl }}" class="block">
          @if($hasLogo)
            <img src="{{ $logoUrl }}" alt="{{ $logoAlt }}"
                 class="w-16 h-16 rounded object-cover" />
          @else
            <span class="w-16 h-16 rounded bg-base-200 flex items-center justify-center text-xs text-center overflow-hidden">
              {{ $logoAlt }}
            </span>
          @endif
        </a>
      @else
        <div>
          @if($hasLogo)
            <img src="{{ $logoUrl }}" alt="{{ $logoAlt }}"
                 class="w-16 h-16 rounded object-cover opacity-50" />
          @else
            <span class="w-16 h-16 rounded bg-base-200 flex items-center justify-center text-xs text-center overflow-hidden opacity-50">
              {{ $logoAlt }}
            </span>
          @endif
        </div>
      @endif
    </div>

    {{-- Details --}}
    <div class="flex-1 min-w-0">
      <div class="text-xs text-base-content/50">{{ e($cn) }}</div>
      @if($published)
        <a href="{{ $groupUrl }}" class="font-semibold link link-hover">
          {{ $desc }}
        </a>
      @else
        <span class="font-semibold text-base-content/50">{{ $desc }}</span>
      @endif

      @if($published && $status)
        <span class="badge badge-sm ml-2
          @if($status === 'manager') badge-primary
          @elseif($status === 'member') badge-success
          @elseif($status === 'pending') badge-warning
          @elseif($status === 'invitee') badge-info
          @endif">
          @switch($status)
            @case('manager') {{ Lang::txt('COM_GROUPS_BROWSE_STATUS_MANAGER') }} @break
            @case('member') {{ Lang::txt('COM_GROUPS_BROWSE_STATUS_MEMBER') }} @break
            @case('pending') {{ Lang::txt('COM_GROUPS_BROWSE_STATUS_PENDING') }} @break
            @case('invitee') {{ Lang::txt('COM_GROUPS_BROWSE_STATUS_INVITED') }} @break
          @endswitch
        </span>
      @endif

      {{-- Tags --}}
      @if($published)
        @php
          $gt = new \Components\Groups\Models\Tags($group->get('gidNumber'));
          $tagHtml = $gt->render();
        @endphp
        @if($tagHtml)
          <div class="mt-1">{!! $tagHtml !!}</div>
        @endif
      @endif
    </div>

    {{-- Meta / Actions --}}
    <div class="shrink-0 text-right text-sm">
      @if(!$published)
        <span class="badge badge-ghost">
          {{ Lang::txt('COM_GROUPS_STATUS_NOT_PUBLISHED_GROUP') }}
        </span>
      @elseif($status === 'pending')
        <span class="text-base-content/60">
          {{ Lang::txt('COM_GROUPS_BROWSE_STATUS_PENDING_APPROVAL') }}
        </span>
      @elseif($status === 'invitee')
        <a class="btn btn-success btn-sm"
           href="{{ Route::url('index.php?option=' . $option . '&cn=' . $cn . '&task=accept') }}">
          {{ Lang::txt('COM_GROUPS_TOOLBAR_ACCEPT') }}
        </a>
      @elseif($status === 'member' || $status === 'manager')
        @if(!$isArchived)
          <div class="text-base-content/60">
            <span class="block">
              @php
                $activity = \Hubzero\Activity\Recipient::all()
                    ->including('log')
                    ->whereEquals('scope', 'group')
                    ->whereEquals('scope_id', $group->get('gidNumber'))
                    ->whereEquals('state', 1)
                    ->ordered()
                    ->row();
                if (!$activity->get('id')) {
                    $activity->set('created', $group->get('created'));
                }
              @endphp
              @if(!$activity->get('created') || $activity->get('created') == '0000-00-00 00:00:00')
                {{ Lang::txt('COM_GROUPS_UNKNOWN') }}
              @else
                @php
                  $dt = Date::of($activity->get('created'));
                  $ct = Date::of('now');
                  $lapsed = $ct->toUnix() - $dt->toUnix();
                @endphp
                @if($lapsed < 30)
                  {{ Lang::txt('COM_GROUPS_ACTIVITY_JUST_NOW') }}
                @elseif($lapsed > 30 && $lapsed < 60)
                  {{ Lang::txt('COM_GROUPS_ACTIVITY_A_MINUTE_AGO') }}
                @else
                  {{ $dt->relative('week') }}
                @endif
              @endif
            </span>
            <span class="text-xs">{{ Lang::txt('COM_GROUPS_ACTIVITY_LAST') }}</span>
          </div>
          <div class="text-base-content/60 mt-1">
            <span class="font-semibold">{{ count($members) }}</span>
            <span class="text-xs">{{ Lang::txt('COM_GROUPS_MEMBERS') }}</span>
          </div>
        @else
          <span class="badge badge-ghost">{{ Lang::txt('COM_GROUPS_BROWSE_STATE_ARCHIVED') }}</span>
        @endif
      @elseif($isArchived)
        <span class="badge badge-ghost">{{ Lang::txt('COM_GROUPS_BROWSE_STATE_ARCHIVED') }}</span>
      @else
        {{-- Non-member, not archived --}}
        @if(!$group->get('join_policy') || $group->get('join_policy') == 1)
          <div class="text-base-content/60">
            <span class="badge badge-sm badge-outline mb-1">
              @if(!$group->get('join_policy'))
                {{ Lang::txt('COM_GROUPS_BROWSE_POLICY_OPEN') }}
              @else
                {{ Lang::txt('COM_GROUPS_BROWSE_POLICY_RESTRICTED') }}
              @endif
            </span>
          </div>
          <a class="btn btn-success btn-sm"
             href="{{ Route::url('index.php?option=' . $option . '&cn=' . $cn . '&task=join') }}">
            {{ Lang::txt('COM_GROUPS_TOOLBAR_JOIN') }}
          </a>
        @elseif($group->get('join_policy') == 3)
          <span class="badge badge-sm badge-ghost">
            {{ Lang::txt('COM_GROUPS_BROWSE_POLICY_CLOSED') }}
          </span>
        @elseif($group->get('join_policy') == 2)
          <span class="badge badge-sm badge-ghost">
            {{ Lang::txt('COM_GROUPS_BROWSE_POLICY_INVITE_ONLY') }}
          </span>
        @endif
      @endif

      {{-- User actions (cancel membership, edit, etc.) --}}
      @if($published && $status)
        <div class="mt-2">
          @if($status === 'member')
            <a class="btn btn-ghost btn-xs"
               href="{{ Route::url('index.php?option=' . $option . '&cn=' . $cn . '&task=cancel') }}">
              {{ Lang::txt('COM_GROUPS_TOOLBAR_CANCEL') }}
            </a>
          @elseif($status === 'manager')
            <a class="btn btn-ghost btn-xs"
               href="{{ Route::url('index.php?option=' . $option . '&cn=' . $cn . '&task=edit') }}">
              {{ Lang::txt('COM_GROUPS_TOOLBAR_EDIT') }}
            </a>
          @elseif($status === 'invitee')
            <a class="btn btn-ghost btn-xs"
               href="{{ Route::url('index.php?option=' . $option . '&cn=' . $cn . '&task=cancel') }}">
              {{ Lang::txt('COM_GROUPS_TOOLBAR_DECLINE') }}
            </a>
          @elseif($status === 'pending')
            <a class="btn btn-ghost btn-xs"
               href="{{ Route::url('index.php?option=' . $option . '&cn=' . $cn . '&task=cancel') }}">
              {{ Lang::txt('COM_GROUPS_TOOLBAR_CANCEL') }}
            </a>
          @endif
        </div>
      @endif
    </div>
  </div>
</div>
