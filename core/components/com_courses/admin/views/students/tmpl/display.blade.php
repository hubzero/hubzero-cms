{{--
  Courses: Students — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo   = \Components\Courses\Helpers\Permissions::getActions();
  $sort    = $filters['sort'] ?? 'name';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  // Build offerings lookup for when no offering filter is set
  $offerings = [];
  $model = \Components\Courses\Models\Courses::getInstance();
  if ($model->courses()->total() > 0) {
      foreach ($model->courses() as $courseItem) {
          foreach ($courseItem->offerings() as $offeringItem) {
              $offerings[$offeringItem->get('id')] = $courseItem->get('alias')
                  . ' : ' . $offeringItem->get('alias');
          }
      }
  }
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_STUDENTS') }}"
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
               placeholder="{{ Lang::txt('COM_COURSES_STUDENTS_SEARCH_PLACEHOLDER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">
          {{ Lang::txt('COM_COURSES_GO') }}
        </button>
        <button type="button" class="btn btn-sm btn-ghost" data-clear-search>
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

        <label for="filter_offering" class="sr-only">{{ Lang::txt('COM_COURSES_OFFERING_SELECT') }}</label>
        <select name="offering" id="filter_offering" class="select select-bordered select-sm" data-submit-on-change>
          <option value="0">{{ Lang::txt('COM_COURSES_OFFERING_SELECT') }}</option>
          @if($model->courses()->total() > 0)
            @foreach($model->courses() as $courseItem)
              <optgroup label="{{ $courseItem->get('alias') }}">
                @foreach($courseItem->offerings() as $offeringItem)
                  <option value="{{ $offeringItem->get('id') }}"
                          @selected($offeringItem->get('id') == $offering->get('id'))>
                    {{ $offeringItem->get('alias') }}
                  </option>
                @endforeach
              </optgroup>
            @endforeach
          @endif
        </select>

        @if($filters['offering'])
          <label for="filter_section" class="sr-only">{{ Lang::txt('COM_COURSES_SECTION_SELECT') }}</label>
          <select name="section" id="filter_section" class="select select-bordered select-sm" data-submit-on-change>
            <option value="0">{{ Lang::txt('COM_COURSES_SECTION_SELECT') }}</option>
            @if($offering->sections()->total() > 0)
              @foreach($offering->sections() as $section)
                <option value="{{ $section->get('id') }}"
                        @selected($section->get('id') == ($filters['section_id'] ?? 0))>
                  {{ $section->get('title') }}
                </option>
              @endforeach
            @endif
          </select>
        @else
          <input type="hidden" name="section" id="filter_section" value="0" />
        @endif
  </x-admin-filters>

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      @if($filters['offering'])
        @php
          $coursesUrl = Route::url('index.php?option=' . $option . '&controller=courses', false);
          $offeringsUrl = Route::url(
              'index.php?option=' . $option . '&controller=offerings&course=' . $course->get('id'), false
          );
        @endphp
        <caption class="text-left px-4 py-2 text-sm">
          (<a href="{{ $coursesUrl }}" class="link link-primary">{{ $course->get('alias') }}</a>)
          <a href="{{ $coursesUrl }}" class="link link-primary">{{ $course->get('title') }}</a>:
          <a href="{{ $offeringsUrl }}" class="link link-primary">{{ $offering->get('title') }}</a>
        </caption>
      @endif
      <thead>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th class="priority-5">
            {{ Lang::txt('COM_COURSES_COL_ID') }}
          </th>
          <th>
            {{ Lang::txt('COM_COURSES_COL_NAME') }}
          </th>
          <th class="priority-4">
            {{ Lang::txt('COM_COURSES_COL_EMAIL') }}
          </th>
          @if(!$filters['offering'])
            <th>
              {{ Lang::txt('COM_COURSES_COL_COURSE_OFFERING') }}
            </th>
          @endif
          <th>
            {{ Lang::txt('COM_COURSES_COL_SECTION') }}
          </th>
          <th class="priority-3">
            {{ Lang::txt('COM_COURSES_COL_CERTIFICATE') }}
          </th>
          <th class="priority-4">
            {{ Lang::txt('COM_COURSES_COL_ENROLLED') }}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="{{ !$filters['offering'] ? '8' : '7' }}">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $section = \Components\Courses\Models\Section::getInstance($row->get('section_id'));

            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&offering=' . $row->get('offering_id')
                . '&id=' . $row->get('id'), false
            );

            $enrolledDisplay = ($row->get('enrolled') && $row->get('enrolled') != '0000-00-00 00:00:00')
                ? Date::of($row->get('enrolled'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'))
                : null;
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     aria-label="{{ $row->get('name') }}"
                     class="checkbox checkbox-sm"
                     data-check-item />
            </td>
            <td class="priority-5">
              {{ $row->get('user_id') }}
            </td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                  {{ $row->get('name') }}
                </a>
              @else
                {{ $row->get('name') }}
              @endif
            </td>
            <td class="priority-4">
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                  {{ $row->get('email') }}
                </a>
              @else
                {{ $row->get('email') }}
              @endif
            </td>
            @if(!$filters['offering'])
              <td>
                {{ $offerings[$row->get('offering_id')] ?? Lang::txt('COM_COURSES_UNKNOWN') }}
              </td>
            @endif
            <td>
              {{ $section->exists() ? $section->get('title') : Lang::txt('COM_COURSES_NONE') }}
            </td>
            <td class="priority-3">
              <span class="badge badge-sm whitespace-nowrap {{ $row->get('token') ? 'badge-success' : 'badge-ghost' }}">
                {{ $row->get('token') ? Lang::txt('COM_COURSES_REDEEMED') : Lang::txt('COM_COURSES_NOT_REDEEMED') }}
              </span>
            </td>
            <td class="priority-4">
              @if($enrolledDisplay)
                <time datetime="{{ $row->get('enrolled') }}">{{ $enrolledDisplay }}</time>
              @else
                {{ Lang::txt('COM_COURSES_UNKNOWN') }}
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
