{{--
  com_members — Plugin manage wrapper

  Variables: $html (string, rendered HTML from plugin event), $option, $controller

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Toolbar;

  Toolbar::title(Lang::txt('Members') . ': ' . Lang::txt('Plugins'), 'members');
@endphp

@if ($html)
  {!! $html !!}
@else
  @php
    Toolbar::cancel();
  @endphp

  <x-admin-edit
      option="{{ $option }}"
      controller="{{ $controller }}"
  >
    <div class="alert alert-warning">
      <p>{{ Lang::txt('No management interface found for this plugin.') }}</p>
    </div>

    <input type="hidden" name="action" value="" />
  </x-admin-edit>
@endif
