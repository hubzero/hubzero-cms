{{--
  Member Groups — group list with filters.

  Variables from plugin (onMembers):
    $groups  — array of group objects
    $state   — current filter state (active/archived)
    $filter  — membership filter (managers/members/applicants/invitees)
    $total   — total group count
    $member  — member profile object
    $option  — component option (com_groups)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\App;
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $__view->css()->js();

  $base = $member->link() . '&active=groups';
  $db   = App::get('db');
@endphp

<h3 class="text-lg font-semibold mb-4">
  {{ Lang::txt('PLG_MEMBERS_GROUPS') }}
</h3>

@if (User::authorise('core.create', 'com_groups'))
  <div class="flex justify-end mb-4">
    <a class="btn btn-primary btn-sm" href="{{ Route::url('index.php?option=com_groups&task=new') }}">
      <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
           stroke-width="1.5" stroke="currentColor" class="size-4" aria-hidden="true">
        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
      </svg>
      {{ Lang::txt('PLG_MEMBERS_GROUPS_CREATE') }}
    </a>
  </div>
@endif

@if ($total)
  {{-- Filters --}}
  <nav class="mb-4" aria-label="{{ Lang::txt('JGLOBAL_FILTER_AND_SORT_RESULTS') }}">
    <div class="flex flex-wrap gap-4 justify-between">
      {{-- State filter --}}
      <div role="tablist" class="tabs tabs-border">
        <a role="tab" class="tab {{ $state === 'active' ? 'tab-active' : '' }}"
           href="{{ Route::url($base . '&filter=' . $filter) }}"
           @if($state === 'active') aria-selected="true" @endif>
          {{ Lang::txt('PLG_MEMBERS_GROUPS_STATE_ACTIVE') }}
        </a>
        <a role="tab" class="tab {{ $state === 'archived' ? 'tab-active' : '' }}"
           href="{{ Route::url($base . '&filter=' . $filter . '&state=archived') }}"
           @if($state === 'archived') aria-selected="true" @endif>
          {{ Lang::txt('PLG_MEMBERS_GROUPS_STATE_ARCHIVED') }}
        </a>
      </div>

      {{-- Membership filter --}}
      <div role="tablist" class="tabs tabs-border">
        @php
          $membershipFilters = [
              ''           => ['label' => Lang::txt('PLG_MEMBERS_GROUPS_STATUS_ALL', $total), 'status' => 'all'],
              'managers'   => ['label' => Lang::txt('PLG_MEMBERS_GROUPS_STATUS_MANAGER'), 'status' => 'manager'],
              'members'    => ['label' => Lang::txt('PLG_MEMBERS_GROUPS_STATUS_MEMBER'), 'status' => 'member'],
              'applicants' => ['label' => Lang::txt('PLG_MEMBERS_GROUPS_STATUS_APPLICANT'), 'status' => 'applicant'],
              'invitees'   => ['label' => Lang::txt('PLG_MEMBERS_GROUPS_STATUS_INVITEES'), 'status' => 'invitee'],
          ];
        @endphp
        @foreach ($membershipFilters as $filterKey => $filterData)
          @php
            $isActive = ($filter === $filterKey);
            $filterUrl = $filterKey
                ? Route::url($base . '&state=' . $state . '&filter=' . $filterKey)
                : Route::url($base);
          @endphp
          <a role="tab" class="tab {{ $isActive ? 'tab-active' : '' }}"
             href="{{ $filterUrl }}"
             data-status="{{ $filterData['status'] }}"
             @if($isActive) aria-selected="true" @endif>
            {{ $filterData['label'] }}
          </a>
        @endforeach
      </div>
    </div>
  </nav>

  {{-- Group cards --}}
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach ($groups as $group)
      @php
        // Determine membership status
        $status = '';
        $actionUrl = '';
        $actionLabel = '';
        $actionTitle = '';
        $actionClass = '';
        $editUrl = '';

        if ($group->manager) {
            $status = 'manager';
            $editUrl = Route::url('index.php?option=' . $option . '&cn=' . $group->cn . '&task=edit');
        } elseif ($group->registered && $group->regconfirmed) {
            $status = 'member';
            $actionUrl = Route::url('index.php?option=' . $option . '&cn=' . $group->cn . '&task=cancel');
            $actionLabel = Lang::txt('PLG_MEMBERS_GROUPS_ACTION_CANCEL');
            $actionTitle = Lang::txt('PLG_MEMBERS_GROUPS_ACTION_CANCEL_TITLE');
            $actionClass = 'btn-warning';
        } elseif ($group->registered && !$group->regconfirmed) {
            $status = 'pending';
            $actionUrl = Route::url('index.php?option=' . $option . '&cn=' . $group->cn . '&task=cancel');
            $actionLabel = Lang::txt('PLG_MEMBERS_GROUPS_ACTION_CANCEL');
            $actionTitle = Lang::txt('PLG_MEMBERS_GROUPS_ACTION_CANCEL_TITLE');
            $actionClass = 'btn-warning';
        } elseif (!$group->registered && $group->regconfirmed) {
            $status = 'invitee';
            $actionUrl = Route::url('index.php?option=' . $option . '&cn=' . $group->cn . '&task=cancel');
            $actionLabel = Lang::txt('PLG_MEMBERS_GROUPS_ACTION_DECLINE');
            $actionTitle = Lang::txt('PLG_MEMBERS_GROUPS_ACTION_DECLINE_TITLE');
            $actionClass = 'btn-ghost';
        }

        $published = (bool) $group->published;
        $approved  = (bool) $group->approved;
        $archived  = ($group->published == 2);
        $groupUrl  = Route::url('index.php?option=' . $option . '&cn=' . $group->cn);
        $groupDesc = e(stripslashes($group->description));
        $truncDesc = e(\Hubzero\Utility\Str::truncate(stripslashes($group->description), 60));

        // Logo
        $logoPath = PATH_APP . '/site/groups/' . $group->gidNumber . '/uploads/' . $group->logo;
        $hasLogo  = ($group->logo && is_file($logoPath));
        $logoUrl  = $hasLogo ? with(new \Hubzero\Content\Moderator($logoPath))->getUrl() : '';
      @endphp

      <div class="card bg-base-100 shadow-sm {{ !$published ? 'opacity-60' : '' }}"
           id="group{{ $group->gidNumber }}"
           data-id="{{ $group->gidNumber }}"
           data-status="{{ $status }}">
        <div class="card-body p-4">
          {{-- Logo + title --}}
          <div class="flex items-start gap-3">
            <div class="size-12 shrink-0 rounded bg-base-200 flex items-center justify-center overflow-hidden">
              @if ($hasLogo)
                <img src="{{ $logoUrl }}" alt="{{ $groupDesc }}" class="size-12 object-cover" />
              @else
                <span class="text-xs text-base-content/40">{{ mb_substr($groupDesc, 0, 2) }}</span>
              @endif
            </div>
            <div class="min-w-0 flex-1">
              <p class="text-xs text-base-content/50">{{ e($group->cn) }}</p>
              @if ($published)
                <a class="font-semibold link link-hover line-clamp-1" href="{{ $groupUrl }}">
                  {{ $truncDesc }}
                </a>
              @else
                <span class="font-semibold line-clamp-1">{{ $truncDesc }}</span>
              @endif
            </div>
          </div>

          {{-- Status badges --}}
          <div class="mt-2 flex flex-wrap gap-2 items-center text-xs">
            @if (!$published)
              <span class="badge badge-ghost badge-sm">
                {{ Lang::txt('PLG_MEMBERS_GROUPS_STATUS_NOT_PUBLISHED_GROUP') }}
              </span>
            @elseif (!$approved)
              <span class="badge badge-warning badge-sm">
                {{ Lang::txt('PLG_MEMBERS_GROUPS_STATUS_NEW_GROUP') }}
              </span>
            @else
              @if ($status)
                @php
                  $statusLabels = [
                      'manager' => Lang::txt('PLG_MEMBERS_GROUPS_STATUS_MANAGER'),
                      'member'  => Lang::txt('PLG_MEMBERS_GROUPS_STATUS_MEMBER'),
                      'pending' => Lang::txt('PLG_MEMBERS_GROUPS_STATUS_PENDING'),
                      'invitee' => Lang::txt('PLG_MEMBERS_GROUPS_STATUS_INVITED'),
                  ];
                  $badgeClass = match($status) {
                      'manager' => 'badge-primary',
                      'member'  => 'badge-info',
                      'pending' => 'badge-warning',
                      'invitee' => 'badge-accent',
                      default   => 'badge-ghost',
                  };
                @endphp
                <span class="badge {{ $badgeClass }} badge-sm">{{ $statusLabels[$status] ?? $status }}</span>
              @endif

              @if ($archived)
                <span class="text-base-content/50">
                  {{ Lang::txt('PLG_MEMBERS_GROUPS_STATE_ARCHIVED') }}
                </span>
              @elseif ($group->registered && !$group->regconfirmed)
                <span class="text-base-content/50">
                  {{ Lang::txt('Membership request requires approval.') }}
                </span>
              @elseif (!$group->registered && $group->regconfirmed)
                {{-- Invitation — show accept button --}}
                <a class="btn btn-success btn-xs"
                   href="{{ Route::url('index.php?option=' . $option . '&cn=' . $group->cn . '&task=accept') }}"
                   title="{{ Lang::txt('PLG_MEMBERS_GROUPS_ACTION_ACCEPT_TITLE') }}">
                  {{ Lang::txt('PLG_MEMBERS_GROUPS_ACTION_ACCEPT') }}
                </a>
              @else
                {{-- Activity + member count --}}
                @php
                  $activity = \Hubzero\Activity\Recipient::all()
                      ->including('log')
                      ->whereEquals('scope', 'group')
                      ->whereEquals('scope_id', $group->gidNumber)
                      ->whereEquals('state', 1)
                      ->ordered()
                      ->row();
                  if (!$activity->get('id')) {
                      $activity->set('created', $group->created);
                  }
                  $created = $activity->get('created');
                  if (!$created || $created === '0000-00-00 00:00:00') {
                      $activityText = Lang::txt('PLG_MEMBERS_GROUPS_ACTIVITY_UNKNOWN');
                  } else {
                      $dt = Date::of($created);
                      $ct = Date::of('now');
                      $lapsed = $ct->toUnix() - $dt->toUnix();
                      if ($lapsed < 30) {
                          $activityText = Lang::txt('PLG_MEMBERS_GROUPS_ACTIVITY_JUST_NOW');
                      } elseif ($lapsed > 30 && $lapsed < 60) {
                          $activityText = Lang::txt('PLG_MEMBERS_GROUPS_ACTIVITY_A_MINUTE_AGO');
                      } else {
                          $activityText = $dt->relative('week');
                      }
                  }

                  $query = "SELECT COUNT(*) FROM `#__xgroups_members` WHERE `gidNumber`=" . (int) $group->gidNumber;
                  $db->setQuery($query);
                  $memberCount = (int) $db->loadResult();
                @endphp
                <span class="text-base-content/50">{{ $activityText }}</span>
                <span class="text-base-content/50">&middot;</span>
                <span class="text-base-content/50">
                  {{ $memberCount }} {{ Lang::txt('PLG_MEMBERS_GROUPS_MEMBERS') }}
                </span>
              @endif
            @endif
          </div>

          {{-- Actions --}}
          @if ($published && $group->published != 2)
            <div class="mt-2 flex gap-2">
              @if ($editUrl)
                <a class="btn btn-ghost btn-xs"
                   href="{{ $editUrl }}"
                   title="{{ Lang::txt('PLG_MEMBERS_GROUPS_ACTION_EDIT_TITLE') }}">
                  {{ Lang::txt('PLG_MEMBERS_GROUPS_ACTION_EDIT') }}
                </a>
              @endif
              @if ($actionUrl)
                <a class="btn {{ $actionClass }} btn-xs"
                   href="{{ $actionUrl }}"
                   title="{{ $actionTitle }}">
                  {{ $actionLabel }}
                </a>
              @endif
            </div>
          @endif
        </div>
      </div>
    @endforeach
  </div>

  {{-- No results (hidden by default, shown by JS filter) --}}
  <div class="text-center text-base-content/60 mt-4 {{ count($groups) ? 'hidden' : '' }}">
    <p>{{ Lang::txt('PLG_MEMBERS_GROUPS_NONE_FOUND') }}</p>
  </div>

@else
  {{-- Empty state --}}
  <div class="text-center py-8">
    <p class="text-base-content/70 mb-4">{{ Lang::txt('PLG_MEMBERS_GROUPS_YOURS_EXPLANATION') }}</p>
    <p class="mb-2"><strong>{{ Lang::txt('PLG_MEMBERS_GROUPS_WHAT_ARE_GROUPS') }}</strong></p>
    <p class="text-base-content/70 mb-4">{{ Lang::txt('PLG_MEMBERS_GROUPS_EXPLANATION') }}</p>
    <p>{!! Lang::txt('PLG_MEMBERS_GROUPS_GO_TO_GROUPS', Route::url('index.php?option=com_groups')) !!}</p>
  </div>
@endif
