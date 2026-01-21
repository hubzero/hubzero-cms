<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<li>
    <?php $routeUrl = Route::url($this->row->link('thumb')); ?>
    <span class="pub-thumb"><img width="40" height="40" src="<?php echo $routeUrl; ?>" alt="" /></span>
    <span class="pub-details">
        <?php $routeUrl = Route::url('index.php?option=com_publications&id=' . $this->row->get('id')); ?>
        <?php $val2 = stripslashes($this->row->get('abstract')); ?>
        <?php $val3 = \Hubzero\Utility\Str::truncate(stripslashes($this->row->get('title')), 100); ?>
        <a href="<?php echo $routeUrl; ?>" title="<?php echo $val2; ?>"><?php echo $val3; ?></a>
        <span class="block details"><?php echo implode(' <span>|</span> ', $this->info); ?></span>
    </span>
</li>
