{{--
  Courses: Asset Groups — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Courses\Helpers\Permissions::getActions();
  $__view->css();

  // Build ordering lookup for tree structure
  $ordering = [];
  foreach ($rows as $r) {
      $ordering[$r->get('parent')][] = $r->get('id');
  }
@endphp

@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(
      Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_ASSET_GROUPS'),
      'courses'
  );
  if ($canDo->get('core.create')) {
      Toolbar::custom('assetdup', 'star', '', 'COM_COURSES_ASSET_DUPLICATE', true);
      Toolbar::spacer();
      Toolbar::custom('copy', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
      Toolbar::spacer();
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList('COM_COURSES_DELETE_CONFIRM', 'remove');
  }
  Toolbar::spacer();
  Toolbar::help('assetgroups');
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
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

        <label for="filter-state" class="sr-only">{{ Lang::txt('COM_COURSES_ALL_STATES') }}</label>
        <select name="state" id="filter-state" class="select select-bordered select-sm" data-submit-on-change>
          <option value="-1" @selected(($filters['state'] ?? '-1') == '-1')>
            {{ Lang::txt('COM_COURSES_ALL_STATES') }}
          </option>
          <option value="0" @selected(($filters['state'] ?? '') === '0')>
            {{ Lang::txt('COM_COURSES_UNPUBLISHED') }}
          </option>
          <option value="1" @selected(($filters['state'] ?? '') == '1')>
            {{ Lang::txt('COM_COURSES_PUBLISHED') }}
          </option>
          <option value="3" @selected(($filters['state'] ?? '') == '3')>
            {{ Lang::txt('COM_COURSES_DRAFT') }}
          </option>
          <option value="2" @selected(($filters['state'] ?? '') == '2')>
            {{ Lang::txt('COM_COURSES_TRASHED') }}
          </option>
        </select>
  </x-admin-filters>

  @php
    $offeringsUrl = Route::url(
        'index.php?option=' . $option . '&controller=offerings&course=' . $course->get('id'), false
    );
    $unitsUrl = Route::url(
        'index.php?option=' . $option . '&controller=units&offering=' . $unit->get('offering_id'), false
    );
  @endphp

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table" id="assetgroups">
      <caption class="text-left px-4 py-2 text-sm">
        (<a href="{{ $offeringsUrl }}" class="link link-primary">{{ $course->get('alias') }}</a>)
        <a href="{{ $offeringsUrl }}" class="link link-primary">{{ $course->get('title') }}</a>:
        <a href="{{ $unitsUrl }}" class="link link-primary">{{ $offering->get('title') }}</a>:
        {{ $unit->get('title') }}
      </caption>
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th class="priority-5">{{ Lang::txt('COM_COURSES_COL_ID') }}</th>
          <th>{{ Lang::txt('COM_COURSES_COL_TITLE') }}</th>
          <th class="priority-4">{{ Lang::txt('COM_COURSES_COL_ALIAS') }}</th>
          <th class="priority-3">{{ Lang::txt('COM_COURSES_COL_STATE') }}</th>
          <th class="priority-3" colspan="2">{{ Lang::txt('COM_COURSES_COL_ORDERING') }}</th>
          <th class="priority-3">{{ Lang::txt('COM_COURSES_COL_ASSETS') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8">
            <div class="admin-pagination">
              @php
                $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
                echo $pageNav->render();
              @endphp
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @php $n = count($rows); @endphp
        @foreach($rows as $i => $row)
          @php
            $orderkey = array_search($row->get('id'), $ordering[$row->get('parent')] ?? []);
            $assets = $row->assets()->total();

            switch ($row->get('state')) {
                case 1:
                    $statusLabel = Lang::txt('COM_COURSES_PUBLISHED');
                    $statusBadge = 'badge-success';
                    $statusTask  = 'unpublish';
                    break;
                case 2:
                    $statusLabel = Lang::txt('COM_COURSES_TRASHED');
                    $statusBadge = 'badge-error';
                    $statusTask  = 'publish';
                    break;
                case 0:
                default:
                    $statusLabel = Lang::txt('COM_COURSES_UNPUBLISHED');
                    $statusBadge = 'badge-ghost';
                    $statusTask  = 'publish';
                    break;
            }

            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&unit=' . $unit->get('id')
                . '&id=' . $row->get('id'), false
            );
            $stateUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=' . $statusTask
                . '&unit=' . $unit->get('id')
                . '&id=' . $row->get('id')
                . '&' . Session::getFormToken() . '=1', false
            );

            $canOrderUp = isset($ordering[$row->get('parent')][$orderkey - 1]);
            $canOrderDown = isset($ordering[$row->get('parent')][$orderkey + 1]);
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
            <td class="priority-5">
              {{ $row->get('id') }}
            </td>
            <td>
              {!! $row->treename ?? '' !!}
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                  {{ $row->get('title') }}
                </a>
              @else
                {{ $row->get('title') }}
              @endif
            </td>
            <td class="priority-4">
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}" class="link link-hover text-primary">
                  {{ $row->get('alias') }}
                </a>
              @else
                {{ $row->get('alias') }}
              @endif
            </td>
            <td class="priority-3">
              @if($canDo->get('core.edit.state'))
                <a href="{{ $stateUrl }}" class="badge badge-sm whitespace-nowrap {{ $statusBadge }}">
                  {{ $statusLabel }}
                </a>
              @else
                <span class="badge badge-sm whitespace-nowrap {{ $statusBadge }}">
                  {{ $statusLabel }}
                </span>
              @endif
            </td>
            <td class="priority-3">
              {!! $pageNav->orderUpIcon($i, $canOrderUp, 'orderup', 'COM_COURSES_MOVE_UP', true) !!}
            </td>
            <td class="priority-3">
              {!! $pageNav->orderDownIcon($i, $n, $canOrderDown, 'orderdown', 'COM_COURSES_MOVE_DOWN', true) !!}
            </td>
            <td class="priority-3">
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}" class="link link-primary text-sm">
                  {{ $assets }}
                </a>
              @else
                {{ $assets }}
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="offering" value="{{ $offering->get('id') }}" />
  <input type="hidden" name="unit" value="{{ $unit->get('id') }}" />
</x-admin-form>
