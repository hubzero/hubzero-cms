<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$this->css();
?>
<div class="item-watch <?php echo $this->watched ? 'watching' : ''; ?>">
    <?php if ($this->watched) { ?>
        <p>
            <?php
            $unsubscribeUrl = Route::url(
                $this->publication->link('version') . '&active=watch&confirm=1&action=unsubscribe',
            );
            ?>
            <button type="submit" form="hz-post-action" formaction="<?= $unsubscribeUrl ?>" class="btn unsubscribe"><?= Lang::txt('PLG_PUBLICATIONS_WATCH_UNSUBSCRIBE') ?></button>
        </p>
    <?php } else { ?>
        <p>
            <?php
            $subscribeUrl = Route::url(
                $this->publication->link('version') . '&active=watch&confirm=1&action=subscribe',
            );
            ?>
            <button type="submit" form="hz-post-action" formaction="<?= $subscribeUrl ?>" class="btn subscribe"><?= Lang::txt('PLG_PUBLICATIONS_WATCH_SUBSCRIBE') ?></button>
        </p>
    <?php } ?>

    <p>
        <?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_EXPLAIN'); ?>
    </p>
</div>
