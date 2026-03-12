{{--
  Meta Remove — Admin delete confirmation

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_STOREFRONT') . ': Meta', 'storefront');
  Toolbar::cancel();

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
          <th>{{ Lang::txt('COM_KB_CHOOSE_DELETE_OPTION') }}</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" name="action" id="action_delete"
                     value="deletefaqs" checked
                     class="radio radio-sm" />
              {{ Lang::txt('COM_KB_DELETE_ALL') }}
            </label>
          </td>
        </tr>
        <tr>
          <td>
            <label class="flex items-center gap-2 cursor-pointer">
              <input type="radio" name="action" id="action_remove"
                     value="removefaqs"
                     class="radio radio-sm" />
              {{ Lang::txt('COM_KB_DELETE_ONLY_CATEGORY') }}
            </label>
          </td>
        </tr>
        <tr>
          <td>
            <button type="submit" class="btn btn-sm btn-error">
              {{ Lang::txt('COM_KB_NEXT') }}
            </button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <input type="hidden" name="task" value="{{ $task }}" />
  <input type="hidden" name="id" value="{{ $id }}" />
  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  {!! Html::input('token') !!}
</form>
