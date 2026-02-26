<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// no direct access
defined('_HZEXEC_') or die();
?>
<li class="group<?php if ($group->published == 2) {
    echo ' archived';
                } ?>">
    <a href="<?php echo Route::url('index.php?option=com_groups&cn=' . $group->cn); ?>">
        <?php echo $this->escape(stripslashes($group->description)); ?>
    </a>
    <span><span class="<?php echo $status; ?> status"><?php
        echo Lang::txt('MOD_MYGROUPS_STATUS_' . strtoupper($status));
    ?></span></span>
    <?php if (!$group->approved) : ?>
        <span class="status pending-approval"><?php echo Lang::txt('MOD_MYGROUPS_GROUP_STATUS_PENDING'); ?></span>
    <?php endif; ?>
    <?php if ($group->published == 2) : ?>
        <span class="status archived"><?php echo Lang::txt('MOD_MYGROUPS_GROUP_STATUS_ARCHIVED'); ?></span>
    <?php endif; ?>
    <?php if ($group->regconfirmed && !$group->registered) : ?>
        <span class="actions">
            <?php
            $acceptUrl = Route::url(
                'index.php?option=com_groups&cn='
                . $group->cn . '&task=accept'
            );
            $cancelUrl = Route::url(
                'index.php?option=com_groups&cn='
                . $group->cn . '&task=cancel'
            );
            ?>
            <a class="action-accept"
                href="<?php echo $acceptUrl; ?>"
            >
                <?php echo Lang::txt('MOD_MYGROUPS_ACTION_ACCEPT'); ?>
            </a>
            <a class="action-cancel"
                href="<?php echo $cancelUrl; ?>"
            >
                <?php echo Lang::txt('MOD_MYGROUPS_ACTION_DECLINE'); ?>
            </a>
        </span>
    <?php endif; ?>
</li>
