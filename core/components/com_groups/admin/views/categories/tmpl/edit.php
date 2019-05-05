<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Groups\Helpers\Permissions::getActions('group');

Toolbar::title($this->group->get('description') . ': ' . Lang::txt('COM_GROUPS_PAGES_CATEGORIES'), 'groups');

if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php require_once dirname(dirname(__DIR__)) . DS . 'pages' . DS . 'tmpl' . DS . 'menu.php'; ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&gid=' . $this->group->cn); ?>" name="adminForm" id="item-form" method="post" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORIES_CATEGORY'); ?></span></legend>

		<div class="input-wrap">
			<label for="field-type"><?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
			<input type="text" name="category[title]" id="field-title" class="required" value="<?php echo $this->escape($this->category->get('title')); ?>" />
		</div>
		<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_COLOR_HINT'); ?>">
			<label for="field-color"><?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_COLOR'); ?>:</label>
			<input maxlength="6" type="text" name="category[color]" id="field-color" value="<?php echo $this->escape($this->category->get('color')); ?>" placeholder="<?php echo Lang::txt('COM_GROUPS_PAGES_CATEGORY_COLOR_PLACEHOLDER'); ?>" />
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