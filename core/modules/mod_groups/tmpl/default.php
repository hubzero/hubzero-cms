<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

defined('_HZEXEC_') or die();

$this->css();
?>
<div class="<?php echo $this->module->module; ?>">
    <table class="stats-overview">
        <tbody>
            <tr>
                <td class="public">
                    <?php
                    $visibleUrl = Route::url(
                        'index.php?option=com_groups&controller=manage&type='
                        . $this->type . '&discoverability=0&policy='
                    );
                    ?>
                    <a href="<?php echo $visibleUrl; ?>"
                        title="<?php echo Lang::txt('MOD_GROUPS_VISIBLE_TITLE'); ?>"
                    >
                        <?php echo $this->escape($this->visible); ?>
                        <span><?php echo Lang::txt('MOD_GROUPS_VISIBLE'); ?></span>
                    </a>
                </td>
                <td class="protected">
                    <?php
                    $hiddenUrl = Route::url(
                        'index.php?option=com_groups&controller=manage&type='
                        . $this->type . '&discoverability=1&policy='
                    );
                    ?>
                    <a href="<?php echo $hiddenUrl; ?>"
                        title="<?php echo Lang::txt('MOD_GROUPS_HIDDEN_TITLE'); ?>"
                    >
                        <?php echo $this->escape($this->hidden); ?>
                        <span><?php echo Lang::txt('MOD_GROUPS_HIDDEN'); ?></span>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="stats-overview">
        <tbody>
            <tr>
                <td class="closed">
                    <?php
                    $closedUrl = Route::url(
                        'index.php?option=com_groups&controller=manage&type='
                        . $this->type . '&discoverability=&policy=closed&approved='
                    );
                    ?>
                    <a href="<?php echo $closedUrl; ?>"
                        title="<?php echo Lang::txt('MOD_GROUPS_CLOSED_TITLE'); ?>"
                    >
                        <?php echo $this->escape($this->closed); ?>
                        <span><?php echo Lang::txt('MOD_GROUPS_CLOSED'); ?></span>
                    </a>
                </td>
                <td class="invite">
                    <?php
                    $inviteUrl = Route::url(
                        'index.php?option=com_groups&controller=manage&type='
                        . $this->type . '&discoverability=&policy=invite&approved='
                    );
                    ?>
                    <a href="<?php echo $inviteUrl; ?>"
                        title="<?php echo Lang::txt('MOD_GROUPS_INVITE_TITLE'); ?>"
                    >
                        <?php echo $this->escape($this->invite); ?>
                        <span><?php echo Lang::txt('MOD_GROUPS_INVITE'); ?></span>
                    </a>
                </td>
                <td class="restricted">
                    <?php
                    $restrictedUrl = Route::url(
                        'index.php?option=com_groups&controller=manage&type='
                        . $this->type
                        . '&discoverability=&policy=restricted&approved='
                    );
                    ?>
                    <a href="<?php echo $restrictedUrl; ?>"
                        title="<?php echo Lang::txt('MOD_GROUPS_RESTRICTED_TITLE'); ?>"
                    >
                        <?php echo $this->escape($this->restricted); ?>
                        <span><?php echo Lang::txt('MOD_GROUPS_RESTRICTED'); ?></span>
                    </a>
                </td>
                <td class="open">
                    <?php
                    $openUrl = Route::url(
                        'index.php?option=com_groups&controller=manage&type='
                        . $this->type . '&discoverability=&policy=open&approved='
                    );
                    ?>
                    <a href="<?php echo $openUrl; ?>"
                        title="<?php echo Lang::txt('MOD_GROUPS_OPEN_TITLE'); ?>"
                    >
                        <?php echo $this->escape($this->open); ?>
                        <span><?php echo Lang::txt('MOD_GROUPS_OPEN'); ?></span>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>

    <table class="stats-overview">
        <tbody>
            <tr>
                <td class="approved">
                    <?php
                    $approvedUrl = Route::url(
                        'index.php?option=com_groups&controller=manage&type='
                        . $this->type . '&approved=1&discoverability=&policy='
                    );
                    ?>
                    <a href="<?php echo $approvedUrl; ?>"
                        title="<?php echo Lang::txt('MOD_GROUPS_PUBLISHED_TITLE'); ?>"
                    >
                        <?php echo $this->escape($this->approved); ?>
                        <span><?php echo Lang::txt('MOD_GROUPS_PUBLISHED'); ?></span>
                    </a>
                </td>
                <td class="pending">
                    <?php
                    $pendingUrl = Route::url(
                        'index.php?option=com_groups&controller=manage&type='
                        . $this->type . '&approved=0&discoverability=&policy='
                    );
                    ?>
                    <a href="<?php echo $pendingUrl; ?>"
                        title="<?php echo Lang::txt('MOD_GROUPS_PENDING_TITLE'); ?>"
                    >
                        <?php echo $this->escape($this->pending); ?>
                        <span><?php echo Lang::txt('MOD_GROUPS_PENDING'); ?></span>
                    </a>
                </td>
                <td class="newest">
                    <?php
                    $newUrl = Route::url(
                        'index.php?option=com_groups&controller=manage&type='
                        . $this->type
                        . '&created=pastday&discoverability=&policy=&approved='
                    );
                    ?>
                    <a href="<?php echo $newUrl; ?>"
                        title="<?php echo Lang::txt('MOD_GROUPS_NEW_TITLE'); ?>"
                    >
                        <?php echo $this->escape($this->pastDay); ?>
                        <span><?php echo Lang::txt('MOD_GROUPS_NEW'); ?></span>
                    </a>
                </td>
            </tr>
        </tbody>
    </table>
</div>