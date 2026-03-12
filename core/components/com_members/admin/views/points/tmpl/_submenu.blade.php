{{--
  Points sub-navigation

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $currentTask = Request::getCmd('task', '');
@endphp
<nav role="navigation" class="sub sub-navigation">
  <ul>
    <li>
      <a @class(['active' => !$currentTask])
         href="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller, false) !!}">
        Summary
      </a>
    </li>
    <li>
      <a @class(['active' => $currentTask == 'edit'])
         href="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=edit', false) !!}">
        Look up User Balance
      </a>
    </li>
    <li>
      <a @class(['active' => $currentTask == 'config'])
         href="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=config', false) !!}">
        Configuration
      </a>
    </li>
    <li>
      <a @class(['active' => $currentTask == 'batch'])
         href="{!! Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=batch', false) !!}">
        Batch Transaction
      </a>
    </li>
  </ul>
</nav>
