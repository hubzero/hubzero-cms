{{--
  Tools Pipeline — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $sort    = $filters['sort'] ?? 'toolname';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_TOOLS'), 'tools');
  Toolbar::preferences($option, '550');
  Toolbar::spacer();
  Toolbar::help('tools');

  $__view->css();

  $stateMap = [
    0 => ['label' => Lang::txt('JUNPUBLISHED'),        'badge' => 'badge-ghost'],
    1 => ['label' => Lang::txt('COM_TOOLS_REGISTERED'), 'badge' => 'badge-info'],
    2 => ['label' => Lang::txt('COM_TOOLS_CREATED'),    'badge' => 'badge-info'],
    3 => ['label' => Lang::txt('COM_TOOLS_UPLOADED'),   'badge' => 'badge-info'],
    4 => ['label' => Lang::txt('COM_TOOLS_INSTALLED'),  'badge' => 'badge-info'],
    5 => ['label' => Lang::txt('COM_TOOLS_UPDATED'),    'badge' => 'badge-warning'],
    6 => ['label' => Lang::txt('COM_TOOLS_APPROVED'),   'badge' => 'badge-accent'],
    7 => ['label' => Lang::txt('JPUBLISHED'),           'badge' => 'badge-success'],
    8 => ['label' => Lang::txt('COM_TOOLS_RETIRED'),    'badge' => 'badge-neutral'],
    9 => ['label' => Lang::txt('COM_TOOLS_ABANDONED'),  'badge' => 'badge-error'],
  ];
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
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('COM_TOOLS_SEARCH_PLACEHOLDER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_TOOLS_GO') }}</button>
        <button type="button" class="btn btn-sm btn-ghost" data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      <label for="filter-state" class="sr-only">{{ Lang::txt('COM_TOOLS_ALL_STATES') }}</label>
      <select name="state" id="filter-state" class="select select-bordered select-sm" data-submit-on-change>
        <option value="-1" @selected(($filters['state'] ?? -1) == -1)>{{ Lang::txt('COM_TOOLS_ALL_STATES') }}</option>
        <option value="0"  @selected(($filters['state'] ?? -1) === 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
        <option value="1"  @selected(($filters['state'] ?? -1) == 1)>{{ Lang::txt('COM_TOOLS_REGISTERED') }}</option>
        <option value="2"  @selected(($filters['state'] ?? -1) == 2)>{{ Lang::txt('COM_TOOLS_CREATED') }}</option>
        <option value="3"  @selected(($filters['state'] ?? -1) == 3)>{{ Lang::txt('COM_TOOLS_UPLOADED') }}</option>
        <option value="4"  @selected(($filters['state'] ?? -1) == 4)>{{ Lang::txt('COM_TOOLS_INSTALLED') }}</option>
        <option value="5"  @selected(($filters['state'] ?? -1) == 5)>{{ Lang::txt('COM_TOOLS_UPDATED') }}</option>
        <option value="6"  @selected(($filters['state'] ?? -1) == 6)>{{ Lang::txt('COM_TOOLS_APPROVED') }}</option>
        <option value="7"  @selected(($filters['state'] ?? -1) == 7)>{{ Lang::txt('JPUBLISHED') }}</option>
        <option value="8"  @selected(($filters['state'] ?? -1) == 8)>{{ Lang::txt('COM_TOOLS_RETIRED') }}</option>
        <option value="9"  @selected(($filters['state'] ?? -1) == 9)>{{ Lang::txt('COM_TOOLS_ABANDONED') }}</option>
      </select>
    </x-admin-filters>
  @endslot

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th class="column-check"></th>
          <th class="priority-5">
            {!! Html::grid('sort', 'COM_TOOLS_COL_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_TOOLS_COL_NAME', 'toolname', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_TOOLS_COL_TITLE', 'title', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_TOOLS_COL_STATE', 'state', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_TOOLS_COL_REGISTERED', 'registered', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_TOOLS_COL_STATECHANGED', 'state_changed', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_TOOLS_COL_VERSIONS', 'versions', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8">
            {!! $__view->pagination($total, $filters['start'] ?? 0, $filters['limit'] ?? 25) !!}
          </td>
        </tr>
      </tfoot>
      <tbody>
        @forelse($rows as $i => $row)
          @php
            $state      = $stateMap[$row['state']] ?? ['label' => $row['state'], 'badge' => 'badge-ghost'];
            $editUrl    = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&id=' . $row['id'], false);
            $versionsUrl = Route::url('index.php?option=' . $option . '&controller=versions&id=' . $row['id'], false);
          @endphp
          <tr>
            <td>
              <input type="radio"
                     name="id"
                     id="cb{{ $i }}"
                     value="{{ $row['id'] }}"
                     class="radio radio-sm"
                     aria-label="{{ $row['id'] }}" />
            </td>
            <td class="priority-5">{{ $row['id'] }}</td>
            <td>
              <a href="{{ $editUrl }}" class="link link-hover font-medium">
                {{ $row['toolname'] }}
              </a>
            </td>
            <td class="priority-4">
              <a href="{{ $editUrl }}" class="link link-hover">
                {{ $row['title'] }}
              </a>
            </td>
            <td>
              <span class="badge badge-sm {{ $state['badge'] }}">{{ $state['label'] }}</span>
            </td>
            <td class="priority-3">
              <time>{{ $row['registered'] }}</time>
            </td>
            <td class="priority-3">
              <time>{{ $row['state_changed'] }}</time>
            </td>
            <td>
              <a href="{{ $versionsUrl }}" class="badge badge-sm badge-outline">
                {{ $row['versions'] }}
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="8" class="text-center text-muted-foreground py-6">
              {{ Lang::txt('COM_TOOLS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <input type="hidden" name="task" value="view" autocomplete="off" />
</x-admin-form>
