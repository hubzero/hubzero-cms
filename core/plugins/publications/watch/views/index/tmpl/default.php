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
<div class="item-watch <?php echo $this->watched ? 'watching' : ''; ?>">
    <?php if ($this->watched) { ?>
        <p>
            <?php
            $unsubUrl = Route::url(
                $this->publication->link('version')
                . '&active=watch&confirm=1&action=unsubscribe'
            );
            ?>
            <a class="btn unsubscribe"
                href="<?php echo $unsubUrl; ?>"><?php
                    echo Lang::txt('PLG_PUBLICATIONS_WATCH_UNSUBSCRIBE');
                ?></a>
        </p>
    <?php } else { ?>
        <p>
            <?php
            $subUrl = Route::url(
                $this->publication->link('version')
                . '&active=watch&confirm=1&action=subscribe'
            );
            ?>
            <a class="btn subscribe"
                href="<?php echo $subUrl; ?>"><?php
                    echo Lang::txt('PLG_PUBLICATIONS_WATCH_SUBSCRIBE');
                ?></a>
        </p>
    <?php } ?>

    <p>
        <?php echo Lang::txt('PLG_PUBLICATIONS_WATCH_EXPLAIN'); ?>
    </p>
</div>
