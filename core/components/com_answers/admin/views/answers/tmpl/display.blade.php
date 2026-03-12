{{--
  Responses — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;

  $canDo   = \Components\Answers\Helpers\Permissions::getActions('answer');
  $sort    = $filters['sort'] ?? '';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(Lang::txt('COM_ANSWERS_TITLE') . ': ' . Lang::txt('COM_ANSWERS_RESPONSES'), 'answers');
  if ($canDo->get('core.create') && $filters['question_id']) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
      Toolbar::spacer();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList('COM_ANSWERS_CONFIRM_DELETE');
      Toolbar::spacer();
  }
  Toolbar::help('responses');
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
        {{-- No search for answers, but slot required --}}
      @endslot

      <label for="filter-state" class="text-sm">{{ Lang::txt('COM_ANSWERS_FILTER_BY') }}:</label>
      <select name="state" id="filter-state"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="-1" @selected($filters['state'] == -1)>{{ Lang::txt('COM_ANSWERS_FILTER_BY_ALL_RESPONSES') }}</option>
        <option value="1" @selected($filters['state'] == 1)>{{ Lang::txt('COM_ANSWERS_FILTER_BY_ACCEPTED') }}</option>
        <option value="0" @selected($filters['state'] === 0 || $filters['state'] === '0')>{{ Lang::txt('COM_ANSWERS_FILTER_BY_UNACCEPTED') }}</option>
      </select>
    </x-admin-filters>
  @endslot

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        @if($question->get('id'))
          <tr>
            <th colspan="7" class="text-sm font-normal text-muted-foreground">
              #{{ $question->get('id') }} -
              <a href="{{ Route::url('index.php?option=' . $option . '&controller=questions&task=edit&id=' . $question->get('id'), false) }}"
                 class="link link-hover text-primary">
                {{ strip_tags($question->get('subject')) }}
              </a>
            </th>
          </tr>
        @else
          <tr>
            <th colspan="7" class="text-sm font-normal text-muted-foreground">
              {{ Lang::txt('COM_ANSWERS_RESPONSES_TO_ALL') }}
            </th>
          </tr>
        @endif
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{!! Html::grid('sort', 'COM_ANSWERS_COL_ID', 'id', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_ANSWERS_COL_ANSWER', 'answer', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_ANSWERS_COL_ACCEPTED', 'state', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_ANSWERS_COL_CREATED', 'created', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_ANSWERS_COL_CREATOR', 'created_by', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_ANSWERS_COL_VOTES', 'helpful', $sortDir, $sort) !!}</th>
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
            switch (intval($row->get('state'))) {
                case 1:
                    $task = 'reject';
                    $alt  = Lang::txt('COM_ANSWERS_STATE_ACCEPTED');
                    $cls  = 'badge-success';
                    break;
                case 0:
                default:
                    $task = 'accept';
                    $alt  = Lang::txt('COM_ANSWERS_STATE_UNACCEPTED');
                    $cls  = 'badge-ghost';
                    break;
            }

            $creatorName = $row->creator->get('name')
                . ' (' . $row->get('created_by') . ')';
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->get('id') }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ \Hubzero\Utility\Str::truncate(strip_tags($row->get('answer')), 75) }}"
                     data-check-item />
            </td>
            <td>{{ $row->get('id') }}</td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&id=' . $row->get('id') . '&qid=' . $question->get('id'), false) }}"
                   class="link link-hover text-primary font-medium">
                  {{ \Hubzero\Utility\Str::truncate(strip_tags($row->get('answer')), 75) }}
                </a>
              @else
                {{ \Hubzero\Utility\Str::truncate(strip_tags($row->get('answer')), 75) }}
              @endif
            </td>
            <td class="column-status">
              @if($canDo->get('core.edit.state'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=' . $task . '&id=' . $row->get('id') . '&qid=' . $question->get('id') . '&' . Session::getFormToken() . '=1', false) }}">
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
                <br /><span class="text-xs text-muted-foreground">({{ Lang::txt('JANONYMOUS') }})</span>
              @endif
            </td>
            <td>
              <span class="text-[#15803d] font-medium">+{{ $row->get('helpful', 0) }}</span>
              <span class="text-[#b91c1c] font-medium">-{{ $row->get('nothelpful', 0) }}</span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="qid" value="{{ $question->get('id') }}" />
</x-admin-form>
