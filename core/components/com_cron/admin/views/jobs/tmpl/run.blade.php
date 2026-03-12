{{--
  Cron Jobs — Run output view

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_CRON') . ': ' . Lang::txt('COM_CRON_RUN'), 'cron');
@endphp

<form action="{{ Route::url('index.php?option=' . $option, false) }}"
      method="post" name="adminForm" id="adminForm">

  <div class="bg-base-100 rounded-box border border-base-300 overflow-x-auto">
    <table class="admin-table">
      <tbody>
        <tr>
          <td>
            <pre class="p-4 text-sm">{{ json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  {!! Html::input('token') !!}
</form>
