{{--
  Event Respondents — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Date;
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  Toolbar::title(
      Lang::txt('COM_EVENTS') . ': ' . Lang::txt('COM_EVENTS_RESPONDANTS'),
      'user.png'
  );
  Toolbar::custom('download', 'upload', 'COM_EVENTS_DOWNLOAD_CSV', 'COM_EVENTS_DOWNLOAD_CSV', false, false);
  Toolbar::deleteList('', 'remove', 'COM_EVENTS_DELETE');
  Toolbar::cancel();

  $sort    = $filters['sort'] ?? 'name';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  $id = Request::getArray('id', []);
  $idValue = is_array($id) ? implode(',', $id) : $id;
@endphp

<form action="{{ Route::url('index.php?option=' . $option, false) }}"
      method="post" name="adminForm" id="adminForm">

  <h2 class="text-lg font-semibold mb-4">
    {{ $event->title }}
  </h2>

  <fieldset class="admin-filters-bar flex flex-wrap items-end gap-3 mb-4">
    <input type="text"
           name="search"
           id="filter_search"
           class="input input-sm input-bordered w-64"
           placeholder="{{ Lang::txt('COM_EVENTS_SEARCH_PLACEHOLDER') }}"
           value="{{ $filters['search'] ?? '' }}" />

    <button type="submit" class="btn btn-sm btn-primary">
      {{ Lang::txt('COM_EVENTS_GO') }}
    </button>
  </fieldset>

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
            {!! Html::grid('sort', 'COM_EVENTS_RESPONDANT_NAME', 'name', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_EVENTS_EMAIL', 'email', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_EVENTS_RESPONDANT_REGISTERED', 'registered', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_EVENTS_SPECIAL_NEEDS', 'special', $sortDir, $sort) !!}
          </th>
          <th>{{ Lang::txt('COM_EVENTS_COMMENT') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="6">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $respUrl = Route::url(
                'index.php?option=' . $option . '&controller=' . $controller
                . '&task=respondent&id=' . $row->id
                . '&event_id=' . $event->id,
                false, false
            );
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="rid[]"
                     id="cb{{ $i }}"
                     value="{{ $row->id }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ $row->last_name }}, {{ $row->first_name }}"
                     data-check-item />
            </td>
            <td>
              <a href="{{ $respUrl }}"
                 class="link link-hover text-primary font-medium">
                {{ $row->last_name . ', ' . $row->first_name }}
              </a>
            </td>
            <td>
              <a href="mailto:{{ $row->email }}" class="link link-hover">
                {{ $row->email }}
              </a>
            </td>
            <td>
              {{ Date::of($row->registered)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) }}
            </td>
            <td>
              @if(!empty($row->dietary_needs))
                <span class="badge badge-sm badge-warning">
                  {{ $row->dietary_needs }}
                </span>
              @endif
              @if($row->disability_needs)
                <span class="badge badge-sm badge-info">
                  {{ Lang::txt('COM_EVENTS_RESPONDANT_DISABILITY_REQUESTED') }}
                </span>
              @endif
            </td>
            <td class="text-sm">
              {{ $row->comment }}
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="event" value="{{ $idValue }}" />
  <input type="hidden" name="id[]" value="{{ $idValue }}" />
  <input type="hidden" name="task" value="" autocomplete="" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="{{ $sort }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $sortDir }}" />
  {!! Html::input('token') !!}
</form>
