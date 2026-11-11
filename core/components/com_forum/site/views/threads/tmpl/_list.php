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
 * @var \Hubzero\Component\View&object{
 *     category: mixed,
 *     cls: mixed,
 *     comments: mixed,
 *     config: mixed,
 *     depth: mixed,
 *     filters: mixed,
 *     likes: mixed,
 *     parent: mixed,
 *     search: mixed,
 *     thread: mixed,
 * } $this
 */

$hash_map = [];

foreach ($this->likes as $like) {
    $postId = $like->postId;

    if (isset($hash_map[$postId])) {
        $hash_map[$postId][] = $like;
    } else {
        $hash_map[$postId] = [$like];
    }
}


// Only emit <ol> when there are comments (ARIA: list requires listitem children)
$hasComments = false;
if (!empty($this->comments)) {
    if (is_countable($this->comments)) {
        $hasComments = count($this->comments) > 0;
    } elseif ($this->comments instanceof \Traversable) {
        foreach ($this->comments as $_c) {
            $hasComments = true;
            break;
        }
    }
}
if ($hasComments) :
    $cls = 'odd';
    if (isset($this->cls)) {
        $cls = ($this->cls == 'odd') ? 'even' : 'odd';
    }
    if (!isset($this->search)) {
        $this->set('search', '');
    }
    $depth = $this->depth + 1;
    ?>
<ol class="comments" id="t<?php echo $this->parent; ?>">
    <?php foreach ($this->comments as $comment) : ?>
        <?php
        $postId = $comment->get('id');
        $likesByPostId = isset($hash_map[$postId]) ? $hash_map[$postId] : [];
        $this
         ->view('_comment')
         ->set('option', $this->option)
         ->set('controller', $this->controller)
         ->set('comment', $comment)
         ->set('like', $likesByPostId)
         ->set('likes', $this->likes)
         ->set('thread', $this->thread)
         ->set('config', $this->config)
         ->set('depth', $depth)
         ->set('cls', $cls)
         ->set('filters', $this->filters)
         ->set('category', $this->category)
         ->display();
        ?>
    <?php endforeach; ?>
</ol>
<?php endif; ?>
