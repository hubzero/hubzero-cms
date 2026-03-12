{{--
  Items — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Collections\Helpers\Permissions::getActions('post');

  $sort    = $filters['sort'] ?? 'id';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(
      Lang::txt('COM_COLLECTIONS') . ': ' . Lang::txt('COM_COLLECTIONS_ITEMS'),
      'collections'
  );
  if ($canDo->get('core.create')) {
      Toolbar::addNew();
  }
  if ($canDo->get('core.edit')) {
      Toolbar::editList();
  }
  if ($canDo->get('core.delete')) {
      Toolbar::deleteList();
  }
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

      <label for="filter-type" class="text-sm">{{ Lang::txt('COM_COLLECTIONS_FILTER_TYPE') }}:</label>
      <select name="type" id="filter-type"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="">{{ Lang::txt('COM_COLLECTIONS_FILTER_TYPE_ALL') }}</option>
        @foreach($types as $type)
          <option value="{{ $type->get('type') }}"
                  @selected($type->get('type') == $filters['type'])>
            {{ $type->get('type') }}
          </option>
        @endforeach
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
          <th>{!! Html::grid('sort', 'COM_COLLECTIONS_COL_DESCRIPTION', 'description', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_COLLECTIONS_COL_CREATED', 'created', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_COLLECTIONS_COL_CREATEDBY', 'created_by', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_COLLECTIONS_COL_TYPE', 'type', $sortDir, $sort) !!}</th>
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
            $content = \Hubzero\Utility\Str::truncate(
                strip_tags($row->get('description')),
                75
            );
            $content = $content ?: Lang::txt('COM_COLLECTIONS_NONE');
            $creatorName = $row->creator->get(
                'name',
                Lang::txt('COM_COLLECTIONS_UNKNOWN')
            );
          @endphp
          <tr>
            <td class="column-check">
              @if($canDo->get('core.edit'))
                <input type="checkbox"
                       name="id[]"
                       id="cb{{ $i }}"
                       value="{{ $row->get('id') }}"
                       class="checkbox checkbox-sm"
                       aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
                       data-check-item />
              @endif
            </td>
            <td>{{ $row->get('id') }}</td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}"
                   class="link link-hover text-primary font-medium">
                  {{ $content }}
                </a>
              @else
                {{ $content }}
              @endif
            </td>
            <td>
              <time datetime="{{ $row->get('created') }}">
                {{ $row->get('created') }}
              </time>
            </td>
            <td>{{ $creatorName }}</td>
            <td>{{ $row->get('type') }}</td>
            <td>{{ $row->posts()->total() }}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
