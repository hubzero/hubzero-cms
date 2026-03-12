{{--
  Tag activity log item partial

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Components\Tags\Helpers\ActivityLogPresenter;

  $parser    = new ActivityLogPresenter();
  $parsedLog = $parser->parse($log);
@endphp

@if ($parsedLog->activityDescription)
  <li class="{{ $parsedLog->class }}" data-id="{{ $log->get('id') }}">
    <span class="entry-log-data">
      {{ $parsedLog->activityDescription }}
    </span>
  </li>
@endif
