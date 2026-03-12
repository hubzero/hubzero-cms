{{--
  Courses: Roles — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo   = \Components\Courses\Helpers\Permissions::getActions();
  $sort    = $filters['sort'] ?? 'title';
  $sortDir = $filters['sort_Dir'] ?? 'asc';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_ROLES') }}"
    icon="courses"
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
      @slot('search')
        <input type="text"
               name="search"
               id="filter_search"
               class="input input-bordered input-sm w-60"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('COM_COURSES_SEARCH_PLACEHOLDER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">
          {{ Lang::txt('COM_COURSES_GO') }}
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
            {!! Html::grid('sort', 'COM_COURSES_COL_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_COURSES_COL_ALIAS', 'alias', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_COURSES_COL_TITLE', 'type', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_COURSES_COL_OFFERING', 'offering_id', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_COURSES_COL_TOTAL', 'total', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
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
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->id }}"
                     aria-label="{{ $row->title }}"
                     class="checkbox checkbox-sm"
                     data-check-item />
            </td>
            <td class="priority-4">
              {{ $row->id }}
            </td>
            <td class="priority-3">
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                  {{ $row->alias }}
                </a>
              @else
                {{ $row->alias }}
              @endif
            </td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}" class="link link-hover text-sm">
                  {{ $row->title }}
                </a>
              @else
                {{ $row->title }}
              @endif
            </td>
            <td>
              {{ $row->offering_id }}
            </td>
            <td class="priority-3">
              {{ $row->total }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
