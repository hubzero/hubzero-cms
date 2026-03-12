{{--
  Blog Entries — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\App;
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $canDo  = \Components\Blog\Admin\Helpers\Permissions::getActions('entry');
  $access = Html::access('assetgroups');
  $sort    = $filters['sort'] ?? '';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  $now      = Date::of('now');
  $db       = App::get('db');
  $nullDate = $db->getNullDate();

  // Custom toolbar — blog uses deleteList with confirm message
  Toolbar::title(Lang::txt('COM_BLOG_TITLE'), 'blog');
  if ($canDo->get('core.admin')) {
      Toolbar::preferences($option, '550');
      Toolbar::spacer();
  }
  if ($canDo->get('core.edit.state')) {
      Toolbar::publishList();
      Toolbar::unpublishList();
      Toolbar::spacer();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList('COM_BLOG_CONFIRM_DELETE', 'delete');
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  Toolbar::spacer();
  Toolbar::help('entries');
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
               placeholder="{{ Lang::txt('COM_BLOG_FILTER_SEARCH_PLACEHOLDER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_BLOG_GO') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      <label for="filter-scope" class="text-sm">{{ Lang::txt('COM_BLOG_FIELD_SCOPE') }}:</label>
      {!! \Components\Blog\Admin\Helpers\Html::scopes(
          $filters['scope'],
          'scope',
          'filter-scope',
          'class="select select-bordered select-sm" data-submit-on-change'
      ) !!}

      <label for="filter-state" class="text-sm">{{ Lang::txt('COM_BLOG_FIELD_STATE') }}:</label>
      <select name="state" id="filter-state"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="-1" @selected($filters['state'] == '-1')>{{ Lang::txt('COM_BLOG_ALL_STATES') }}</option>
        <option value="0" @selected($filters['state'] === 0)>{{ Lang::txt('JUNPUBLISHED') }}</option>
        <option value="1" @selected($filters['state'] === 1)>{{ Lang::txt('JPUBLISHED') }}</option>
        <option value="2" @selected($filters['state'] === 2)>{{ Lang::txt('JTRASHED') }}</option>
      </select>

      <label for="filter-access" class="text-sm">{{ Lang::txt('JFIELD_ACCESS_LABEL') }}:</label>
      <select name="access" id="filter-access"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="">{{ Lang::txt('JOPTION_SELECT_ACCESS') }}</option>
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
          <th>{!! Html::grid('sort', 'COM_BLOG_COL_ID', 'id', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_BLOG_COL_TITLE', 'title', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_BLOG_COL_CREATOR', 'created_by', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_BLOG_COL_STATE', 'state', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_BLOG_COL_CREATED', 'created', $sortDir, $sort) !!}</th>
          <th colspan="2">{{ Lang::txt('COM_BLOG_COL_COMMENTS') }}</th>
          <th>{!! Html::grid('sort', 'COM_BLOG_COL_SCOPE', 'scope_id', $sortDir, $sort) !!}</th>
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
        @foreach($rows as $i => $row)
          @php
            $publish_up   = Date::of($row->get('publish_up'));
            $publish_down = Date::of($row->get('publish_down'));

            $stateText = Lang::txt('JUNPUBLISHED');
            $stateCls  = 'badge-ghost';
            $stateTask = 'publish';

            if ($now->toUnix() <= $publish_up->toUnix() && $row->get('state') == 1) {
                $stateText = Lang::txt('JPUBLISHED');
                $stateCls  = 'badge-success';
                $stateTask = 'unpublish';
            } elseif (
                ($now->toUnix() <= $publish_down->toUnix()
                    || !$row->get('publish_down')
                    || $row->get('publish_down') == $nullDate)
                && $row->get('state') == 1
            ) {
                $stateText = Lang::txt('JPUBLISHED');
                $stateCls  = 'badge-success';
                $stateTask = 'unpublish';
            } elseif ($now->toUnix() > $publish_down->toUnix() && $row->get('state') == 1) {
                $stateText = Lang::txt('COM_BLOG_EXPIRED');
                $stateCls  = 'badge-warning';
                $stateTask = 'unpublish';
            } elseif ($row->get('state') == 2 || $row->get('state') == -1) {
                $stateText = Lang::txt('JTRASHED');
                $stateCls  = 'badge-warning';
                $stateTask = 'publish';
            }

            // Comments toggle
            if ($row->get('allow_comments') == 0) {
                $calt  = Lang::txt('JOFF');
                $ccls  = 'badge-ghost';
                $cstate = 1;
            } else {
                $calt  = Lang::txt('JON');
                $ccls  = 'badge-success';
                $cstate = 0;
            }

            $creatorName = $row->creator->get('name');
            $creatorName = $creatorName
                ? $creatorName
                : Lang::txt('COM_BLOG_UNKNOWN') . ' (' . $row->get('created_by') . ')';
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
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&id=' . $row->get('id'), false) }}"
                   class="link link-hover text-primary font-medium">
                  {{ $row->get('title') }}
                </a>
              @else
                {{ $row->get('title') }}
              @endif
            </td>
            <td>{{ $creatorName }}</td>
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
              <time datetime="{{ $row->get('created') }}">
                {{ $row->created('date') }}
              </time>
            </td>
            <td>
              <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=setcomments&state=' . $cstate . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1', false) }}">
                <span class="badge badge-sm {{ $ccls }}">{{ $calt }}</span>
              </a>
            </td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=comments&entry_id=' . $row->get('id'), false) }}"
                   class="link link-hover text-primary">
                  {{ Lang::txt('COM_BLOG_COMMENTS', $row->comments()->total()) }}
                </a>
              @else
                {{ Lang::txt('COM_BLOG_COMMENTS', $row->comments()->total()) }}
              @endif
            </td>
            <td>{{ $row->get('scope') . ' (' . $row->get('scope_id') . ')' }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
