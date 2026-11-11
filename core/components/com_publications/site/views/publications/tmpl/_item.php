<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{info: mixed, row: mixed} $this
 */

?>
<li>
    <span class="pub-thumb"><img width="40" height="40" src="<?= Route::url($this->row->link('thumb')) ?>"
        alt="" /></span>
    <span class="pub-details">
        <?php
        $linkUrl = Route::url('index.php?option=com_publications&id=' . $this->row->get('id'));
        $linkText = $this->escape(\Hubzero\Utility\Str::truncate(stripslashes($this->row->get('title')), 100));
        ?>
        <a href="<?= $linkUrl ?>"
            title="<?= $this->escape(stripslashes($this->row->get('abstract'))) ?>"><?= $linkText ?></a>
        <span class="block details"><?php echo implode(' <span>|</span> ', $this->info); ?></span>
    </span>
</li>
