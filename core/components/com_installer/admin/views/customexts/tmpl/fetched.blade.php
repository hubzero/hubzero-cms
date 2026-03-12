{{--
  com_installer customexts fetched — Results of fetching from upstream repos

  Variables: $success, $failed, $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  $canDo = \Components\Installer\Admin\Helpers\Installer::getActions();

  Toolbar::title(Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADER_CUSTOMEXTS'), 'customexts');
  Toolbar::custom('default', 'back', 'back', 'COM_INSTALLER_CUSTOMEXTS_BACK', false);
  Toolbar::spacer();
  Toolbar::custom('doupdate', 'merge', '', 'COM_INSTALLER_CUSTOMEXTS_MERGE_CODE', false);
@endphp

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
      method="post" name="adminForm" id="adminForm">

  @if (!empty($success))
    <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto mb-6">
      <table class="admin-table">
        <thead>
          <tr>
            <th>{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_FETCH_SUCCESS') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($success as $item)
            @php
              $cloneTxt    = Lang::txt('COM_INSTALLER_CUSTOMEXTS_CLONE_SUCCESSFUL');
              $upToDateTxt = Lang::txt('COM_INSTALLER_CUSTOMEXTS_FETCH_CODE_UP_TO_DATE');
              $firstMsg    = $item['message'][0];
              $showMerge   = ($firstMsg !== $cloneTxt)
                  && ($firstMsg !== $upToDateTxt)
                  && !preg_match('/ineligible/', $firstMsg);
            @endphp
            <tr>
              <td class="py-3">
                <strong>Extension: {{ $item['extension'] }}</strong>
                @if ($showMerge)
                  <p class="text-sm mt-1 text-muted-foreground">{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_FETCH_SUCCESS_DESC') }}</p>
                @endif
                <hr class="my-2 border-base-300">
                <code class="text-xs block whitespace-pre-wrap">{!! implode('<br>', $item['message']) !!}</code>
                @if ($showMerge)
                  <label class="flex items-center gap-2 mt-3 cursor-pointer">
                    <input type="checkbox"
                           name="id[]"
                           value="{{ $item['ext_id'] }}"
                           checked
                           class="checkbox checkbox-sm" />
                    <span class="text-sm font-medium">{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_MERGE') }}</span>
                  </label>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

  @if (!empty($failed))
    <div class="bg-base-100 rounded-box border border-error/30 overflow-x-auto">
      <table class="admin-table">
        <thead>
          <tr>
            <th class="text-error">{{ Lang::txt('COM_INSTALLER_CUSTOMEXTS_FETCH_FAIL') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($failed as $item)
            @php
              $msgs = is_array($item['message']) ? $item['message'] : [$item['message']];
            @endphp
            <tr>
              <td class="py-3">
                <strong>Extension: {{ $item['extension'] }}</strong>
                <hr class="my-2 border-base-300">
                <pre class="text-xs bg-base-200 p-2 rounded">{!! implode("\n", $msgs) !!}</pre>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="doupdate" />
  <input type="hidden" name="boxchecked" value="0" />
  {!! Html::input('token') !!}
</form>
