<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{lines: mixed, supported: mixed} $this
 */
?>
<ol class="resources results">
    <?php
    foreach ($this->lines as $line) {
        // Instantiate a new view
        $this
            ->view('item', 'browse')
            ->set('line', $line)
            ->set('supported', isset($this->supported) ? $this->supported : [])
            ->display();
    }
    ?>
</ol>