{{--
  Event Pages — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_EVENTS') . ': ' . Lang::txt('COM_EVENTS_PAGES'), 'event');
  Toolbar::addNew();
  Toolbar::editList();
  Toolbar::deleteList();

  $eventEditUrl = Route::url(
      'index.php?option=' . $option . '&task=edit&id=' . $event->id,
      false, false
  );

  $pageNav = $rows->pagination;
  $orderings = $rows->fieldsByKey('ordering');
@endphp

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
      method="post" name="adminForm" id="adminForm">

  <fieldset class="admin-filters-bar flex flex-wrap items-end gap-3 mb-4">
    <input type="text"
           name="search"
           id="filter_search"
           class="input input-sm input-bordered w-64"
           placeholder="{{ Lang::txt('COM_EVENTS_SEARCH_PLACEHOLDER') }}"
           value="{{ $filters['search'] ?? '' }}" />

    <button type="submit" class="btn btn-sm btn-primary">
      {{ Lang::txt('COM_EVENTS_SEARCH_GO') }}
    </button>
  </fieldset>

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th colspan="6">
            <a href="{{ $eventEditUrl }}"
               class="link link-hover text-primary font-medium">
              {{ $event->title }}
            </a>
          </th>
        </tr>
        <tr>
          <th class="column-check">
            <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
          </th>
          <th>{{ Lang::txt('COM_EVENTS_ID') }}</th>
          <th>{{ Lang::txt('COM_EVENTS_TITLE') }}</th>
          <th colspan="3">{{ Lang::txt('COM_EVENTS_REORDER') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            <div class="admin-pagination">
              {!! $pageNav->render() !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $rowEditUrl = Route::url(
                'index.php?option=' . $option . '&controller=' . $controller
                . '&task=edit&id=' . $row->id . '&event_id=' . $event->id,
                false, false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->id }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $row->title }}"
                     data-check-item />
            </td>
            <td>{{ $row->id }}</td>
            <td>
              <a href="{{ $rowEditUrl }}"
                 class="link link-hover text-primary font-medium">
                {{ $row->title }}
              </a>
              <span class="text-muted-foreground text-xs ml-1">
                ({{ $row->alias }})
              </span>
            </td>
            <td>
              {!! $pageNav->orderUpIcon($i, ($row->ordering != @$orderings[$i - 1])) !!}
            </td>
            <td>
              {!! $pageNav->orderDownIcon($i, $pageNav->total, ($row->ordering != @$orderings[$i + 1])) !!}
            </td>
            <td>{{ $row->ordering }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="event_id" value="{{ $event->id }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" autocomplete="" />
  <input type="hidden" name="boxchecked" value="0" />
  {!! Html::input('token') !!}
</form>
