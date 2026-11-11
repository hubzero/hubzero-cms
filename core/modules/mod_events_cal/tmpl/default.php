<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Module\Module&object{content: mixed} $this
 */

if ($this->getError()) {
    ?>
    <p class="error"><?php echo $this->getError(); ?></p>
    <?php
} else {
    echo $this->content;
}
