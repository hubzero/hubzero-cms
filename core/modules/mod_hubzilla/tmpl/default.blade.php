{{--
  mod_hubzilla -- Easter egg (Hubzilla mascot)

  Variables: $params

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $reveal = strtolower(Request::getWord('reveal', ''));
  $base   = rtrim(Request::base(true), '/');
@endphp
<div id="hubzilla" @if ($reveal === 'eastereggs') class="revealed" @endif
     $style="position:fixed;top:{{ $params->get('posTop', 'auto') }};right:{{ $params->get('posRight', '5px') }};bottom:{{ $params->get('posBottom', '5px') }};left:{{ $params->get('posLeft', 'auto') }};">
  <audio preload="auto" id="hubzilla-roar">
    <source src="{{ $base }}/core/modules/mod_hubzilla/assets/sounds/roar.ogg" type="audio/ogg" />
    <source src="{{ $base }}/core/modules/mod_hubzilla/assets/sounds/roar.mp3" type="audio/mp3" />
  </audio>
</div>
