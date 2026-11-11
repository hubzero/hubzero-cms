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
 *     parent: mixed,
 * } $this
 */
?>
<?php
if (isset($this->comments) && count($this->comments)) {
    $cls = 'odd';
    if (isset($this->cls)) {
        $cls = ($this->cls == 'odd') ? 'even' : 'odd';
    }

    $depth = $this->depth + 1;
    ?>
<ol class="comments" id="t<?php echo isset($this->parent) ? $this->parent : '0'; ?>">
    <?php
    foreach ($this->comments as $comment) {
        $this
             ->view('_comment')
             ->set('option', $this->option)
             ->set('comment', $comment)
             ->set('config', $this->config)
             ->set('depth', $depth)
             ->set('cls', $cls)
             ->set('base', $this->base)
             ->display();
    }
    ?>
</ol>
    <?php
}
?>
