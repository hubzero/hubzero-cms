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

defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_STOREFRONT') . ': Delete Products', 'storefront');
Toolbar::cancel();

?>

<?php
$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&task=' . $this->task . '&step=2'
);
?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="adminForm">
    <table class="adminlist">
        <thead>
        <tr>
            <th><?php echo Lang::txt('Are you sure you want to delete all selected SKUs?'); ?></th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>
                <input type="checkbox" name="delete" value="1" id="field-delete" />
                <label for="field-delete">I'm positive. Go ahead and do the delete.</label>
            </td>
        </tr>
        <tr>
            <td><input type="submit" name="Submit" value="<?php echo Lang::txt('COM_STOREFRONT_NEXT'); ?>" /></td>
        </tr>
        </tbody>
    </table>

    <input type="hidden" name="task" value="<?php echo $this->task; ?>" />
    <?php
    foreach ($this->pIds as $pId) {
        echo '<input type="hidden" name="pIds[]" value="' . $pId . '" />';
    }
    ?>
    <input type="hidden" name="option" value="<?php echo $this->option; ?>">
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>">

    <?php echo Html::input('token'); ?>
</form>
