{{--
  com_messages — Inbox list

  Variables: $rows (paginated Message models), $filters (search, state, sort, sort_Dir)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo   = \Components\Messages\Helpers\Utilities::getActions();
  $sort    = $filters['sort'] ?? 'a.date_time';
  $sortDir = $filters['sort_Dir'] ?? 'DESC';

  Toolbar::title(Lang::txt('COM_MESSAGES_MANAGER_MESSAGES'), 'inbox');

  if ($canDo->get('core.create')) {
      Toolbar::addNew('add');
  }
  if ($canDo->get('core.edit.state')) {
      Toolbar::spacer();
      Toolbar::publish('publish', 'COM_MESSAGES_TOOLBAR_MARK_AS_READ');
      Toolbar::unpublish('unpublish', 'COM_MESSAGES_TOOLBAR_MARK_AS_UNREAD');
  }

  Toolbar::spacer();
  if (isset($filters['state']) && $filters['state'] == -2 && $canDo->get('core.delete')) {
      Toolbar::deleteList('', 'delete', 'JTOOLBAR_EMPTY_TRASH');
  } elseif ($canDo->get('core.edit.state')) {
      Toolbar::trash('trash');
  }

  Toolbar::spacer();
  $configUrl = Route::url(
      'index.php?option=com_messages&controller=configs&tmpl=component', false
  );
  Toolbar::appendButton(
      'Popup',
      'options',
      'COM_MESSAGES_TOOLBAR_MY_SETTINGS',
      $configUrl,
      640,
      420
  );

  if ($canDo->get('core.admin')) {
      Toolbar::preferences('com_messages');
  }

  Toolbar::spacer();
  Toolbar::help('inbox');
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
               name="filter_search"
               id="filter_search"
               class="input input-bordered input-sm w-64"
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('COM_MESSAGES_SEARCH_IN_SUBJECT') }}" />
        <button type="submit" class="btn btn-sm btn-primary">
          {{ Lang::txt('JSEARCH_FILTER_SUBMIT') }}
        </button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      @slot('selects')
        <label for="filter_state" class="text-sm">{{ Lang::txt('JSTATUS') }}:</label>
        <select name="filter_state"
                id="filter_state"
                class="select select-bordered select-sm"
                data-submit-on-change>
          <option value="">{{ Lang::txt('JOPTION_SELECT_PUBLISHED') }}</option>
          {!! Html::select('options',
              \Components\Messages\Helpers\Utilities::getStateOptions(),
              'value', 'text',
              $filters['state'] ?? '') !!}
        </select>
      @endslot
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
          <th>
            {!! Html::grid('sort', 'COM_MESSAGES_HEADING_SUBJECT', 'a.subject', $sortDir, $sort) !!}
          </th>
          <th class="w-28 text-center">
            {!! Html::grid('sort', 'COM_MESSAGES_HEADING_READ', 'a.state', $sortDir, $sort) !!}
          </th>
          <th class="w-40" data-priority="2">
            {!! Html::grid('sort', 'COM_MESSAGES_HEADING_FROM', 'a.user_id_from', $sortDir, $sort) !!}
          </th>
          <th class="w-40 whitespace-nowrap" data-priority="3">
            {!! Html::grid('sort', 'JDATE', 'a.date_time', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="5">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach ($rows as $i => $item)
          @php
            $canChange = \Hubzero\Facades\User::authorise('core.edit.state', 'com_messages');
            $viewUrl   = Route::url(
                'index.php?option=' . $option
                . '&task=view&message_id=' . (int) $item->message_id,
                false, false
            );
            $state     = (int) $item->get('state');
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="cid[]"
                     id="cb{{ $i }}"
                     value="{{ $item->message_id }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
                     data-check-item />
            </td>
            <td class="{{ $state === 0 ? 'font-semibold' : '' }}">
              <a href="{{ $viewUrl }}"
                 class="link link-hover {{ $state === 0 ? 'text-primary' : '' }}">
                {{ $item->subject }}
              </a>
            </td>
            <td class="text-center">
              @if ($state === -2)
                <span class="badge badge-sm badge-error">{{ Lang::txt('JTRASHED') }}</span>
              @elseif ($state === 1)
                @if ($canChange)
                  <a href="#"
                     class="badge badge-sm badge-ghost"
                     data-list-item-task="unpublish"
                     data-cb="cb{{ $i }}"
                     title="{{ Lang::txt('COM_MESSAGES_MARK_AS_UNREAD') }}">
                    {{ Lang::txt('COM_MESSAGES_OPTION_READ') }}
                  </a>
                @else
                  <span class="badge badge-sm badge-ghost">{{ Lang::txt('COM_MESSAGES_OPTION_READ') }}</span>
                @endif
              @else
                @if ($canChange)
                  <a href="#"
                     class="badge badge-sm badge-info"
                     data-list-item-task="publish"
                     data-cb="cb{{ $i }}"
                     title="{{ Lang::txt('COM_MESSAGES_MARK_AS_READ') }}">
                    {{ Lang::txt('COM_MESSAGES_OPTION_UNREAD') }}
                  </a>
                @else
                  <span class="badge badge-sm badge-info">{{ Lang::txt('COM_MESSAGES_OPTION_UNREAD') }}</span>
                @endif
              @endif
            </td>
            <td data-priority="2">{{ $item->from ? $item->from->name : '' }}</td>
            <td class="whitespace-nowrap" data-priority="3">
              @if ($item->date_time && $item->date_time !== '0000-00-00 00:00:00')
                <time datetime="{{ $item->date_time }}">
                  {{ Date::of($item->date_time)->toLocal(Lang::txt('DATE_FORMAT_LC2')) }}
                </time>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="filter_order" value="{{ $sort }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $sortDir }}" />
</x-admin-form>
