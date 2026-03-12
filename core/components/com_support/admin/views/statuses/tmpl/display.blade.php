{{--
  Support — Statuses list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo   = \Components\Support\Helpers\Permissions::getActions('status');
  $sort    = $filters['sort'] ?? 'id';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(
      Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_STATUSES'),
      'support'
  );
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList();
  }
  Toolbar::spacer();
  Toolbar::help('status');
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  @slot('filters')
    <x-admin-filters>
      <label for="filter-open" class="sr-only">{{ Lang::txt('COM_SUPPORT_COL_FOR') }}</label>
      <select name="open"
              id="filter-open"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="-1" @selected(($filters['open'] ?? -1) < 0)>
          {{ Lang::txt('COM_SUPPORT_FOR_ALL') }}
        </option>
        <option value="0" @selected(($filters['open'] ?? -1) == 0)>
          {{ Lang::txt('COM_SUPPORT_FOR_CLOSED') }}
        </option>
        <option value="1" @selected(($filters['open'] ?? -1) == 1)>
          {{ Lang::txt('COM_SUPPORT_FOR_OPEN') }}
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
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_SUPPORT_COL_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">
            {!! Html::grid('sort', 'COM_SUPPORT_COL_FOR', 'open', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_SUPPORT_COL_TITLE', 'title', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_SUPPORT_COL_ALIAS', 'alias', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_SUPPORT_COL_COLOR', 'color', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
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
                . '&task=edit&id=' . $row->get('id'),
                false, false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     aria-label="{{ $row->get('title') }}"
                     class="checkbox checkbox-sm"
                     data-check-item />
            </td>
            <td class="priority-4">{{ (int) $row->get('id') }}</td>
            <td class="priority-2">
              @if($row->get('open'))
                {{ Lang::txt('COM_SUPPORT_FOR_OPEN') }}
              @else
                {{ Lang::txt('COM_SUPPORT_FOR_CLOSED') }}
              @endif
            </td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}"
                   class="link link-hover text-primary font-medium">
                  {{ $row->get('title') }}
                </a>
              @else
                {{ $row->get('title') }}
              @endif
            </td>
            <td class="priority-3">
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}"
                   class="link link-hover">
                  {{ $row->get('alias') }}
                </a>
              @else
                {{ $row->get('alias') }}
              @endif
            </td>
            <td class="priority-4">
              @if($row->get('color'))
                <span class="inline-block w-5 h-5 rounded border border-base-300"
                      data-style-bg="#{{ $row->get('color') }}"
                      title="#{{ $row->get('color') }}"></span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
