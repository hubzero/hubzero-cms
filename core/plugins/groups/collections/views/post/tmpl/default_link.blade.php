{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$item = $row->item();

$content = $row->description('parsed');
$content = ($content ?: $item->description('parsed'));
@endphp

<h4>
    <a class="link link-primary" href="{{ stripslashes($item->get('url')) }}" rel="external nofollow noreferrer">
        {{ e(stripslashes($item->get('title', $item->get('url')))) }}
    </a>
</h4>

@if ($content)
    <div class="prose prose-sm">
        {!! $content !!}
    </div>
@endif
