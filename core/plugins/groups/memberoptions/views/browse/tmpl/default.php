<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Component;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

$params = $params =  Component::params('com_groups');

$forumCommentEmailNotifications = $params->get('email_forum_comments');

// Be sure to update this if you add more options
$atLeastOneOption = false;
if ($forumCommentEmailNotifications) {
    $atLeastOneOption = true;
}

$formUrl = Route::url(
    'index.php?option=' . $this->option
    . '&cn=' . $this->group->get('cn')
    . '&active=memberoptions'
);
?>

<form action="<?php echo $formUrl; ?>" method="post" id="memberoptionform">
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
    <input type="hidden" name="action" value="savememberoptions" />
    <input type="hidden" name="memberoptionid" value="<?php echo $this->recvEmailOptionID;?>" />

    <div class="group-content-header">
        <h3><?php echo Lang::txt('GROUP_MEMBEROPTIONS'); ?></h3>
    </div>

    <p><?php echo Lang::txt('GROUP_MEMBEROPTIONS_DESC'); ?></p>

    <?php if ($forumCommentEmailNotifications) { ?>
        <div class="input-wrap">
            <input type="checkbox"
                id="recvpostemail"
                value="1"
                name="recvpostemail" <?php if ($this->recvEmailOptionValue == 1) {
                    echo 'checked="checked"';
                                     } ?> />
            <label for="recvpostemail"><?php echo Lang::txt('GROUP_RECEIVE_EMAILS_DISCUSSION_POSTS'); ?></label>
        </div>
    <?php } ?>

    <?php if ($atLeastOneOption) { ?>
        <div class="submit">
            <input type="submit" class="btn" value="<?php echo Lang::txt('Save'); ?>" />
        </div>
    <?php } else { ?>
        <?php echo Lang::txt('GROUP_MEMBEROPTIONS_NONE'); ?>
    <?php } ?>
</form>
