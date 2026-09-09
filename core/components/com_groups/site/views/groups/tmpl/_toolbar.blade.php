{{--
  Group action toolbar partial (sidebar).

  Displays login/join/membership controls based on user state and group
  join policy. Managers get a dropdown with invite/edit/pages/delete actions.

  Variables (passed via Helper\View::displayToolbar):
    $group      — Group object
    $user       — User object
    $classOrId  — string: HTML attribute for the <ul> (e.g. 'id="group_options"')
    $logoutLink — bool: whether to show logout link

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $params = new \Hubzero\Config\Registry($group->get('params'));
  $membership_control = $params->get('membership_control', 1);

  $currentUrl   = Request::current(true);
  $groupUrl     = 'index.php?option=com_groups&cn=' . $group->get('cn');
  $loginReturn  = base64_encode($currentUrl);
  $logoutReturn = base64_encode(Route::url($groupUrl));
  $loginUrl     = Route::url('index.php?option=com_users&view=login&return=' . $loginReturn);
  $logoutUrl    = Route::url('index.php?option=com_users&view=login&task=logout&return=' . $logoutReturn);

  if ($group->isSuperGroup()) {
      $loginUrl = Route::url($groupUrl . '&active=login&return=' . base64_encode(Route::url($currentUrl)));
  }

  $cn        = $group->get('cn');
  $baseUrl   = Route::url('index.php?option=com_groups&cn=' . $cn);
  $members   = $group->get('members');
  $invitees  = $group->get('invitees');
  $applicants = $group->get('applicants');
  $managers  = $group->get('managers');
  $userId    = User::get('id');
  $isGuest   = User::isGuest();
  $isMember  = in_array($userId, $members);
  $isManager = in_array($userId, $managers);
  $joinPolicy = $group->get('join_policy');

  $perms = '\Components\Groups\Helpers\Permissions';
@endphp

<ul {!! $classOrId !!}>
  @if($isGuest)
    <li>
      <a class="login btn" href="{{ $loginUrl }}">
        {{ Lang::txt('COM_GROUPS_TOOLBAR_LOGIN') }}
      </a>
    </li>

  @elseif(in_array($userId, $invitees))
    @if($membership_control == 1)
      <li>
        <a class="invited btn btn-success" href="{{ $baseUrl }}&task=accept">
          {{ Lang::txt('COM_GROUPS_TOOLBAR_ACCEPT') }}
        </a>
      </li>
      <li>
        <a class="invited btn btn-secondary" href="{{ $baseUrl }}&task=cancel">
          {{ Lang::txt('COM_GROUPS_TOOLBAR_DECLINE') }}
        </a>
      </li>
    @endif

  @elseif($joinPolicy == 3 && !$isMember)
    <li>
      <span class="closed">{{ Lang::txt('COM_GROUPS_TOOLBAR_CLOSED') }}</span>
    </li>

  @elseif($joinPolicy == 2 && !$isMember)
    <li>
      <span class="inviteonly">{{ Lang::txt('COM_GROUPS_TOOLBAR_INVITE_ONLY') }}</span>
    </li>

  @elseif($joinPolicy == 0 && !$isMember)
    @if($membership_control == 1)
      <li>
        @php $joinUrl = Route::url('index.php?option=com_groups&cn=' . $cn . '&task=join'); @endphp
        <a class="join btn" href="{{ $joinUrl }}">
          {{ Lang::txt('COM_GROUPS_TOOLBAR_JOIN') }}
        </a>
      </li>
    @endif

  @elseif($joinPolicy == 1 && !$isMember)
    @if($membership_control == 1)
      @if(in_array($userId, $applicants))
        <li>
          <span class="pending">{{ Lang::txt('COM_GROUPS_TOOLBAR_PENDING') }}</span>
        </li>
      @else
        <li>
          @php $reqUrl = Route::url('index.php?option=com_groups&cn=' . $cn . '&task=join'); @endphp
          <a class="request btn" href="{{ $reqUrl }}">
            {{ Lang::txt('COM_GROUPS_TOOLBAR_REQUEST') }}
          </a>
        </li>
      @endif
    @endif

  @else
    {{-- Current member/manager — dropdown --}}
    @php
      $canCancel = ($isManager && count($managers) > 1)
          || (!$isManager && $isMember);
      $roleLabel = Lang::txt('COM_GROUPS_GROUP') . ' '
          . ($isManager ? Lang::txt('COM_GROUPS_TOOLBAR_MANAGER') : Lang::txt('COM_GROUPS_TOOLBAR_MEMBER'));
    @endphp
    <li>
      <div class="dropdown {{ $isManager ? 'manager' : 'member' }}">
        <button type="button" tabindex="0" role="button"
                class="btn btn-sm m-1">
          {{ $roleLabel }}
          <svg class="inline-block w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
          </svg>
        </button>
        <ul tabindex="0" class="dropdown-content menu bg-base-100 rounded-box z-10 w-52 p-2 shadow">
          @if($group->get('published') != 2)
            @if($isManager)
              @if($membership_control == 1 && $joinPolicy != 3)
                <li>
                  <a class="group-invite" href="{{ $baseUrl }}&task=invite">
                    {{ Lang::txt('COM_GROUPS_TOOLBAR_INVITE') }}
                  </a>
                </li>
              @endif
              <li>
                <a class="group-edit" href="{{ $baseUrl }}&task=edit">
                  {{ Lang::txt('COM_GROUPS_TOOLBAR_EDIT') }}
                </a>
              </li>
              <li>
                <a class="group-pages" href="{{ $baseUrl }}&task=pages">
                  {{ Lang::txt('COM_GROUPS_TOOLBAR_PAGES') }}
                </a>
              </li>
              @if($membership_control == 1)
                <li class="divider"></li>
              @endif
            @endif

            @if(!$isManager && $perms::userHasPermissionForGroupAction($group, 'group.invite'))
              @if($membership_control == 1)
                <li>
                  <a class="group-invite" href="{{ $baseUrl }}&task=invite">
                    {{ Lang::txt('COM_GROUPS_TOOLBAR_INVITE') }}
                  </a>
                </li>
              @endif
            @endif

            @if(!$isManager && $perms::userHasPermissionForGroupAction($group, 'group.edit'))
              <li>
                <a class="group-edit" href="{{ $baseUrl }}&task=edit">
                  {{ Lang::txt('COM_GROUPS_TOOLBAR_EDIT') }}
                </a>
              </li>
            @endif

            @if(!$isManager && $perms::userHasPermissionForGroupAction($group, 'group.pages'))
              <li>
                <a class="group-pages" href="{{ $baseUrl }}&task=pages">
                  {{ Lang::txt('COM_GROUPS_TOOLBAR_PAGES') }}
                </a>
              </li>
            @endif
          @endif

          @if($canCancel && $membership_control == 1)
            <li>
              @php $cancelUrl = Route::url('index.php?option=com_groups&cn=' . $cn . '&task=cancel'); @endphp
              <a class="group-cancel cancel_group_membership" href="{{ $cancelUrl }}">
                {{ Lang::txt('COM_GROUPS_TOOLBAR_CANCEL') }}
              </a>
            </li>
            @if($isManager)
              <li class="divider"></li>
            @endif
          @endif

          @if($isManager && $membership_control == 1)
            <li>
              <a class="group-delete" href="{{ $baseUrl }}&task=delete">
                {{ Lang::txt('COM_GROUPS_TOOLBAR_DELETE') }}
              </a>
            </li>
          @endif

          @if($logoutLink)
            <li class="divider"></li>
            <li>
              <a class="logout" href="{{ $logoutUrl }}">
                {{ Lang::txt('COM_GROUPS_TOOLBAR_LOGOUT') }}
              </a>
            </li>
          @endif
        </ul>
      </div>
    </li>
  @endif
</ul>
