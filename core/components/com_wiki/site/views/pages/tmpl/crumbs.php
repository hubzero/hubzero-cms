<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// The page's ancestors, root first (empty when page crumbs are turned off)
if (empty($this->parents)) {
    return;
}
?>
<p class="wiki-crumbs">
    <?php foreach ($this->parents as $parent) { ?>
        <a class="wiki-crumb" href="<?php echo Route::url($parent->link()); ?>"><?php echo $this->escape($parent->title); ?></a> /
    <?php } ?>
</p>
