{{--
  Groups — Component view (iframe content for member group memberships)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = (User::authorise('core.admin', 'com_groups') || User::authorise('core.manage', 'com_groups'));
  $canEdit = ($canDo && User::authorise('core.edit', 'com_groups'));
@endphp

<div id="groups">
  @if($canDo)
    @php
      $addUrl = Route::url(
          'index.php?option=' . $option
          . '&controller=' . $controller
          . '&id=' . $id, false
      );
    @endphp
    <form action="{!! $addUrl !!}" method="post">
      <table>
        <tbody>
          <tr>
            <td>
              <input type="hidden" name="option" value="{{ $option }}" />
              <input type="hidden" name="controller" value="{{ $controller }}" />
              <input type="hidden" name="tmpl" value="component" />
              <input type="hidden" name="id" value="{{ $id }}" />
              <input type="hidden" name="task" value="add" />
              {!! Html::input('token') !!}

              <select name="gid" class="select select-bordered select-sm" aria-label="{{ Lang::txt('COM_MEMBERS_SELECT') }}">
                <option value="">{{ Lang::txt('COM_MEMBERS_SELECT') }}</option>
                @if($rows)
                  @foreach($rows as $row)
                    <option value="{{ $row->gidNumber }}">{{ $row->description }} ({{ $row->cn }})</option>
                  @endforeach
                @endif
              </select>

              <select name="tbl" class="select select-bordered select-sm" aria-label="{{ Lang::txt('COM_MEMBERS_GROUPS_ROLE') }}">
                <option value="invitees">{{ Lang::txt('COM_MEMBERS_GROUPS_INVITEES') }}</option>
                <option value="applicants">{{ Lang::txt('COM_MEMBERS_GROUPS_APPLICANTS') }}</option>
                <option value="members" selected>{{ Lang::txt('COM_MEMBERS_GROUPS_MEMBERS') }}</option>
                <option value="managers">{{ Lang::txt('COM_MEMBERS_GROUPS_MANAGERS') }}</option>
              </select>

              <input type="submit"
                     class="btn btn-sm"
                     value="{{ Lang::txt('COM_MEMBERS_GROUPS_ADD') }}" />
            </td>
          </tr>
        </tbody>
      </table>
    </form>
    <br />
  @endif

  @php
    $updateUrl = Route::url(
        'index.php?option=' . $option
        . '&controller=' . $controller
        . '&id=' . $id, false
    );
  @endphp
  <form action="{!! $updateUrl !!}" method="post">
    <input type="hidden" name="option" value="{{ $option }}" />
    <input type="hidden" name="controller" value="{{ $controller }}" />
    <input type="hidden" name="tmpl" value="component" />
    <input type="hidden" name="id" value="{{ $id }}" />
    <input type="hidden" name="task" value="update" />

    <table class="paramlist admintable">
      <tbody>
        @php
          $applicants = \Hubzero\User\Helper::getGroups($id, 'applicants');
          $invitees   = \Hubzero\User\Helper::getGroups($id, 'invitees');
          $members    = \Hubzero\User\Helper::getGroups($id, 'members');
          $managers   = \Hubzero\User\Helper::getGroups($id, 'managers');

          $applicants = is_array($applicants) ? $applicants : array();
          $invitees   = is_array($invitees) ? $invitees : array();
          $members    = is_array($members) ? $members : array();
          $managers   = is_array($managers) ? $managers : array();

          $groups = array_merge($applicants, $invitees);
          $managerids = array();
          foreach ($managers as $manager) {
              $groups[] = $manager;
              $managerids[] = $manager->cn;
          }
          foreach ($members as $mem) {
              if (!in_array($mem->cn, $managerids)) {
                  $groups[] = $mem;
              }
          }

          $db = App::get('db');
        @endphp

        @foreach($groups as $group)
          <tr>
            <td>
              @if($canEdit)
                @php
                  $editUrl = Route::url(
                      'index.php?option=com_groups&controller=manage&task=edit&id=' . $group->cn, false
                  );
                @endphp
                <a href="{!! $editUrl !!}" target="_parent">
                  {{ $group->description }} ({{ $group->cn }})
                </a>
              @else
                {{ $group->description }} ({{ $group->cn }})
              @endif

              @php
                $db->setQuery(
                    "SELECT * FROM `#__xgroups_memberoption` WHERE userid="
                    . $db->quote($id)
                    . " AND gidNumber="
                    . $db->quote($group->gidNumber)
                );
                $memberOptions = $db->loadObjectList();
              @endphp
              @if($memberOptions)
                @foreach($memberOptions as $mo)
                  <div class="admin-field">
                    <label for="memberoption-{{ $mo->id }}" class="label">{{ $mo->optionname }}</label>
                    <input name="memberoption[{{ $mo->id }}]"
                           id="memberoption-{{ $mo->id }}"
                           class="input input-bordered input-sm"
                           size="3"
                           value="{{ $mo->optionvalue }}" />
                    <input type="submit"
                           class="btn btn-sm"
                           value="{{ Lang::txt('COM_MEMBERS_UPDATE') }}" />
                  </div>
                @endforeach
              @endif
            </td>
            <td>
              @php
                if ($group->registered) {
                    $status = Lang::txt('COM_MEMBERS_GROUPS_MEMBER');
                    if ($group->regconfirmed) {
                        $status = Lang::txt('COM_MEMBERS_GROUPS_MEMBER');
                        if ($group->manager) {
                            $status = Lang::txt('COM_MEMBERS_GROUPS_MANAGER');
                        }
                    } else {
                        $status = Lang::txt('COM_MEMBERS_GROUPS_APPLICANT');
                    }
                } else {
                    $status = Lang::txt('COM_MEMBERS_GROUPS_INVITEE');
                }
              @endphp
              {{ $status }}
            </td>
            <td>
              @if($canDo)
                @php
                  $removeUrl = Route::url(
                      'index.php?option=' . $option
                      . '&controller=' . $controller
                      . '&task=remove&tmpl=component&id=' . $id
                      . '&gid=' . $group->cn
                      . '&' . Session::getFormToken() . '=1', false
                  );
                @endphp
                <a class="state trash icon-trash" href="{!! $removeUrl !!}">
                  <span>{{ Lang::txt('COM_MEMBERS_GROUPS_REMOVE') }}</span>
                </a>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>

    {!! Html::input('token') !!}
  </form>
</div>
