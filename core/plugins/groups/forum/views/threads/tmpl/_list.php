<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{
 *     category: mixed,
 *     cls: mixed,
 *     comments: mixed,
 *     config: mixed,
 *     depth: mixed,
 *     filters: mixed,
 *     group: mixed,
 *     likes: mixed,
 *     option: mixed,
 *     parent: mixed,
 *     search: mixed,
 *     thread: mixed,
 * } $this
 */

$hash_map = [];

if (isset($this->likes)) {
    foreach ($this->likes as $like) {
        $postId = $like->postId;

        if (isset($hash_map[$postId])) {
            $hash_map[$postId][] = $like;
        } else {
            $hash_map[$postId] = [$like];
        }
    }
}

?>
<?php if ($this->comments && count($this->comments) > 0) : ?>
<ol class="comments" id="t<?php echo $this->parent; ?>">
    <?php
    {
    $cls = 'odd';
    if (isset($this->cls)) {
        $cls = ($this->cls == 'odd') ? 'even' : 'odd';
    }

    if (!isset($this->search)) {
        $this->set('search', '');
    }

    $this->set('depth', $this->depth + 1);

    foreach ($this->comments as $comment) {
        $postId = $comment->get('id');
        $likesByPostId = isset($hash_map[$postId]) ? $hash_map[$postId] : [];

        $this
             ->view('_comment')
             ->set('option', $this->option)
             ->set('group', $this->group)
             ->set('comment', $comment)
             ->set('like', $likesByPostId)
             ->set('likes', isset($this->likes) ? $this->likes : '')
             ->set('thread', $this->thread)
             ->set('config', $this->config)
             ->set('depth', $this->depth)
             ->set('cls', $cls)
             ->set('filters', $this->filters)
             ->set('category', $this->category)
             ->display();
    }
    }
    ?>
</ol>
<?php endif; ?>
