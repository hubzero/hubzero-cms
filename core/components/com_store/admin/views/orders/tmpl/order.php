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

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Store\Helpers\Permissions::getActions('component');

$text = (!$this->store_enabled) ? ' (store is disabled)' : '';
Toolbar::title(Lang::txt('COM_STORE_MANAGER') . $text, 'store');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('order');

$order_date = (intval($this->row->ordered) <> 0) ? Date::of($this->row->ordered)->toLocal(Lang::txt('COM_STORE_DATE_FORMAT_HZ1')) : NULL ;
$status_changed = (intval($this->row->status_changed) <> 0) ? Date::of($this->row->status_changed)->toLocal(Lang::txt('COM_STORE_DATE_FORMAT_HZ1')) : NULL;

$class  = 'completed-item';
switch ($this->row->status)
{
	case '1':
		$status = strtolower(Lang::txt('COM_STORE_COMPLETED'));
	break;
	case '0':
	default:
		$status = strtolower(Lang::txt('COM_STORE_NEW'));
		$class  = 'new-item';
	break;
	case '2':
		$status = strtolower(Lang::txt('COM_STORE_CANCELLED'));
		$class  = 'cancelled-item';
	break;
}

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">

<?php if (isset($this->row->id)) { ?>
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_STORE_ORDER') . ' #' . $this->row->id . ' ' . Lang::txt('COM_STORE_DETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_STORE_ITEMS'); ?>:</label><br />
					<p><?php
						$k=1;
						foreach ($this->orderitems as $o)
						{
							$avail = ($o->available) ?  'available' : 'unavailable';
							$html  = $k . ') ';
							$html .= $o->title . ' (x' . $o->quantity . ')';
							$html .= ($o->selectedsize) ? '- size ' . $o->selectedsize : '';
							$html .= '<br /><span style="color:#999;">' . Lang::txt('COM_STORE_ITEM') . ' ' . Lang::txt('COM_STORE_STORE') . ' ' . Lang::txt('COM_STORE_ID') . ' #' . $o->itemid . '. ' . Lang::txt('COM_STORE_STATUS') . ': ' . $avail;
							if (!$o->sizeavail) {
								$html .= Lang::txt('COM_STORE_WARNING_NOT_IN_STOCK');
							}
							$html .= '. ' . Lang::txt('COM_STORE_CURRENT_PRICE') . ': ' . $o->price . '</span><br />';
							$k++;
							echo $html;
						}
					?></p>
				</div>
				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_STORE_SHIPPING'); ?>:</label><br />
					<pre><?php echo $this->escape(stripslashes($this->row->details)); ?></pre>
				</div>
				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_STORE_PROFILE_INFO'); ?>:</label><br />
					<?php echo Lang::txt('COM_STORE_LOGIN'); ?>: <?php echo $this->escape(stripslashes($this->customer->get('username'))); ?> <br />
					<?php echo Lang::txt('COM_STORE_NAME'); ?>: <?php echo $this->escape(stripslashes($this->customer->get('name'))); ?> <br />
					<?php echo Lang::txt('COM_STORE_EMAIL'); ?>: <?php echo $this->escape(stripslashes($this->customer->get('email'))); ?>
				</div>
				<div class="input-wrap">
					<label for="field-notes"><?php echo Lang::txt('COM_STORE_ADMIN_NOTES'); ?>:</label><br />
					<textarea name="notes" id="field-notes" cols="50" rows="10"><?php echo $this->escape(stripslashes($this->row->notes)); ?></textarea>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_STORE_PROCESS_ORDER'); ?></span></legend>
				<div class="input-wrap">
					<table>
						<tbody>
							<tr>
								<th><?php echo Lang::txt('COM_STORE_STATUS'); ?>:</th>
								<td><span class="<?php echo $class; ?>"><?php echo $status; ?></span></td>
							</tr>
							<tr>
								<th><?php echo Lang::txt('COM_STORE_ORDER_PLACED'); ?>:</th>
								<td><?php echo $order_date ?></td>
							</tr>
							<?php if ($this->row->status != 0) { ?>
								<tr>
									<th><?php echo Lang::txt('COM_STORE_ORDER') . ' ' . $status; ?>:</th>
									<td><?php echo $status_changed ?></td>
								</tr>
							<?php } ?>
							<tr>
								<th><?php echo Lang::txt('COM_STORE_ORDER') . ' ' . Lang::txt('COM_STORE_TOTAL'); ?>:</th>
								<td><label><?php if ($this->row->status == 0) { ?>
										<input type="text" name="total" value="<?php echo $this->row->total ?>"  />
									<?php } else { ?>
										<?php echo $this->row->total ?>
										<input type="hidden" name="total" value="<?php echo $this->row->total ?>"  />
									<?php } ?>
									</label>
								</td>
							</tr>
							<tr>
								<th><?php echo Lang::txt('COM_STORE_CURRENCY'); ?>:</th>
								<td><?php echo Lang::txt('COM_STORE_POINTS'); ?></td>
							</tr>
							<tr>
								<th><?php echo Lang::txt('COM_STORE_CURRENT_BALANCE'); ?>:</th>
								<td><?php echo Lang::txt('<strong>%s</strong> points', $this->funds); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</fieldset>
			<?php if ($this->row->status == 0) { ?>
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_STORE_MANAGE_ORDER'); ?></legend>
				<div class="input-wrap">
					<input type="radio" name="action" id="field-action-message" value="message" />
					<label for="field-action-message"><?php echo Lang::txt('COM_STORE_ORDER_ON_HOLD'); ?></label>
				</div>
				<div class="input-wrap">
					<input type="radio" name="action" id="field-action-complete_order" value="complete_order" />
					<label for="field-action-complete_order"><?php echo Lang::txt('COM_STORE_PROCESS_TRANSACTION'); ?></label>
				</div>
				<div class="input-wrap">
					<input type="radio" name="action" id="field-action-cancel_order" value="cancel_order" />
					<label for="field-action-cancel_order"><?php echo Lang::txt('COM_STORE_RELEASE_FUNDS'); ?></label>
				</div>

				<div class="input-wrap">
					<label for="field-message"><?php echo Lang::txt('COM_STORE_SEND_A_MSG'); ?>:</label><br />
					<textarea name="message" id="field-message" cols="30" rows="5"></textarea>
				</div>
			</fieldset>
			<?php } ?>
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="task" value="save" />
<?php  } // end if id exists ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

	<?php echo Html::input('token'); ?>
</form>
