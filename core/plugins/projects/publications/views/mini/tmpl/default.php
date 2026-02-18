<?php

// @phpcs:disable PSR1.Files.SideEffects

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css();

?>
<div class="sidebox<?php if (count($this->items) == 0) {
    echo ' suggestions';
                   } ?>">
    <?php
        $pubsUrl = Route::url($this->model->link('publications'));
        $linkTitle = Lang::txt('COM_PROJECTS_VIEW') . ' '
            . strtolower(Lang::txt('COM_PROJECTS_PROJECT')) . ' '
            . strtolower(Lang::txt('COM_PROJECTS_TAB_PUBLICATIONS'));
        $tabLabel = ucfirst(Lang::txt('COM_PROJECTS_TAB_PUBLICATIONS'));
        $seeAll = ucfirst(Lang::txt('COM_PROJECTS_SEE_ALL'));
        $startUrl = Route::url(
            $this->model->link('publications') . '&action=start'
        );
        $startLabel = Lang::txt('PLG_PROJECTS_PUBLICATIONS_START_PUBLICATION');
        ?>
    <h4>
        <a href="<?php echo $pubsUrl; ?>"
            class="hlink"
            title="<?php echo $linkTitle; ?>"><?php echo $tabLabel; ?></a>
        <?php if (count($this->items) > 0) { ?>
            <span><a href="<?php echo $pubsUrl; ?>"><?php
                echo $seeAll; ?> </a></span>
        <?php } ?>
    </h4>
    <?php if (count($this->items) == 0) { ?>
        <p class="s-publications"><a href="<?php echo $startUrl; ?>"><?php
            echo $startLabel; ?></a></p>
    <?php } else { ?>
        <ul>
            <?php foreach ($this->items as $pub) {
                $status = $pub->getStatusName();
                ?>
            <li>
                <span class="pub-thumb"><img src="<?php echo Route::url($pub->link('thumb')); ?>" alt=""/></span>
                <span class="pub-details">
                    <a href="<?php echo Route::url($pub->link('editversion')); ?>"
                        title="<?php echo $this->escape($pub->get('title')); ?>"
                        ><?php echo \Hubzero\Utility\Str::truncate(stripslashes($pub->get('title')), 100); ?></a>
                     <span class="block faded mini">
                        <span>v. <?php echo $pub->get('version_label'); ?> (<?php echo $status; ?>)</span>
                    </span>
                </span>
            </li>
            <?php } ?>
        </ul>
    <?php } ?>
</div>
