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
 *     base: mixed,
 *     cls: mixed,
 *     comments: mixed,
 *     config: mixed,
 *     depth: mixed,
 *     group: mixed,
 *     option: mixed,
 *     parent: mixed,
 * } $this
 */
?>
<?php if ($this->comments && count($this->comments) > 0) : ?>
<ol class="comments" id="t<?php echo (isset($this->parent)) ? $this->parent : '0'; ?>">
    <?php
    $cls = 'odd';
    if (isset($this->cls)) {
        $cls = ($this->cls == 'odd') ? 'even' : 'odd';
    }

    $this->set('depth', $this->depth + 1);

    foreach ($this->comments as $comment) {
        $this
             ->view('_comment', 'comments')
             ->set('option', $this->option)
             ->set('comment', $comment)
             ->set('config', $this->config)
             ->set('depth', $this->depth)
             ->set('cls', $cls)
             ->set('base', $this->base)
             ->set('group', $this->group)
             ->display();
    }
    ?>
</ol>
<?php endif; ?>