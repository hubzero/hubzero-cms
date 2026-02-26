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

$canDo = \Components\Groups\Helpers\Permissions::getActions('group');

Toolbar::title($this->group->get('description') . ': ' . Lang::txt('COM_GROUPS_PAGES_CATEGORIES'), 'groups');

if ($canDo->get('core.edit')) {
    Toolbar::save();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php require_once dirname(dirname(__DIR__)) . DS . 'pages' . DS . 'tmpl' . DS . 'menu.php'; ?>

<?php
$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&gid=' . $this->group->cn
);
$invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
?>
<form action="<?php echo $formAction; ?>"
    name="adminForm"
    id="item-form"
    method="post"
    class="editform form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
>
    <fieldset class="adminform">
        <legend><span><?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORIES_CATEGORY'); ?></span></legend>

        <div class="input-wrap">
            <?php $titleLabel = Lang::txt('COM_GROUPS_PAGES_CATEGORY_TITLE'); ?>
            <?php $requiredTxt = Lang::txt('JOPTION_REQUIRED'); ?>
            <label for="field-type">
                <?php echo $titleLabel; ?>: <span class="required"><?php echo $requiredTxt; ?></span>
            </label>
            <input type="text"
                name="category[title]"
                id="field-title"
                class="required"
                value="<?php echo $this->escape($this->category->get('title')); ?>"
            />
        </div>
        <div class="input-wrap" data-hint="<?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_COLOR_HINT'); ?>">
            <label for="field-color"><?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_COLOR'); ?>:</label>
            <?php $colorPlaceholder = Lang::txt('COM_GROUPS_PAGES_CATEGORY_COLOR_PLACEHOLDER'); ?>
            <input maxlength="6"
                type="text"
                name="category[color]"
                id="field-color"
                value="<?php echo $this->escape($this->category->get('color')); ?>"
                placeholder="<?php echo $colorPlaceholder; ?>"
            />
        </div>
    </fieldset>

    <input type="hidden" name="category[id]" value="<?php echo $this->category->get('id'); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
    <input type="hidden" name="gid" value="<?php echo $this->group->cn; ?>" />
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo Html::input('token'); ?>
</form>