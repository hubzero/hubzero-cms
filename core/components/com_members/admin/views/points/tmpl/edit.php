<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('MEMBERS') . ': ' . Lang::txt('Manage Points'), 'user.png');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$this->view('_submenu')
     ->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span5">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('User Details'); ?></span></legend>

				<div class="input-wrap">
					<label for="account-uid"><?php echo Lang::txt('User ID:'); ?></label>
					<input type="text" name="account[uid]" id="account-uid" class="required" size="20" maxlength="250" value="<?php echo $this->escape($this->row->uid); ?>" />
					<input type="hidden" name="uid" value="<?php echo $this->escape($this->row->uid); ?>" />
				</div>
				<div class="input-wrap">
					<label for="account-balance"><?php echo Lang::txt('Point Balance:'); ?></label>
					<input type="text" name="account[balance]" id="account-balance" size="20" maxlength="250" value="<?php echo $this->escape($this->row->balance); ?>" />
				</div>
				<div class="input-wrap">
					<label for="account-earnings"><?php echo Lang::txt('Total Earnings:'); ?></label>
					<input type="text" name="account[earnings]" id="account-earnings" size="20" maxlength="250" value="<?php echo $this->escape($this->row->earnings); ?>" />
				</div>
			</fieldset>
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('New Transaction'); ?></span></legend>

				<div class="input-wrap">
					<label for="type"><?php echo Lang::txt('Type:'); ?></label>
					<select name="transaction[type]" id="type">
						<option><?php echo Lang::txt('deposit'); ?></option>
						<option><?php echo Lang::txt('withdraw'); ?></option>
						<option><?php echo Lang::txt('creation'); ?></option>
					</select>
				</div>
				<div class="input-wrap">
					<label for="transaction-amount"><?php echo Lang::txt('Amount:'); ?></label>
					<input type="text" name="transaction[amount]" id="transaction-amount" size="11" maxlength="11" value="" />
				</div>
				<div class="input-wrap">
					<label for="transaction-description"><?php echo Lang::txt('Description:'); ?></label>
					<input type="text" name="transaction[description]" id="transaction-description" size="20" maxlength="250" value="" />
				</div>
				<div class="input-wrap" data-hint="<?php echo Lang::txt('E.g. answers, store, survey, general etc.'); ?>">
					<label for="transaction-category"><?php echo Lang::txt('Category:'); ?></label>
					<input type="text" name="transaction[category]" id="transaction-category" size="20" maxlength="250" value="" />
					<span class="hint"><?php echo Lang::txt('E.g. answers, store, survey, general etc.'); ?></span>
				</div>
				<p>
					<input type="submit" name="submit" value="<?php echo Lang::txt('Save change'); ?>" />
				</p>
			</fieldset>
		</div>
		<div class="col span7">
			<table class="adminlist">
				<caption><?php echo Lang::txt('Transaction History'); ?></caption>
				<thead>
					<tr>
						<th scope="col"><?php echo Lang::txt('Date'); ?></th>
						<th scope="col"><?php echo Lang::txt('Description'); ?></th>
						<th scope="col"><?php echo Lang::txt('Category'); ?></th>
						<th scope="col"><?php echo Lang::txt('Type'); ?></th>
						<th scope="col"><?php echo Lang::txt('Amount'); ?></th>
						<th scope="col"><?php echo Lang::txt('Balance'); ?></th>
					</tr>
				</thead>
				<tbody>
				<?php
				if (count($this->history) > 0) {
					foreach ($this->history as $item)
					{
					?>
					<tr>
						<td><?php echo Date::of($item->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1') . ' ' . Lang::txt('TIME_FORMAT_HZ1')); ?></td>
						<td><?php echo $this->escape($item->description); ?></td>
						<td><?php echo $this->escape($item->category); ?></td>
						<td><?php echo $this->escape($item->type); ?></td>
						<?php if ($item->type == 'withdraw') { ?>
							<td class="aRight"><span class="points-subtract">-<?php echo $this->escape($item->amount); ?></span></td>
						<?php } else if ($item->type == 'hold') { ?>
							<td class="aRight"><span class="points-hold"> <?php echo $this->escape($item->amount); ?></span></td>
						<?php } else { ?>
							<td class="aRight"><span class="points-add">+<?php echo $this->escape($item->amount); ?></span></td>
						<?php } ?>
						<td class="aRight"><?php echo $this->escape($item->balance); ?></td>
					</tr>
					<?php
					}
				} else {
				?>
					<tr>
						<td colspan="6"><?php echo Lang::txt('There is no information available on this user\'s transactions.'); ?></td>
					</tr>
				<?php
				}
				?>
				</tbody>
			</table>
		</div>
	</div>

	<input type="hidden" name="account[id]" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	<?php echo Html::input('token'); ?>
</form>
