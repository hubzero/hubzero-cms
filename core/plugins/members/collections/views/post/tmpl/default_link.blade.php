@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$item = $row->item();
$content = $row->description('parsed');
$content = ($content ?: $item->description('parsed'));
@endphp

<h4>
    <a href="{{ stripslashes($item->get('url')) }}" rel="external nofollow noreferrer">
        {{ e(stripslashes($item->get('title', $item->get('url')))) }}
    </a>
</h4>

@if ($content)
    <div class="description">
        {!! $content !!}
    </div>
@endif
