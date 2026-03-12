{{--
  Course Pages — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Courses\Helpers\Permissions::getActions();

  $pageNav = $__view->pagination($total, $filters['start'], $filters['limit']);
@endphp

@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(
      Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_PAGES'),
      'courses'
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
  Toolbar::help('pages');
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

    <label for="filter-active" class="sr-only">{{ Lang::txt('COM_COURSES_ALL_STATES') }}</label>
    <select name="active" id="filter-active" class="select select-bordered select-sm" data-submit-on-change>
      <option value="-1" @selected(($filters['active'] ?? -1) == -1)>
        {{ Lang::txt('COM_COURSES_ALL_STATES') }}
      </option>
      <option value="1" @selected(($filters['active'] ?? '') == '1')>
        {{ Lang::txt('COM_COURSES_PUBLISHED') }}
      </option>
      <option value="0" @selected(($filters['active'] ?? '') === '0')>
        {{ Lang::txt('COM_COURSES_UNPUBLISHED') }}
      </option>
    </select>
  </x-admin-filters>

  {{-- Breadcrumb caption --}}
  <div class="text-sm breadcrumbs mb-2 px-1">
    <ul>
      @if($course->exists())
        <li>
          <a href="{{ Route::url('index.php?option=' . $option, false) }}">
            {{ $course->get('title') }}
          </a>
        </li>
        @if($offering->exists())
          @php
            $offeringsUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=offerings&course=' . $course->get('id'), false
            );
          @endphp
          <li>
            <a href="{{ $offeringsUrl }}">
              {{ $offering->get('title') }}
            </a>
          </li>
        @endif
        <li>{{ Lang::txt('COM_COURSES_PAGES') }}</li>
      @else
        <li>{{ Lang::txt('COM_COURSES_PAGES_USER_GUIDE') }}</li>
        <li>{{ Lang::txt('COM_COURSES_PAGES') }}</li>
      @endif
    </ul>
  </div>

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
          <th class="priority-5">
            {{ Lang::txt('COM_COURSES_COL_ID') }}
          </th>
          <th>
            {{ Lang::txt('COM_COURSES_COL_TITLE') }}
          </th>
          <th class="priority-3">
            {{ Lang::txt('COM_COURSES_COL_STATE') }}
          </th>
          <th>
            {{ Lang::txt('COM_COURSES_COL_ORDERING') }}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5">
            <div class="admin-pagination">
              {!! $pageNav->render() !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @php
          $rowsArray = [];
          foreach ($rows as $page) {
              $rowsArray[] = $page;
          }
          $n = count($rowsArray);
        @endphp
        @if($n > 0)
          @foreach($rowsArray as $i => $page)
            @php
              $editUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=' . $controller
                  . '&task=edit&id=' . $page->get('id'), false
              );

              switch ($page->get('active')) {
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

              $stateUrl = Route::url(
                  'index.php?option=' . $option
                  . '&controller=' . $controller
                  . '&task=' . $statusTask . '&id=' . $page->get('id')
                  . '&' . Session::getFormToken() . '=1', false
              );
            @endphp
            <tr>
              <td class="column-check">
                <input type="checkbox"
                       name="id[]"
                       id="cb{{ $i }}"
                       value="{{ $page->get('id') }}"
                       aria-label="{{ $page->get('title') }}"
                       class="checkbox checkbox-sm"
                       data-check-item />
              </td>
              <td class="priority-5">
                {{ $page->get('id') }}
              </td>
              <td>
                @if($canDo->get('core.edit'))
                  <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                    {{ $page->get('title') }}
                  </a>
                @else
                  {{ $page->get('title') }}
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
              <td class="order">
                {{ $page->get('ordering') }}
                <span>{!! $pageNav->orderUpIcon($i, isset($rowsArray[$i - 1]), 'orderup', 'COM_COURSES_MOVE_UP', true) !!}</span>
                <span>{!! $pageNav->orderDownIcon($i, $n, isset($rowsArray[$i + 1]), 'orderdown', 'COM_COURSES_MOVE_DOWN', true) !!}</span>
              </td>
            </tr>
          @endforeach
        @else
          <tr>
            <td colspan="5">{{ Lang::txt('COM_COURSES_NONE_FOUND') }}</td>
          </tr>
        @endif
      </tbody>
    </table>
  </div>

  <input type="hidden" name="course" value="{{ $filters['course'] ?? '' }}" />
  <input type="hidden" name="offering" value="{{ $filters['offering'] ?? '' }}" />
</x-admin-form>
