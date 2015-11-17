<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('MEMBERS') . ': ' . Lang::txt('Manage Points'), 'user.png');

$this->view('_submenu')
     ->display();
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}

	// do field validation
	if (form.uid.value == ''){
		alert("<?php echo Lang::txt('You must fill in a UID'); ?>");
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span5">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('User Details'); ?></span></legend>

				<div class="input-wrap">
					<label for="account-uid"><?php echo Lang::txt('User ID:'); ?></label>
					<input type="text" name="account[uid]" id="account-uid" size="20" maxlength="250" value="<?php echo $this->escape($this->row->uid); ?>" />
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
						<th><?php echo Lang::txt('Date'); ?></th>
						<th><?php echo Lang::txt('Description'); ?></th>
						<th><?php echo Lang::txt('Category'); ?></th>
						<th><?php echo Lang::txt('Type'); ?></th>
						<th><?php echo Lang::txt('Amount'); ?></th>
						<th><?php echo Lang::txt('Balance'); ?></th>
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
							<td class="aRight"><span style="color: red;">-<?php echo $this->escape($item->amount); ?></span></td>
						<?php } else if ($item->type == 'hold') { ?>
							<td class="aRight"><span style="color: #999;"> <?php echo $this->escape($item->amount); ?></span></td>
						<?php } else { ?>
							<td class="aRight"><span style="color: green;">+<?php echo $this->escape($item->amount); ?></span></td>
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
