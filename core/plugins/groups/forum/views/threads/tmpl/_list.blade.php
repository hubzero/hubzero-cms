{{--
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
--}}

@php
$hash_map = [];

if (isset($likes)) {
    foreach ($likes as $likeItem) {
        $postId = $likeItem->postId;

        if (isset($hash_map[$postId])) {
            $hash_map[$postId][] = $likeItem;
        } else {
            $hash_map[$postId] = [$likeItem];
        }
    }
}
@endphp

<ol class="comments" id="t{{ $parent }}">
@php
if ($comments) {
    $cls = 'odd';
    if (isset($cls)) {
        $cls = ($cls == 'odd') ? 'even' : 'odd';
    }

    if (!isset($search)) {
        $search = '';
    }

    $depth++;

    foreach ($comments as $commentItem) {
        $postId = $commentItem->get('id');
        $likesByPostId = isset($hash_map[$postId]) ? $hash_map[$postId] : [];

        $__view->view('_comment')
             ->set('option', $option)
             ->set('group', $group)
             ->set('comment', $commentItem)
             ->set('like', $likesByPostId)
             ->set('likes', isset($likes) ? $likes : '')
             ->set('thread', $thread)
             ->set('config', $config)
             ->set('depth', $depth)
             ->set('cls', $cls)
             ->set('filters', $filters)
             ->set('category', $category)
             ->display();
    }
}
@endphp
</ol>
