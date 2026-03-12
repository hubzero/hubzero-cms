{{--
  Publications Licenses — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  $canDo  = \Components\Publications\Helpers\Permissions::getActions('license');
  $sort   = $filters['sort'] ?? 'ordering';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  $formUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);

  Toolbar::title(
      Lang::txt('COM_PUBLICATIONS_PUBLICATIONS') . ': ' . Lang::txt('COM_PUBLICATIONS_LICENSES'),
      'publications'
  );
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.edit.state')) {
      Toolbar::save('makedefault', 'COM_PUBLICATIONS_MAKE_DEFAULT');
      Toolbar::publishList('changestatus', 'COM_PUBLICATIONS_PUBLISH_UNPUBLISH');
  }
  if ($canDo->get('core.delete')) {
      Toolbar::spacer();
      Toolbar::deleteList();
  }

  $__view->css();
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
               value="{{ $filters['search'] ?? '' }}"
               placeholder="{{ Lang::txt('COM_PUBLICATIONS_SEARCH') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_PUBLICATIONS_GO') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
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
          <th class="priority-4">
            {!! Html::grid('sort', 'COM_PUBLICATIONS_FIELD_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_PUBLICATIONS_FIELD_NAME', 'name', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_PUBLICATIONS_FIELD_TITLE', 'title', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">
            {!! Html::grid('sort', 'COM_PUBLICATIONS_FIELD_STATUS', 'active', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">{{ Lang::txt('COM_PUBLICATIONS_FIELD_DEFAULT') }}</th>
          <th>
            {!! Html::grid('sort', 'COM_PUBLICATIONS_FIELD_ORDER', 'ordering', $sortDir, $sort) !!}
          </th>
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
        @php
          $i = 0;
          $n = $rows->count();
          $orderings = $rows->fieldsByKey('ordering');
        @endphp
        @forelse($rows as $row)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->id, false
            );
            $activeClass = $row->active == 1 ? 'badge-success' : 'badge-ghost';
            $activeLabel = $row->active == 1 ? Lang::txt('jon') : Lang::txt('joff');
          @endphp
          <tr>
            <td class="column-check">
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->id }}"
                     class="checkbox checkbox-sm"
                     data-check-item />
              <label for="cb{{ $i }}" class="sr-only">{{ $row->id }}</label>
            </td>
            <td class="priority-4">{{ $row->id }}</td>
            <td class="priority-3">
              <a href="{!! $editUrl !!}" class="link link-hover text-primary font-medium">
                {{ $row->name }}
              </a>
            </td>
            <td>
              <a href="{!! $editUrl !!}" class="link link-hover text-primary">
                {{ $row->title }}
              </a>
            </td>
            <td class="priority-2 text-center">
              <span class="badge {{ $activeClass }}">{{ $activeLabel }}</span>
            </td>
            <td class="priority-2 text-center">
              @if($row->main == 1)
                <span class="badge badge-info">{{ Lang::txt('JYES') }}</span>
              @endif
            </td>
            <td class="order">
              @if($sort == 'ordering')
                <span>
                  @if($i > 0)
                    {!! Html::grid('orderUp', $i, 'orderup', '', 'JLIB_HTML_MOVE_UP', true, 'cb') !!}
                  @else
                    &#160;
                  @endif
                </span>
                <span>
                  @if($i < ($n - 1))
                    {!! Html::grid('orderDown', $i, 'orderdown', '', 'JLIB_HTML_MOVE_DOWN', true, 'cb') !!}
                  @else
                    &#160;
                  @endif
                </span>
              @endif
              <input type="text"
                     name="order[]"
                     size="5"
                     value="{{ $row->ordering }}"
                     disabled="disabled"
                     aria-label="{{ Lang::txt('JGRID_HEADING_ORDERING') }}"
                     class="input input-bordered input-xs w-12" />
            </td>
          </tr>
          @php $i++; @endphp
        @empty
          <tr>
            <td colspan="7" class="text-center text-muted-foreground py-4">
              {{ Lang::txt('COM_PUBLICATIONS_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
