{{--
  Collections — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo = \Components\Collections\Helpers\Permissions::getActions('collection');

  $sort    = $filters['sort'] ?? 'title';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_COLLECTIONS'), 'collections');
  if ($canDo->get('core.admin')) {
      Toolbar::preferences($option, '550');
      Toolbar::spacer();
  }
  if ($canDo->get('core.edit.state')) {
      Toolbar::publishList();
      Toolbar::unpublishList();
      Toolbar::spacer();
  }
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
  Toolbar::help('collections');

  $accessMap = [0 => 'public', 1 => 'registered', 4 => 'private'];
  $accessCls = [
      'public'     => 'badge-success',
      'registered' => 'badge-warning',
      'private'    => 'badge-error',
  ];
  $accessTask = [
      'public'     => 'accessregistered',
      'registered' => 'accessprivate',
      'private'    => 'accesspublic',
  ];
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
        <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
        <input type="text"
               name="search"
               id="filter_search"
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] }}"
               placeholder="{{ Lang::txt('COM_COLLECTIONS_FILTER_SEARCH_PLACEHOLDER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_COLLECTIONS_GO') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      <label for="filter-state" class="text-sm">{{ Lang::txt('COM_COLLECTIONS_FIELD_STATE') }}:</label>
      <select name="state" id="filter-state"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="-1" @selected($filters['state'] == -1)>{{ Lang::txt('COM_COLLECTIONS_ALL_STATES') }}</option>
        <option value="0" @selected($filters['state'] === 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
        <option value="1" @selected($filters['state'] === 1)>{{ Lang::txt('JPUBLISHED') }}</option>
        <option value="2" @selected($filters['state'] === 2)>{{ Lang::txt('JTRASHED') }}</option>
      </select>

      <label for="filter-access" class="text-sm">{{ Lang::txt('COM_COLLECTIONS_FIELD_ACCESS') }}:</label>
      <select name="access" id="filter-access"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="-1" @selected($filters['access'] == -1)>{{ Lang::txt('JOPTION_SELECT_ACCESS') }}</option>
        <option value="0" @selected($filters['access'] === 0)>{{ Lang::txt('COM_COLLECTIONS_ACCESS_PUBLIC') }}</option>
        <option value="1" @selected($filters['access'] === 1)>{{ Lang::txt('COM_COLLECTIONS_ACCESS_REGISTERED') }}</option>
        <option value="4" @selected($filters['access'] === 4)>{{ Lang::txt('COM_COLLECTIONS_ACCESS_PRIVATE') }}</option>
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
          <th>{!! Html::grid('sort', 'COM_COLLECTIONS_COL_ID', 'id', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_COLLECTIONS_COL_TITLE', 'title', $sortDir, $sort) !!}</th>
          <th>{{ Lang::txt('COM_COLLECTIONS_COL_STATE') }}</th>
          <th>{{ Lang::txt('COM_COLLECTIONS_COL_ACCESS') }}</th>
          <th>{{ Lang::txt('COM_COLLECTIONS_COL_OWNER') }}</th>
          <th>{{ Lang::txt('COM_COLLECTIONS_COL_POSTS') }}</th>
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
            $editUrl = Route::url(
                'index.php?option=' . $option . '&controller=' . $controller
                . '&task=edit&id=' . $row->get('id'),
                false, false
            );

            // State
            if ($row->get('state') == 1) {
                $stateText = Lang::txt('JPUBLISHED');
                $stateCls  = 'badge-success';
                $stateTask = 'unpublish';
            } elseif ($row->get('state') == 2) {
                $stateText = Lang::txt('JTRASHED');
                $stateCls  = 'badge-warning';
                $stateTask = 'publish';
            } else {
                $stateText = Lang::txt('JUNPUBLISHED');
                $stateCls  = 'badge-ghost';
                $stateTask = 'publish';
            }

            // Access
            $accessKey  = $accessMap[$row->get('access')] ?? 'public';
            $accessText = Lang::txt('COM_COLLECTIONS_ACCESS_' . strtoupper($accessKey));

            // Owner
            $ownerText = $row->get('object_type') . ' (' . $row->get('object_id') . ')';
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
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
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=' . $stateTask . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1', false) }}">
                  <span class="badge badge-sm {{ $stateCls }}">{{ $stateText }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $stateCls }}">{{ $stateText }}</span>
              @endif
            </td>
            <td>
              @if($canDo->get('core.edit.state'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=' . $accessTask[$accessKey] . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1', false) }}">
                  <span class="badge badge-sm {{ $accessCls[$accessKey] }}">{{ $accessText }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $accessCls[$accessKey] }}">{{ $accessText }}</span>
              @endif
            </td>
            <td>{{ $ownerText }}</td>
            <td>
              <a href="{{ Route::url('index.php?option=' . $option . '&controller=posts&collection_id=' . $row->get('id'), false) }}"
                 class="link link-hover text-primary">
                {{ $row->posts()->total() }}
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
