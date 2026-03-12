{{--
  Access Groups — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo   = \Components\Members\Helpers\Admin::getActions('component');
  $sort    = $filters['sort'] ?? 'title';
  $sortDir = $filters['sort_Dir'] ?? 'asc';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS') }}: {{ Lang::txt('COM_MEMBERS_ACCESSGROUPS') }}"
    icon="user"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<nav role="navigation" class="sub sub-navigation">
  <ul>
    <li>
      <a class="active"
         href="{!! Route::url('index.php?option=' . $option . '&controller=accessgroups', false) !!}">
        {{ Lang::txt('COM_MEMBERS_ACCESSGROUPS') }}
      </a>
    </li>
    <li>
      <a href="{!! Route::url('index.php?option=' . $option . '&controller=accesslevels', false) !!}">
        {{ Lang::txt('COM_MEMBERS_ACCESSLEVELS') }}
      </a>
    </li>
  </ul>
</nav>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  <x-admin-filters>
    @slot('search')
      <input type="text"
             name="filter_search"
             id="filter_search"
             class="input input-bordered input-sm w-60"
             value="{{ $filters['search'] ?? '' }}"
             placeholder="{{ Lang::txt('COM_MEMBERS_SEARCH_IN_GROUPS') }}" />
      <button type="submit" class="btn btn-sm btn-primary">
        {{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}
      </button>
      <button type="button" class="btn btn-sm btn-ghost" data-clear-search>
        {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
      </button>
    @endslot
  </x-admin-filters>

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
          <th class="priority-4">
            {{ Lang::txt('JGRID_HEADING_ID') }}
          </th>
          <th>
            {{ Lang::txt('COM_MEMBERS_HEADING_GROUP_TITLE') }}
          </th>
          <th class="priority-3">
            {{ Lang::txt('COM_MEMBERS_HEADING_USERS_IN_GROUP') }}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="4">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $canEdit = User::authorise('core.edit', $option);
            if (!User::authorise('core.admin')
                && Hubzero\Access\Access::checkGroup($row->get('id'), 'core.admin')
            ) {
                $canEdit = false;
            }

            $level = Hubzero\Access\Group::all()
                ->where('lft', '<', $row->get('lft'))
                ->where('rgt', '>', $row->get('rgt'))
                ->total();

            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->get('id'), false
            );

            $userCount = $row->maps()
                ->select('group_id', 'count', true)
                ->rows(false)
                ->first()
                ->count;
          @endphp
          <tr>
            <td class="column-check">
              @if($canEdit)
                {!! Html::grid('id', $i, $row->get('id')) !!}
              @endif
            </td>
            <td class="priority-4">{{ (int) $row->get('id') }}</td>
            <td>
              {!! str_repeat('<span class="gi text-faint-foreground">|&mdash;</span>', $level) !!}
              @if($canEdit)
                <a href="{!! $editUrl !!}" class="link link-hover text-primary font-medium">
                  {{ $row->get('title') }}
                </a>
              @else
                {{ $row->get('title') }}
              @endif
              @if(Config::get('debug'))
                @php
                  $debugUrl = Route::url(
                      'index.php?option=' . $option
                      . '&controller=' . $controller
                      . '&task=debug&id=' . (int) $row->get('id'), false
                  );
                @endphp
                <a href="{!! $debugUrl !!}" class="btn btn-xs btn-ghost ml-2">
                  {{ Lang::txt('COM_MEMBERS_DEBUG_GROUP') }}
                </a>
              @endif
            </td>
            <td class="priority-3">{{ $userCount }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
