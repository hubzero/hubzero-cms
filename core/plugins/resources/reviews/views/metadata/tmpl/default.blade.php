{{--
  Reviews metadata — displays review count + "review this" link.

  Variables (from plugin):
    $url       — string: link to reviews tab
    $url2      — string: link to add review form
    $reviews   — array: review objects
    $isAuthor  — bool: whether current user is the resource author

  @package    hubzero-cms
  @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Lang;
@endphp

<p class="review">
  <a href="{{ $url }}">{{ Lang::txt('PLG_RESOURCES_REVIEWS_NUM_REVIEWS', count($reviews)) }}</a>
  @if(!$isAuthor)
    (<a href="{{ $url2 }}">{{ Lang::txt('PLG_RESOURCES_REVIEWS_REVIEW_THIS') }}</a>)
  @endif
</p>
