{{--
  mod_youtube -- YouTube video embed

  Variables: $id, $lazy, $html, $params

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $ytClass = 'youtube_' . $params->get('layout')
      . ' ' . $params->get('moduleclass_sfx');
@endphp
<div id="youtube_feed_{{ $id }}" class="{{ trim($ytClass) }}">
  @if ($lazy)
    {{ Lang::txt('MOD_YOUTUBE_LOADING_FEED') }}
    <noscript>
      <div role="alert" class="alert alert-error">
        <span>{{ Lang::txt('MOD_YOUTUBE_ERROR_JAVASCRIPT_REQUIRED') }}</span>
      </div>
    </noscript>
  @else
    {!! $html !!}
  @endif
</div>
