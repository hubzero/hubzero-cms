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

?>
<li>
    <span class="mypub-options">
        <?php
        $titleText = Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW_TITLE');
        ?>
        <a href="<?= $this->row->link('version') ?>" title="<?= $titleText ?>"><?= strtolower(Lang::txt('PLG_PROJECTS_PUBLICATIONS_VIEW')) ?></a> |
        <?php
        $titleText = Lang::txt('PLG_PROJECTS_PUBLICATIONS_MANAGE_TITLE');
        ?>
        <a href="<?= $this->row->link('editversion') ?>" title="<?= $titleText ?>"><?= Lang::txt('PLG_PROJECTS_PUBLICATIONS_MANAGE') ?></a>
    </span>
    <span class="pub-thumb"><img src="<?php echo Route::url($this->row->link('thumb')); ?>" alt=""/></span>
    <span class="pub-details">
        <?php echo $this->escape($this->row->get('title')); ?>
        <span class="block faded mini">
            <?= Lang::txt('PLG_PROJECTS_PUBLICATIONS_VERSION') . ' '
                . $this->escape($this->row->get('version_label')) ?>
            <span class="<?= $this->row->getStatusCss() ?> major_status"><?= $this->row->getStatusName() ?></span>
            <span class="block">
                <?= ucfirst(Lang::txt('PLG_PROJECTS_PUBLICATIONS_CREATED')) . ' ' . $this->row->created('date') . ' '
                    . Lang::txt('PLG_PROJECTS_PUBLICATIONS_BY') . ' ' . $this->escape($this->row->creator('name')) ?>
                <?= !$this->row->project()->isProvisioned()
                    ? Lang::txt('PLG_PROJECTS_PUBLICATIONS_IN_PROJECT') . ' <a href="' . $this->row->project()->link()
                        . '">'
                        . $this->escape(
                            \Hubzero\Utility\Str::truncate(stripslashes($this->row->project()->get('title')), 80),
                        ) . '</a>'
                    : '' ?></span>
        </span>
    </span>
</li>
