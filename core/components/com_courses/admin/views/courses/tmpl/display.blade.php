{{--
  Courses — Admin list view

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
  Toolbar::title(Lang::txt('COM_COURSES'), 'courses');
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
          <th class="priority-5">
            {!! Html::grid('sort', 'COM_COURSES_COL_ALIAS', 'alias', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_COURSES_COL_PUBLISHED', 'state', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {{ Lang::txt('COM_COURSES_COL_CERTIFICATE') }}
          </th>
          <th class="priority-3">
            {{ Lang::txt('COM_COURSES_COL_MANAGERS') }}
          </th>
          <th>
            {{ Lang::txt('COM_COURSES_COL_OFFERINGS') }}
          </th>
          <th>
            {{ Lang::txt('COM_COURSES_COL_PAGES') }}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="9">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $offerings = $row->offerings(array('count' => true));
            $pages     = $row->pages(array('count' => true, 'active' => array(0, 1)));
            $managers  = $row->managers(array('count' => true));

            $hasCert = $row->certificate()->exists() && $row->certificate()->hasFile();

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
                . '&task=' . $statusTask . '&id=' . $row->get('id')
                . '&' . Session::getFormToken() . '=1', false
            );
            $certUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=certificates&course=' . $row->get('id'), false
            );
            $offeringsUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=offerings&course=' . $row->get('id'), false
            );
            $addOfferingUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=offerings&course=' . $row->get('id')
                . '&task=add', false
            );
            $pagesUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=pages&course=' . $row->get('id')
                . '&offering=0', false
            );
            $addPageUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=pages&course=' . $row->get('id')
                . '&offering=0&task=add', false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('alias') }}"
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
            <td class="priority-5">
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}" class="link link-hover text-sm">
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
            <td class="priority-4">
              <a href="{{ $certUrl }}" class="badge badge-sm whitespace-nowrap {{ $hasCert ? 'badge-success' : 'badge-ghost' }}">
                {{ $hasCert ? Lang::txt('COM_COURSES_CERTIFICATE_SET') : Lang::txt('COM_COURSES_CERTIFICATE_NOT_SET') }}
              </a>
            </td>
            <td class="priority-3">
              {{ $managers }}
            </td>
            <td>
              @if($canDo->get('core.manage') && $offerings > 0)
                <a href="{{ $offeringsUrl }}" class="link link-primary text-sm">
                  {{ $offerings }}
                </a>
              @else
                {{ $offerings }}
                @if($canDo->get('core.manage'))
                  <a href="{{ $addOfferingUrl }}" class="btn btn-xs btn-ghost">
                    {{ Lang::txt('COM_COURSES_ADD') }}
                  </a>
                @endif
              @endif
            </td>
            <td>
              @if($canDo->get('core.manage') && $pages > 0)
                <a href="{{ $pagesUrl }}" class="link link-primary text-sm">
                  {{ $pages }}
                </a>
              @else
                {{ $pages }}
                @if($canDo->get('core.manage'))
                  <a href="{{ $addPageUrl }}" class="btn btn-xs btn-ghost">
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
</x-admin-form>
