{{--
  Publications — Admin list view

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
  use Hubzero\Facades\User;

  $canDo      = \Components\Publications\Helpers\Permissions::getActions('item');
  $sort       = $filters['sortby'] ?? 'title';
  $sortDir    = $filters['sortdir'] ?? 'asc';
  $pagination = $__view->pagination($total, $filters['start'] ?? 0, $filters['limit'] ?? 20);

  Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATION_MANAGER'), 'publications');
  if ($canDo->get('core.admin')) {
      Toolbar::preferences($option, '550');
  }
  if ($canDo->get('core.edit')) {
      Toolbar::spacer();
      Toolbar::editList();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::spacer();
      Toolbar::deleteList('COM_PUBLICATIONS_CONFIRM_DELETE_ITEM');
  }

  $__view->css()->js();

  // Status badge helper
  $statusMap = [
      0  => ['badge-ghost',     'COM_PUBLICATIONS_VERSION_UNPUBLISHED'],
      1  => ['badge-success',   'COM_PUBLICATIONS_VERSION_PUBLISHED'],
      2  => ['badge-error',     'COM_PUBLICATIONS_VERSION_DELETED'],
      3  => ['badge-info',      'COM_PUBLICATIONS_VERSION_DRAFT'],
      4  => ['badge-accent',    'COM_PUBLICATIONS_VERSION_READY'],
      5  => ['badge-warning',   'COM_PUBLICATIONS_VERSION_PENDING'],
      7  => ['badge-neutral',   'COM_PUBLICATIONS_VERSION_WIP'],
      10 => ['badge-secondary', 'COM_PUBLICATIONS_VERSION_PRESERVING'],
  ];
@endphp

@if($config->get('enabled') == 0)
  <p class="alert alert-warning">{{ Lang::txt('COM_PUBLICATIONS_COMPONENT_DISABLED') }}</p>
@endif

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
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('JSEARCH_FILTER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_PUBLICATIONS_GO') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      @slot('extra')
        {{-- Status filter --}}
        <label for="status" class="sr-only">{{ Lang::txt('COM_PUBLICATIONS_FIELD_STATUS') }}</label>
        <select name="status"
                id="status"
                class="select select-bordered select-sm"
                data-submit-on-change>
          <option value="all" {{ ($filters['status'] ?? 'all') === 'all' ? 'selected' : '' }}>
            {{ Lang::txt('COM_PUBLICATIONS_ALL_STATUS') }}
          </option>
          <option value="3" {{ ($filters['status'] ?? '') == 3 ? 'selected' : '' }}>
            {{ Lang::txt('COM_PUBLICATIONS_VERSION_DRAFT') }}
          </option>
          <option value="5" {{ ($filters['status'] ?? '') == 5 ? 'selected' : '' }}>
            {{ Lang::txt('COM_PUBLICATIONS_VERSION_PENDING') }}
          </option>
          <option value="0" {{ (($filters['status'] ?? 'all') !== 'all' && ($filters['status'] ?? '') == 0) ? 'selected' : '' }}>
            {{ Lang::txt('COM_PUBLICATIONS_VERSION_UNPUBLISHED') }}
          </option>
          <option value="10" {{ ($filters['status'] ?? '') == 10 ? 'selected' : '' }}>
            {{ Lang::txt('COM_PUBLICATIONS_VERSION_PRESERVING') }}
          </option>
          <option value="7" {{ ($filters['status'] ?? '') == 7 ? 'selected' : '' }}>
            {{ Lang::txt('COM_PUBLICATIONS_VERSION_WIP') }}
          </option>
          <option value="1" {{ ($filters['status'] ?? '') == 1 ? 'selected' : '' }}>
            {{ Lang::txt('COM_PUBLICATIONS_VERSION_PUBLISHED') }}
          </option>
          <option value="4" {{ ($filters['status'] ?? '') == 4 ? 'selected' : '' }}>
            {{ Lang::txt('COM_PUBLICATIONS_VERSION_READY') }}
          </option>
          <option value="2" {{ ($filters['status'] ?? '') == 2 ? 'selected' : '' }}>
            {{ Lang::txt('COM_PUBLICATIONS_VERSION_DELETED') }}
          </option>
        </select>

        {{-- Category filter --}}
        <label for="category" class="sr-only">{{ Lang::txt('COM_PUBLICATIONS_FIELD_CATEGORY') }}</label>
        <select name="category"
                id="category"
                class="select select-bordered select-sm"
                data-submit-on-change>
          <option value="" {{ !($filters['category'] ?? '') ? 'selected' : '' }}>
            {{ Lang::txt('COM_PUBLICATIONS_ALL_CATEGORIES') }}
          </option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}"
                    {{ ($filters['category'] ?? '') == $cat->id ? 'selected' : '' }}>
              {{ $cat->name }}
            </option>
          @endforeach
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
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_PUBLICATIONS_FIELD_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_PUBLICATIONS_FIELD_TITLE', 'title', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">{{ Lang::txt('@v.') }}</th>
          <th>{{ Lang::txt('COM_PUBLICATIONS_FIELD_STATUS') }}</th>
          <th class="priority-2">
            {!! Html::grid('sort', 'COM_PUBLICATIONS_FIELD_PROJECT', 'project', $sortDir, $sort) !!}
          </th>
          <th class="priority-4">{{ Lang::txt('COM_PUBLICATIONS_FIELD_RELEASES') }}</th>
          <th class="priority-4" colspan="2">{{ Lang::txt('COM_PUBLICATIONS_FIELD_TYPE_CAT') }}</th>
          <th class="priority-5">{{ Lang::txt('COM_PUBLICATIONS_FIELD_LAST_MODIFIED') }}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="10">
            <div class="admin-pagination">
              {!! $pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @php
          $filterstring  = $sort ? '&sort=' . $sort : '';
          $filterstring .= '&status=' . ($filters['status'] ?? 'all');
          $filterstring .= ($filters['category'] ?? '') ? '&category=' . $filters['category'] : '';
        @endphp
        @forelse($rows as $i => $row)
          @php
            $now     = Date::toSql();
            $isPending = method_exists($row, 'isPending') && $row->isPending();
            $checkedOut = $row->checked_out
                || ($row->checked_out_time && $row->checked_out_time != '0000-00-00 00:00:00');

            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->id . $filterstring, false
            );
            $versUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=versions&id=' . $row->id . $filterstring, false
            );
            $projectUrl = Route::url(
                'index.php?option=com_projects&task=edit&id=' . $row->project_id, false
            );

            $statusBadge = $statusMap[$row->state ?? 0] ?? ['badge-ghost', 'COM_PUBLICATIONS_VERSION_UNPUBLISHED'];
            $statusLabel = Lang::txt($statusBadge[1]);
            $statusClass = $statusBadge[0];

            $modDate = method_exists($row, 'modified')
                ? ($row->modified() ? $row->modified('datetime') : $row->created('datetime'))
                : ($row->modified ?? $row->created ?? '');

            if ($checkedOut) {
                $coDate    = Date::of($row->checked_out_time)->toLocal(Lang::txt('DATE_FORMAT_LC1'));
                $coTime    = Date::of($row->checked_out_time)->toLocal('H:i');
                $checker   = User::getInstance($row->checked_out);
                $coName    = $checker ? $checker->get('name', $row->checked_out) : $row->checked_out;
            }
          @endphp
          <tr class="{{ $isPending ? 'bg-warning/10' : '' }}">
            <td class="column-check">
              @if(!$checkedOut)
                <input type="checkbox"
                       name="id[]"
                       id="cb{{ $i }}"
                       value="{{ $row->id }}"
                       class="checkbox checkbox-sm"
                       data-check-item />
                <label for="cb{{ $i }}" class="sr-only">{{ $row->id }}</label>
              @else
                <span class="badge badge-warning badge-sm"
                      title="{{ ($coName ?? '') }} — {{ $coDate ?? '' }} {{ $coTime ?? '' }}">
                  {{ Lang::txt('JLIB_HTML_CHECKED_OUT') }}
                </span>
              @endif
            </td>
            <td class="priority-3">{{ $row->id }}</td>
            <td>
              <a href="{!! $editUrl !!}" class="link link-hover text-primary font-medium">
                {{ $row->title }}
              </a>
              @if($checkedOut)
                <span class="badge badge-warning badge-sm ml-1">{{ Lang::txt('JLIB_HTML_CHECKED_OUT') }}</span>
              @endif
            </td>
            <td class="priority-4">{{ $row->version_label ?? '' }}</td>
            <td>
              <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
            </td>
            <td class="priority-2">
              @if(!empty($row->project_title))
                <a href="{!! $projectUrl !!}" class="link link-hover text-sm">
                  {{ \Hubzero\Utility\Str::truncate($row->project_title, 50) }}
                </a>
              @endif
            </td>
            <td class="priority-4">
              <a href="{!! $versUrl !!}" class="link link-hover text-sm">
                {{ $row->versions ?? 0 }}
              </a>
            </td>
            <td class="priority-4 text-sm">{{ $row->base ?? '' }}</td>
            <td class="priority-4 text-sm">{{ $row->cat_name ?? '' }}</td>
            <td class="priority-5 text-sm text-muted-foreground">{!! $modDate !!}</td>
          </tr>
        @empty
          <tr>
            <td colspan="10" class="text-center text-muted-foreground py-4">
              {{ Lang::txt('COM_PUBLICATIONS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  @php $__view->view('_statuskey')->display(); @endphp
</x-admin-form>
