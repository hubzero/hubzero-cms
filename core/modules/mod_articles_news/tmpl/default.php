<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die;

/**
 * What this template is given.
 *
 * @var \Hubzero\Module\Module $this
 */
?>
<div class="newsflash<?php echo $moduleclass_sfx; ?>">
    <?php foreach ($list as $item) : ?>
        <?php require $this->getLayoutPath('_item'); ?>
    <?php endforeach; ?>
</div>
