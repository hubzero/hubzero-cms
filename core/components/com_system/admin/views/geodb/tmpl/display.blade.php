{{--
  System — GeoIP Database configuration

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_SYSTEM_GEO_CONFIGURATION'), 'config');
  Toolbar::preferences($option, '550');
@endphp

<form action="{{ Route::url('index.php?option=' . $option . '&controller=' . $controller, false) }}"
      method="post"
      name="adminForm"
      id="item-form">

  <div class="max-w-2xl">
    <p class="text-muted-foreground">
      {{ Lang::txt('COM_SYSTEM_GEO_HELP') }}
    </p>
  </div>

  <input type="hidden" name="option" value="{{ $option }}" />
  <input type="hidden" name="controller" value="{{ $controller }}" />
  <input type="hidden" name="task" value="" />
</form>
