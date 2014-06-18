<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

JToolBarHelper::title(JText::_( 'Services &amp; Subscriptions Manager' ), 'addedit.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();

$added = (intval( $this->subscription->added ) <> 0) ? JHTML::_('date', $this->subscription->added, JText::_('DATE_FORMAT_HZ1')) : NULL ;
$updated = (intval( $this->subscription->updated ) <> 0) ? JHTML::_('date', $this->subscription->updated, JText::_('DATE_FORMAT_HZ1')) : 'N/A';
$expires = (intval( $this->subscription->expires) <> 0) ? JHTML::_('date', $this->subscription->expires, JText::_('DATE_FORMAT_HZ1')) : 'N/A';

$status = '';
$pending = $this->subscription->currency.' '.$this->subscription->pendingpayment;
$now = JFactory::getDate()->toSql();

$onhold_msg = ($this->subscription->status==2) ? JText::_('No action / send message to user') : JText::_('Subscription on hold (pending payment or verification)');

switch ($this->subscription->status)
{
	case '1':
		$status = ($this->subscription->expires > $now) ? '<span style="color:#197f11;">'.strtolower(JText::_('Active')).'</span>' : '<span style="color:#ef721e;">'.strtolower(JText::_('Expired')).'</span>';
		break;
	case '0':
		$status = '<span style="color:#ff0000;">'.strtolower(JText::_('Pending')).'</span>';
		break;
	case '2':
		$status = '<span style="color:#999;">'.strtolower(JText::_('Cancelled')).'</span>';
		$pending .= ($this->subscription->pendingpayment) ? ' ('.JText::_('refund').')' : '';
		break;
}

$priceline  = $this->subscription->currency.' '.$this->subscription->unitprice.'  </strong>'.JText::_( 'per' ).' '.$this->subscription->unitmeasure;
$priceline .= ($this->subscription->pointsprice > 0) ? ' or '.$this->subscription->pointsprice.' '.JText::_('points') : '';

?>

<script type="text/javascript">
	function submitbutton(pressbutton)
	{
		var form = document.adminForm;

		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}

		submitform( pressbutton );
	}
</script>

<form action="index.php" method="post" name="adminForm" id="item-form">

<?php if (isset($this->subscription->id)) { ?>
	<div class="col width-60 fltlft">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Subscription').' #'.$this->subscription->id.' ('.$this->subscription->code.') '; ?></span></legend>

			<div class="input-wrap">
				<label><?php echo JText::_('Service'); ?>:</label><br />
				<?php echo $this->subscription->title.' - <strong>'.$priceline.'</strong>'; ?>
			</div>

			<div class="input-wrap">
				<label><?php echo JText::_('Profile Info'); ?>:</label><br />
				<?php echo JText::_('Login'); ?>: 		<?php echo $this->customer->get('username') ?> <br />
				<?php echo JText::_('Name'); ?>:  		<?php echo $this->customer->get('name') ?> <br />
				<?php echo JText::_('Email'); ?>: 		<?php echo $this->customer->get('email') ?> <br />
				<?php echo JText::_('Tel.'); ?>: 		<?php echo $this->customer->get('phone') ?>
			</div>

			<div class="input-wrap">
				<label><?php echo JText::_('Employer Info'); ?>:</label><br />
				<?php echo JText::_('Company Name'); ?>: 	<?php echo $this->subscription->companyName; ?> <br />
				<?php echo JText::_('Company Location'); ?>: <?php echo $this->subscription->companyLocation; ?> <br />
				<?php echo JText::_('Company URL'); ?>: 		<?php echo $this->subscription->companyWebsite; ?>
			</div>

			<div class="input-wrap">
				<label for="field-notes"><?php echo JText::_('Administrator Notes'); ?>:</label><br />
				<textarea name="notes" id="field-notes" cols="50" rows="10"><?php echo $this->escape(stripslashes($this->subscription->notes)); ?></textarea>
			</div>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<table class="meta">
			<tbody>
				<tr>
					<th><?php echo JText::_('Status'); ?>:</th>
					<td><?php echo $status ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('Added'); ?>:</th>
					<td><?php echo $added ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('Expires'); ?>:</th>
					<td><?php echo $expires ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('Last Updated'); ?>:</th>
					<td><?php echo $updated ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('Total paid'); ?>:</th>
					<td><?php echo $this->subscription->totalpaid; ?> <?php if ($this->subscription->usepoints) { echo JText::_('POINTS'); } else { echo $this->subscription->currency; } ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('Pending payment'); ?>:</th>
					<td><?php echo $this->subscription->pendingpayment; ?> <?php if ($this->subscription->usepoints) { echo JText::_('POINTS'); } else { echo $this->subscription->currency; } ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('Active units'); ?>:</th>
					<td><?php echo $this->subscription->units; ?></td>
				</tr>
				<tr>
					<th><?php echo JText::_('Pending units'); ?>:</th>
					<td><?php echo $this->subscription->pendingunits; ?></td>
				</tr>
			</tbody>
		</table>

		<fieldset class="adminform">
			<legend><span><?php echo JText::_('Manage Subscription'); ?></span></legend>

			<div class="input-wrap">
				<input type="radio" name="action" id="field-action-message" value="message" />
				<label for="field-action-message"><?php echo $onhold_msg; ?></label>
			</div>
		<?php if ($this->subscription->status == 2) { ?>
			<?php if ($this->subscription->pendingpayment > 0) { ?>
				<div class="input-wrap">
					<input type="radio" name="action" id="field-action-refund" value="refund" />
					<label for="field-action-refund"><?php echo JText::_('Process refund / remove pending items'); ?></label>
				</div>
				<div class="input-wrap">
					<label><?php echo JText::_('Pending Refund').' <br />for '.$this->subscription->pendingunits.' '.JText::_('unit(s)'); ?>:</label><br />
					<?php echo $this->subscription->pendingpayment; ?><?php if ($this->subscription->usepoints) { echo JText::_('POINTS'); } else { echo $this->subscription->currency; } ?>
				</div>
				<div class="input-wrap">
					<label for="field-received_refund"><?php echo JText::_('Refund posted'); ?>:</label><br />
					<input type="text" name="received_refund" id="field-received_refund" value="<?php echo $this->subscription->pendingpayment ?>"  /> <?php if ($this->subscription->usepoints) { echo JText::_('POINTS'); } else { echo $this->subscription->currency; } ?>
				</div>
			<?php } ?>
		<?php } else { ?>
			<div class="input-wrap">
				<input type="radio" name="action" id="field-action-activate" value="activate" />
				<label for="field-action-activate"><?php echo JText::_('Activate/ Extend this subscription (new payment or verification received) '); ?></label>
			</div>
			<div class="input-wrap">
				<label for="field-received_payment"><?php echo JText::_('New payment received'); ?>:</label><br />
				<?php if  ($this->subscription->pendingpayment > 0 ) { ?> <input type="text" name="received_payment" id="field-received_payment" value="<?php echo $this->subscription->pendingpayment ?>"  />  <?php } else { echo $this->subscription->pendingpayment;  } ?><?php if ($this->subscription->usepoints) { echo JText::_('POINTS'); } else { echo $this->subscription->currency; } ?>
			</div>
			<div class="input-wrap">
				<label for="field-newunits"><?php echo JText::_('Activate units'); ?>:</label><br />
				<?php if  ($this->subscription->pendingunits > 0 or $this->subscription->expires < $now ) { ?> <input type="text" name="newunits" id="field-newunits" value="<?php echo $this->subscription->pendingunits ?>"  /> <?php } else { echo $this->subscription->pendingunits;  } ?>
			</div>
			<div class="input-wrap">
				<input type="radio" name="action" id="field-action-cancelsub" value="cancelsub" />
				<label for="field-action-cancelsub"><?php echo JText::_('Cancel this subscription'); ?></label>
			</div>
		<?php } ?>
			<div class="input-wrap">
				<label for="field-message"><?php echo JText::_('Send user a message').' '.JText::_('(optional)'); ?>:</label><br />
				<textarea name="message" id="field-message"  cols="30" rows="5"></textarea>
			</div>
		</fieldset>
	</div>
	<div class="clr"></div>

	<input type="hidden" name="usepoints" value="<?php echo $this->subscription->usepoints; ?>" />
	<input type="hidden" name="id" value="<?php echo $this->subscription->id; ?>" />
	<input type="hidden" name="task" value="save" />
<?php  } // end if id exists ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
