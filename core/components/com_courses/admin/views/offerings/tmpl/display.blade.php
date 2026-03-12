{{--
  Courses: Offerings — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo   = \Components\Courses\Helpers\Permissions::getActions();
  $sort    = $filters['sort'] ?? 'title';
  $sortDir = $filters['sort_Dir'] ?? 'asc';
@endphp

@php
  Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_OFFERINGS'), 'courses');
  if ($canDo->get('core.create')) {
      Toolbar::custom('copy', 'copy', '', Lang::txt('JTOOLBAR_DUPLICATE'), true);
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList();
  }
  Toolbar::spacer();
  Toolbar::help('courses');
@endphp

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

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <caption class="text-left px-4 py-2 text-sm">
        ({{ $course->get('alias') }})
        {{ $course->get('title') }}
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
            {!! Html::grid('sort', 'COM_COURSES_COL_STARTS', 'publish_up', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_COURSES_COL_ENDS', 'publish_down', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_COURSES_COL_PUBLISHED', 'state', $sortDir, $sort) !!}
          </th>
          <th>{{ Lang::txt('COM_COURSES_COL_SECTIONS') }}</th>
          <th class="priority-2">{{ Lang::txt('COM_COURSES_COL_ENROLLMENT') }}</th>
          <th>{{ Lang::txt('COM_COURSES_COL_UNITS') }}</th>
          <th class="priority-2">{{ Lang::txt('COM_COURSES_COL_PAGES') }}</th>
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
            $units    = $row->units(array('count' => true));
            $students = 0;
            $s = $row->sections();
            if ($s->total() > 0) {
                $sids = array();
                foreach ($s as $section) {
                    $sids[] = $section->get('id');
                }
                $students = $row->members(array(
                    'count' => true,
                    'student' => 1,
                    'section_id' => $sids
                ));
            }
            $pages    = $row->pages(array('count' => true, 'active' => array(0, 1)));
            $sections = $row->sections(array('count' => true));

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
                . '&task=' . $statusTask . '&course=' . $course->get('id')
                . '&id=' . $row->get('id')
                . '&' . Session::getFormToken() . '=1', false
            );

            $pubUpDisplay = ($row->get('publish_up') && $row->get('publish_up') != '0000-00-00 00:00:00')
                ? Date::of($row->get('publish_up'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
                : Lang::txt('COM_COURSES_NO_DATE');
            $pubDownDisplay = ($row->get('publish_down') && $row->get('publish_down') != '0000-00-00 00:00:00')
                ? Date::of($row->get('publish_down'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
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
              {{ $pubUpDisplay }}
            </td>
            <td class="priority-4">
              {{ $pubDownDisplay }}
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
            <td>
              @if($canDo->get('core.manage') && $sections > 0)
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=sections&offering=' . $row->get('id'), false) }}"
                   class="link link-primary text-sm">
                  {{ $sections }}
                </a>
              @else
                {{ $sections }}
                @if($canDo->get('core.manage'))
                  <a href="{{ Route::url('index.php?option=' . $option . '&controller=sections&offering=' . $row->get('id') . '&task=add', false) }}"
                     class="btn btn-xs btn-ghost">
                    {{ Lang::txt('COM_COURSES_ADD') }}
                  </a>
                @endif
              @endif
            </td>
            <td class="priority-2">
              @if($canDo->get('core.manage'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=students&offering=' . $row->get('id') . '&section=0', false) }}"
                   class="link link-primary text-sm">
                  {{ $students }}
                </a>
              @else
                {{ $students }}
              @endif
            </td>
            <td>
              @if($canDo->get('core.manage') && $units > 0)
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=units&offering=' . $row->get('id'), false) }}"
                   class="link link-primary text-sm">
                  {{ $units }}
                </a>
              @else
                {{ $units }}
                @if($canDo->get('core.manage'))
                  <a href="{{ Route::url('index.php?option=' . $option . '&controller=units&offering=' . $row->get('id') . '&task=add', false) }}"
                     class="btn btn-xs btn-ghost">
                    {{ Lang::txt('COM_COURSES_ADD') }}
                  </a>
                @endif
              @endif
            </td>
            <td class="priority-2">
              @if($canDo->get('core.manage') && $pages > 0)
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=pages&offering=' . $row->get('id'), false) }}"
                   class="link link-primary text-sm">
                  {{ $pages }}
                </a>
              @else
                {{ $pages }}
                @if($canDo->get('core.manage'))
                  <a href="{{ Route::url('index.php?option=' . $option . '&controller=pages&course=' . $course->get('id') . '&offering=' . $row->get('id') . '&task=add', false) }}"
                     class="btn btn-xs btn-ghost">
                    {{ Lang::txt('COM_COURSES_ADD') }}
                  </a>
                @endif
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="course" value="{{ $course->get('id') }}" />
</x-admin-form>
