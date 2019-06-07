<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_STOREFRONT') . ': Delete SKU', 'storefront');
Toolbar::cancel();

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=' . $this->task . '&step=2'); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo Lang::txt('Are you sure you want to delete all selected options?'); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<input type="checkbox" name="delete" value="delete"
						   id="field-delete">
					<label for="field-delete"><?php echo Lang::txt('I\'m positive. Go ahead and do the delete.'); ?></label>
				</td>
			</tr>
			<tr>
				<td><input type="submit" name="Submit" value="<?php echo Lang::txt('COM_STOREFRONT_NEXT'); ?>" /></td>
			</tr>
		</tbody>
	</table>

	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<?php
		foreach ($this->oId as $oId)
		{
			echo '<input type="hidden" name="oId[]" value="' . $oId . '" />';
		}
	?>
	<input type="hidden" name="ogId" value="<?php echo $this->ogId; ?>">
	<input type="hidden" name="option" value="<?php echo $this->option; ?>">
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>">

	<?php echo Html::input('token'); ?>
</form>
