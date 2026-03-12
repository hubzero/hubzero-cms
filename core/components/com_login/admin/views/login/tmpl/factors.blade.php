{{--
  Admin Login — Multi-factor authentication challenge

  Variables from controller:
    $factors — array of objects with ->html (rendered challenge HTML per factor)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Request;
  use Hubzero\Facades\Toolbar;

  Request::setVar('hidemainmenu', 1);
  Toolbar::title(Lang::txt('COM_LOGIN_FACTORS_VERIFICATION'));

  // Keep component CSS — factor UI styling
  $__view->css('factors');
@endphp

<div class="factors">
  @foreach($factors as $factor)
    <div class="factor-wrap">
      <div class="factor">
        {!! $factor->html !!}
      </div>
    </div>
  @endforeach
</div>
