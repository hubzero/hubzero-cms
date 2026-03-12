{{--
  Services — Admin list

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $canDo   = \Components\Services\Helpers\Permissions::getActions('service');
  $sortDir = $filters['sort_Dir'] ?? 'asc';
  $sort    = $filters['sort'] ?? 'id';
@endphp

<x-admin-toolbar
    title="{{ Lang::txt('COM_SERVICES') }}: {{ Lang::txt('COM_SERVICES_SERVICES') }}"
    icon="services"
    :canDo="$canDo"
    option="{{ $option }}"
/>

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <table class="admin-table">
    <thead>
      <tr>
        <th scope="col">
          <input type="checkbox"
                   class="checkbox checkbox-sm"
                   data-check-all
                   aria-label="{{ Lang::txt('JGLOBAL_CHECK_ALL') }}" />
        </th>
        <th scope="col">{{ Lang::txt('COM_SERVICES_COL_ID') }}</th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_SERVICES_COL_TITLE', 'title', $sortDir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_SERVICES_COL_CATEGORY', 'category', $sortDir, $sort) !!}
        </th>
        <th scope="col">
          {!! Html::grid('sort', 'COM_SERVICES_COL_STATUS', 'status', $sortDir, $sort) !!}
        </th>
      </tr>
    </thead>
    <tbody>
      @foreach ($rows as $i => $row)
        @php
          $editUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=edit&id=' . $row->id,
              false, false
          );
        @endphp
        <tr>
          <td>
            @if ($canDo->get('core.edit'))
              <input type="checkbox"
                     name="id[]"
                     id="cb{{ $i }}"
                     value="{{ $row->id }}"
                     class="checkbox checkbox-sm"
                     aria-label="{{ Lang::txt('JGRID_CHECKBOX_ROW_N', $i + 1) }}"
                     data-check-item />
            @endif
          </td>
          <td>{{ $row->id }}</td>
          <td>
            @if ($canDo->get('core.edit'))
              <a href="{{ $editUrl }}"
                 class="link link-hover text-primary font-medium">
                {{ $row->title }}
              </a>
            @else
              {{ $row->title }}
            @endif
          </td>
          <td>{{ $row->category }}</td>
          <td>
            @if ($row->status == 1)
              <span class="badge badge-success">
                {{ Lang::txt('COM_SERVICES_STATE_ACTIVE') }}
              </span>
            @else
              <span class="badge badge-ghost">
                {{ Lang::txt('COM_SERVICES_STATE_INACTIVE') }}
              </span>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
    <tfoot>
      <tr>
        <td colspan="5">{!! $rows->pagination !!}</td>
      </tr>
    </tfoot>
  </table>

  <input type="hidden" name="task" value="services" />
  <input type="hidden" name="boxchecked" value="0" />
  <input type="hidden" name="filter_order" value="{{ $sort }}" />
  <input type="hidden" name="filter_order_Dir" value="{{ $sortDir }}" />
</x-admin-form>
