{{--
  Windows — Unconfigured / configuration required

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_WINDOWS'), 'tools');
  Toolbar::help('windows');
@endphp

@include('com_tools::admin.views.windows.tmpl._submenu')

<div class="alert alert-warning mt-4">
  {{ Lang::txt('COM_TOOLS_WINDOWS_CONFIGURATION_REQUIRED') }}
</div>
