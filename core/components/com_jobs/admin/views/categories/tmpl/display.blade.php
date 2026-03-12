{{--
  Job Categories — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $canDo   = \Components\Jobs\Helpers\Permissions::getActions('category');
  $sort    = $filters['sort'] ?? 'ordernum';
  $sortDir = $filters['sort_Dir'] ?? 'ASC';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_JOBS') }}: {{ Lang::txt('COM_JOBS_CATEGORIES') }}"
    icon="category"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
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
          <th class="w-16 priority-2">
            {!! Html::grid('sort', 'COM_JOBS_COL_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th class="w-24">
            {!! Html::grid('sort', 'COM_JOBS_COL_ORDER', 'ordernum', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_JOBS_COL_TITLE', 'category', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="4">
            <div class="admin-pagination">
              {!! $__view->pagination($total, $filters['start'], $filters['limit']) !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            $editUrl = Route::url(
                'index.php?option=' . $option
                . '&controller=' . $controller
                . '&task=edit&id=' . $row->id,
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
                     aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
                     data-check-item />
            </td>
            <td class="priority-2">
              {{ $row->id }}
            </td>
            <td>
              <input type="text"
                     name="order[{{ $row->id }}]"
                     class="input input-bordered input-sm w-16 text-center"
                     aria-label="{{ Lang::txt('COM_JOBS_COL_ORDER') }}"
                     value="{{ $row->ordernum }}" />
            </td>
            <td>
              @if($canDo->get('core.edit'))
                <a href="{{ $editUrl }}"
                   class="link link-hover text-primary font-medium">
                  {{ $row->category }}
                </a>
              @else
                {{ $row->category }}
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
