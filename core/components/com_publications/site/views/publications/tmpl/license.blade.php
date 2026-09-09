{{--
  Publication license text popup.

  Variables from controller:
    $title       — page title
    $publication — publication model

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  $error = $__view->getError();

  $text = $publication->version->get('license_text')
      ? $publication->version->get('license_text')
      : $publication->license()->text;
  $text = preg_replace("/\r\n/", "\r", trim($text));
@endphp

<header>
  <h2>{{ e($title) }}</h2>
</header>

<div class="p-4">
  @if($error)
    <div role="alert" class="alert alert-error">
      <span>{{ e($error) }}</span>
    </div>
  @else
    <pre class="whitespace-pre-wrap text-sm bg-base-200 p-4 rounded-box">{{ $text }}</pre>
  @endif
</div>
