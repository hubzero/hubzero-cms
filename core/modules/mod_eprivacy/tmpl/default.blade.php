{{--
  E-Privacy (cookie consent) module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div class="{{ $module->module }}" id="{{ $moduleid }}">
  <div role="alert" class="alert shadow-sm">
    <span>{!! $message !!}</span>
    <a class="btn btn-sm btn-ghost"
       href="{{ $uri }}"
       data-$duration="{{ $duration }}"
       $title="{{ Lang::txt('MOD_EPRIVACY_CLOSE_TITLE') }}">
      {{ Lang::txt('MOD_EPRIVACY_CLOSE') }}
    </a>
  </div>
</div>
