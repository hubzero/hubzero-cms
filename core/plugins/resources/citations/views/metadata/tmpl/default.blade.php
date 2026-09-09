{{--
  Citations metadata — displays citation count link.

  Variables (from plugin):
    $url       — string: link to citations tab
    $citations — array: citation objects

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

<p class="citation">
  <a href="{{ $url }}">{{ Lang::txt('PLG_RESOURCES_CITATIONS_COUNT', count($citations)) }}</a>
</p>
