{{--
  Login-required notice for group pages.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

<div class="group-login">
  <h2>{{ Lang::txt('COM_GROUPS_VIEW_LOGIN') }}</h2>
  {!! \Hubzero\Module\Helper::renderModule('mod_login') !!}
</div>
