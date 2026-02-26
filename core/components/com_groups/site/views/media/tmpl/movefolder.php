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

<?php $url = Route::url('index.php?option=com_groups&cn=' . $this->group->get('cn')); ?>
<form action="<?php echo $url . '&controller=media&task=domovefolder&no_html=1'; ?>" method="post" class="hubForm">
    <fieldset>
        <legend><?php echo Lang::txt('COM_GROUPS_MEDIA_MOVE_FOLDER'); ?></legend>
        <label>
            <?php echo Lang::txt('COM_GROUPS_MEDIA_MOVE_CURRENT_FOLDER'); ?>:
            <input type="text" name="current" value="<?php echo $this->escape($this->folder); ?>" readonly="readonly" />
        </label>
        <label>
            <?php echo Lang::txt('COM_GROUPS_MEDIA_MOVE_MOVE_TO'); ?>:
            <?php echo $this->folderList; ?>
        </label>
        <p class="controls">
            <?php echo Html::input('token'); ?>
            <button type="submit" class="btn icon-move"><?php echo Lang::txt('COM_GROUPS_MEDIA_MOVE'); ?></button>
        </p>
    </fieldset>
</form>