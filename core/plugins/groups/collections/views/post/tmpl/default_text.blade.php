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

@if ($item->get('title'))
    <h4>{{ e(stripslashes($item->get('title'))) }}</h4>
@endif

@if ($content)
    <div class="prose prose-sm">
        {!! $content !!}
    </div>
@endif
