{{--
  Primary document launch button.

  Variables:
    $disabled — bool, whether the button is disabled
    $class    — string, additional CSS class
    $msg      — string, button label
    $href     — string, button URL
    $title    — string, button title attribute
    $action   — string, additional HTML attributes
    $pop      — string, popup content HTML

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
<div id="primary-document" class="mb-4">
  @if(!empty($disabled) && $disabled)
    <span class="btn btn-disabled {{ $class ?? '' }}">{{ $msg }}</span>
  @else
    <a class="btn btn-primary {{ $class ?? '' }}"
       @if(!empty($href)) href="{{ $href }}" @endif
       @if(!empty($title)) title="{{ e($title) }}" @endif
       {!! !empty($action) ? $action : '' !!}>
      {{ $msg }}
    </a>
  @endif
</div>

@if(!empty($pop))
  <div id="primary-document_pop" class="card bg-base-100 shadow-sm mb-4">
    <div class="card-body text-sm">
      {!! $pop !!}
    </div>
  </div>
@endif
