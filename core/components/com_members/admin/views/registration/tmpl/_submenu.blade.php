{{--
  Registration sub-navigation

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $currentController = Request::getCmd('controller', 'registration');
@endphp
<nav role="navigation" class="sub sub-navigation">
  <ul>
    <li>
      <a @class(['active' => $currentController == 'registration'])
         href="{!! Route::url('index.php?option=com_members&controller=registration', false) !!}">
        {{ Lang::txt('COM_MEMBERS_REGISTRATION_CONFIG') }}
      </a>
    </li>
    <li>
      <a @class(['active' => $currentController == 'incremental'])
         href="{!! Route::url('index.php?option=com_members&controller=incremental', false) !!}">
        {{ Lang::txt('COM_MEMBERS_INCREMENTAL') }}
      </a>
    </li>
    <li>
      <a @class(['active' => $currentController == 'premis'])
         href="{!! Route::url('index.php?option=com_members&controller=premis', false) !!}">
        {{ Lang::txt('COM_MEMBERS_PREMIS') }}
      </a>
    </li>
  </ul>
</nav>
