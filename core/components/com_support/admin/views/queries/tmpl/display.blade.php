{{--
  Support — Queries list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  $sort    = $filters['sort'] ?? 'id';
  $sortDir = $filters['sort_Dir'] ?? 'asc';

  Toolbar::title(
      Lang::txt('COM_SUPPORT_TICKET') . ': ' . Lang::txt('COM_SUPPORT_QUERIES'),
      'support'
  );
  if (User::authorise('core.admin', $option)) {
      Toolbar::custom('reset', 'refresh', 'refresh', 'COM_SUPPORT_RESET', false);
      Toolbar::spacer();
      Toolbar::addNew();
      Toolbar::editList();
      Toolbar::deleteList();
      Toolbar::spacer();
  }
  Toolbar::help('queries');
@endphp

<x-admin-form
    option="{{ $option }}"
    controller="{{ $controller }}"
    sort="{{ $sort }}"
    sortDir="{{ $sortDir }}"
>
  <p class="mb-4 text-sm text-muted-foreground">{!! Lang::txt('COM_SUPPORT_QUERY_INFO') !!}</p>

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
            {!! Html::grid('sort', 'COM_SUPPORT_COL_ID', 'id', $sortDir, $sort) !!}
          </th>
          <th>
            {!! Html::grid('sort', 'COM_SUPPORT_COL_TITLE', 'title', $sortDir, $sort) !!}
          </th>
          <th class="priority-2">
            {!! Html::grid('sort', 'COM_SUPPORT_COL_TYPE', 'iscore', $sortDir, $sort) !!}
          </th>
        </tr>
      </thead>
      <tfoot>
        <tr>
          <td colspan="4">
            <div class="admin-pagination">
              {!! $rows->pagination !!}
            </div>
          </td>
        </tr>
      </tfoot>
      <tbody>
        @foreach($rows as $i => $row)
          @php
            switch ($row->iscore) {
                case 4:
                    $iscore = Lang::txt('COM_SUPPORT_QUERY_TYPE_COMMON_NO_ACL');
                    break;
                case 2:
                    $iscore = Lang::txt('COM_SUPPORT_QUERY_TYPE_COMMON_ACL');
                    break;
                case 1:
                    $iscore = Lang::txt('COM_SUPPORT_QUERY_TYPE_MINE');
                    break;
                case 0:
                default:
                    $iscore = Lang::txt('COM_SUPPORT_QUERY_TYPE_CUSTOM');
                    break;
            }
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
                     data-check-item />
              <label for="cb{{ $i }}" class="sr-only">{{ $row->id }}</label>
            </td>
            <td class="priority-4 text-sm text-muted-foreground">{{ $row->id }}</td>
            <td>
              <a href="{{ $editUrl }}"
                 class="link link-hover text-primary font-medium">
                {{ $row->title }}
              </a>
            </td>
            <td class="priority-2 text-sm">
              <span class="badge badge-ghost">{{ $iscore }}</span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</x-admin-form>
