{{--
  Quotas sub-navigation

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $currentTask = strtolower(Request::getCmd('task', ''));
@endphp
<nav role="navigation" class="sub sub-navigation">
  <ul>
    <li>
      <a @class(['active' => !$currentTask])
         href="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller, false) !!}">
        {{ Lang::txt('COM_MEMBERS_QUOTAS') }}
      </a>
    </li>
    <li>
      <a @class(['active' => $currentTask == 'displayclasses'])
         href="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=displayClasses', false) !!}">
        {{ Lang::txt('COM_MEMBERS_QUOTA_CLASSES') }}
      </a>
    </li>
    <li>
      <a @class(['active' => $currentTask == 'import'])
         href="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=import', false) !!}">
        {{ Lang::txt('COM_MEMBERS_QUOTAS_IMPORT') }}
      </a>
    </li>
  </ul>
</nav>
