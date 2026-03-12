{{--
  Resource Types — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $canDo     = \Components\Resources\Helpers\Permissions::getActions('type');
  $sort      = $filters['sort'] ?? 'type';
  $sortDir   = $filters['sort_Dir'] ?? 'asc';
  $protected = [1, 2, 3, 6, 7, 31];
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_RESOURCES') }}: {{ Lang::txt('COM_RESOURCES_TYPES') }}"
    icon="resources"
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
        <label for="filter-category" class="text-sm">{{ Lang::txt('COM_RESOURCES_FILTER_CATEGORY') }}:</label>
        {!! \Components\Resources\Helpers\Html::selectType(
            $cats,
            'category',
            $filters['category'] ?? '',
            'filter-category',
            Lang::txt('COM_RESOURCES_SELECT'),
            'class="select select-bordered select-sm" data-submit-on-change',
            '',
            ''
        ) !!}
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
            {!! Html::grid('sort', 'COM_RESOURCES_COL_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_RESOURCES_COL_TITLE', 'type', $sortDir, $sort) !!}
          </th>
          <th class="priority-3">
            {!! Html::grid('sort', 'COM_RESOURCES_COL_ALIAS', 'alias', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">
            {!! Html::grid('sort', 'COM_RESOURCES_COL_CATEGORY', 'category', $sortDir, $sort) !!}
          </th>
          <th class="priority-1">
            {!! Html::grid('sort', 'COM_RESOURCES_COL_PUBLISHED', 'state', $sortDir, $sort) !!}
          </th>
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
        @forelse($rows as $i => $row)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->get('id'), false
            );
            $isProtected = in_array($row->get('id'), $protected);
            $token = Session::getFormToken();

            if ($row->get('state') == 1) {
                $stateTask  = 'unpublish';
                $stateBadge = 'badge-success';
                $stateText  = Lang::txt('JPUBLISHED');
            } elseif ($row->get('state') == 2) {
                $stateTask  = 'publish';
                $stateBadge = 'badge-error';
                $stateText  = Lang::txt('JTRASHED');
            } else {
                $stateTask  = 'publish';
                $stateBadge = 'badge-ghost';
                $stateText  = Lang::txt('JUNPUBLISHED');
            }

            $catTitle = '';
            foreach ($cats as $cat) {
                if ($row->get('category') == $cat->get('id')) {
                    $catTitle = $cat->get('type');
                }
            }
          @endphp
          <tr>
            <td class="column-check">
              @if($canDo->get('core.edit') && !$isProtected)
                <input type="checkbox"
                       name="id[]"
                       id="cb{{ $i }}"
                       value="{{ $row->get('id') }}"
                       class="checkbox checkbox-sm"
                       data-check-item
                       aria-label="{{ Lang::txt('JGLOBAL_SELECT_AN_ITEM', $row->get('type')) }}" />
              @endif
            </td>
            <td class="priority-4">
              {{ $row->get('id') }}
            </td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}" class="link link-hover text-primary font-medium">
                  {{ $row->get('type') }}
                </a>
              @else
                {{ $row->get('type') }}
              @endif
            </td>
            <td class="priority-3">
              {{ $row->get('alias') }}
            </td>
            <td class="priority-2">
              {{ $catTitle }}
            </td>
            <td class="priority-1">
              @php
                $stateUrl = Route::url(
                    'index.php?option=' . $option
                    . '&controller=' . $controller
                    . '&task=' . $stateTask
                    . '&id=' . $row->get('id')
                    . '&' . $token . '=1', false
                );
              @endphp
              <a href="{{ $stateUrl }}">
                <span class="badge badge-sm {{ $stateBadge }}">{{ $stateText }}</span>
              </a>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-muted-foreground py-4">
              {{ Lang::txt('COM_RESOURCES_NO_RESULTS') }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</x-admin-form>
