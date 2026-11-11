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
 * @var \Hubzero\Component\View&object{parents: mixed} $this
 */

// The page's ancestors, root first (empty when page crumbs are turned off)
if (empty($this->parents)) {
    return;
}
?>
<p class="wiki-crumbs">
    <?php foreach ($this->parents as $parent) { ?>
        <a class="wiki-crumb" href="<?= Route::url($parent->link()) ?>"><?= $this->escape($parent->title) ?></a> /
    <?php } ?>
</p>
