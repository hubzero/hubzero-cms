{{--
  mod_twitterfeed -- Twitter/X timeline embed

  Variables: $params, $module

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $count = $params->get('tweetcount', 5);
  if (!is_numeric($count) || $count > 20 || $count < 1) {
      $count = 5;
  }

  $screenName = ltrim($params->get('twitterID'), '@');

  $widgetSettings  = '';
  $widgetSettings .= ($params->get('displayHeader') == 'no') ? ' noheader' : '';
  $widgetSettings .= ($params->get('displayFooter') == 'no') ? ' nofooter' : '';
  $widgetSettings .= ($params->get('displayBorders') == 'no') ? ' noborders' : '';
@endphp
<div class="{{ $module->module }} {{ $params->get('moduleclass_sfx', '') }}">
  @if ($params->get('moduleTitle', '') != '')
    <h3>{{ $params->get('moduleTitle') }}</h3>
  @endif

  <a class="twitter-timeline"
     href="https://twitter.com/{{ $screenName }}"
     data-tweet-$limit="{{ $count }}"
     data-chrome="{{ trim($widgetSettings) }}">
    {{ Lang::txt('MOD_TWITTERFEED_LOADING') }}
  </a>
  <script async src="https://platform.twitter.com/widgets.js"></script>
</div>
