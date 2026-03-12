{{--
  Admin Login — default view
  Rendered inside login.php template wrapper (tmpl=login), not the admin chrome.
  Delegates entirely to the mod_adminlogin module + any other login-position modules.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Components\Login\Models\Login;

  $loginmodule = Login::getLoginModule('mod_adminlogin');
  $modules     = Module::byPosition('login');
@endphp

{!! Module::render($loginmodule, ['style' => 'none']) !!}

@foreach($modules as $module)
  @if($module->module !== 'mod_adminlogin')
    {!! Module::render($module, ['style' => 'none']) !!}
  @endif
@endforeach
