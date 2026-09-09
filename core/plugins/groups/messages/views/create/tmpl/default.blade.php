{{--
  Group Messages — compose message form.

  Variables from plugin:
    $option       — component option string
    $group        — group object
    $no_html      — ajax mode flag
    $users        — pre-selected user IDs array
    $members      — group member IDs
    $member_roles — group member roles array
    $params       — plugin params

  @package    hubzero-cms
  @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\User;

  $group_statuses = [
      'all'        => Lang::txt('All Group Members'),
      'managers'   => Lang::txt('All Group Managers'),
      'invitees'   => Lang::txt('All Group Invitees'),
      'applicants' => Lang::txt('All Group Applicants'),
  ];

  $role_name = '';
  $role_id = Request::getInt('role_id');
  if ($role_id) {
      foreach ($member_roles as $role) {
          if ($role['id'] == $role_id) {
              $role_name = $role['name'];
              break;
          }
      }
  }

  if ($params->get('stamp_logo')) {
      $__view->css('
          .hub-mail .cont {
              background: #fff url("' . $params->get('stamp_logo') . '") no-repeat 99% 4%;
          }
      ');
  }

  $messagesUrl = Route::url(
      'index.php?option=' . $option
      . '&cn=' . $group->get('cn')
      . '&active=messages'
  );
  $newMessageUrl = Route::url(
      'index.php?option=' . $option
      . '&cn=' . $group->get('cn')
      . '&active=messages&action=new'
  );
@endphp

<div class="subject">
  @if (!$no_html)
    <div role="tablist" class="tabs tabs-border mb-4">
      <a role="tab" class="tab" href="{{ $messagesUrl }}">
        {{ Lang::txt('PLG_GROUPS_MESSAGES_SENT') }}
      </a>
      <a role="tab" class="tab tab-active" href="{{ $newMessageUrl }}">
        {{ Lang::txt('PLG_GROUPS_MESSAGES_SEND') }}
      </a>
    </div>
  @endif

  <form action="{{ $messagesUrl }}"
        method="post"
        id="hubForm{{ $no_html ? '-ajax' : '' }}">
    <fieldset class="space-y-4">
      <legend class="text-lg font-semibold">
        {{ Lang::txt('Compose Message to Group') }}
      </legend>

      <div class="form-control w-full">
        <label class="label" for="msg-recipient">
          <span class="label-text">
            {{ Lang::txt('GROUP_MESSAGE_USERS') }}
            <span class="text-error" aria-hidden="true">*</span>
          </span>
        </label>
        <select name="users[]" id="msg-recipient" class="select select-bordered w-full">
          <optgroup label="Group Status">
            @foreach ($group_statuses as $val => $name)
              <option value="{{ $val }}" {{ $val == $users[0] ? 'selected' : '' }}>
                {{ $name }}
              </option>
            @endforeach
          </optgroup>
          @if (count($member_roles) > 0)
            <optgroup label="Group Member Roles">
              @foreach ($member_roles as $role)
                <option value="role_{{ $role['id'] }}"
                        {{ $role['name'] == $role_name ? 'selected' : '' }}>
                  {{ $role['name'] }}
                </option>
              @endforeach
            </optgroup>
          @endif
          @if (count($members) > 0)
            <optgroup label="Group Members">
              @foreach ($members as $m)
                @php $u = User::getInstance($m); @endphp
                <option value="{{ $u->get('id') }}"
                        {{ $u->get('id') == $users[0] ? 'selected' : '' }}>
                  {{ $u->get('name') }}
                </option>
              @endforeach
            </optgroup>
          @endif
        </select>
      </div>

      <div class="form-control w-full">
        <label class="label" for="msg-subject">
          <span class="label-text">
            {{ Lang::txt('GROUP_MESSAGE_SUBJECT') }}
            <span class="text-error" aria-hidden="true">*</span>
          </span>
        </label>
        <input type="text"
               name="subject"
               id="msg-subject"
               value=""
               class="input input-bordered w-full"
               required
               aria-required="true" />
      </div>

      <div class="form-control w-full">
        <label class="label" for="msg-message">
          <span class="label-text">
            {{ Lang::txt('GROUP_MESSAGE') }}
            <span class="text-error" aria-hidden="true">*</span>
          </span>
        </label>
        <textarea name="message"
                  id="msg-message"
                  rows="12"
                  class="textarea textarea-bordered w-full"
                  required
                  aria-required="true"></textarea>
      </div>

      <div class="mt-4">
        <button type="submit" class="btn btn-primary">
          {{ Lang::txt('GROUP_MESSAGE_SEND') }}
        </button>
      </div>
    </fieldset>

    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="cn" value="{{ $group->get('cn') }}" />
    <input type="hidden" name="active" value="messages" />
    <input type="hidden" name="action" value="send" />
    <input type="hidden" name="no_html" value="{{ $no_html }}" />
  </form>
</div>
