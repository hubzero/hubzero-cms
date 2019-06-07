<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_KB'), 'storefront');
Toolbar::cancel();

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=' . $this->task . '&step=2'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo Lang::txt('COM_KB_CHOOSE_DELETE_OPTION'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<input type="radio" name="action" id="action_delete" value="deletefaqs" checked="checked" />
					<label for="action_delete"><?php echo Lang::txt('COM_KB_DELETE_ALL'); ?></label>
				</td>
			</tr>
			<tr>
				<td>
					<input type="radio" name="action" id="action_remove" value="removefaqs" />
					<label for="action_remove"><?php echo Lang::txt('COM_KB_DELETE_ONLY_CATEGORY'); ?></label>
				</td>
			</tr>
			<tr>
				<td><input type="submit" name="Submit" value="<?php echo Lang::txt('COM_KB_NEXT'); ?>" /></td>
			</tr>
		</tbody>
	</table>

	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>">
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">

	<?php echo Html::input('token'); ?>
</form>
