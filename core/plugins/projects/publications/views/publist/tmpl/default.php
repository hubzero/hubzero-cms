<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

if (count($this->items) > 0) { ?>
<div class="public-list-header">
    <h3><?php echo Lang::txt('COM_PROJECTS_PUBLICATIONS'); ?></h3>
</div>
<div class="public-list-wrap">
    <ul class="public-list">
        <?php foreach ($this->items as $pub) { ?>
            <li>
                <span class="pub-thumb"><img src="<?php echo Route::url($pub->link('thumb')); ?>" alt="" /></span>
                <span class="pub-details">
                    <?php
                    $linkUrl = Route::url($pub->link('version'));
                    $linkText = $this->escape(stripslashes($pub->get('title'))) . ' v.'
                        . $this->escape($pub->get('version_label'));
                    ?>
                    <a href="<?= $linkUrl ?>" title="<?= $this->escape($pub->get('title')) ?>"><?= $linkText ?></a>
                    <span class="public-list-info">
                        <?php
                        $publishedText = Lang::txt('COM_PROJECTS_PUBLISHED') . ' ' . $pub->published('date') . ' '
                            . Lang::txt('COM_PROJECTS_IN') . ' <a href="' . Route::url($pub->link('category')) . '">'
                            . $pub->category()->name . '</a>';
                        ?>
                        - <?= $publishedText ?>
                    </span>
                </span>
            </li>
        <?php } ?>
    </ul>
</div>
<?php }
