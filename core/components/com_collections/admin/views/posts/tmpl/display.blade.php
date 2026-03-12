{{--
  Posts — Admin list view

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
      Lang::txt('COM_COLLECTIONS') . ': ' . Lang::txt('COM_COLLECTIONS_POSTS'),
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

  $colSpan = $filters['collection_id'] ? 7 : 8;
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
    </x-admin-filters>
  @endslot

  <input type="hidden" name="collection_id" value="{{ $filters['collection_id'] }}" />

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        @if($filters['collection_id'])
          @php
            $collection = \Components\Collections\Models\Orm\Collection::oneOrFail(
                $filters['collection_id']
            );
          @endphp
          <tr>
            <th colspan="{{ $colSpan }}">
              ({{ $collection->get('alias') }})
              {{ $collection->get('title') }}
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
          <th>{!! Html::grid('sort', 'COM_COLLECTIONS_COL_ID', 'id', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_COLLECTIONS_COL_DESCRIPTION', 'description', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_COLLECTIONS_COL_POSTED', 'created', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_COLLECTIONS_COL_POSTEDBY', 'created_by', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_COLLECTIONS_COL_ITEM_ID', 'item_id', $sortDir, $sort) !!}</th>
          @if(!$filters['collection_id'])
            <th>{!! Html::grid('sort', 'COM_COLLECTIONS_COL_COLLECTION_ID', 'collection_id', $sortDir, $sort) !!}</th>
          @endif
          <th>{!! Html::grid('sort', 'COM_COLLECTIONS_COL_ORIGINAL', 'original', $sortDir, $sort) !!}</th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="{{ $colSpan }}">
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
            $creatorName = $row->creator->get(
                'name',
                Lang::txt('COM_COLLECTIONS_UNKNOWN')
            );
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
                <a href="{{ $editUrl }}"
                   class="link link-hover text-primary font-medium">
                  {{ Lang::txt('COM_COLLECTIONS_NONE') }}
                </a>
              @else
                {{ Lang::txt('COM_COLLECTIONS_NONE') }}
              @endif
            </td>
            <td>
              <time datetime="{{ $row->get('created') }}">
                {{ $row->get('created') }}
              </time>
            </td>
            <td>{{ $creatorName }}</td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=items&task=edit&id=' . $row->get('item_id'), false) }}"
                   class="link link-hover text-primary">
                  {{ $row->get('item_id') }}
                </a>
              @else
                {{ $row->get('item_id') }}
              @endif
            </td>
            @if(!$filters['collection_id'])
              <td>
                @if($canDo->get('core.edit'))
                  <a href="{{ Route::url('index.php?option=' . $option . '&controller=collections&task=edit&id=' . $row->get('collection_id'), false) }}"
                     class="link link-hover text-primary">
                    {{ $row->get('collection_id') }}
                  </a>
                @else
                  {{ $row->get('collection_id') }}
                @endif
              </td>
            @endif
            <td>
              @if($row->get('original'))
                <span class="badge badge-sm badge-success">{{ Lang::txt('COM_COLLECTIONS_IS_ORIGINAL') }}</span>
              @else
                <span class="badge badge-sm badge-ghost">{{ Lang::txt('COM_COLLECTIONS_IS_NOT_ORIGINAL') }}</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
