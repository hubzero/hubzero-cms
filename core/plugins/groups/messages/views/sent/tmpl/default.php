<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

?>
<h3 class="section-header" id="messages">
    <?php echo Lang::txt('MESSAGES'); ?>
</h3>

<?php if ($this->authorized == 'manager') { ?>
    <?php
    $newMessageUrl = Route::url(
        'index.php?option=' . $this->option
        . '&cn=' . $this->group->get('cn')
        . '&active=messages&action=new'
    );
    ?>
<ul id="page_options">
    <li>
        <a id="new-group-message"
            class="icon-email message btn"
            href="<?php echo $newMessageUrl; ?>">
            <span><?php echo Lang::txt('PLG_GROUPS_MESSAGES_SEND'); ?></span>
        </a>
    </li>
</ul>
<?php } ?>

<div class="section">
    <div class="container">
        <?php
        $messagesUrl = Route::url(
            'index.php?option=' . $this->option
            . '&cn=' . $this->group->get('cn')
            . '&active=messages'
        );
        ?>
        <form action="<?php echo $messagesUrl; ?>"
            method="post">
            <table class="groups entries">
                <?php $sentTxt = Lang::txt('PLG_GROUPS_MESSAGES_SENT'); ?>
                <caption>
                    <?php echo $sentTxt; ?>
                    <span>(<?php echo count($this->rows); ?>)</span>
                </caption>
                <thead>
                    <tr>
                        <th scope="col"><?php echo Lang::txt('Subject'); ?></th>
                        <th scope="col"><?php echo Lang::txt('Message From'); ?></th>
                        <th scope="col"><?php echo Lang::txt('Date Sent'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($this->rows->count() > 0) { ?>
                        <?php foreach ($this->rows as $row) { ?>
                            <?php
                            $viewUrl = Route::url(
                                'index.php?option=' . $this->option
                                . '&cn=' . $this->group->get('cn')
                                . '&active=messages&action=viewmessage&msg='
                                . $row->id
                            );
                            $memberUrl = Route::url(
                                'index.php?option=com_members&id='
                                . $row->created_by
                            );
                            $subject = $this->escape(
                                stripslashes($row->subject)
                            );
                            $name = $this->escape(
                                stripslashes($row->name)
                            );
                            $dateFormatted = Date::of($row->created)
                                ->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
                            ?>
                            <tr>
                                <td>
                                    <a href="<?php echo $viewUrl; ?>"><?php echo $subject; ?></a>
                                </td>
                                <td>
                                    <a href="<?php echo $memberUrl; ?>"><?php echo $name; ?></a>
                                </td>
                                <td>
                                    <time datetime="<?php echo $row->created; ?>">
                                        <?php echo $dateFormatted; ?>
                                    </time>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="3"><?php echo Lang::txt('No messages found'); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </form>

        <?php
        // Initiate paging
        $pageNav = $this->pagination(
            $this->total,
            $this->filters['start'],
            $this->filters['limit']
        );
        $pageNav->setAdditionalUrlParam('cn', $this->group->get('cn'));
        $pageNav->setAdditionalUrlParam('active', 'messages');

        echo $pageNav->render();
        ?>
        <div class="clearfix"></div>
    </div><!-- / .container -->
</div><!-- / .section -->

