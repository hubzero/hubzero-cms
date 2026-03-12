{{--
  Resource Authors — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo   = \Components\Resources\Helpers\Permissions::getActions('contributor');
  $sort    = $filters['sort'] ?? 'name';
  $sortDir = $filters['sort_Dir'] ?? 'asc';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_RESOURCES') }}: {{ Lang::txt('COM_RESOURCES_AUTHORS') }}"
    icon="resources"
    :canDo="$canDo"
    option="{{ $option }}"
/>

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
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('COM_RESOURCES_SEARCH_PLACEHOLDER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_RESOURCES_GO') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot
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
            {!! Html::grid('sort', 'COM_RESOURCES_COL_ID', 'authorid', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_RESOURCES_COL_NAME', 'name', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">
            {{ Lang::txt('COM_RESOURCES_COL_MEMBER') }}
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
        @forelse($rows as $i => $row)
          @php
            if ($row->authorid > 0 && !$row->name) {
                $u = User::getInstance($row->authorid);
                $row->name = $u->get('name');
            }
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->authorid, false
            );
            $authorName = $row->name
                ? e($row->name)
                : Lang::txt('COM_RESOURCES_UNKNOWN');
          @endphp
          <tr>
            <td class="column-check">
              @if($canDo->get('core.edit'))
                <input type="checkbox"
                       name="id[]"
                       id="cb{{ $i }}"
                       value="{{ $row->authorid }}"
                       class="checkbox checkbox-sm"
                       data-check-item
                       aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $authorName) }}" />
              @endif
            </td>
            <td class="priority-4">
              {{ $row->authorid }}
            </td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                  {{ $authorName }}
                </a>
              @else
                {{ $authorName }}
              @endif
            </td>
            <td class="priority-2">
              @if($row->authorid > 0)
                @php $memberUrl = Route::url('index.php?option=com_members&task=edit&id=' . $row->authorid, false); @endphp
                <a href="{{ $memberUrl }}">
                  <span class="badge badge-sm badge-success">{{ Lang::txt('JYES') }}</span>
                </a>
              @else
                <span class="badge badge-sm badge-ghost">{{ Lang::txt('JNO') }}</span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="text-center text-muted-foreground py-4">
              {{ Lang::txt('COM_RESOURCES_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
