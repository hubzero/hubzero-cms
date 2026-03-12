{{--
  Tool Zone Locations — Legacy sub-view (loaded inside zone edit iframe)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_LOCATIONS'), 'tools');
  Toolbar::addNew();
  Toolbar::spacer();
  Toolbar::apply();
  Toolbar::save();
  Toolbar::cancel();
@endphp

<x-admin-edit
    option="{{ $option }}"
    controller="{{ $controller }}"
>
  <div class="p-4 text-muted-foreground">—</div>
</x-admin-edit>
