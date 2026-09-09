{{--
  Events Calendar module — daisyUI layout.

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@if ($__module->getError())
  <div role="alert" class="alert alert-error">
    <span>{{ $__module->getError() }}</span>
  </div>
@else
  {!! $content !!}
@endif
