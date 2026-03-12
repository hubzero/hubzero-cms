{{--
  Tags — Admin list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $canDo   = \Components\Tags\Helpers\Permissions::getActions();
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $sort    = $filters['sort'] ?? 'id';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_TAGS') }}"
    icon="tags"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  @slot('filters')
    <x-admin-filters>
      @slot('search')
        <label for="filter_search" class="sr-only">{{ Lang::txt('JSEARCH_FILTER') }}</label>
        <input type="text"
               name="search"
               id="filter_search"
               class="input input-bordered input-sm"
               placeholder="{{ Lang::txt('COM_TAGS_SEARCH_PLACEHOLDER') }}"
               value="{{ $filters['search'] ?? '' }}"
               data-submit-on-change />
      @endslot

      <label for="filter-by" class="sr-only">{{ Lang::txt('COM_TAGS_FILTER_ALL_TAGS') }}</label>
      <select name="filterby"
              id="filter-by"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="all"
                @selected(($filters['by'] ?? '') == 'all')>
          {{ Lang::txt('COM_TAGS_FILTER_ALL_TAGS') }}
        </option>
        <option value="user"
                @selected(($filters['by'] ?? '') == 'user')>
          {{ Lang::txt('COM_TAGS_FILTER_USER_TAGS') }}
        </option>
        <option value="admin"
                @selected(($filters['by'] ?? '') == 'admin')>
          {{ Lang::txt('COM_TAGS_FILTER_ADMIN_TAGS') }}
        </option>
      </select>
    </x-admin-filters>
  @endslot

  <table class="admin-table">
    <thead>
      <tr>
        <th scope="col">
          <input type="checkbox" data-check-all aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_TAGS_COL_RAW_TAG', 'raw_tag', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-2">
          {!! Html::grid('sort', 'COM_TAGS_COL_TAG', 'tag', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-2">
          {!! Html::grid('sort', 'COM_TAGS_COL_TYPE', 'admin', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-3">
          {!! Html::grid('sort', 'COM_TAGS_COL_NUMBER_TAGGED', 'objects', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-3">
          {!! Html::grid('sort', 'COM_TAGS_COL_ALIAS', 'substitutes', $sortDir, $sort) !!}
        </th>
        <th scope="col" class="priority-4">
          {!! Html::grid('sort', 'COM_TAGS_COL_CREATED', 'created', $sortDir, $sort) !!}
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $i => $row)
        @php
          $rowId = $row->get('id');
          $editUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=edit&id=' . $rowId,
              false, false
          );
          $taggedUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=tagged&tag=' . $rowId,
              false, false
          );
          $typeLabel = ['User', 'Admin', 'Core'][$row->get('admin')] ?? 'User';
          $created = $row->created();
        @endphp
        <tr>
          <td>
            @if ($canDo->get('core.edit'))
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $rowId }}"
                     aria-label="{{ $row->get('raw_tag') }}"
                     data-check-item />
            @endif
          </td>
          <td>
            @if ($canDo->get('core.edit'))
              <a href="{{ $editUrl }}">
                {{ $row->get('raw_tag') }}
              </a>
            @else
              {{ $row->get('raw_tag') }}
            @endif
          </td>
          <td class="priority-2">
            @if ($canDo->get('core.edit'))
              <a href="{{ $editUrl }}">
                {{ $row->get('tag') }}
              </a>
            @else
              {{ $row->get('tag') }}
            @endif
          </td>
          <td class="priority-2">{{ $typeLabel }}</td>
          <td class="priority-3">
            <a href="{{ $taggedUrl }}">{{ $row->get('objects', 0) }}</a>
          </td>
          <td class="priority-3">{{ $row->get('substitutes', 0) }}</td>
          <td class="priority-4">
            <time datetime="{{ $created }}">
              {{ ($created && $created != '0000-00-00 00:00:00')
                  ? $created : Lang::txt('COM_TAGS_UNKNOWN') }}
            </time>
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="7">{!! $rows->pagination !!}</td>
      </tr>
    </tfoot>
  </table>

  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="{{ $sort }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $sortDir }}" />
</x-admin-form>
