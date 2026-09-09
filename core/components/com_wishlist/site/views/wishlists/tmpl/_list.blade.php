{{--
 * Recursive comment list iterator
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
--}}

<ol class="space-y-3 {{ $depth > 0 ? 'ml-6 mt-3' : '' }}"
    id="t{{ $parent ?? '0' }}">
    @if(isset($comments))
        @php
            $cls = $cls ?? 'odd';
            $depth++;
        @endphp
        @foreach($comments as $comment)
            {!! $__view->view('_comment')
                ->set('option', $option)
                ->set('comment', $comment)
                ->set('depth', $depth)
                ->set('cls', $cls)
                ->set('wish', $wish)
                ->set('wishlist', $wishlist)
                ->loadTemplate() !!}
        @endforeach
    @endif
</ol>
