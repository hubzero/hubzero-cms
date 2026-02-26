<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(
    Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_TAGS') . ' #' . $this->row->id,
    'resources'
);
Toolbar::save();
Toolbar::cancel();

$this->js();
?>

<?php $actionUrl = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>
<form action="<?php echo $actionUrl; ?>" method="post" name="adminForm">
    <fieldset class="adminform">
        <legend><span><?php echo Lang::txt('COM_RESOURCES_TAGS_CREATE'); ?></span></legend>

        <p><?php echo Lang::txt('COM_RESOURCES_TAGS_CREATE_HELP'); ?></p>

        <div class="input-wrap">
            <label for="tags-men"><?php echo Lang::txt('COM_RESOURCES_TAGS_FIELD_NEW_TAGS'); ?>:</label>
            <input type="text" name="tags" id="tags-men" size="65" value="" />
        </div>
    </fieldset>

    <fieldset class="adminform">
        <legend><span><?php echo Lang::txt('COM_RESOURCES_TAGS_EXISTING'); ?></span></legend>

        <p><?php echo Lang::txt('COM_RESOURCES_TAGS_EXISTING_HELP'); ?></p>

        <table class="adminlist">
            <thead>
                <tr>
                    <th></th>
                    <th><?php echo Lang::txt('COM_RESOURCES_TAGS_RAW_TAG'); ?></th>
                    <th><?php echo Lang::txt('COM_RESOURCES_TAGS_TAG'); ?></th>
                    <th><?php echo Lang::txt('COM_RESOURCES_TAGS_ADMIN'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php
            $k = 0;
            $i = 0;
            foreach ($this->tags as $tag) {
                ?>
                <tr class="<?php echo "row$k"; ?>">
                    <?php $isChecked = in_array($tag->tag, $this->mytagarray) ? ' checked="checked"' : ''; ?>
                    <td><input type="checkbox"
                               name="tgs[]"
                               id="cb<?php echo $i; ?>"<?php echo $isChecked; ?>
                               value="<?php echo $this->escape($tag->tag); ?>"
                               /></td>
                    <?php $tagStr = stripslashes($tag->tag); ?>
                    <td><a href="#"
                           class="addtag"
                           data-tag="<?php echo $tagStr; ?>"><?php
                               echo $this->escape($tag->raw_tag);
                            ?></a></td>
                    <td><a href="#"
                           class="addtag"
                           data-tag="<?php echo $tagStr; ?>"><?php
                               echo $this->escape($tag->tag);
                            ?></a></td>
                    <td><?php if ($tag->admin == 1) {
                        echo '<span class="check">admin</span>';
                        } ?></td>
                </tr>
                <?php
                $k = 1 - $k;
                $i++;
            }
            ?>
            </tbody>
        </table>
    </fieldset>

    <input type="hidden" name="id" value="<?php echo $this->id; ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
