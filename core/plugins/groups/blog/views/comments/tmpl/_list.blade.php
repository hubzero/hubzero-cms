{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

<ol class="comments" id="t{{ isset($parent) ? $parent : '0' }}">
@if ($comments)
    @php
    $cls = 'odd';
    if (isset($cls)) {
        $cls = ($cls == 'odd') ? 'even' : 'odd';
    }

    $depth++;
    @endphp

    @foreach ($comments as $comment)
        @php
        $__view->view('_comment', 'comments')
            ->set('option', $option)
            ->set('comment', $comment)
            ->set('config', $config)
            ->set('depth', $depth)
            ->set('cls', $cls)
            ->set('base', $base)
            ->set('group', $group)
            ->display();
        @endphp
    @endforeach
@endif
</ol>
