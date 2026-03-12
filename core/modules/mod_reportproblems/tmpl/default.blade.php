{{--
  Report Problems module — Blade layout.

  Renders a container with the support form URL so the page shell
  (or any other consumer) can open it.  When used standalone, the
  accompanying blade.js opens the form in a native <dialog>.

  Variables: $module, $params, $referrer, $verified,
             $guestOrTmpAccount, $os, $os_version, $browser,
             $browser_ver, $supportParams, $allowed

  @package    hubzero-cms
  @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $__module->css()
           ->js();

  $trigger = $params->get('trigger', '#tab');
  $formUrl = Route::url(
      'index.php?option=com_support&task=new&tmpl=component&referrer=' . $referrer,
      false
  );
@endphp
<div id="help-pane"
     data-form="{{ $formUrl }}"
     data-trigger="{{ $trigger }}">
</div>
