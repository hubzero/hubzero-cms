{{--
  Login/Logout dispatcher.

  Loads the login sub-template for guests or the logout sub-template
  for authenticated users.

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\User;
@endphp

@if(User::isGuest())
  {!! $__view->loadTemplate('login') !!}
@else
  {!! $__view->loadTemplate('logout') !!}
@endif
