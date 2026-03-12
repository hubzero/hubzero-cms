{{--
  KB Articles — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo  = \Components\Kb\Admin\Helpers\Permissions::getActions('article');
  $access = Html::access('assetgroups');
  $sort    = $filters['sort'] ?? '';
  $sortDir = $filters['sort_Dir'] ?? 'asc';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_KB') }}: {{ Lang::txt('COM_KB_ARTICLES') }}"
    icon="kb"
    :canDo="$canDo"
    option="{{ $option }}"
/>

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
               placeholder="{{ Lang::txt('JSEARCH_FILTER') }}" />
        <button type="submit" class="btn btn-sm btn-primary">{{ Lang::txt('COM_KB_GO') }}</button>
        <button type="button"
                class="btn btn-sm btn-ghost"
                data-clear-search="filter_search">
          {{ Lang::txt('JSEARCH_FILTER_CLEAR') }}
        </button>
      @endslot

      <label for="filter-category" class="text-sm">{{ Lang::txt('COM_KB_CATEGORY') }}:</label>
      {!! \Components\Kb\Admin\Helpers\Html::categories(
          $categories,
          $filters['category'],
          'category',
          'filter-category',
          'class="select select-bordered select-sm" data-submit-on-change'
      ) !!}

      <label for="filter-access" class="text-sm">{{ Lang::txt('JFIELD_ACCESS_LABEL') }}:</label>
      <select name="access" id="filter-access"
              class="select select-bordered select-sm"
              data-submit-on-change>
        <option value="">{{ Lang::txt('JOPTION_SELECT_ACCESS') }}</option>
        {!! Html::select('options', $access, 'value', 'text', $filters['access']) !!}
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
          <th>{!! Html::grid('sort', 'COM_KB_TITLE', 'title', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_KB_PUBLISHED', 'state', $sortDir, $sort) !!}</th>
          <th>{!! Html::grid('sort', 'COM_KB_ACCESS', 'access', $sortDir, $sort) !!}</th>
          <th>{{ Lang::txt('COM_KB_CATEGORY') }}</th>
          <th>{{ Lang::txt('COM_KB_VOTES') }}</th>
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
            $state = (int) $row->get('state', 0);
            if ($state === 1) {
                $stateClass = 'badge-success';
                $stateText  = Lang::txt('JPUBLISHED');
                $stateTask  = 'unpublish';
            } elseif ($state === 2) {
                $stateClass = 'badge-warning';
                $stateText  = Lang::txt('JTRASHED');
                $stateTask  = 'publish';
            } else {
                $stateClass = 'badge-ghost';
                $stateText  = Lang::txt('JUNPUBLISHED');
                $stateTask  = 'publish';
            }

            $accessLevel = '';
            foreach ($access as $ac) {
                if ($row->get('access') == $ac->value) {
                    $accessLevel = $ac->text;
                    break;
                }
            }

            $categoryTitle = '';
            foreach ($categories as $cat) {
                if ($row->get('category') == $cat->get('id')) {
                    $categoryTitle = $cat->get('title');
                    break;
                }
            }
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
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit&id=' . $row->get('id'), false) }}"
                   class="link link-hover text-primary font-medium">
                  {{ $row->get('title') }}
                </a>
              @else
                {{ $row->get('title') }}
              @endif
            </td>
            <td class="column-status">
              @if($canDo->get('core.edit.state'))
                <a href="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=' . $stateTask . '&id=' . $row->get('id') . '&category=' . $filters['category'], false) }}"
                   title="{{ Lang::txt('COM_KB_SET_TASK', $stateTask) }}">
                  <span class="badge badge-sm {{ $stateClass }}">{{ $stateText }}</span>
                </a>
              @else
                <span class="badge badge-sm {{ $stateClass }}">{{ $stateText }}</span>
              @endif
            </td>
            <td>{{ $accessLevel }}</td>
            <td>{{ $categoryTitle }}</td>
            <td>
              <span class="text-success-dark">+{{ $row->get('helpful', 0) }}</span>
              <span class="text-error">-{{ $row->get('nothelpful', 0) }}</span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
