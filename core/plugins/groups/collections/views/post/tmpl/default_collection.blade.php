{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
if ($row instanceof \Components\Collections\Models\Collection) {
    $collection = $row;
} else {
    $collection = \Components\Collections\Models\Collection::getInstance($row->item()->get('object_id'));
    if ($row->get('description')) {
        $collection->set('description', $row->get('description'));
    }
}
@endphp

<h4 @if ($collection->get('access', 0) == 4) class="text-warning" @endif>
    <a class="link link-hover" href="{{ Route::url($collection->link()) }}">
        {{ e(stripslashes($collection->get('title'))) }}
    </a>
</h4>
<div class="prose prose-sm">
    {!! $collection->description('parsed') !!}
</div>
