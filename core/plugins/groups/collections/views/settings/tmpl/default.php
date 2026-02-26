<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();
?>

<?php if ($this->getError()) { ?>
    <p class="error"><?php echo $this->getError(); ?></p>
<?php } ?>

    <?php
    $formAction = Route::url(
        'index.php?option=' . $this->option
        . '&cn=' . $this->group->get('cn')
        . '&active=collections&action=savesettings'
    );
    $collAllSelected = !$this->params->get('create_collection', 1)
        ? ' selected="selected"'
        : '';
    $collMgrSelected = ($this->params->get('create_collection', 1) == 1)
        ? ' selected="selected"'
        : '';
    $collAllTxt = Lang::txt(
        'PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_COLLECTIONS_ALL'
    );
    $collMgrTxt = Lang::txt(
        'PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_COLLECTIONS_MANAGERS'
    );
    ?>
    <form action="<?php echo $formAction; ?>"
        method="post"
        id="hubForm"
        class="full">

        <fieldset class="settings">
            <legend><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS'); ?></legend>

            <label for="param-posting">
                <?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_COLLECTIONS'); ?>
                <select name="params[create_collection]" id="param-create_collection">
                    <option value="0"<?php echo $collAllSelected; ?>><?php echo $collAllTxt; ?></option>
                    <option value="1"<?php echo $collMgrSelected; ?>><?php echo $collMgrTxt; ?></option>
                </select>
            </label>

            <p class="info">
                <?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_COLLECTIONS_INFO'); ?>
            </p>
        </fieldset>
        <fieldset class="settings">
            <legend><?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_POSTS'); ?></legend>

            <?php
            $postAllSelected = !$this->params->get('create_post', 0)
                ? ' selected="selected"'
                : '';
            $postMgrSelected = ($this->params->get('create_post', 0) == 1)
                ? ' selected="selected"'
                : '';
            $postAllTxt = Lang::txt(
                'PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_POSTS_ALL'
            );
            $postMgrTxt = Lang::txt(
                'PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_POSTS_MANAGERS'
            );
            ?>
            <label for="param-posting">
                <?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_POSTS'); ?>
                <select name="params[create_post]" id="param-create_post">
                    <option value="0"<?php echo $postAllSelected; ?>><?php echo $postAllTxt; ?></option>
                    <option value="1"<?php echo $postMgrSelected; ?>><?php echo $postMgrTxt; ?></option>
                </select>
            </label>

            <p class="info">
                <?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SETTINGS_CREATE_POSTS_INFO'); ?>
            </p>
        </fieldset>
        <div class="clear"></div>

        <input type="hidden" name="cn" value="<?php echo $this->group->get('cn'); ?>" />
        <input type="hidden" name="process" value="1" />
        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="active" value="collections" />
        <input type="hidden" name="action" value="savesettings" />

        <?php echo Html::input('token'); ?>

        <p class="submit">
            <input type="submit"
                class="btn btn-success"
                value="<?php echo Lang::txt('PLG_GROUPS_COLLECTIONS_SAVE'); ?>"/>

            <?php
            $cancelUrl = Route::url(
                'index.php?option=' . $this->option
                . '&cn=' . $this->group->get('cn')
                . '&active=collections'
            );
            ?>
            <a class="btn btn-secondary"
                href="<?php echo $cancelUrl; ?>">
                <?php echo Lang::txt('JCANCEL'); ?>
            </a>
        </p>
    </form>
