@php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Route;

if ($row instanceof \Components\Collections\Models\Collection) {
    $collection = $row;
} else {
    $collection = \Components\Collections\Models\Collection::getInstance($row->item()->get('object_id'));
    if ($row->get('description')) {
        $collection->set('description', $row->get('description'));
    }
}
@endphp

<h4{!! $collection->get('access', 0) == 4 ? ' class="private"' : '' !!}>
    <a href="{{ Route::url($collection->link()) }}">
        {{ e(stripslashes($collection->get('title'))) }}
    </a>
</h4>
<div class="description">
    {!! $collection->description('parsed') !!}
</div>
