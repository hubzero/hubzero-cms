{{--
  mod_collect -- collection/bookmark button

  Variables: $params

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $url  = urldecode(Request::path());
  $url  = implode('/', array_map('rawurlencode', explode('/', $url)));
  $url .= (strstr($url, '?') ? '&' : '?') . 'tryto=collect';
@endphp
<p class="collector"{!! $params->get('id') ? ' id="' . e($params->get('id')) . '"' : '' !!}>
  <a class="btn btn-sm btn-outline gap-2 collect-this"
     href="{{ htmlspecialchars($url) }}">
    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 20 20" fill="currentColor">
      <path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"/>
    </svg>
    {{ Lang::txt('MOD_COLLECT_ACTION') }}
  </a>
</p>
