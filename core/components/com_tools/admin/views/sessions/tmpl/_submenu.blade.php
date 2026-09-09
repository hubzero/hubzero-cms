{{--
  Sessions — Sub-navigation (Active / Classes)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Request;

  $currentTask = strtolower(Request::getCmd('task', ''));
  $baseUrl     = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);
  $classesUrl  = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=classes', false);
@endphp

<nav role="navigation" class="sub sub-navigation mb-4">
  <div role="tablist" class="tabs tabs-border">
    <a role="tab"
       href="{{ $baseUrl }}"
       class="tab {{ !$currentTask ? 'tab-active' : '' }}">
      {{ Lang::txt('COM_TOOLS_SESSION_ACTIVE') }}
    </a>
    <a role="tab"
       href="{{ $classesUrl }}"
       class="tab {{ $currentTask == 'classes' ? 'tab-active' : '' }}">
      {{ Lang::txt('COM_TOOLS_SESSION_CLASSES') }}
    </a>
  </div>
</nav>
