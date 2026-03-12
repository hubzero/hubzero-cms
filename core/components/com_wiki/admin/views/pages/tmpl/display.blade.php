{{--
  Wiki Pages — Admin list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $canDo   = \Components\Wiki\Helpers\Permissions::getActions('page');
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $sort    = $filters['sort'] ?? 'id';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_WIKI') }}"
    icon="wiki"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  @slot('filters')
    <x-admin-filters>
      @slot('search')
        <input type="text"
               name="search"
               class="input input-bordered input-sm"
               placeholder="{{ Lang::txt('COM_WIKI_FILTER_SEARCH_PLACEHOLDER') }}"
               value="{{ $filters['search'] ?? '' }}"
               data-submit-on-change />
      @endslot

      <label for="filter-scope" class="sr-only">{{ Lang::txt('COM_WIKI_COL_SCOPE') }}</label>
      <select name="scope"
              id="filter-scope"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="">{{ Lang::txt('COM_WIKI_FILTER_SCOPE_SELECT') }}</option>
        @foreach ($scopes as $scope)
          @php
            $scopeVal = $scope->get('scope') . ':' . $scope->get('scope_id');
          @endphp
          <option value="{{ $scopeVal }}"
                  @selected($scopeVal == ($filters['scope'] ?? ''))>
            {{ $scopeVal }}
          </option>
        @endforeach
      </select>

      <label for="filter-namespace" class="sr-only">{{ Lang::txt('COM_WIKI_FILTER_NAMESPACE_SELECT') }}</label>
      <select name="namespace"
              id="filter-namespace"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="">{{ Lang::txt('COM_WIKI_FILTER_NAMESPACE_SELECT') }}</option>
        @foreach ($namespaces as $nspace)
          @if (trim($nspace->get('namespace')))
            <option value="{{ $nspace->get('namespace') }}"
                    @selected(($filters['namespace'] ?? '') == $nspace->get('namespace'))>
              {{ $nspace->get('namespace') }}
            </option>
          @endif
        @endforeach
      </select>
    </x-admin-filters>
  @endslot

  <table class="admin-table">
    <thead>
      <tr>
        <th scope="col">
          <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
        </th>
        <th scope="col" class="priority-5">
          {!! Html::grid('sort', 'COM_WIKI_COL_ID', 'id', $sortDir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_WIKI_COL_TITLE', 'title', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-4">{{ Lang::txt('COM_WIKI_COL_MODE') }}</th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_WIKI_COL_STATE', 'state', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-5">
          {!! Html::grid('sort', 'COM_WIKI_COL_LOCKED', 'protected', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-4">
          {!! Html::grid('sort', 'COM_WIKI_COL_SCOPE', 'scope', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-2">{{ Lang::txt('COM_WIKI_COL_REVISIONS') }}</th>
        <th scope="col" class="priority-3">{{ Lang::txt('COM_WIKI_COL_COMMENTS') }}</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $i => $row)
        @php
          $editUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=edit&id=' . $row->get('id'),
              false, false
          );
          $revisionsUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=versions&pageid=' . $row->get('id'),
              false, false
          );
          $commentsUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=comments&page_id=' . $row->get('id'),
              false, false
          );
          $pageNamePath = ($row->get('path') ? $row->get('path') . '/' : '')
              . $row->get('pagename', '');

          // State badge
          switch ($row->get('state')) {
              case 2:
                  $stateBadge = 'badge-ghost';
                  $stateText  = Lang::txt('COM_WIKI_STATE_TRASHED');
                  $stateTask  = 0;
                  break;
              case 1:
                  $stateBadge = 'badge-success';
                  $stateText  = Lang::txt('JPUBLISHED');
                  $stateTask  = 0;
                  break;
              default:
                  $stateBadge = 'badge-warning';
                  $stateText  = Lang::txt('JUNPUBLISHED');
                  $stateTask  = 1;
                  break;
          }

          $stateUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=state&id=' . $row->get('id')
              . '&state=' . $stateTask
              . '&' . Session::getFormToken() . '=1',
              false, false
          );
        @endphp
        <tr>
          <td>
            <input type="checkbox"
                   name="id[]"
                   id="cb{{ $i }}"
                   value="{{ $row->get('id') }}"
                   class="checkbox checkbox-sm"
                   aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
                   data-check-item />
          </td>
          <td class="priority-5">{{ $row->get('id') }}</td>
          <td>
            @if ($canDo->get('core.edit'))
              <a href="{{ $editUrl }}">
                {{ $row->get('title', Lang::txt('COM_WIKI_NONE')) }}
              </a>
            @else
              {{ $row->get('title', Lang::txt('COM_WIKI_NONE')) }}
            @endif
            <br />
            <span class="text-xs text-muted-foreground">/wiki/ {{ $pageNamePath }}</span>
          </td>
          <td class="priority-4">{{ $row->param('mode') }}</td>
          <td>
            @if ($canDo->get('core.edit.state'))
              <a href="{{ $stateUrl }}">
                <span class="badge badge-sm {{ $stateBadge }}">{{ $stateText }}</span>
              </a>
            @else
              <span class="badge badge-sm {{ $stateBadge }}">{{ $stateText }}</span>
            @endif
          </td>
          <td class="priority-5">
            @if ($row->get('protected'))
              <span class="badge badge-sm badge-error">{{ Lang::txt('COM_WIKI_STATE_LOCKED') }}</span>
            @else
              <span class="badge badge-sm badge-ghost">{{ Lang::txt('COM_WIKI_STATE_OPEN') }}</span>
            @endif
          </td>
          <td class="priority-4">
            {{ $row->get('scope') . ':' . $row->get('scope_id') }}
          </td>
          <td class="priority-2">
            <a href="{{ $revisionsUrl }}">
              {{ Lang::txt('COM_WIKI_NUM_REVISIONS', $row->versions->count()) }}
            </a>
          </td>
          <td class="priority-3">
            @if ($canDo->get('core.edit'))
              <a href="{{ $commentsUrl }}">
                {{ Lang::txt('COM_WIKI_NUM_COMMENTS', $row->comments->count()) }}
              </a>
            @else
              {{ Lang::txt('COM_WIKI_NUM_COMMENTS', $row->comments->count()) }}
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="9">{!! $rows->pagination !!}</td>
      </tr>
    </tfoot>
  </table>

</x-admin-form>
