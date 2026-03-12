{{--
  Questions — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $canDo   = \Components\Answers\Helpers\Permissions::getActions('question');
  $sort    = $filters['sort'] ?? '';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_ANSWERS_TITLE') . ': ' . Lang::txt('COM_ANSWERS_QUESTIONS'), 'answers');
  if ($canDo->get('core.admin')) {
      Toolbar::preferences($option, '550');
      Toolbar::spacer();
  }
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList('COM_ANSWERS_CONFIRM_DELETE');
  }
  Toolbar::spacer();
  Toolbar::help('questions');
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
               placeholder="{{ Lang::txt('COM_ANSWERS_FILTER_SEARCH_PLACEHOLDER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_ANSWERS_GO') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      <label for="filter-state" class="text-sm">{{ Lang::txt('COM_ANSWERS_FILTER_BY') }}:</label>
      <select name="state" id="filter-state"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="-1" @selected($filters['state'] == -1)>{{ Lang::txt('COM_ANSWERS_FILTER_BY_ALL') }}</option>
        <option value="0" @selected($filters['state'] === 0 || $filters['state'] === '0')>{{ Lang::txt('COM_ANSWERS_FILTER_BY_OPEN') }}</option>
        <option value="1" @selected($filters['state'] == 1)>{{ Lang::txt('COM_ANSWERS_FILTER_BY_CLOSED') }}</option>
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
          <th>{!! Html::grid('sort', 'COM_ANSWERS_COL_ID', 'id', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_ANSWERS_COL_SUBJECT', 'subject', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_ANSWERS_COL_STATE', 'state', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_ANSWERS_COL_CREATED', 'created', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_ANSWERS_COL_CREATOR', 'created_by', $sortDir, $sort) !!}</th>
          <th>{{ Lang::txt('COM_ANSWERS_COL_ANSWERS') }}</th>
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
            switch ($row->get('state')) {
                case 2:
                    $task = 'open';
                    $alt  = Lang::txt('COM_ANSWERS_STATE_TRASHED');
                    $cls  = 'badge-warning';
                    break;
                case 1:
                    $task = 'open';
                    $alt  = Lang::txt('COM_ANSWERS_STATE_CLOSED');
                    $cls  = 'badge-ghost';
                    break;
                case 0:
                default:
                    $task = 'close';
                    $alt  = Lang::txt('COM_ANSWERS_STATE_OPEN');
                    $cls  = 'badge-success';
                    break;
            }

            $comments    = $row->responses->count();
            $creatorName = $row->creator->get('name');
            $creatorName = $creatorName
                ? e($creatorName) . ' (' . $row->get('created_by') . ')'
                : Lang::txt('COM_ANSWERS_UNKNOWN') . ' (' . $row->get('created_by') . ')';
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ strip_tags($row->get('subject')) }}"
                     data-check-item />
            </td>
            <td>{{ $row->get('id') }}</td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&id=' . $row->get('id'), false) }}"
                   class="link link-hover text-primary font-medium">
                  {{ strip_tags($row->get('subject')) }}
                </a>
              @else
                {{ strip_tags($row->get('subject')) }}
              @endif
            </td>
            <td class="column-status">
              @if($canDo->get('core.edit.state'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=' . $task . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1', false) }}">
                  <span class="badge badge-sm {{ $cls }}">{{ $alt }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $cls }}">{{ $alt }}</span>
              @endif
            </td>
            <td>
              <time datetime="{{ $row->created() }}">
                {{ $row->created('date') }}
              </time>
            </td>
            <td>
              {{ $creatorName }}
              @if($row->get('anonymous'))
                <br /><span class="text-xs text-muted-foreground">({{ Lang::txt('COM_ANSWERS_FIELD_ANONYMOUS') }})</span>
              @endif
            </td>
            <td>
              <a href="{{ Route::url('index.php?option=' . $option . '&controller=answers&qid=' . $row->get('id'), false) }}"
                 class="link link-hover text-primary">
                {{ $comments > 0 ? Lang::txt('COM_ANSWERS_NUM_RESPONSES', $comments) : '0' }}
              </a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
