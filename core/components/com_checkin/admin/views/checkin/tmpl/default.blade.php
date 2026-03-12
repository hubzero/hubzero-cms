{{--
  Checkin — Admin list view

  Variables from controller:
    $state   — Hubzero\Base\Obj  (filter.search, list.ordering, list.direction, list.start, list.limit)
    $items   — array<tableName, count>  tables with checked-out items
    $total   — int  total number of such tables

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;
  use Hubzero\Pagination\Paginator;

  Toolbar::title(Lang::txt('COM_CHECKIN_GLOBAL_CHECK_IN'), 'checkin');
  if (User::authorise('core.admin', 'com_checkin')) {
      Toolbar::custom('checkin', 'checkin', '', 'JTOOLBAR_CHECKIN', true);
      Toolbar::divider();
      Toolbar::preferences('com_checkin');
      Toolbar::divider();
  }
  Toolbar::help('checkin');

  $sort    = $state->get('list.ordering', 'table');
  $sortDir = $state->get('list.direction', 'asc');
  $search  = $state->get('filter.search', '');

  $pagination = new Paginator(
      $total,
      $state->get('list.start', 0),
      $state->get('list.limit', 25)
  );
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  {{-- Filters --}}
  <x-admin-filters>
    <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER_LABEL') }}</label>
    <input type="text"
           name="filter_search"
           id="filter_search"
           class="input input-sm w-64"
           value="{{ $search }}"
           placeholder="{{ Lang::txt('COM_CHECKIN_FILTER_SEARCH_DESC') }}" />
    <button type="submit" class="btn btn-sm btn-primary">
      {{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}
    </button>
    @if($search)
      <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
         class="btn btn-sm btn-ghost btn-outline">
        {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
      </a>
    @endif
  </x-admin-filters>

  <div class="mt-4 bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{!! Html::grid('sort', 'COM_CHECKIN_DATABASE_TABLE', 'table', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_CHECKIN_ITEMS_TO_CHECK_IN', 'count', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="3">
            <div class="admin-pagination">
              {!! $pagination->render() !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($items as $table => $count)
          @php $i = $loop->index; @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="cid[]"
                     id="cb{{ $i }}"
                     value="{{ $table }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
                     data-check-item />
            </td>
            <td>{!! Lang::txt('COM_CHECKIN_TABLE', e($table)) !!}</td>
            <td>{{ $count }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="text-center py-6 text-muted-foreground">
              {{ Lang::txt('COM_CHECKIN_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <input type="hidden" name="boxchecked" value="0" />
</x-admin-form>
