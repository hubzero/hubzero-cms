<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$newUrlDesc = Lang::txt('COM_REDIRECT_FIELD_NEW_URL_DESC');
$commentDesc = Lang::txt('COM_REDIRECT_FIELD_COMMENT_DESC');
?>

    <fieldset class="batch">
        <legend><?php echo Lang::txt('COM_REDIRECT_HEADING_UPDATE_LINKS'); ?></legend>

        <div class="grid">
            <div class="col span8">
                <div class="input-wrap" data-hint="<?php echo $newUrlDesc; ?>">
                    <label for="new_url">
                        <?php echo Lang::txt('COM_REDIRECT_FIELD_NEW_URL_LABEL'); ?>
                    </label>
                    <input
                        type="text"
                        name="new_url"
                        id="new_url"
                        value=""
                        size="50"
                        title="<?php echo $newUrlDesc; ?>"
                    />
                </div>
                <div class="input-wrap" data-hint="<?php echo $commentDesc; ?>">
                    <label for="comment">
                        <?php echo Lang::txt('COM_REDIRECT_FIELD_COMMENT_LABEL'); ?>
                    </label>
                    <input
                        type="text"
                        name="comment"
                        id="comment"
                        value=""
                        size="50"
                        title="<?php echo $commentDesc; ?>"
                    />
                </div>
            </div>
            <div class="col span4">
                <div class="input-wrap">
                    <button type="button" id="update-links">
                        <?php echo Lang::txt('COM_REDIRECT_BUTTON_UPDATE_LINKS'); ?>
                    </button>
                </div>
            </div>
        </div>
    </fieldset>
