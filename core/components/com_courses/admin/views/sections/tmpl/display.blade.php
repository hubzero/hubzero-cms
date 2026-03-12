{{--
  Courses: Sections — Admin list view

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
    title="{{ Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_SECTIONS') }}"
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
  @endphp

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <caption class="text-left px-4 py-2 text-sm">
        (<a href="{{ $offeringsUrl }}" class="link link-primary">{{ $course->get('alias') }}</a>)
        <a href="{{ $offeringsUrl }}" class="link link-primary">{{ $course->get('title') }}</a>:
        {{ $offering->get('title') }}
      </caption>
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th class="priority-5">
            {!! Html::grid('sort', 'COM_COURSES_COL_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_COURSES_COL_TITLE', 'title', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_COURSES_COL_ALIAS', 'alias', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_COURSES_COL_DEFAULT', 'is_default', $sortDir, $sort) !!}
          </th>
          @if($canDo->get('core.edit.state'))
            <th>
              {!! Html::grid('sort', 'COM_COURSES_COL_STATE', 'state', $sortDir, $sort) !!}
            </th>
          @endif
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_COURSES_COL_STARTS', 'start_date', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_COURSES_COL_ENDS', 'end_date', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">{{ Lang::txt('COM_COURSES_COL_ENROLLED') }}</th>
          <th>{{ Lang::txt('COM_COURSES_COL_CODES') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="10">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $students = $row->members(array('count' => true, 'student' => 1));
            $allcodes = $row->codes(array('count' => true));
            $redeemed = $row->codes(array('count' => true, 'redeemed' => 1));

            // Status
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
                case 3:
                    $statusLabel = Lang::txt('COM_COURSES_DRAFT');
                    $statusBadge = 'badge-warning';
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
                . '&task=edit&id=' . $row->get('id'), false
            );
            $stateUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=' . $statusTask
                . '&id=' . $row->get('id')
                . '&offering=' . $offering->get('id')
                . '&' . Session::getFormToken() . '=1', false
            );
            $defaultUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=makedefault&id=' . $row->get('id')
                . '&offering=' . $offering->get('id')
                . '&' . Session::getFormToken() . '=1', false
            );

            $startDisplay = ($row->get('start_date') && $row->get('start_date') != '0000-00-00 00:00:00')
                ? Date::of($row->get('start_date'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
                : Lang::txt('COM_COURSES_NO_DATE');
            $endDisplay = ($row->get('end_date') && $row->get('end_date') != '0000-00-00 00:00:00')
                ? Date::of($row->get('end_date'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
                : Lang::txt('COM_COURSES_NEVER');
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
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                  {{ $row->get('title') }}
                </a>
              @else
                {{ $row->get('title') }}
              @endif
            </td>
            <td class="priority-4">
              {{ $row->get('alias') }}
            </td>
            <td class="priority-3">
              @if($canDo->get('core.edit'))
                <a href="{{ $defaultUrl }}" class="badge badge-sm whitespace-nowrap {{ $row->get('is_default') ? 'badge-success' : 'badge-ghost' }}">
                  {{ $row->get('is_default') ? Lang::txt('JYES') : Lang::txt('JNO') }}
                </a>
              @else
                <span class="badge badge-sm whitespace-nowrap {{ $row->get('is_default') ? 'badge-success' : 'badge-ghost' }}">
                  {{ $row->get('is_default') ? Lang::txt('JYES') : Lang::txt('JNO') }}
                </span>
              @endif
            </td>
            @if($canDo->get('core.edit.state'))
              <td>
                <a href="{{ $stateUrl }}" class="badge badge-sm whitespace-nowrap {{ $statusBadge }}">
                  {{ $statusLabel }}
                </a>
              </td>
            @endif
            <td class="priority-4">
              {{ $startDisplay }}
            </td>
            <td class="priority-4">
              {{ $endDisplay }}
            </td>
            <td class="priority-2">
              @if($canDo->get('core.manage') && $students > 0)
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=students&offering=' . $row->get('offering_id') . '&section=' . $row->get('id'), false) }}"
                   class="link link-primary text-sm">
                  {{ $students }}
                </a>
              @else
                {{ $students }}
                @if($canDo->get('core.manage'))
                  <a href="{{ Route::url('index.php?option=' . $option . '&controller=students&offering=' . $row->get('offering_id') . '&section=' . $row->get('id') . '&task=add', false) }}"
                     class="btn btn-xs btn-ghost">
                    {{ Lang::txt('COM_COURSES_ADD') }}
                  </a>
                @endif
              @endif
            </td>
            <td>
              <a href="{{ Route::url('index.php?option=' . $option . '&controller=codes&section=' . $row->get('id'), false) }}"
                 class="link link-primary text-sm">
                {{ Lang::txt('COM_COURSES_NUM_OF_TOTAL_REDEEMED', $redeemed, $allcodes) }}
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="offering" value="{{ $offering->get('id') }}" />
</x-admin-form>
