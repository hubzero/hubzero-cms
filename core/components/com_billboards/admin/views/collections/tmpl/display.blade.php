{{--
  Billboard Collections — Admin list view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;
  use Hubzero\Facades\User;

  Toolbar::title(
      Lang::txt('COM_BILLBOARDS_MANAGER') . ': ' . Lang::txt('COM_BILLBOARDS_COLLECTIONS'),
      'billboards'
  );
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
  Toolbar::help('collections');
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
        <th scope="col">{{ Lang::txt('COM_BILLBOARDS_COL_COLLECTION') }}</th>
      </tr>
    </thead>
    <tfoot>
      <tr>
        <td colspan="3">{!! $rows->pagination !!}</td>
      </tr>
    </tfoot>
    <tbody>
      @php $i = 0; @endphp
      @foreach($rows as $row)
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
            <input type="checkbox" name="id[]" id="cb{{ $i }}"
                   value="{{ $row->id }}" class="checkbox-toggle" />
            <label for="cb{{ $i }}" class="sr-only visually-hidden">
              {{ $row->id }}
            </label>
          </td>
          <td>{{ $row->id }}</td>
          <td>
            <a href="{{ $editUrl }}">{{ $row->name }}</a>
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
