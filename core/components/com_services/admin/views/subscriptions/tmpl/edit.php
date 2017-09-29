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

Toolbar::title(Lang::txt('COM_SERVICES') . ': ' . Lang::txt('COM_SERVICES_SUCSCRIPTIONS'), 'services');
Toolbar::save();
Toolbar::cancel();

$added   = (intval($this->subscription->added) <> 0)   ? Date::of($this->subscription->added)->toLocal(Lang::txt('DATE_FORMAT_HZ1'))   : NULL;
$updated = (intval($this->subscription->updated) <> 0) ? Date::of($this->subscription->updated)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) : Lang::txt('COM_SERVICES_NOT_APPLICABLE');
$expires = (intval($this->subscription->expires) <> 0) ? Date::of($this->subscription->expires)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) : Lang::txt('COM_SERVICES_NOT_APPLICABLE');

$status = '';
$pending = $this->subscription->currency . ' ' . $this->subscription->pendingpayment;
$now = Date::toSql();

$onhold_msg = ($this->subscription->status==2) ? Lang::txt('COM_SERVICES_SEND_MESSAGE') : Lang::txt('COM_SERVICES_SUBSCRIPTION_ON_HOLD');

switch ($this->subscription->status)
{
	case '1':
		$status = ($this->subscription->expires > $now) ? '<span style="color:#197f11;">' . strtolower(Lang::txt('COM_SERVICES_STATE_ACTIVE')) . '</span>' : '<span style="color:#ef721e;">' . strtolower(Lang::txt('COM_SERVICES_STATE_EXPIRED')) . '</span>';
		break;
	case '0':
		$status = '<span style="color:#ff0000;">' . strtolower(Lang::txt('COM_SERVICES_STATE_PENDING')) . '</span>';
		break;
	case '2':
		$status = '<span style="color:#999;">' . strtolower(Lang::txt('COM_SERVICES_STATE_CANCELED')) . '</span>';
		$pending .= ($this->subscription->pendingpayment) ? ' (' . Lang::txt('COM_SERVICES_REFUND') . ')' : '';
		break;
}

$priceline  = Lang::txt('COM_SERVICES_PRICE_PER_UNIT', $this->subscription->currency . ' ' . $this->subscription->unitprice, $this->subscription->unitmeasure);
$priceline .= ($this->subscription->pointsprice > 0) ? Lang::txt('COM_SERVICES_OR_POINTS', $this->subscription->pointsprice) : '';

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

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">

<?php if (isset($this->subscription->id)) { ?>
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_SERVICES_SUBSCRIPTION_NUM', $this->subscription->id, $this->subscription->code); ?></span></legend>

				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_SERVICES_FIELD_SERVICE'); ?>:</label><br />
					<?php echo $this->subscription->title . ' - <strong>' . $priceline . '</strong>'; ?>
				</div>

				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_SERVICES_FIELD_PROFILE'); ?>:</label><br />
					<?php echo Lang::txt('Login'); ?>: <?php echo $this->customer->get('username') ?> <br />
					<?php echo Lang::txt('Name'); ?>: <?php echo $this->customer->get('name') ?> <br />
					<?php echo Lang::txt('Email'); ?>: <?php echo $this->customer->get('email') ?> <br />
					<?php echo Lang::txt('Tel.'); ?>: <?php echo $this->customer->get('phone') ?>
				</div>

				<div class="input-wrap">
					<label><?php echo Lang::txt('COM_SERVICES_FIELD_EMPLOYER'); ?>:</label><br />
					<?php echo Lang::txt('Company Name'); ?>: <?php echo $this->subscription->companyName; ?> <br />
					<?php echo Lang::txt('Company Location'); ?>: <?php echo $this->subscription->companyLocation; ?> <br />
					<?php echo Lang::txt('Company URL'); ?>: <?php echo $this->subscription->companyWebsite; ?>
				</div>

				<div class="input-wrap">
					<label for="field-notes"><?php echo Lang::txt('COM_SERVICES_FIELD_NOTES'); ?>:</label><br />
					<textarea name="notes" id="field-notes" cols="50" rows="10"><?php echo $this->escape(stripslashes($this->subscription->notes)); ?></textarea>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('COM_SERVICES_COL_STATUS'); ?>:</th>
						<td><?php echo $status ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_SERVICES_COL_ADDED'); ?>:</th>
						<td><?php echo $added ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_SERVICES_COL_EXPIRES'); ?>:</th>
						<td><?php echo $expires ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_SERVICES_COL_LAST_UPDATED'); ?>:</th>
						<td><?php echo $updated ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_SERVICES_COL_TOTAL_PAID'); ?>:</th>
						<td><?php echo $this->subscription->totalpaid; ?> <?php if ($this->subscription->usepoints) { echo Lang::txt('COM_SERVICES_POINTS'); } else { echo $this->subscription->currency; } ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_SERVICES_COL_PENDING_PAYMENT'); ?>:</th>
						<td><?php echo $this->subscription->pendingpayment; ?> <?php if ($this->subscription->usepoints) { echo Lang::txt('COM_SERVICES_POINTS'); } else { echo $this->subscription->currency; } ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_SERVICES_COL_ACTIVE_UNITS'); ?>:</th>
						<td><?php echo $this->subscription->units; ?></td>
					</tr>
					<tr>
						<th><?php echo Lang::txt('COM_SERVICES_COL_PENDING_UNITS'); ?>:</th>
						<td><?php echo $this->subscription->pendingunits; ?></td>
					</tr>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_SERVICES_FIELDSET_MANAGE'); ?></span></legend>

				<div class="input-wrap">
					<input type="radio" name="action" id="field-action-message" value="message" />
					<label for="field-action-message"><?php echo $onhold_msg; ?></label>
				</div>
			<?php if ($this->subscription->status == 2) { ?>
				<?php if ($this->subscription->pendingpayment > 0) { ?>
					<div class="input-wrap">
						<input type="radio" name="action" id="field-action-refund" value="refund" />
						<label for="field-action-refund"><?php echo Lang::txt('COM_SERVICES_FIELD_PROCESS_REFUND'); ?></label>
					</div>
					<div class="input-wrap">
						<label><?php echo Lang::txt('COM_SERVICES_FIELD_PENDING_REFUND_FOR', $this->subscription->pendingunits); ?>:</label><br />
						<?php echo $this->subscription->pendingpayment; ?>
						<?php if ($this->subscription->usepoints) { echo Lang::txt('COM_SERVICES_POINTS'); } else { echo $this->subscription->currency; } ?>
					</div>
					<div class="input-wrap">
						<label for="field-received_refund"><?php echo Lang::txt('COM_SERVICES_FIELD_REFUND_POSTED'); ?>:</label><br />
						<input type="text" name="received_refund" id="field-received_refund" value="<?php echo $this->escape($this->subscription->pendingpayment) ?>" />
						<?php if ($this->subscription->usepoints) { echo Lang::txt('COM_SERVICES_POINTS'); } else { echo $this->subscription->currency; } ?>
					</div>
				<?php } ?>
			<?php } else { ?>
				<div class="input-wrap">
					<input type="radio" name="action" id="field-action-activate" value="activate" />
					<label for="field-action-activate"><?php echo Lang::txt('COM_SERVICES_FIELD_ACTIVATE'); ?></label>
				</div>
				<div class="input-wrap">
					<label for="field-received_payment"><?php echo Lang::txt('COM_SERVICES_FIELD_PAYMENT_RECEIVED'); ?>:</label><br />
					<?php if ($this->subscription->pendingpayment > 0 ) { ?>
						<input type="text" name="received_payment" id="field-received_payment" value="<?php echo $this->escape($this->subscription->pendingpayment) ?>" />
					<?php } else { echo $this->subscription->pendingpayment; } ?>
					<?php if ($this->subscription->usepoints) { echo Lang::txt('COM_SERVICES_POINTS'); } else { echo $this->subscription->currency; } ?>
				</div>
				<div class="input-wrap">
					<label for="field-newunits"><?php echo Lang::txt('COM_SERVICES_FIELD_ACTIVE_UNITS'); ?>:</label><br />
					<?php if ($this->subscription->pendingunits > 0 or $this->subscription->expires < $now ) { ?>
						<input type="text" name="newunits" id="field-newunits" value="<?php echo $this->escape($this->subscription->pendingunits) ?>" />
					<?php } else { echo $this->subscription->pendingunits; } ?>
				</div>
				<div class="input-wrap">
					<input type="radio" name="action" id="field-action-cancelsub" value="cancelsub" />
					<label for="field-action-cancelsub"><?php echo Lang::txt('COM_SERVICES_FIELD_CANCEL_SUBSCRIPTION'); ?></label>
				</div>
			<?php } ?>
				<div class="input-wrap">
					<label for="field-message"><?php echo Lang::txt('COM_SERVICES_FIELD_SEND_MESSAGE'); ?>:</label><br />
					<textarea name="message" id="field-message"  cols="30" rows="5"></textarea>
				</div>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="usepoints" value="<?php echo $this->subscription->usepoints; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->subscription->id; ?>" />
	<input type="hidden" name="task" value="save" />
<?php  } // end if id exists ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

	<?php echo Html::input('token'); ?>
</form>
