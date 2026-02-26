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

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Citations\Helpers\Permissions::getActions('sponsor');

$text = ($this->task == 'edit' ? Lang::txt('EDIT') : Lang::txt('NEW'));

Toolbar::title(Lang::txt('CITATIONS') . ' ' . Lang::txt('CITATION_SPONSORS') . ': ' . $text, 'citation');
if ($canDo->get('core.edit')) {
    Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('sponsor');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php
$actionUrl = Route::url('index.php?option=' . $this->option);
$invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
?>
<form
    action="<?php echo $actionUrl; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
>
    <fieldset class="adminform">
        <legend><span><?php echo Lang::txt('CITATION_SPONSORS'); ?></span></legend>

        <div class="input-wrap">
            <?php
            $sponsorVal = $this->escape(
                stripslashes($this->sponsor->get('sponsor'))
            );
            ?>
            <label for="field-sponsor">
                <?php echo Lang::txt('CITATION_SPONSORS_NAME'); ?>
            </label>
            <input
                type="text"
                name="sponsor[sponsor]"
                id="field-sponsor"
                value="<?php echo $sponsorVal; ?>"
            />
        </div>
        <div class="input-wrap">
            <?php
            $linkVal = $this->escape(
                stripslashes($this->sponsor->get('link'))
            );
            ?>
            <label for="field-link">
                <?php echo Lang::txt('CITATION_SPONSORS_LINK'); ?>
            </label>
            <input
                type="text"
                name="sponsor[link]"
                id="field-link"
                value="<?php echo $linkVal; ?>"
            />
        </div>
        <div class="input-wrap">
            <?php
            $imageVal = $this->escape(
                stripslashes($this->sponsor->get('image'))
            );
            ?>
            <label for="field-image">
                <?php echo Lang::txt('CITATION_SPONSORS_IMAGE'); ?>
            </label>
            <input
                type="text"
                name="sponsor[image]"
                id="field-image"
                value="<?php echo $imageVal; ?>"
            />
        </div>
    </fieldset>

    <input type="hidden" name="sponsor[id]" value="<?php echo $this->sponsor->get('id'); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
