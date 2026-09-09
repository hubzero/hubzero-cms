{{--
 * Wiki comments — list iterator (recursive)
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

<ol class="space-y-4" id="t{{ $parent ?? '0' }}">
    @if($comments)
        @php
            $cls = 'odd';
            if (isset($cls_passed)) {
                $cls = ($cls_passed == 'odd') ? 'even' : 'odd';
            }
            $depth++;
        @endphp

        @foreach($comments as $comment)
            @php $comment->set('page_id', $page->get('id')); @endphp
            {!! $__view->view('_comment')
                ->set('option', $option)
                ->set('comment', $comment)
                ->set('config', $config)
                ->set('depth', $depth)
                ->set('page', $page)
                ->set('version', $version)
                ->set('cls', $cls)
                ->loadTemplate() !!}
        @endforeach
    @endif
</ol>
