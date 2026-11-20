<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access
defined('_HZEXEC_') or die();

/**
 * What this template is given.
 *
 * @var \Hubzero\Component\View&object{folder: mixed, folderList: mixed, group: mixed} $this
 */
?>

<?php
$url = Route::url(
    'index.php?option=com_groups&cn=' . $this->group->get('cn')
        . '&controller=media&task=domovefolder&no_html=1',
);
?>
<form action="<?php echo $url; ?>" method="post" class="hubForm">
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