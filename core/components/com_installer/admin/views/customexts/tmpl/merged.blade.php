{{--
  com_installer customexts merged — Results of merging/pulling code

  Variables: $msg, $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_INSTALLER'));
  Toolbar::custom('display', 'back', 'back', 'COM_INSTALLER_CUSTOMEXTS_BACK', false);
@endphp

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
      method="post" name="adminForm" id="adminForm">

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th>{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_PULL_SUCCESS') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach ($msg as $item)
          <tr>
            <td class="py-3">
              <strong>Extension: {{ $item['extension'] }}</strong>
              <pre class="text-xs bg-base-200 p-2 rounded mt-2">{{ $item['message'] }}</pre>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" />
  <input type="hidden" name="boxchecked" value="0" />
  {!! Html::input('token') !!}
</form>
