<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

$this->css();
?>

<div class="item-watch <?php echo $this->watched->get('id') ? 'watching' : ''; ?>">
    <?php if ($this->watched->get('id')) { ?>
        <p>
            <a class="btn unsubscribe"
                href="<?php echo Route::url($this->link . '&action=unsubscribe'); ?>"
                ><?php echo Lang::txt('PLG_RESOURCES_WATCH_UNSUBSCRIBE'); ?></a>
        </p>
    <?php } else { ?>
        <p>
            <a class="btn subscribe"
                href="<?php echo Route::url($this->link . '&action=subscribe'); ?>"
                ><?php echo Lang::txt('PLG_RESOURCES_WATCH_SUBSCRIBE'); ?></a>
        </p>
    <?php } ?>

    <p>
        <?php echo Lang::txt('PLG_RESOURCES_WATCH_EXPLAIN'); ?>
    </p>
</div>