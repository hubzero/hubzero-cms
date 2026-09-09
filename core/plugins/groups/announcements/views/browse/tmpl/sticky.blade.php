{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$__view->css();
@endphp

@if ($rows->count() > 0)
    <div class="scontainer">
        @foreach ($rows as $row)
            @php
                $__view->view('item')
                    ->set('option', $option)
                    ->set('group', $group)
                    ->set('authorized', $authorized)
                    ->set('announcement', $row)
                    ->set('showClose', true)
                    ->display();
            @endphp
        @endforeach
    </div>
@endif
