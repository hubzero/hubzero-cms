{{--
  Groups — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Groups\Helpers\Permissions::getActions();

  $__view->css();

  Toolbar::title(Lang::txt('COM_GROUPS'), 'groups');

  if ($canDo->get('core.admin')) {
      Toolbar::preferences('com_groups', '550');
      Toolbar::spacer();
  }
  if ($canDo->get('core.manage') && $config->get('super_gitlab', 0)) {
      Toolbar::custom('update', 'refresh', '', 'COM_GROUPS_UPDATE_CODE');
      Toolbar::spacer();
  }
  if ($canDo->get('core.edit.state')) {
      Toolbar::publishList();
      Toolbar::unpublishList();
      Toolbar::archiveList();
      Toolbar::spacer();
  }
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList('COM_GROUPS_DELETE_CONFIRM', 'delete');
  }
  Toolbar::spacer();
  Toolbar::help('groups');

  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $sort    = $filters['sort'] ?? 'description';
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  @slot('filters')
    <x-admin-filters>
      @slot('search')
        <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
        <input type="text"
               name="search"
               id="filter_search"
               class="input input-bordered input-sm"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('COM_GROUPS_SEARCH') }}" />
        <button type="submit" class="btn btn-sm btn-primary">
          {{ Lang::txt('COM_GROUPS_GO') }}
        </button>
        <button type="button" class="btn btn-sm btn-ghost filter-clear">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      <label for="filter-type" class="sr-only visually-hidden">
        {{ Lang::txt('COM_GROUPS_TYPE') }}
      </label>
      <select name="type"
              id="filter-type"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="all" @selected(($filters['type'][0] ?? 'all') == 'all')>
          {{ Lang::txt('COM_GROUPS_TYPE') }}
        </option>
        <option value="hub" @selected(($filters['type'][0] ?? '') == 'hub')>
          Hub
        </option>
        <option value="super" @selected(($filters['type'][0] ?? '') == 'super')>
          Super
        </option>
        @if($canDo->get('core.admin'))
          <option value="system" @selected(($filters['type'][0] ?? '') == 'system')>
            System
          </option>
        @endif
        <option value="project" @selected(($filters['type'][0] ?? '') == 'project')>
          Project
        </option>
        <option value="course" @selected(($filters['type'][0] ?? '') == 'course')>
          Course
        </option>
      </select>

      <label for="filter-discoverability" class="sr-only visually-hidden">
        {{ Lang::txt('COM_GROUPS_DISCOVERABILITY') }}
      </label>
      <select name="discoverability"
              id="filter-discoverability"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="" @selected(($filters['discoverability'] ?? null) === null || ($filters['discoverability'] ?? '') === '')>
          {{ Lang::txt('COM_GROUPS_DISCOVERABILITY') }}
        </option>
        <option value="0" @selected(isset($filters['discoverability']) && $filters['discoverability'] === '0')>
          {{ Lang::txt('COM_GROUPS_DISCOVERABILITY_VISIBLE') }}
        </option>
        <option value="1" @selected(($filters['discoverability'] ?? '') === '1' || ($filters['discoverability'] ?? null) === 1)>
          {{ Lang::txt('COM_GROUPS_DISCOVERABILITY_HIDDEN') }}
        </option>
      </select>

      <label for="filter-policy" class="sr-only visually-hidden">
        {{ Lang::txt('COM_GROUPS_JOIN_POLICY') }}
      </label>
      <select name="policy"
              id="filter-policy"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="" @selected(($filters['policy'] ?? '') == '')>
          {{ Lang::txt('COM_GROUPS_JOIN_POLICY') }}
        </option>
        <option value="open" @selected(($filters['policy'] ?? '') == 'open')>
          {{ Lang::txt('COM_GROUPS_JOIN_POLICY_PUBLIC') }}
        </option>
        <option value="restricted" @selected(($filters['policy'] ?? '') == 'restricted')>
          {{ Lang::txt('COM_GROUPS_JOIN_POLICY_RESTRICTED') }}
        </option>
        <option value="invite" @selected(($filters['policy'] ?? '') == 'invite')>
          {{ Lang::txt('COM_GROUPS_JOIN_POLICY_INVITE') }}
        </option>
        <option value="closed" @selected(($filters['policy'] ?? '') == 'closed')>
          {{ Lang::txt('COM_GROUPS_JOIN_POLICY_CLOSED') }}
        </option>
      </select>

      <label for="filter-state" class="sr-only visually-hidden">
        {{ Lang::txt('COM_GROUPS_STATE') }}
      </label>
      <select name="state"
              id="filter-state"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="-1" @selected(($filters['state'] ?? -1) == -1)>
          {{ Lang::txt('COM_GROUPS_STATE') }}
        </option>
        <option value="0" @selected(($filters['state'] ?? -1) == 0)>
          {{ Lang::txt('COM_GROUPS_UNPUBLISHED') }}
        </option>
        <option value="1" @selected(($filters['state'] ?? -1) == 1)>
          {{ Lang::txt('COM_GROUPS_PUBLISHED') }}
        </option>
        <option value="2" @selected(($filters['state'] ?? -1) == 2)>
          {{ Lang::txt('COM_GROUPS_ARCHIVED') }}
        </option>
      </select>
    </x-admin-filters>
  @endslot

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th class="priority-6">
            {!! Html::grid('sort', 'COM_GROUPS_ID', 'gidNumber', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_GROUPS_NAME', 'description', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_GROUPS_CN', 'cn', $sortDir, $sort) !!}
          </th>
          <th class="priority-5">
            {!! Html::grid('sort', 'COM_GROUPS_TYPE', 'type', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_GROUPS_PUBLISHED', 'published', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_GROUPS_APPROVED', 'approved', $sortDir, $sort) !!}
          </th>
          <th>{{ Lang::txt('COM_GROUPS_MEMBERS') }}</th>
          <th>{{ Lang::txt('COM_GROUPS_PAGES') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="9">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'] ?? 0, $filters['limit'] ?? 25) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @php
          $database = \Hubzero\Facades\App::get('db');
          $p        = new \Components\Groups\Tables\Page($database);
          $token    = Session::getFormToken();
        @endphp
        @forelse($rows as $i => $row)
          @php
            $group = new \Hubzero\User\Group();
            $group->read($row->gidNumber);

            $type = match ((string) $row->type) {
                '0' => '<span class="badge badge-sm badge-ghost">'
                    . Lang::txt('COM_GROUPS_TYPE_SYSTEM') . '</span>',
                '1' => '<span class="badge badge-sm badge-info">'
                    . Lang::txt('COM_GROUPS_TYPE_HUB') . '</span>',
                '2' => '<span class="badge badge-sm badge-warning">'
                    . Lang::txt('COM_GROUPS_TYPE_PROJECT') . '</span>',
                '3' => '<span class="badge badge-sm badge-success">'
                    . Lang::txt('COM_GROUPS_TYPE_SUPER') . '</span>',
                '4' => '<span class="badge badge-sm badge-secondary">'
                    . Lang::txt('COM_GROUPS_TYPE_COURSE') . '</span>',
                default => '',
            };

            $pages = $p->count(['gidNumber' => $row->gidNumber]);

            $inviteemails = \Hubzero\User\Group\InviteEmail::all()
                ->whereEquals('gidNumber', $group->get('gidNumber'))
                ->total();

            $members    = $group->get('members');
            $managers   = $group->get('managers');
            $applicants = $group->get('applicants');
            $invitees   = $group->get('invitees');
            $true_members = array_diff($members, $managers);

            $tip  = '<table><tbody>';
            $tip .= '<tr><th>' . Lang::txt('COM_GROUPS_MEMBERS') . '</th>'
                . '<td>' . count($true_members) . '</td></tr>';
            $tip .= '<tr><th>' . Lang::txt('COM_GROUPS_MANAGERS') . '</th>'
                . '<td>' . count($managers) . '</td></tr>';
            $tip .= '<tr><th>' . Lang::txt('COM_GROUPS_APPLICANTS') . '</th>'
                . '<td>' . count($applicants) . '</td></tr>';
            $tip .= '<tr><th>' . Lang::txt('COM_GROUPS_INVITEES') . '</th>'
                . '<td>' . (count($invitees) + $inviteemails) . '</td></tr>';
            $tip .= '</tbody></table>';

            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->cn, false
            );
            $publishUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=publish&id=' . $row->cn
                . '&' . $token . '=1', false
            );
            $unpublishUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=unpublish&id=' . $row->cn
                . '&' . $token . '=1', false
            );
            $approveUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=approve&id=' . $row->cn
                . '&' . $token . '=1', false
            );
            $unapproveUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=unapprove&id=' . $row->cn
                . '&' . $token . '=1', false
            );
            $memberUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=membership&gid=' . $row->cn, false
            );
            $pagesUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=pages&gid=' . $row->cn, false
            );
            $memberTip  = Lang::txt('COM_GROUPS_MANAGE_MEMBERSHIP') . '::' . $tip;
            $descDisplay = $row->description
                ? e($row->description)
                : '<span class="empty-field smallsub">' . Lang::txt('COM_GROUPS_NONE') . '</span>';
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->cn }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $row->description ?: $row->cn) }}"
                     data-check-item />
            </td>
            <td class="priority-6">{{ $row->gidNumber }}</td>
            <td class="priority-4">
              @if($canDo->get('core.edit'))
                <a href="{!! $editUrl !!}" class="link link-hover font-medium">
                  {!! $descDisplay !!}
                </a>
              @else
                <span>{!! $descDisplay !!}</span>
              @endif
            </td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{!! $editUrl !!}" class="link link-hover">{{ $row->cn }}</a>
              @else
                {{ $row->cn }}
              @endif
            </td>
            <td class="priority-5">{!! $type !!}</td>
            <td class="priority-3">
              @if($canDo->get('core.edit.state'))
                @if($row->published == 2)
                  <a href="{!! $publishUrl !!}"
                     class="badge badge-sm badge-warning"
                     title="{{ Lang::txt('COM_GROUPS_PUBLISH') }}">
                    {{ Lang::txt('COM_GROUPS_ARCHIVED') }}
                  </a>
                @elseif($row->published == 1)
                  <a href="{!! $unpublishUrl !!}"
                     class="badge badge-sm badge-success"
                     title="{{ Lang::txt('COM_GROUPS_UNPUBLISH') }}">
                    {{ Lang::txt('COM_GROUPS_PUBLISHED') }}
                  </a>
                @else
                  <a href="{!! $publishUrl !!}"
                     class="badge badge-sm badge-ghost"
                     title="{{ Lang::txt('COM_GROUPS_PUBLISH') }}">
                    {{ Lang::txt('COM_GROUPS_UNPUBLISHED') }}
                  </a>
                @endif
              @endif
            </td>
            <td class="priority-3">
              @if($canDo->get('core.edit.state'))
                @if(!$group->get('approved'))
                  <a href="{!! $approveUrl !!}"
                     class="badge badge-sm badge-error"
                     title="{{ Lang::txt('COM_GROUPS_APPROVE') }}">
                    {{ Lang::txt('COM_GROUPS_UNAPPROVED') }}
                  </a>
                @else
                  <a href="{!! $unapproveUrl !!}"
                     class="badge badge-sm badge-success"
                     title="{{ Lang::txt('COM_GROUPS_UNAPPROVE') }}">
                    {{ Lang::txt('COM_GROUPS_APPROVED') }}
                  </a>
                @endif
              @endif
            </td>
            <td>
              @if($canDo->get('core.manage'))
                <a class="glyph member hasTip"
                   href="{!! $memberUrl !!}"
                   title="{{ $memberTip }}">
                  {{ count($members) }}
                </a>
              @else
                <span class="glyph member" title="{{ $memberTip }}">
                  {{ count($members) }}
                </span>
              @endif
            </td>
            <td>
              @if($canDo->get('core.manage'))
                <a href="{!! $pagesUrl !!}">
                  {{ Lang::txt('COM_GROUPS_PAGES_COUNT', $pages) }}
                </a>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center text-muted-foreground py-8">
              {{ Lang::txt('COM_GROUPS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
