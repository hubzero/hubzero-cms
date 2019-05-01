<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_PLUGINS'), 'resources');

if ($this->html)
{
	echo $this->html;
}
else
{
	Toolbar::cancel();
	?>
	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
		<p class="warning"><?php echo Lang::txt('COM_RESOURCES_ERROR_PLUGIN_NO_INTERFACE'); ?></p>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="action" value="" />

		<?php echo Html::input('token'); ?>
	</form>
	<?php
}
