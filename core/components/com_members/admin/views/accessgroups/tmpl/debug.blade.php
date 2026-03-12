{{--
  Access Groups — Debug permissions

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $sort    = $filters['sort'] ?? 'name';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  $title = Lang::txt(
      'COM_MEMBERS_VIEW_DEBUG_GROUP_TITLE',
      $group->get('id'),
      $group->get('title')
  );

  Toolbar::title($title, 'groups');
  Toolbar::help('JHELP_USERS_DEBUG_GROUPS');
@endphp

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

@php
  $formAction = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=debug&id=' . (int) $group->get('id'), false
  );
@endphp
<form action="{!! $formAction !!}"
      method="post"
      name="adminForm"
      id="adminForm">

  <x-admin-filters>
    @slot('search')
      <input type="text"
             name="filter_search"
             id="filter_search"
             class="input input-bordered input-sm w-60"
             value="{{ $filters['search'] ?? '' }}"
             placeholder="{{ Lang::txt('COM_MEMBERS_SEARCH_ASSETS') }}" />
      <button type="submit" class="btn btn-sm btn-primary">
        {{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}
      </button>
      <button type="button" class="btn btn-sm btn-ghost" data-clear-search>
        {{ Lang::txt('JSEARCH_RESET') }}
      </button>
    @endslot

    <select name="filter_component"
            class="select select-bordered select-sm"
            data-submit-on-change
            aria-label="{{ Lang::txt('COM_MEMBERS_OPTION_SELECT_COMPONENT') }}">
      <option value="">{{ Lang::txt('COM_MEMBERS_OPTION_SELECT_COMPONENT') }}</option>
      @if(!empty($components))
        {!! Html::select('options', $components, 'value', 'text', $filters['component'] ?? '') !!}
      @endif
    </select>

    <select name="filter_level_start"
            class="select select-bordered select-sm"
            data-submit-on-change
            aria-label="{{ Lang::txt('COM_MEMBERS_OPTION_SELECT_LEVEL_START') }}">
      <option value="">{{ Lang::txt('COM_MEMBERS_OPTION_SELECT_LEVEL_START') }}</option>
      {!! Html::select('options', $levels, 'value', 'text', $filters['level_start'] ?? '') !!}
    </select>

    <select name="filter_level_end"
            class="select select-bordered select-sm"
            data-submit-on-change
            aria-label="{{ Lang::txt('COM_MEMBERS_OPTION_SELECT_LEVEL_END') }}">
      <option value="">{{ Lang::txt('COM_MEMBERS_OPTION_SELECT_LEVEL_END') }}</option>
      {!! Html::select('options', $levels, 'value', 'text', $filters['level_end'] ?? '') !!}
    </select>
  </x-admin-filters>

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <caption class="p-3 text-sm text-muted-foreground">
        {{ Lang::txt('COM_MEMBERS_DEBUG_LEGEND') }}
        <span class="ml-2">—</span>
        <span class="ml-2 badge badge-sm badge-ghost">{!! Lang::txt('COM_MEMBERS_DEBUG_NO_CHECK', '-') !!}</span>
        <span class="ml-1 badge badge-sm badge-warning">{!! Lang::txt('COM_MEMBERS_DEBUG_IMPLICIT_DENY', '-') !!}</span>
        <span class="ml-1 badge badge-sm badge-success">{!! Lang::txt('COM_MEMBERS_DEBUG_EXPLICIT_ALLOW', '&#10003;') !!}</span>
        <span class="ml-1 badge badge-sm badge-error">{!! Lang::txt('COM_MEMBERS_DEBUG_EXPLICIT_DENY', '&#10007;') !!}</span>
      </caption>
      <thead>
        <tr>
          <th>
            {!! Html::grid('sort', 'COM_MEMBERS_HEADING_ASSET_TITLE', 'title', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_MEMBERS_HEADING_ASSET_NAME', 'name', $sortDir, $sort) !!}
          </th>
          @foreach($actions as $key => $action)
            <th class="text-center text-xs" title="{{ Lang::txt($action[1]) }}">
              {{ Lang::txt($key) }}
            </th>
          @endforeach
          <th>
            {!! Html::grid('sort', 'COM_MEMBERS_HEADING_LFT', 'lft', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'JGRID_HEADING_ID', 'id', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="15">
            <div class="admin-pagination">
              {!! $assets->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($assets as $item)
          @php $checks = $item->get('checks'); @endphp
          <tr>
            <td>{{ $item->get('title') }}</td>
            <td>
              {!! str_repeat('<span class="gi text-faint-foreground">|&mdash;</span>', $item->get('level')) !!}
              {{ $item->get('name') }}
            </td>
            @foreach($actions as $action)
              @php
                $name  = $action[0];
                $check = $checks[$name];
              @endphp
              <td class="text-center">
                @if($check === true)
                  <span class="badge badge-sm badge-success">&#10003;</span>
                @elseif($check === false)
                  <span class="badge badge-sm badge-error">&#10007;</span>
                @elseif($check === null)
                  <span class="badge badge-sm badge-warning">-</span>
                @else
                  &nbsp;
                @endif
              </td>
            @endforeach
            <td class="text-center text-sm">
              {{ (int) $item->get('lft') }} - {{ (int) $item->get('rgt') }}
            </td>
            <td class="text-center text-sm">{{ (int) $item->get('id') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="task" value="" autocomplete="off" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="{{ $sort }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $sortDir }}" />
  {!! Html::input('token') !!}
</form>
