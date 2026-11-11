<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

if (substr($this->xmessage->type, -8) == '_message') {
    $u = User::getInstance($this->xmessage->created_by);
    $from = '<a href="' . Route::url('index.php?option=' . $this->option . '&id=' . $u->get('id')) . '">'
        . $this->escape($u->get('name')) . '</a>';
} else {
    $from = Lang::txt('System') . ' (' . $this->xmessage->component . ')';
}

?>
<div class="subject">
    <?php if (!$this->no_html) : ?>
    <ul class="entries-menu">
        <?php
        $linkUrl = Route::url(
            'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=messages',
        );
        ?>
        <li><a class="active" href="<?= $linkUrl ?>"><span><?= Lang::txt('PLG_GROUPS_MESSAGES_SENT') ?></span></a></li>
        <?php if ($this->authorized == 'admin' || $this->authorized == 'manager') { ?>
            <?php
            $newUrl = Route::url(
                'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=messages&action=new',
            );
            ?>
            <li><a href="<?= $newUrl ?>"><span><?= Lang::txt('PLG_GROUPS_MESSAGES_SEND') ?></span></a></li>
        <?php } ?>
    </ul>
    <br class="clear" />
    <?php endif; ?>

    <?php
    $actionUrl = Route::url(
        'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=messages',
    );
    ?>
    <form action="<?= $actionUrl ?>" method="post">
        <fieldset class="hub-mail">
            <table class="groups entries">
                <caption>
                    <?php echo Lang::txt('PLG_GROUPS_MESSAGE'); ?>
                    <?php if (!$this->no_html) : ?>
                        <span>
                            <?php
                            $linkUrl = Route::url(
                                'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn')
                                    . '&active=messages',
                            );
                            ?>
                            <small>( <a href="<?= $linkUrl ?>"><?= Lang::txt('&lsaquo; Back to Sent Messages') ?></a> )</small>
                        </span>
                    <?php endif; ?>
                </caption>
                <tbody>
                    <tr>
                        <th><?php echo Lang::txt('PLG_GROUPS_MESSAGES_RECEIVED'); ?>:</th>
                        <td><?= Date::of($this->xmessage->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) ?></td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('PLG_GROUPS_MESSAGES_FROM'); ?>:</th>
                        <td><?php echo $from; ?></td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('PLG_GROUPS_MESSAGES_SUBJECT'); ?>:</th>
                        <td><?php echo $this->escape(stripslashes($this->xmessage->subject)); ?></td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('PLG_GROUPS_MESSAGES_MESSAGE'); ?>:</th>
                        <td><?php echo $this->xmessage->message; ?></td>
                    </tr>
                </tbody>
            </table>
        </fieldset>
    </form>
</div><!-- // .subject -->

