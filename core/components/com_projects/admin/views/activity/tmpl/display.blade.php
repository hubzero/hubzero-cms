{{--
  Projects Activity — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $sort    = $filters['sort'] ?? 'created';
  $sortDir = $filters['sort_Dir'] ?? 'DESC';

  Toolbar::title(
      Lang::txt('COM_PROJECTS') . ': ' . Lang::txt('COM_PROJECTS_ACTIVITY'),
      'projects'
  );
  if (User::authorise('core.delete', $option . '.component')) {
      Toolbar::deleteList('COM_PROJECTS_ACTIVITY_DELETE', 'delete');
      Toolbar::spacer();
  }
  Toolbar::help('activity');
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
        <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER_LABEL') }}</label>
        <input type="text"
               name="filter_search"
               id="filter_search"
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('COM_PROJECTS_FILTER_SEARCH_DESC') }}" />
        <button type="submit" class="btn btn-sm btn-primary">
          {{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}
        </button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      <label for="filter_action" class="sr-only">{{ Lang::txt('COM_PROJECTS_FILTER_ACTION') }}</label>
      <select name="action"
              id="filter_action"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="" @selected(($filters['action'] ?? '') === '')>
          {{ Lang::txt('COM_PROJECTS_FILTER_ACTION') }}
        </option>
        @foreach(['created','updated','deleted','joined','uploaded','accepted','cancelled','submitted','emailed'] as $act)
          <option value="{{ $act }}" @selected(($filters['action'] ?? '') === $act)>
            {{ $act }}
          </option>
        @endforeach
      </select>

      <label for="filter_filter" class="sr-only">{{ Lang::txt('COM_PROJECTS_FILTER_FILTER') }}</label>
      <select name="filter"
              id="filter_filter"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="" @selected(($filters['filter'] ?? '') === '')>
          {{ Lang::txt('COM_PROJECTS_FILTER_FILTER') }}
        </option>
        <option value="starred" @selected(($filters['filter'] ?? '') === 'starred')>
          {{ Lang::txt('COM_PROJECTS_FILTER_STARRED') }}
        </option>
      </select>

      <label for="filter_state" class="sr-only">{{ Lang::txt('COM_PROJECTS_ALL_STATES') }}</label>
      <select name="state"
              id="filter_state"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="-1" @selected(($filters['state'] ?? '-1') == '-1')>
          {{ Lang::txt('COM_PROJECTS_ALL_STATES') }}
        </option>
        <option value="0" @selected(($filters['state'] ?? '') === 0 || ($filters['state'] ?? '') === '0')>
          {{ Lang::txt('JUNPUBLISHED') }}
        </option>
        <option value="1" @selected(($filters['state'] ?? '') === 1 || ($filters['state'] ?? '') === '1')>
          {{ Lang::txt('JPUBLISHED') }}
        </option>
        <option value="2" @selected(($filters['state'] ?? '') === 2 || ($filters['state'] ?? '') === '2')>
          {{ Lang::txt('JTRASHED') }}
        </option>
      </select>
    </x-admin-filters>
  @endslot

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          @if(!empty($filters['project']))
            {{-- Project scoped header row --}}
          @endif
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th class="priority-6">
            {!! Html::grid('sort', 'COM_PROJECTS_ACTIVITY_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_PROJECTS_ACTIVITY_CREATED', 'created', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_PROJECTS_ACTIVITY_CREATED_BY', 'created_by', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_PROJECTS_ACTIVITY_ACTION', 'action', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_PROJECTS_ACTIVITY_DESCRIPTION', 'description', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">{{ Lang::txt('COM_PROJECTS_ACTIVITY_PROJECT') }}</th>
          <th class="priority-2">{{ Lang::txt('COM_PROJECTS_ACTIVITY_STATE') }}</th>
          <th class="priority-2">{{ Lang::txt('COM_PROJECTS_ACTIVITY_STARRED') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="9">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @if(!empty($filters['project']) && isset($project))
          <tr class="bg-base-200">
            <td colspan="9" class="py-2 px-4 text-sm">
              <a href="{{ Route::url('index.php?option=' . $option, false) }}"
                 class="text-muted-foreground hover:text-base-content underline">
                {{ Lang::txt('COM_PROJECTS') }}
              </a>
              &rsaquo;
              <span class="font-medium">({{ $project->get('alias') }})</span>
              {{ $project->get('title') }}
            </td>
          </tr>
        @endif
        @forelse($rows as $i => $row)
          @php
            $state = (int)$row->get('state');
            switch ($state) {
                case 2:
                    $stateCls  = 'badge-error';
                    $stateText = Lang::txt('JTRASHED');
                    $stateTask = 'publish';
                    break;
                case 1:
                    $stateCls  = 'badge-success';
                    $stateText = Lang::txt('JPUBLISHED');
                    $stateTask = 'unpublish';
                    break;
                default:
                    $stateCls  = 'badge-ghost';
                    $stateText = Lang::txt('JUNPUBLISHED');
                    $stateTask = 'publish';
            }

            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->get('id'), false
            );
            $projectEditUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=projects&task=edit&id=' . $row->get('scope_id'), false
            );
            $stateUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=' . $stateTask
                . '&id=' . $row->get('id')
                . '&' . Session::getFormToken() . '=1', false
            );
            $starUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=' . ($row->get('starred') ? 'unstar' : 'star')
                . '&id=' . $row->get('id')
                . '&' . Session::getFormToken() . '=1', false
            );
            $canEditState = User::authorise('core.edit.state', $option . '.component');
            $creator = User::getInstance($row->log->get('created_by'));
            $desc    = e(\Hubzero\Utility\Str::truncate(
                strip_tags($row->log->get('description')),
                100
            ));
            $scopeModel = new \Components\Projects\Models\Project($row->get('scope_id'));
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     class="checkbox checkbox-sm"
                     data-check-item />
              <label for="cb{{ $i }}" class="sr-only">{{ $row->get('id') }}</label>
            </td>
            <td class="priority-6">{{ $row->get('id') }}</td>
            <td class="priority-4 whitespace-nowrap text-sm">{{ $row->get('created') }}</td>
            <td class="priority-3">
              {{ $creator->get('name', Lang::txt('COM_PROJECTS_UNKNOWN')) }}
            </td>
            <td class="priority-3">
              <span class="badge badge-ghost badge-sm">{{ $row->log->get('action') }}</span>
            </td>
            <td>
              @if(strpos($row->log->get('scope'), '.comment') !== false)
                <a href="{{ $editUrl }}" class="link link-primary">{!! $desc !!}</a>
              @else
                {!! $desc !!}
              @endif
            </td>
            <td class="priority-2">
              <a href="{{ $projectEditUrl }}" class="link link-primary text-sm">
                {{ $scopeModel->get('alias') }}
              </a>
            </td>
            <td class="priority-2">
              @if($canEditState)
                <a href="{{ $stateUrl }}"
                   class="badge {{ $stateCls }} badge-sm cursor-pointer">
                  {{ $stateText }}
                </a>
              @else
                <span class="badge {{ $stateCls }} badge-sm">{{ $stateText }}</span>
              @endif
            </td>
            <td class="priority-2">
              @if($canEditState)
                <a href="{{ $starUrl }}"
                   class="badge {{ $row->get('starred') ? 'badge-warning' : 'badge-ghost' }} badge-sm cursor-pointer">
                  {{ $row->get('starred') ? Lang::txt('JYES') : Lang::txt('JNO') }}
                </a>
              @else
                <span class="badge {{ $row->get('starred') ? 'badge-warning' : 'badge-ghost' }} badge-sm">
                  {{ $row->get('starred') ? Lang::txt('JYES') : Lang::txt('JNO') }}
                </span>
              @endif
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="9" class="text-center py-6 text-muted-foreground">
              {{ Lang::txt('COM_PROJECTS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
