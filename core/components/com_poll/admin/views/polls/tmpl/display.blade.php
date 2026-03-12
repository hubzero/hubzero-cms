{{--
  Polls — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $canDo   = \Components\Poll\Helpers\Permissions::getActions('component');
  $sort    = $filters['order'] ?? '';
  $sortDir = $filters['order_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_POLL'), 'poll');
  if ($canDo->get('core.admin')) {
      Toolbar::preferences('com_poll', '550');
      Toolbar::spacer();
  }
  if ($canDo->get('core.edit.state')) {
      Toolbar::publishList();
      Toolbar::unpublishList();
      Toolbar::spacer();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList('COM_POLL_CONFIRM_DELETE');
      Toolbar::spacer();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  Toolbar::spacer();
  Toolbar::help('polls');
@endphp

<x-admin-form
    option="{{ $option }}"
    controller=""
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
               placeholder="{{ Lang::txt('COM_POLL_SEARCH_PLACEHOLDER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_POLL_GO') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      <label for="filter-state" class="text-sm">{{ Lang::txt('COM_POLL_COL_PUBLISHED') }}:</label>
      <select name="state" id="filter-state"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="">{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}</option>
        <option value="1" @selected(($filters['state'] ?? '') === '1')>{{ Lang::txt('JPUBLISHED') }}</option>
        <option value="0" @selected(($filters['state'] ?? '') === '0')>{{ Lang::txt('JUNPUBLISHED') }}</option>
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
          <th>{!! Html::grid('sort', 'COM_POLL_COL_TITLE', 'title', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_POLL_COL_PUBLISHED', 'state', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_POLL_COL_OPEN', 'open', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_POLL_COL_VOTES', 'voters', $sortDir, $sort) !!}</th>
          <th>{{ Lang::txt('COM_POLL_COL_OPTIONS') }}</th>
          <th>{!! Html::grid('sort', 'COM_POLL_COL_LAG', 'lag', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_POLL_COL_ID', 'id', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="8">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $task  = $row->get('state') ? 'unpublish' : 'publish';
            $alt   = $row->get('state') ? Lang::txt('JPUBLISHED') : Lang::txt('JUNPUBLISHED');
            $cls   = $row->get('state') ? 'badge-success' : 'badge-ghost';

            $task2 = ($row->get('open') == 1) ? 'close' : 'open';
            $alt2  = ($row->get('open') == 1) ? Lang::txt('COM_POLL_OPEN') : Lang::txt('COM_POLL_CLOSED');
            $cls2  = ($row->get('open') == 1) ? 'badge-success' : 'badge-ghost';

            $checkedOut = $row->get('checked_out')
                && $row->get('checked_out') != User::get('id');
            $noEdit = $checkedOut || !$canDo->get('core.edit');

            $token = Session::getFormToken();
          @endphp
          <tr>
            <td class="column-check">
              @if(!$noEdit)
                <input type="checkbox"
                       name="id[]"
                       id="cb{{ $i }}"
                       value="{{ $row->get('id') }}"
                       class="checkbox checkbox-sm"
                       aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
                       data-check-item />
              @endif
            </td>
            <td>
              @if(!$noEdit)
                <a href="{{ Route::url('index.php?option=' . $option . '&view=poll&task=edit&id=' . $row->get('id'), false) }}"
                   class="link link-hover text-primary font-medium">
                  {{ $row->get('title') }}
                </a>
              @else
                {{ $row->get('title') }}
              @endif
            </td>
            <td class="column-status">
              @if($canDo->get('core.edit.state'))
                <a href="{{ Route::url('index.php?option=' . $option . '&task=' . $task . '&id=' . $row->get('id') . '&' . $token . '=1', false) }}"
                   title="{{ Lang::txt('COM_POLL_SET_TO', $task) }}">
                  <span class="badge badge-sm {{ $cls }}">{{ $alt }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $cls }}">{{ $alt }}</span>
              @endif
            </td>
            <td>
              @if($canDo->get('core.edit.state'))
                <a href="{{ Route::url('index.php?option=' . $option . '&task=' . $task2 . '&id=' . $row->get('id') . '&' . $token . '=1', false) }}"
                   title="{{ Lang::txt('COM_POLL_SET_TO', $task2) }}">
                  <span class="badge badge-sm {{ $cls2 }}">{{ $alt2 }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $cls2 }}">{{ $alt2 }}</span>
              @endif
            </td>
            <td>{{ $row->dates->count() }}</td>
            <td>{{ $row->options->count() }}</td>
            <td>{{ $row->get('lag') }}</td>
            <td>{{ $row->get('id') }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
