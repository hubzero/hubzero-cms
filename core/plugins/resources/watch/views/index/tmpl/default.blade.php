{{--
  Watch/subscribe button for resources.

  Variables (from plugin):
    $watched — object: watch record (->get('id') to check if watching)
    $link    — string: base URL for subscribe/unsubscribe actions

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Route;

  $__view->css();
@endphp

<div class="item-watch {{ $watched->get('id') ? 'watching' : '' }}">
  @if($watched->get('id'))
    <p>
      <a class="btn unsubscribe"
         href="{{ Route::url($link . '&action=unsubscribe') }}">{{ Lang::txt('PLG_RESOURCES_WATCH_UNSUBSCRIBE') }}</a>
    </p>
  @else
    <p>
      <a class="btn subscribe"
         href="{{ Route::url($link . '&action=subscribe') }}">{{ Lang::txt('PLG_RESOURCES_WATCH_SUBSCRIBE') }}</a>
    </p>
  @endif

  <p>{{ Lang::txt('PLG_RESOURCES_WATCH_EXPLAIN') }}</p>
</div>
