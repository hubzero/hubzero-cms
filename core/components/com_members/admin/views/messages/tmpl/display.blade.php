{{--
  Messaging Actions — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo   = \Components\Members\Helpers\Permissions::getActions('component');
  $sort    = $filters['sort'] ?? 'component';
  $sortDir = $filters['sort_Dir'] ?? 'asc';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_MEMBERS') }}: {{ Lang::txt('COM_MEMBERS_MENU_MESSAGING') }}"
    icon="user"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  <x-admin-filters>
    <select name="component"
            id="field-component"
            class="select select-bordered select-sm"
            data-submit-on-change
            aria-label="{{ Lang::txt('COM_MEMBERS_FILTER_COMPONENT') }}">
      <option value="">{{ Lang::txt('COM_MEMBERS_FILTER_COMPONENT') }}</option>
      @if($components->count())
        @foreach($components as $comp)
          <option value="{{ $comp->component }}"
                  @selected(($filters['component'] ?? '') == $comp->component)>
            {{ $comp->component }}
          </option>
        @endforeach
      @endif
    </select>
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
            {!! Html::grid('sort', 'COM_MEMBERS_COL_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">
            {!! Html::grid('sort', 'COM_MEMBERS_COL_COMPONENT', 'component', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_MEMBERS_COL_ACTION', 'action', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_MEMBERS_COL_TITLE', 'title', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->id, false
            );
          @endphp
          <tr>
            <td class="column-check">
              @if($canDo->get('core.edit'))
                <input type="checkbox"
                       name="id[]"
                       id="cb{{ $i }}"
                       value="{{ $row->id }}"
                       class="checkbox checkbox-sm"
                       data-check-item
                       aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $row->action) }}" />
              @endif
            </td>
            <td class="priority-4">{{ $row->id }}</td>
            <td class="priority-2">{{ $row->component }}</td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{!! $editUrl !!}" class="link link-hover text-primary font-medium">
                  {{ $row->action }}
                </a>
              @else
                {{ $row->action }}
              @endif
            </td>
            <td class="priority-3">{{ $row->title }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
