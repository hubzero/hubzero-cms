{{--
  Projects Team — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $__view->css();

  $sort    = $filters['sort'] ?? 'name';
  $sortDir = $filters['sort_Dir'] ?? 'ASC';

  $group      = $project->groupOwner();
  $members    = $group ? $group->get('members') : [];
  $managers   = $group ? $group->get('managers') : [];
  $groupSynced = ($group && $project->get('sync_group'));

  $pagination = $__view->pagination($total, $filters['start'] ?? 0, $filters['limit'] ?? 25);

  $newMemberUrl = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&tmpl=component&task=new&project=' . ($filters['project'] ?? ''), false
  );

  Toolbar::title(Lang::txt('COM_PROJECTS') . ': ' . Lang::txt('COM_PROJECTS_TEAM'), 'projects');
  Toolbar::appendButton('Popup', 'new', 'COM_PROJECTS_TEAM_NEW', $newMemberUrl, 570, 520);
  Toolbar::spacer();
  Toolbar::deleteList('COM_PROJECTS_TEAM_DELETE', 'delete');
  Toolbar::spacer();
  Toolbar::help('team');
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  @if($groupSynced)
    <div class="alert alert-warning mb-4 text-sm">
      @php
        $groupUrl  = Route::url(
            'index.php?option=com_groups&controller=membership&gid=' . $group->get('cn'), false
        );
        $groupLink = '<a href="' . $groupUrl . '" class="font-medium underline">'
            . e($group->get('description'))
            . ' (' . e($group->get('cn')) . ')</a>';
        $syncMsg = 'Membership is synced with group "%s".'
            . ' Addition or removal of members in that'
            . ' group must handled through the group\'s membership interface.';
      @endphp
      {!! Lang::txt($syncMsg, $groupLink) !!}
    </div>
  @endif

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th colspan="8" class="py-2 px-4 text-sm font-medium text-muted-foreground bg-base-200">
            <a href="{{ Route::url('index.php?option=' . $option, false) }}"
               class="hover:text-base-content underline">
              {{ Lang::txt('COM_PROJECTS') }}
            </a>
            &rsaquo;
            <span class="text-base-content">({{ $project->get('alias') }})</span>
            {{ $project->get('title') }}
          </th>
        </tr>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_PROJECTS_TEAM_USERID', 'uidNumber', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_PROJECTS_TEAM_NAME', 'name', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_PROJECTS_TEAM_USERNAME', 'username', $sortDir, $sort) !!}
          </th>
          <th class="priority-5">{{ Lang::txt('COM_PROJECTS_TEAM_ROLE') }}</th>
          <th class="priority-4">{{ Lang::txt('COM_PROJECTS_TEAM_JOINED') }}</th>
          <th class="priority-4">{{ Lang::txt('COM_PROJECTS_TEAM_LAST_VISIT') }}</th>
          <th class="priority-4">{{ Lang::txt('COM_PROJECTS_TEAM_ADDED_AS_PART_OF_GROUP') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8">
            <div class="admin-pagination">
              {!! $pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($rows as $i => $row)
          @php
            $username = $row->username ?: $row->invited_email;
            $isOwner  = ($project->owner('id') == $row->userid);

            $roleConst = \Components\Projects\Models\Orm\Owner::ROLE_MANAGER;
            $reviewConst = \Components\Projects\Models\Orm\Owner::ROLE_REVIEWER;

            switch ($row->role) {
                case \Components\Projects\Models\Orm\Owner::ROLE_MANAGER:
                    $roleLabel = Lang::txt('COM_PROJECTS_TEAM_LABEL_OWNER');
                    $roleCls   = 'badge-info';
                    break;
                case \Components\Projects\Models\Orm\Owner::ROLE_REVIEWER:
                    $roleLabel = Lang::txt('COM_PROJECTS_TEAM_LABEL_REVIEWER');
                    $roleCls   = 'badge-ghost';
                    break;
                default:
                    $roleLabel = Lang::txt('COM_PROJECTS_TEAM_LABEL_COLLABORATOR');
                    $roleCls   = 'badge-ghost';
            }

            $disabled = $row->native
                && (($groupSynced && in_array($row->userid, $members))
                    || ($managers_count == 1 && $row->role == 1));

            $memberEditUrl = Route::url(
                'index.php?option=com_members&controller=members&task=edit&id=' . $row->userid, false
            );

            $lastvisit = ($row->lastvisit && $row->lastvisit != '0000-00-00 00:00:00')
                ? Date::of($row->lastvisit)->relative()
                : Lang::txt('COM_PROJECTS_TEAM_NEVER');
          @endphp
          <tr>
            <td class="column-check">
              @if(!$disabled)
                <input type="checkbox"
                       name="id[]"
                       id="cb{{ $i }}"
                       value="{{ $row->id }}"
                       class="checkbox checkbox-sm"
                       data-check-item />
                <label for="cb{{ $i }}" class="sr-only">{{ $row->id }}</label>
              @endif
            </td>
            <td class="priority-4">{{ $row->userid }}</td>
            <td>
              @if(isset($row->username) && $row->username)
                <a href="{{ $memberEditUrl }}" class="link link-primary">
                  {{ $row->fullname }}
                </a>
              @else
                {{ $row->fullname }}
              @endif
            </td>
            <td class="priority-3 text-sm text-muted-foreground">
              {{ $username }}
            </td>
            <td class="priority-5">
              @if($isOwner)
                <span class="badge badge-primary badge-sm">
                  {{ Lang::txt('COM_PROJECTS_TEAM_OWNER') }} / {{ $roleLabel }}
                </span>
              @else
                <select name="role[{{ $row->userid }}]"
                        class="select select-bordered select-xs"
                        aria-label="{{ Lang::txt('COM_PROJECTS_TEAM_ROLE') }}"
                        data-submit-task="update">
                  <option value="1" @selected($row->role == \Components\Projects\Models\Orm\Owner::ROLE_MANAGER)>
                    {{ Lang::txt('COM_PROJECTS_TEAM_LABEL_OWNER') }}
                  </option>
                  <option value="0" @selected(!$row->role || $row->role == \Components\Projects\Models\Orm\Owner::ROLE_INVITEE)>
                    {{ Lang::txt('COM_PROJECTS_TEAM_LABEL_COLLABORATOR') }}
                  </option>
                  <option value="5" @selected($row->role == \Components\Projects\Models\Orm\Owner::ROLE_REVIEWER)>
                    {{ Lang::txt('COM_PROJECTS_TEAM_LABEL_REVIEWER') }}
                  </option>
                </select>
              @endif
            </td>
            <td class="priority-4 text-sm whitespace-nowrap">
              @if($row->status == 1)
                {{ Date::of($row->added)->toLocal('M d, Y') }}
              @else
                <span class="badge badge-info badge-sm">
                  {{ Lang::txt('COM_PROJECTS_TEAM_INVITED') }}
                </span>
              @endif
            </td>
            <td class="priority-4 text-sm">{{ $lastvisit }}</td>
            <td class="priority-4 text-sm">
              @if($row->groupdesc)
                {{ \Hubzero\Utility\Str::truncate($row->groupdesc, 30) }}
              @endif
              <span class="block text-xs text-muted-foreground">
                {{ $row->groupname ?: Lang::txt('COM_PROJECTS_NONE') }}
              </span>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center py-6 text-muted-foreground">
              {{ Lang::txt('COM_PROJECTS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <input type="hidden" name="project" value="{{ $filters['project'] ?? '' }}" />
</x-admin-form>
