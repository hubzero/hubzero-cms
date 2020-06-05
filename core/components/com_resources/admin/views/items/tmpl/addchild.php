<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_ADD_CHILD'), 'resources');
Toolbar::cancel();

Request::setVar('hidemainmenu', 1);
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<h3><?php echo stripslashes($this->parent->title); ?></h3>

	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_RESOURCES_ADD_CHILD_CHOOSE'); ?></span></legend>

		<?php if ($this->getError()) { echo '<p class="error">' . implode('<br />', $this->getErrors()) . '</p>'; } ?>

		<div class="grid">
			<div class="col span6">
				<div class="input-wrap">
					<input type="radio" name="method" id="child_create" value="create" checked="checked" />
					<label for="child_create"><?php echo Lang::txt('COM_RESOURCES_ADD_CHILD_CREATE'); ?></label>
				</div>
			</div>
			<div class="col span6">
				<div class="input-wrap">
					<input type="radio" name="method" id="child_existing" value="existing" />
					<label for="child_existing"><?php echo Lang::txt('COM_RESOURCES_ADD_CHILD_EXISTING'); ?></label>
				</div>
				<div class="input-wrap">
					<label for="childid"><?php echo Lang::txt('COM_RESOURCES_FIELD_RESOURCE_ID'); ?>:</label>
					<input type="text" name="childid" id="childid" value="" />
				</div>
			</div>
		</div>

		<input type="hidden" name="step" value="2" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="pid" value="<?php echo $this->pid; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

		<?php echo Html::input('token'); ?>
	</fieldset>

	<p class="align-center"><input type="submit" name="Submit" value="<?php echo Lang::txt('COM_RESOURCES_NEXT'); ?>" /></p>
</form>
