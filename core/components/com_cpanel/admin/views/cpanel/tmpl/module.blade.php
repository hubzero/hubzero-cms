{{--
  com_cpanel single-module renderer

  Used by moduleTask() to render one named module (AJAX refresh).
  Request param: ?module=mod_foo

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $name = Request::getString('module');
  if ($name && substr($name, 0, 4) !== 'mod_') {
      $name = 'mod_' . $name;
  }
@endphp
@if($name && !User::isGuest())
  {!! Module::render(Module::byName($name), ['style' => 'none']) !!}
@endif
