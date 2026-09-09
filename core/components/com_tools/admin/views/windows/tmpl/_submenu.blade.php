{{--
  Windows submenu partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $currentTask = strtolower(Request::getCmd('task', ''));
  $baseUrl     = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);
  $sessionsUrl = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=sessions', false);
@endphp

<div class="tabs tabs-border mb-4">
  <a href="{{ $baseUrl }}"
     class="tab {{ !$currentTask ? 'tab-active' : '' }}">
    {{ Lang::txt('COM_TOOLS_WINDOWS_APPS') }}
  </a>
  <a href="{{ $sessionsUrl }}"
     class="tab {{ $currentTask === 'sessions' ? 'tab-active' : '' }}">
    {{ Lang::txt('COM_TOOLS_WINDOWS_SESSIONS') }}
  </a>
</div>
