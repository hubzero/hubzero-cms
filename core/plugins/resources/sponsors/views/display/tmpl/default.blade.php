{{--
  Sponsors display — shows sponsor content with explanation sidebar.

  Variables (from plugin):
    $data — string: sponsor HTML content

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

@if($data)
  <h3>{{ Lang::txt('PLG_RESOURCES_SPONSORS_HEADER') }}</h3>
  <div class="aside">
    <p>{{ Lang::txt('PLG_RESOURCES_SPONSORS_EXPLANATION') }}</p>
  </div>
  <div class="subject" id="sponsors-subject">
    {!! $data !!}
  </div>
@endif
