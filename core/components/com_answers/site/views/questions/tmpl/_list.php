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
 *     base: mixed,
 *     cls: mixed,
 *     comments: mixed,
 *     config: mixed,
 *     depth: mixed,
 *     item_id: mixed,
 *     parent: mixed,
 *     question: mixed,
 *     thread: mixed,
 * } $this
 */

$threadId = (isset($this->thread) ? $this->thread : 't')
    . (isset($this->parent) ? $this->parent : '0');
?>
<ol class="comments" id="<?php echo $threadId; ?>">
    <?php
        $cls = 'odd';
    if (isset($this->cls)) {
        $cls = ($this->cls == 'odd') ? 'even' : 'odd';
    }

        $depth = $this->depth + 1;

    foreach ($this->comments as $comment) {
        $comment->set('qid', $this->question->get('id'));

        $this
             ->view('_comment')
             ->set('item_id', $this->item_id)
             ->set('option', $this->option)
             ->set('comment', $comment)
             ->set('config', $this->config)
             ->set('depth', $depth)
             ->set('question', $this->question)
             ->set('cls', $cls)
             ->set('base', $this->base)
             ->display();
    }
    ?>
</ol>
