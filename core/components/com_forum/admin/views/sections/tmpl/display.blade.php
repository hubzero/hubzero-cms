{{--
  Forum Sections — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Session;

  $canDo   = \Components\Forum\Helpers\Permissions::getActions('section');
  $sort    = $filters['sort'] ?? 'id';
  $sortDir = $filters['sort_Dir'] ?? 'DESC';
  $access  = Html::access('assetgroups');

  // Build scope lookup
  $scopeList = [];
  foreach ($scopes as $result) {
      if (!isset($scopeList[$result->scope])) {
          $scopeList[$result->scope] = [];
      }
      $scopeList[$result->scope][$result->scope_id] = $result;
  }
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_FORUM') }}: {{ Lang::txt('COM_FORUM_SECTIONS') }}"
    icon="forum"
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
        <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
        <input type="text"
               name="search"
               id="filter_search"
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] }}"
               placeholder="{{ Lang::txt('COM_FORUM_FILTER_SEARCH_PLACEHOLDER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_FORUM_GO') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      <label for="scopeinfo" class="text-sm">{{ Lang::txt('COM_FORUM_FILTER_SCOPE') }}:</label>
      <select name="scopeinfo" id="scopeinfo"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="">{{ Lang::txt('COM_FORUM_FILTER_SCOPE_SELECT') }}</option>
        <option value="site:0" @selected($filters['scopeinfo'] == 'site:0')>
          {{ Lang::txt('COM_FORUM_NONE') }}
        </option>
        @foreach($scopeList as $label => $optgroup)
          @if($label !== 'site')
            <optgroup label="{{ $label }}">
              @foreach($optgroup as $result)
                @php $val = $result->scope . ':' . $result->scope_id; @endphp
                <option value="{{ $val }}" @selected($filters['scopeinfo'] == $val)>
                  {{ $result->caption }}
                </option>
              @endforeach
            </optgroup>
          @endif
        @endforeach
      </select>

      <label for="filter-state" class="text-sm">{{ Lang::txt('COM_FORUM_FIELD_STATE') }}:</label>
      <select name="state" id="filter-state"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="-1" @selected($filters['state'] == -1)>{{ Lang::txt('COM_FORUM_ALL_STATES') }}</option>
        <option value="0" @selected($filters['state'] === 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
        <option value="1" @selected($filters['state'] === 1)>{{ Lang::txt('JPUBLISHED') }}</option>
        <option value="2" @selected($filters['state'] === 2)>{{ Lang::txt('JTRASHED') }}</option>
      </select>

      <label for="filter-access" class="text-sm">{{ Lang::txt('JFIELD_ACCESS_LABEL') }}:</label>
      <select name="access" id="filter-access"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="-1">{{ Lang::txt('JOPTION_SELECT_ACCESS') }}</option>
        {!! Html::select('options', $access, 'value', 'text', $filters['access']) !!}
      </select>
    </x-admin-filters>
  @endslot

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
          <th class="w-16">
            {!! Html::grid('sort', 'COM_FORUM_COL_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_FORUM_COL_TITLE', 'title', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_FORUM_COL_STATE', 'state', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_FORUM_COL_ACCESS', 'access', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_FORUM_COL_SCOPE', 'scope', $sortDir, $sort) !!}
          </th>
          <th class="w-24 text-center">{{ Lang::txt('COM_FORUM_CATEGORIES') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="7">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $state = (int) $row->get('state', 0);
            if ($state === 1) {
                $stateClass = 'badge-success';
                $stateText  = Lang::txt('JPUBLISHED');
                $stateTask  = 'unpublish';
            } elseif ($state === 2) {
                $stateClass = 'badge-warning';
                $stateText  = Lang::txt('JTRASHED');
                $stateTask  = 'publish';
            } else {
                $stateClass = 'badge-ghost';
                $stateText  = Lang::txt('JUNPUBLISHED');
                $stateTask  = 'publish';
            }

            $accessLevel = '';
            foreach ($access as $ac) {
                if ($row->get('access') == $ac->value) {
                    $accessLevel = $ac->text;
                    break;
                }
            }

            $rowScope   = $row->get('scope');
            $rowScopeId = $row->get('scope_id');
            $scopeCaption = isset($scopeList[$rowScope][$rowScopeId])
                ? e($scopeList[$rowScope][$rowScopeId]->caption)
                : e($rowScopeId);

            $catCount = $row->categories->count();

            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->get('id'),
                false, false
            );

            $stateUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=' . $stateTask
                . '&id=' . $row->get('id')
                . '&' . Session::getFormToken() . '=1',
                false, false
            );

            $catUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=categories&section_id=' . $row->get('id'),
                false, false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $row->get('title') }}"
                     data-check-item />
            </td>
            <td>{{ $row->get('id') }}</td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}"
                   class="link link-hover text-primary font-medium">
                  {{ $row->get('title') }}
                </a>
              @else
                {{ $row->get('title') }}
              @endif
            </td>
            <td class="column-status">
              @if($canDo->get('core.edit.state'))
                <a href="{{ $stateUrl }}"
                   title="{{ Lang::txt('COM_FORUM_SET_TO', $stateTask) }}">
                  <span class="badge badge-sm {{ $stateClass }}">{{ $stateText }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $stateClass }}">{{ $stateText }}</span>
              @endif
            </td>
            <td>{{ $accessLevel }}</td>
            <td>{{ $rowScope }} ({{ $scopeCaption }})</td>
            <td class="text-center">
              @if($catCount > 0)
                <a href="{{ $catUrl }}" class="link link-hover">{{ $catCount }}</a>
              @else
                {{ $catCount }}
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
