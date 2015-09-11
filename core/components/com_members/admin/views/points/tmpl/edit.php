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

Toolbar::title( Lang::txt( 'MEMBERS' ).': Manage Points', 'user.png' );

?>

<?php
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
		alert( 'You must fill in a UID' );
	} else {
		submitform( pressbutton );
	}
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="col width-40 fltlft">
		<fieldset>
			<legend><span>User Details</span></legend>
			<table class="adminform">
				<tbody>
					<tr>
						<td><label for="uid">UID:</label></td>
						<td><input type="text" name="uid" id="uid" size="20" maxlength="250" value="<?php echo $this->row->uid; ?>" /></td>
					</tr>
					<tr>
						<td><label for="raw_tag">Point Balance:</label></td>
						<td><input type="text" name="balance" id="balance" size="20" maxlength="250" value="<?php echo $this->row->balance; ?>" /></td>
					</tr>
					<tr>
						<td><label for="alias">Total Earnings:</label></td>
						<td><input type="text" name="earnings" id="earnings" size="20" maxlength="250" value="<?php echo $this->row->earnings; ?>" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		<fieldset>
			<legend><span>New Transaction</span></legend>

				<table class="adminform">
			 <tbody>
			  <tr>
			   <td><label for="type">Type:</label></td>
			   <td><select name="type" id="type">
					<option>deposit</option>
					<option>withdraw</option>
					<option>creation</option>
			   </select></td>
			  </tr>
			  <tr>
			   <td><label for="amount">Amount:</label></td>
			   <td><input type="text" name="amount" id="amount" size="11" maxlength="11" value="" /></td>
			  </tr>
			  <tr>
			   <td><label for="description">Description:</label></td>
			   <td><input type="text" name="description" id="description" size="20" maxlength="250" value="" /></td>
			  </tr>
              <tr>
			   <td><label for="category">Category:</label></td>
			   <td><input type="text" name="category" id="category" size="20" maxlength="250" value="" /> <span style="display:block;margin-bottom:1em;">E.g. answers, store, survey, general etc.</span>
               <input type="submit" name="submit" value="Save changes" style="margin-bottom:1.5em;" />
               </td>
			  </tr>
			 </tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-60 fltrt">
		<table class="adminlist">
			<caption>Transaction History</caption>
			<thead>
				<tr>
					<th>Date</th>
					<th>Description</th>
					<th>Category</th>
					<th>Type</th>
					<th>Amount</th>
					<th>Balance</th>
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
					<td><?php echo $item->description; ?></td>
					<td><?php echo $item->category; ?></td>
					<td><?php echo $item->type; ?></td>
<?php if ($item->type == 'withdraw') { ?>
					<td class="aRight"><span style="color: red;">-<?php echo $item->amount; ?></span></td>
<?php } else if ($item->type == 'hold') { ?>
					<td class="aRight"><span style="color: #999;"> <?php echo $item->amount; ?></span></td>
<?php } else { ?>
					<td class="aRight"><span style="color: green;">+<?php echo $item->amount; ?></span></td>
<?php } ?>
					<td class="aRight"><?php echo $item->balance; ?></td>
				</tr>
<?php
		}
	} else {
?>
				<tr>
					<td colspan="6">There is no information available on this user's transactions.</td>
				</tr>
<?php
	}
?>
			</tbody>
		</table>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
	<?php echo Html::input('token'); ?>
</form>
