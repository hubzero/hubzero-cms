<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;

// @phpcs:disable PSR1.Files.SideEffects

// No direct access
defined('_HZEXEC_') or die();

$stamp = $this->publicStamp ? $this->publicStamp->stamp : null;

if ($stamp) { ?>
    <p class="publink">
        <?php echo Lang::txt('COM_PROJECTS_NOTES_PUB_LINK')
            . ' <a href="'
            . trim(Request::base(), DS)
            . Route::url('index.php?option=' . $this->option . '&action=get')
            . '?s='
            . $stamp
            . '" rel="external">'
            . trim(Request::base(), DS)
            . Route::url('index.php?option=' . $this->option . '&action=get&s=' . $stamp)
            . '</a>'; ?>
        <?php if ($this->project->isPublic()) {
            $act = $this->publicStamp->listed ? 'unlist' : 'publist';
            $listedClass = $this->publicStamp->listed ? 'green' : 'urgency';
            $listedLabel = $this->publicStamp->listed
                ? Lang::txt('COM_PROJECTS_NOTES_LISTED')
                : Lang::txt('COM_PROJECTS_NOTES_UNLISTED');
            $shareUrl = Route::url(
                'index.php?option=' . $this->option
                . '&alias=' . $this->project->get('alias')
                . '&active=notes&p=' . $this->page->get('id')
            ) . '&amp;action=share';
            $shareLabel = Lang::txt('COM_PROJECTS_NOTES_SHARE_SETTINGS');
            ?>
        <span>
            <?php echo Lang::txt('COM_PROJECTS_NOTES_THIS_PAGE_IS'); ?>
            <strong class="<?php echo $listedClass; ?>"><?php echo $listedLabel; ?></strong>.
            <a href="<?php echo $shareUrl; ?>"
                class="showinbox"
            ><?php echo $shareLabel; ?> &rsaquo;</a>
        </span>
        <?php } ?>
    </p>
<?php } else { ?>
    <?php
        $generateUrl = Route::url(
            'index.php?option=' . $this->option
            . '&alias=' . $this->project->get('alias')
            . '&active=notes&p=' . $this->page->get('id')
        ) . '&amp;action=share';
    ?>
    <p class="publink">
        <?php echo Lang::txt('COM_PROJECTS_NOTES_SHARE_GET_LINK'); ?>
        <a href="<?php echo $generateUrl; ?>"
            class="showinbox"
        ><?php echo Lang::txt('COM_PROJECTS_NOTES_SHARE_GENERATE_LINK'); ?></a>
    </p>
<?php }
