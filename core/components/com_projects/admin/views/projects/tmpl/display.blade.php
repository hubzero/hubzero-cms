{{--
  Projects — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $__view->css();

  $setup_complete = $config->get('confirm_step', 0) ? 3 : 2;
  $sort    = $filters['sortby'] ?? 'title';
  $sortDir = $filters['sortdir'] ?? 'ASC';

  $base = rtrim(Request::base(), DS);
  if (substr($base, -13) == 'administrator') {
      $base = substr($base, 0, strlen($base) - 13);
  }

  $pagination = $__view->pagination($total, $start, $limit);

  Toolbar::title(Lang::txt('Projects'), 'projects');
  if (User::authorise('core.manage', $option) && $config->get('custom_profile') == 'custom') {
      Toolbar::custom('customizeDescription', 'menus', 'menus', 'COM_PROJECTS_CUSTOM_DESCRIPTION', false);
      Toolbar::spacer();
  }
  if (User::authorise('core.edit.state', $option)) {
      Toolbar::archiveList();
  }
  if (User::authorise('core.edit', $option)) {
      Toolbar::editList();
  }
  if (User::authorise('core.admin', $option)) {
      Toolbar::spacer();
      Toolbar::preferences('com_projects', '550');
  }
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
        <label for="filter_search" class="sr-only">{{ Lang::txt('COM_PROJECTS_SEARCH') }}</label>
        <input type="text"
               name="search"
               id="filter_search"
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('COM_PROJECTS_SEARCH') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_PROJECTS_GO') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      <label for="filter-filterby" class="sr-only">{{ Lang::txt('COM_PROJECTS_FILTER_STATUS') }}</label>
      <select name="filterby" id="filter-filterby"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="" @selected(($filters['filterby'] ?? '') === '')>
          {{ Lang::txt('COM_PROJECTS_FILTER_STATUS_ALL') }}
        </option>
        <option value="active" @selected(($filters['filterby'] ?? '') === 'active')>
          {{ Lang::txt('COM_PROJECTS_FILTER_STATUS_ACTIVE') }}
        </option>
        <option value="archived" @selected(($filters['filterby'] ?? '') === 'archived')>
          {{ Lang::txt('COM_PROJECTS_FILTER_STATUS_ARCHIVED') }}
        </option>
      </select>

      <label for="filter-access" class="sr-only">{{ Lang::txt('JOPTION_SELECT_ACCESS') }}</label>
      <select name="access" id="filter-access"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="">{{ Lang::txt('JOPTION_SELECT_ACCESS') }}</option>
        {!! Html::select('options', Html::access('assetgroups'), 'value', 'text', $filters['access'] ?? '') !!}
      </select>

      <label for="filter-quota" class="sr-only">{{ Lang::txt('COM_PROJECTS_QUOTA') }}</label>
      <select name="quota" id="filter-quota"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="all" @selected(($filters['quota'] ?? 'all') === 'all')>
          {{ Lang::txt('COM_PROJECTS_QUOTA_ALL') }}
        </option>
        <option value="regular" @selected(($filters['quota'] ?? '') === 'regular')>
          {{ Lang::txt('COM_PROJECTS_QUOTA_REGULAR') }}
        </option>
        <option value="premium" @selected(($filters['quota'] ?? '') === 'premium')>
          {{ Lang::txt('COM_PROJECTS_QUOTA_PREMIUM') }}
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
          <th class="priority-5">
            {!! Html::grid('sort', 'ID', 'id', $sortDir, $sort) !!}
          </th>
          <th class="priority-5 w-10"></th>
          <th>{!! Html::grid('sort', 'COM_PROJECTS_TITLE', 'title', $sortDir, $sort) !!}</th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_PROJECTS_OWNER', 'owner', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_PROJECTS_FEATURED', 'featured', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">{{ Lang::txt('COM_PROJECTS_TEAM') }}</th>
          <th>{!! Html::grid('sort', 'COM_PROJECTS_STATUS', 'status', $sortDir, $sort) !!}</th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_PROJECTS_PRIVACY', 'privacy', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">{{ Lang::txt('COM_PROJECTS_QUOTA') }}</th>
          <th class="priority-3">{{ Lang::txt('COM_PROJECTS_ACTIVITY') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="11">
            <div class="admin-pagination">
              {!! $pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($rows as $i => $row)
          @php
            if ($row->owned_by_group && !$row->groupcn) {
                $ownerDisplay = '<em class="text-muted-foreground">'
                    . Lang::txt('COM_PROJECTS_INFO_DELETED_GROUP')
                    . '</em>';
            } elseif ($row->owned_by_group) {
                $ownerDisplay = e($row->groupname)
                    . '<br /><span class="text-xs text-muted-foreground">'
                    . e($row->groupcn) . '</span>';
            } else {
                $ownerDisplay = $row->authorname
                    ? e($row->authorname)
                    : '<em class="text-muted-foreground">' . Lang::txt('(unknown)') . '</em>';
            }
            $ownerIsGroup = (bool)$row->owned_by_group;

            if ($row->state == 1 && $row->setup_stage >= $setup_complete) {
                $statusCls  = 'badge-success';
                $statusText = Lang::txt('Active') . ' ' . Lang::txt('since')
                    . ' ' . Date::of($row->created)->toLocal('M d, Y');
            } elseif ($row->state == 2) {
                $statusCls  = 'badge-error';
                $statusText = Lang::txt('Deleted');
            } elseif ($row->setup_stage < $setup_complete) {
                $statusCls  = 'badge-info';
                $statusText = Lang::txt('Setup in progress');
            } elseif ($row->state == 0) {
                $statusCls  = 'badge-ghost';
                $statusText = Lang::txt('Inactive/Suspended');
            } elseif ($row->state == 3) {
                $statusCls  = 'badge-warning';
                $statusText = Lang::txt('Archived');
            } elseif ($row->state == 5) {
                $statusCls  = 'badge-warning';
                $statusText = Lang::txt('Pending approval');
            } else {
                $statusCls  = 'badge-ghost';
                $statusText = '';
            }

            $rowParams = new \Hubzero\Config\Registry($row->params);
            $quotaVal  = $rowParams->get('quota', $defaultQuota);
            $quotaGB   = \Components\Projects\Helpers\Html::convertSize($quotaVal, 'b', 'GB', 2);

            $accessObj = \Hubzero\Access\Viewlevel::oneOrNew($row->access);

            $isSynced  = ($row->owned_by_group && $row->sync_group);

            $editUrl     = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id[]=' . $row->id, false
            );
            $teamUrl     = Route::url(
                'index.php?option=' . $option
                . '&controller=team&project=' . $row->id, false
            );
            $activityUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=activity&project=' . $row->id, false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->id }}"
                     class="checkbox checkbox-sm"
                     data-check-item />
              <label for="cb{{ $i }}" class="sr-only">{{ $row->id }}</label>
            </td>
            <td class="priority-5 text-right">{{ $row->id }}</td>
            <td class="priority-5">
              <img src="{{ rtrim($base, '/') }}/projects/{{ $row->alias }}/media"
                   width="30" height="30"
                   alt="{{ $row->alias }}"
                   class="rounded"
                   data-style-onerror-display="none" />
            </td>
            <td>
              <a href="{{ $editUrl }}" class="font-medium">{{ $row->title }}</a>
              <br /><span class="text-xs text-muted-foreground">{{ $row->alias }}</span>
            </td>
            <td class="priority-3">
              {!! $ownerDisplay !!}
            </td>
            <td class="priority-4">
              @if($row->featured)
                <span class="badge badge-success badge-sm">{{ Lang::txt('JYES') }}</span>
              @else
                <span class="badge badge-ghost badge-sm">{{ Lang::txt('JNO') }}</span>
              @endif
            </td>
            <td class="priority-3">
              <a href="{{ $teamUrl }}"
                 class="badge {{ $isSynced ? 'badge-info' : 'badge-ghost' }} badge-sm">
                {{ Lang::txt('COM_PROJECTS_TEAM') }}
              </a>
            </td>
            <td>
              <span class="badge {{ $statusCls }} badge-sm whitespace-nowrap">
                {{ $statusText }}
              </span>
            </td>
            <td class="priority-4">
              <span class="badge badge-ghost badge-sm">{{ $accessObj->title }}</span>
            </td>
            <td class="priority-4 whitespace-nowrap">{{ $quotaGB }} GB</td>
            <td class="priority-3">
              <a href="{{ $activityUrl }}" class="badge badge-ghost badge-sm">
                {{ Lang::txt('COM_PROJECTS_ACTIVITY') }}
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="11" class="text-center py-6 text-muted-foreground">
              {{ Lang::txt('COM_PROJECTS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
