{{--
  Access Levels — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo   = \Components\Members\Helpers\Admin::getActions('component');
  $sort    = $filters['sort'] ?? 'title';
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $canOrder  = User::authorise('core.edit.state', $option);
  $saveOrder = $sort == 'a.ordering';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS') }}: {{ Lang::txt('COM_MEMBERS_ACCESSLEVELS') }}"
    icon="user"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<nav role="navigation" class="sub sub-navigation">
  <ul>
    <li>
      <a href="{!! Route::url('index.php?option=' . $option . '&controller=accessgroups', false) !!}">
        {{ Lang::txt('COM_MEMBERS_ACCESSGROUPS') }}
      </a>
    </li>
    <li>
      <a class="active"
         href="{!! Route::url('index.php?option=' . $option . '&controller=accesslevels', false) !!}">
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
             placeholder="{{ Lang::txt('COM_MEMBERS_SEARCH_TITLE_LEVELS') }}" />
      <button type="submit" class="btn btn-sm btn-primary">
        {{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}
      </button>
      <button type="button" class="btn btn-sm btn-ghost" data-clear-search>
        {{ Lang::txt('JSEARCH_RESET') }}
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
          <th class="priority-3">
            {{ Lang::txt('JGRID_HEADING_ID') }}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_MEMBERS_HEADING_LEVEL_NAME', 'title', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'JGRID_HEADING_ORDERING', 'ordering', $sortDir, $sort) !!}
            @if($canOrder && $saveOrder)
              {!! Html::grid('order', $rows) !!}
            @endif
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
        @php $n = $rows->count(); @endphp
        @foreach($rows as $i => $row)
          @php
            $canEdit   = User::authorise('core.edit', $option);
            $canChange = User::authorise('core.edit.state', $option);
            $editUrl   = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->get('id'), false
            );
          @endphp
          <tr>
            <td class="column-check">
              {!! Html::grid('id', $i, $row->get('id')) !!}
            </td>
            <td class="priority-3">{{ (int) $row->get('id') }}</td>
            <td>
              @if($canEdit)
                <a href="{!! $editUrl !!}" class="link link-hover text-primary font-medium">
                  {{ $row->get('title') }}
                </a>
              @else
                {{ $row->get('title') }}
              @endif
            </td>
            <td>
              @if($canChange && $saveOrder)
                @if($sortDir == 'asc')
                  @if($i > 0)
                    {!! Html::grid('orderUp', $i, 'orderup', '', 'JLIB_HTML_MOVE_UP', true, 'cb') !!}
                  @endif
                  @if($i < ($n - 1))
                    {!! Html::grid('orderDown', $i, 'orderdown', '', 'JLIB_HTML_MOVE_DOWN', true, 'cb') !!}
                  @endif
                @elseif($sortDir == 'desc')
                  @if($i > 0)
                    {!! Html::grid('orderUp', $i, 'orderdown', '', 'JLIB_HTML_MOVE_UP', true, 'cb') !!}
                  @endif
                  @if($i < ($n - 1))
                    {!! Html::grid('orderDown', $i, 'orderup', '', 'JLIB_HTML_MOVE_DOWN', true, 'cb') !!}
                  @endif
                @endif
                <input type="text"
                       name="order[]"
                       size="5"
                       class="input input-bordered input-xs w-16 text-center"
                       value="{{ $row->get('ordering') }}"
                       @if(!$saveOrder) disabled @endif />
              @else
                {{ $row->get('ordering') }}
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
