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
 * @var \Hubzero\Component\View&object{content: mixed} $this
 */

?>

<div id="showcase">
    <div id="showcase-prev" ></div>
    <div id="showcase-window">
        <div class="showcase-pane">
            <?php echo $this->content; ?>
        </div>
    </div>
    <div id="showcase-next" ></div>
</div>