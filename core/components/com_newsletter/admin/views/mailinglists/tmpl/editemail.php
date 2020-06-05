<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//set title
$text = ($this->task == 'editEmail' ? Lang::txt('Edit') : Lang::txt('New'));

Toolbar::title(Lang::txt('Newsletter Mailing List Email') . ': ' . $text, 'list');
Toolbar::save('saveemail');
Toolbar::cancel('cancelemail');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span6">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('%s Mailing List Email', $text); ?></legend>
				<table class="admintable">
					<tbody>
						<tr>
							<td class="key" width="200px"><?php echo Lang::txt('Mailing List'); ?>:</td>
							<td><strong><?php echo $this->escape($this->list->name); ?></strong></td>
						</tr>
						<tr>
							<td class="key"><?php echo Lang::txt('Email'); ?>:</td>
							<td><input type="text" name="fields[email]" value="<?php echo $this->escape($this->email->email); ?>" /></td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</div>
		<div class="col span6">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('Date Added'); ?>:</th>
						<td><?php echo gmdate("F d, Y @ g:ia", strtotime($this->email->date_added)); ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('Confirmed?'); ?></th>
						<td><?php echo ($this->email->confirmed) ? Lang::txt('JYes') : Lang::txt('JNo'); ?></td>
					</tr>
					<?php if ($this->email->confirmed) : ?>
						<tr>
							<th><?php echo Lang::txt('Date Confirmed'); ?>:</th>
							<td><?php echo gmdate("F d, Y @ g:ia", strtotime($this->email->date_confirmed)); ?></td>
						</tr>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="fields[mid]" value="<?php echo $this->list->id; ?>" />
	<input type="hidden" name="fields[id]" value="<?php echo $this->email->id; ?>" />
	<input type="hidden" name="task" value="saveemail" />

	<?php echo Html::input('token'); ?>
</form>