{{--
  Usage metadata — displays user count stats.

  Variables (from plugin):
    $resource — object: resource model
    $url      — string: link to usage tab
    $stats    — object: usage stats (->users)
    $clusters — object: cluster stats (->users, ->classes)

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

<p class="usage">
  @if($resource->type == 7)
    <a href="{{ $url }}">{{ Lang::txt('PLG_RESOURCES_USAGE_NUM_USERS_DETAILED', $stats->users) }}</a>
  @elseif($stats->users)
    {{ Lang::txt('PLG_RESOURCES_USAGE_NUM_USERS', $stats->users) }}
  @endif
</p>

@if($clusters->users && $clusters->classes)
  <p class="usage">
    {{ Lang::txt('PLG_RESOURCES_USAGE_NUM_USERS_IN_CLASSES', $clusters->users, $clusters->classes) }}
  </p>
@endif
