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

$this->css();

?>
<div class="sidebox<?php echo count($this->items) == 0 ? ' suggestions' : ''; ?>">
    <h4>
        <?php
        $linkUrl = Route::url($this->model->link('publications'));
        $titleText = Lang::txt('COM_PROJECTS_VIEW') . ' ' . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) . ' '
            . strtolower(Lang::txt('COM_PROJECTS_TAB_PUBLICATIONS'));
        ?>
        <a href="<?= $linkUrl ?>" class="hlink" title="<?= $titleText ?>"><?= ucfirst(Lang::txt('COM_PROJECTS_TAB_PUBLICATIONS')) ?></a>
        <?php if (count($this->items) > 0) { ?>
            <?php
            $linkUrl = Route::url($this->model->link('publications'));
            ?>
            <span><a href="<?= $linkUrl ?>"><?= ucfirst(Lang::txt('COM_PROJECTS_SEE_ALL')) ?> </a></span>
        <?php } ?>
    </h4>
    <?php if (count($this->items) == 0) { ?>
        <?php
        $startUrl = Route::url($this->model->link('publications') . '&action=start');
        ?>
        <p class="s-publications">
            <a href="<?= $startUrl ?>"><?= Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION') ?></a>
        </p>
    <?php } else { ?>
        <ul>
            <?php foreach ($this->items as $pub) {
                $status = $pub->getStatusName();
                ?>
            <li>
                <span class="pub-thumb"><img src="<?php echo Route::url($pub->link('thumb')); ?>" alt=""/></span>
                <span class="pub-details">
                    <?php
                    $linkUrl = Route::url($pub->link('editversion'));
                    ?>
                    <a href="<?= $linkUrl ?>" title="<?= $this->escape($pub->get('title')) ?>"><?= $this->escape(\Hubzero\Utility\Str::truncate(stripslashes($pub->get('title')), 100)) ?></a>
                     <span class="block faded mini">
                        <span>v. <?= $this->escape($pub->get('version_label')) ?> (<?= $this->escape($status) ?>)</span>
                    </span>
                </span>
            </li>
            <?php } ?>
        </ul>
    <?php } ?>
</div>
