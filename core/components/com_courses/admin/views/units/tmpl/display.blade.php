{{--
  Courses: Units — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo = \Components\Courses\Helpers\Permissions::getActions();
  $__view->css();
@endphp

@php
  Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_UNITS'), 'courses');
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
          <th class="priority-5">{{ Lang::txt('COM_COURSES_COL_ID') }}</th>
          <th>{{ Lang::txt('COM_COURSES_COL_TITLE') }}</th>
          <th class="priority-4">{{ Lang::txt('COM_COURSES_COL_ALIAS') }}</th>
          <th class="priority-2">{{ Lang::txt('COM_COURSES_COL_STATE') }}</th>
          <th class="priority-3" colspan="2">{{ Lang::txt('COM_COURSES_COL_ORDERING') }}</th>
          <th>{{ Lang::txt('COM_COURSES_COL_ASSET_GROUPS') }}</th>
          <th>{{ Lang::txt('COM_COURSES_COL_ASSETS') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="9">
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
        @php $n = $rows->total(); @endphp
        @foreach($rows as $i => $row)
          @php
            $assetgroups = $row->assetgroups()->total();
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
                . '&task=edit&id=' . $row->get('id'), false
            );
            $stateUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=' . $statusTask
                . '&offering=' . $offering->get('id')
                . '&id=' . $row->get('id')
                . '&' . Session::getFormToken() . '=1', false
            );
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
            <td class="priority-2">
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
              @php
                $prv = $rows->fetch('prev');
                $prev = is_object($prv) ? $prv->get('ordering') : 0;
              @endphp
              {!! $pageNav->orderUpIcon($i, ($row->get('ordering') != $prev)) !!}
            </td>
            <td class="priority-3">
              @php
                $nxt = $rows->fetch('next');
                $next = is_object($nxt) ? $nxt->get('ordering') : 0;
              @endphp
              {!! $pageNav->orderDownIcon($i, $n, ($row->get('ordering') != $next)) !!}
            </td>
            <td>
              @if($canDo->get('core.manage') && $assetgroups > 0)
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=assetgroups&unit=' . $row->get('id'), false) }}"
                   class="link link-primary text-sm">
                  {{ $assetgroups }}
                </a>
              @else
                {{ $assetgroups }}
                @if($canDo->get('core.manage'))
                  <a href="{{ Route::url('index.php?option=' . $option . '&controller=assetgroups&unit=' . $row->get('id') . '&task=add', false) }}"
                     class="btn btn-xs btn-ghost">
                    {{ Lang::txt('COM_COURSES_ADD') }}
                  </a>
                @endif
              @endif
            </td>
            <td>
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
</x-admin-form>
