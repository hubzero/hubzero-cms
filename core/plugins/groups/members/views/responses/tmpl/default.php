<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Plugin\View&object{group: mixed, option: mixed, responses: mixed, users: mixed} $this
 */

// Always offer one empty row for adding a response
$responses   = $this->responses;
$responses[] = ['title' => '', 'text' => ''];

$backUrl = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=members';
if (count($this->users)) {
    $backUrl .= '&action=deny';
    foreach ($this->users as $user) {
        $backUrl .= '&users[]=' . (int) $user;
    }
} else {
    $backUrl .= '&filter=pending';
}
?>
<?php if ($this->getError()) { ?>
    <p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>

<ul id="page_options">
    <li>
        <a class="icon-browse btn" href="<?php echo Route::url($backUrl); ?>">
            <?php echo Lang::txt(count($this->users) ? 'PLG_GROUPS_MEMBERS_DENY_MEMBERSHIP' : 'PLG_GROUPS_MEMBERS'); ?>
        </a>
    </li>
</ul>

<?php
$actionUrl = Route::url('index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=members');
?>
<form action="<?= $actionUrl ?>" method="post" id="hubForm" class="full deny-responses">
    <div class="explaination">
        <p class="info"><?php echo Lang::txt('PLG_GROUPS_MEMBERS_DENY_RESPONSES_EXPLANATION'); ?></p>
    </div>
    <fieldset>
        <legend><?php echo Lang::txt('PLG_GROUPS_MEMBERS_DENY_RESPONSES'); ?></legend>

        <div class="deny-response-list" data-next="<?php echo count($responses); ?>">
            <?php foreach ($responses as $i => $response) : ?>
                <fieldset class="deny-response">
                    <label for="responses-<?php echo $i; ?>-title">
                        <?php echo Lang::txt('PLG_GROUPS_MEMBERS_DENY_RESPONSE_TITLE'); ?>
                        <input type="text" name="responses[<?= $i ?>][title]" id="responses-<?= $i ?>-title"
                            maxlength="100" value="<?= $this->escape($response['title']) ?>" />
                    </label>
                    <label for="responses-<?php echo $i; ?>-text">
                        <?php echo Lang::txt('PLG_GROUPS_MEMBERS_DENY_RESPONSE_TEXT'); ?>
                        <textarea name="responses[<?= $i ?>][text]" id="responses-<?= $i ?>-text" rows="5"
                            cols="50"><?= $this->escape($response['text']) ?></textarea>
                    </label>
                    <p class="deny-response-remove">
                        <?php
                        $buttonText = Lang::txt('PLG_GROUPS_MEMBERS_DENY_RESPONSE_REMOVE');
                        ?>
                        <button type="button" class="btn btn-danger icon-trash"><?= $buttonText ?></button>
                    </p>
                </fieldset>
            <?php endforeach; ?>
        </div>

        <p>
            <button type="button"
                class="btn icon-add deny-response-add"><?= Lang::txt('PLG_GROUPS_MEMBERS_DENY_RESPONSE_ADD') ?></button>
        </p>
    </fieldset>
    <div class="clear"></div>

    <?php foreach ($this->users as $user) : ?>
        <input type="hidden" name="users[]" value="<?php echo (int) $user; ?>" />
    <?php endforeach; ?>
    <input type="hidden" name="cn" value="<?php echo $this->escape($this->group->get('cn')); ?>" />
    <input type="hidden" name="active" value="members" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="action" value="savedenyresponses" />
    <?php echo Html::input('token'); ?>

    <p class="submit">
        <input type="submit" value="<?php echo Lang::txt('PLG_GROUPS_MEMBERS_SUBMIT'); ?>" />
    </p>
</form>
