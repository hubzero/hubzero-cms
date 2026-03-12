{{--
  Billboards — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Session;
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  Toolbar::title(Lang::txt('COM_BILLBOARDS_MANAGER') . ': ' . Lang::txt('COM_BILLBOARDS'), 'billboards');
  if (User::authorise('core.admin', $option)) {
      Toolbar::preferences($option);
      Toolbar::spacer();
  }
  if (User::authorise('core.edit.state', $option)) {
      Toolbar::publishList();
      Toolbar::unpublishList();
      Toolbar::spacer();
  }
  if (User::authorise('core.create', $option)) {
      Toolbar::addNew();
  }
  if (User::authorise('core.edit', $option)) {
      Toolbar::editList();
      Toolbar::spacer();
  }
  if (User::authorise('core.delete', $option)) {
      Toolbar::deleteList(Lang::txt('COM_BILLBOARDS_CONFIRM_DELETE'));
  }
  Toolbar::spacer();
  Toolbar::help('billboards');

@endphp

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
      method="post"
      name="adminForm"
      id="adminForm">

  <table class="admin-table">
    <thead>
      <tr>
        <th class="admin-table-col-check">
          <input type="checkbox" name="toggle" id="checkall-toggle"
                 value="" class="checkbox-toggle toggle-all" />
          <label for="checkall-toggle" class="sr-only visually-hidden">
            {{ Lang::txt('JGLOBAL_CHECK_ALL') }}
          </label>
        </th>
        <th scope="col">{{ Lang::txt('COM_BILLBOARDS_COL_ID') }}</th>
        <th scope="col">{{ Lang::txt('COM_BILLBOARDS_COL_NAME') }}</th>
        <th scope="col">{{ Lang::txt('COM_BILLBOARDS_COL_COLLECTION') }}</th>
        <th scope="col">
          {{ Lang::txt('COM_BILLBOARDS_COL_ORDERING') }}
          {!! Html::grid('order', $rows->toArray()) !!}
        </th>
        <th scope="col">{{ Lang::txt('COM_BILLBOARDS_COL_PUBLISHED') }}</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="6">{!! $rows->pagination !!}</td>
      </tr>
    </tfoot>
    <tbody>
      @php $i = 0; @endphp
      @foreach($rows as $row)
        @php
          $checkedOut = $row->checked_out
              || ($row->checked_out_time && $row->checked_out_time != '0000-00-00 00:00:00');

          if ($checkedOut) {
              $checkedOutName = User::getInstance($row->checked_out)->get('name');
              $checkbox = Html::grid('checkedout', $row, $checkedOutName, $row->checked_out_time);
          } else {
              $checkbox = Html::grid('id', $i, $row->id, false, 'cid');
          }

          $task  = $row->published ? 'unpublish' : 'publish';
          $alt   = $row->published ? Lang::txt('JPUBLISHED') : Lang::txt('JUNPUBLISHED');
          $badge = $row->published ? 'badge-success' : 'badge-error';
          $editUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=edit&cid=' . $row->id,
              false, false
          );
          $stateUrl = Route::url(
              'index.php?option=' . $option
              . '&controller=' . $controller
              . '&task=' . $task
              . '&cid=' . $row->id
              . '&' . Session::getFormToken() . '=1',
              false, false
          );
        @endphp
        <tr>
          <td>{!! $checkbox !!}</td>
          <td>{{ $row->id }}</td>
          <td>
            @if($checkedOut)
              {{ $row->name }}
            @else
              <a href="{{ $editUrl }}">{{ $row->name }}</a>
            @endif
          </td>
          <td>{{ $row->collection->name ?? '' }}</td>
          <td>
            <input type="text" name="order[]" size="5"
                   value="{{ $row->ordering }}"
                   aria-label="{{ Lang::txt('COM_BILLBOARDS_COL_ORDERING') }}"
                   class="input input-bordered input-xs w-16 text-center" />
          </td>
          <td>
            <a class="badge {{ $badge }} gap-1"
               href="{{ $stateUrl }}"
               title="{{ Lang::txt('COM_BILLBOARDS_SET_TO', $task) }}">
              {{ $alt }}
            </a>
          </td>
        </tr>
        @php $i++; @endphp
      @endforeach
    </tbody>
  </table>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" autocomplete="off" />
  <input type="hidden" name="boxchecked" value="0" />

  {!! Html::input('token') !!}
</form>
