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
 *     cls: mixed,
 *     comments: mixed,
 *     depth: mixed,
 *     obj: mixed,
 *     obj_type: mixed,
 *     option: mixed,
 *     params: mixed,
 *     url: mixed,
 * } $this
 */

if ($this->comments) { ?>
    <ol class="comments">
    <?php
    $cls = 'odd';
    if (isset($this->cls)) {
        $cls = ($this->cls == 'odd') ? 'even' : 'odd';
    }

    $this->set('depth', $this->depth + 1);

    foreach ($this->comments as $comment) {
        $this
             ->view('item')
             ->set('option', $this->option)
             ->set('comment', $comment)
             ->set('obj_type', $this->obj_type)
             ->set('obj', $this->obj)
             ->set('params', $this->params)
             ->set('depth', $this->depth)
             ->set('url', $this->url)
             ->set('cls', $cls)
             ->display();
    }
    ?>
    </ol>
<?php }
