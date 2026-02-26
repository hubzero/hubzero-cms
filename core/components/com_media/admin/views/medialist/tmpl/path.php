<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

?>

<?php
$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=medialist&file=' . urlencode($this->file)
);
?>
<form action="<?php echo $formAction; ?>"
    id="component-form" method="post"
    name="adminForm" autocomplete="off">
    <fieldset>
        <h2 class="modal-title">
            <?php echo Lang::txt('COM_MEDIA_FILE_LINK'); ?>
        </h2>
    </fieldset>
    <div class="manager">
        <input type="text" value="<?php echo $this->escape($this->file); ?>" name="path" />

        <input type="hidden" name="task" value="" />
        <?php echo Html::input('token'); ?>
        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    </div>
</form>
