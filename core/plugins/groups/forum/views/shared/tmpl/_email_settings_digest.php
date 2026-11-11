<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

defined('_HZEXEC_') or die();

$base = $this->base;

// The attribute that marks the current value in the form fields below
$recvEmailOptionValueChecked = function ($value) {
    if ($this->recvEmailOptionValue == $value) {
        return ' checked="checked"';
    }

    return '';
};

$recvEmailOptionValueSelected = function ($value) {
    if ($this->recvEmailOptionValue == $value) {
        return ' selected="selected"';
    }

    return '';
};
?>

<p>
    <?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_CURRENT_SETTINGS'); ?>
    <?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_CURRENT_SETTINGS_' . $this->recvEmailOptionValue); ?>
    <br />
    <a href="#" class="edit-forum-options">
        <?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_CHANGE_SETTINGS'); ?>
    </a>
</p>
<div class="edit-forum-options-panel">
    <form method="post" action="<?php echo Route::url($base); ?>" id="forum-options-extended">
        <div>
            <input type="checkbox"
                class="edit-forum-options-receive-emails"
                value="1"
                name="recvpostemail"<?php echo $this->recvEmailOptionValue >= 1 ? ' checked="checked"' : ''; ?> />
            <label>
                <?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_TOGGLE'); ?>
            </label>
        </div>
        <div class="edit-forum-options-as">
            <?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_INTERVAL'); ?>
        </div>
        <div>
            <input type="radio"
                name="recvpostemail"
                class="edit-forum-options-immediate"
                value="1"<?= $recvEmailOptionValueChecked(1) ?><?= $this->recvEmailOptionValue == 0 ? ' disabled="disabled"' : '' ?> />
            <label>
                <?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_IMMEDIATELY'); ?>
            </label>
        </div>
        <div>
            <input type="radio"
                name="recvpostemail"
                class="edit-forum-options-digest"
                value="2"<?= $this->recvEmailOptionValue >= 2 ? ' checked="checked"' : '' ?><?= $this->recvEmailOptionValue == 0 ? ' disabled="disabled"' : '' ?> />
            <?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_AS_A'); ?>
            <select name="recvpostemail"
                class="edit-forum-options-frequency"<?= $this->recvEmailOptionValue < 2 ? ' disabled="disabled"' : '' ?>>
                <option value="2"<?= $recvEmailOptionValueSelected(2) ?>><?= Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_DAILY') ?></option>
                <option value="3"<?= $recvEmailOptionValueSelected(3) ?>><?= Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_WEEKLY') ?></option>
                <option value="4"<?= $recvEmailOptionValueSelected(4) ?>><?= Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_MONTHLY') ?></option>
            </select>
            <?php echo Lang::txt('PLG_GROUPS_FORUM_EMAIL_POSTS_DIGEST'); ?>
        </div>

        <input type="hidden" name="action" value="savememberoptions" />
        <input type="hidden" name="memberoptionid" value="<?php echo $this->recvEmailOptionID; ?>" />
        <?php echo Html::input('token'); ?>

        <div class="edit-forum-options-actions">
            <input type="submit" class="btn btn-success" value="<?php echo Lang::txt('PLG_GROUPS_FORUM_SAVE'); ?>" />
            <input type="button" class="btn edit-forum-options-cancel" value="<?php echo Lang::txt('JCANCEL'); ?>" />
        </div>
    </form>
</div>
