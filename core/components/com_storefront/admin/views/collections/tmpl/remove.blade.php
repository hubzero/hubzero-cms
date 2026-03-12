{{--
  Storefront Collection — Delete confirmation

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_STOREFRONT') . ': Delete Collection', 'storefront');
  Toolbar::cancel();
@endphp

@php
  $formAction = Route::url(
      'index.php?option=' . $option
      . '&controller=' . $controller
      . '&task=' . $task . '&step=2',
      false, false
  );
@endphp
<form action="{{ $formAction }}" method="post" name="adminForm" id="adminForm">
  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th>{{ Lang::txt('Are you sure you want to delete all selected collections?') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <input type="checkbox"
                   name="delete"
                   value="delete"
                   id="field-delete"
                   class="checkbox checkbox-sm" />
            <label for="field-delete">{{ Lang::txt('I\'m positive. Go ahead and do the delete.') }}</label>
          </td>
        </tr>
        <tr>
          <td>
            <button type="submit" class="btn btn-sm btn-error">
              {{ Lang::txt('COM_STOREFRONT_NEXT') }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <input type="hidden" name="task" value="{{ $task }}" />
  @foreach($cId as $id)
    <input type="hidden" name="cId[]" value="{{ $id }}" />
  @endforeach
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />

  {!! Html::input('token') !!}
</form>
