{{--
  Support — Abuse Reports sub-navigation partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Route;

  $currentTask  = Request::getCmd('task');
  $reportsUrl   = Route::url('index.php?option=' . $option . '&controller=' . $controller, false);
  $checkUrl     = Route::url('index.php?option=' . $option . '&controller=' . $controller . '&task=check', false);
  $reportsClass = ($currentTask != 'check') ? ' class="active"' : '';
  $checkClass   = ($currentTask == 'check') ? ' class="active"' : '';
@endphp

<div role="navigation" class="tabs tabs-border mb-4">
    <a href="{{ $reportsUrl }}"
       class="tab {{ $currentTask != 'check' ? 'tab-active' : '' }}">
        {{ Lang::txt('COM_SUPPORT_ABUSE_REPORTS') }}
    </a>
    <a href="{{ $checkUrl }}"
       class="tab {{ $currentTask == 'check' ? 'tab-active' : '' }}">
        {{ Lang::txt('COM_SUPPORT_ABUSE_CHECK') }}
    </a>
</div>
