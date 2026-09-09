{{--
  Sponsors mini display — compact sponsor content block.

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
  <div id="sponsors" class="container">
    <h3>{{ Lang::txt('PLG_RESOURCES_SPONSORS_HEADER') }}</h3>
    <div class="plg-content">
      {!! $data !!}
    </div>
  </div>
@endif
