<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_WIKI').': '.Lang::txt('COM_WIKI_REVISION') . ': ' . Lang::txt('COM_WIKI_DELETE'), 'wiki.png');
Toolbar::cancel();

?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" class="editform" id="item-form">
	<table class="adminform">
		<tbody>
			<tr>
				<td><input type="radio" name="confirm" id="confirm" value="1" /> <label for="confirm"><?php echo Lang::txt('COM_WIKI_CONFIRM_DELETE'); ?></label></td>
				<td><input type="submit" name="Submit" value="<?php echo Lang::txt('COM_WIKI_NEXT'); ?>" /></td>
			</tr>
		</tbody>
	</table>
	<input type="hidden" name="step" value="2" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<?php foreach ($this->ids as $id) { ?>
		<input type="hidden" name="id[]" value="<?php echo $id; ?>" />
	<?php } ?>
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

	<?php echo Html::input('token'); ?>
</form>