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

$dateFormat = '%d %b, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$tz = false;
}

JToolBarHelper::title( '<a href="index.php?option=com_services">'.JText::_( 'Services &amp; Subscriptions Manager' ).'</a>', 'addedit.png' );
JToolBarHelper::save();
JToolBarHelper::cancel();

$added = (intval( $this->subscription->added ) <> 0) ? JHTML::_('date', $this->subscription->added, $dateFormat, $tz) : NULL ;
$updated = (intval( $this->subscription->updated ) <> 0) ? JHTML::_('date', $this->subscription->updated, $dateFormat, $tz) : 'N/A';
$expires = (intval( $this->subscription->expires) <> 0) ? JHTML::_('date', $this->subscription->expires, $dateFormat, $tz) : 'N/A';

$status = '';
$pending = $this->subscription->currency.' '.$this->subscription->pendingpayment;
$now = date( 'Y-m-d H:i:s', time() );

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
	public function submitbutton(pressbutton) 
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
		<div class="col width-60 fltlft">
			<fieldset class="adminform">
	
        <?php  if (isset($this->subscription->id)) { ?>
		<legend><span><?php echo JText::_('Subscription').' #'.$this->subscription->id.' ('.$this->subscription->code.') '; ?></span></legend>
			<table class="admintable">
			 <tbody>
			  <tr>
			    <td class="key"><label><?php echo JText::_('Service'); ?>:</label></td>
			   <td>
               <?php echo $this->subscription->title.' - <strong>'.$priceline.'</strong>'; ?>
               </td>
			  </tr>
			  <tr>
			   <td class="key"><label><?php echo JText::_('Profile Info'); ?>:</label></td>
			   <td><?php echo JText::_('Login'); ?>: 		<?php echo $this->customer->get('username') ?> <br />
 				   <?php echo JText::_('Name'); ?>:  		<?php echo $this->customer->get('name') ?> <br />
				   <?php echo JText::_('Email'); ?>: 		<?php echo $this->customer->get('email') ?> <br />
                   <?php echo JText::_('Tel.'); ?>: 		<?php echo $this->customer->get('phone') ?>
               </td>
			  </tr>
              <tr>
			   <td class="key"><label><?php echo JText::_('Employer Info'); ?>:</label></td>
			   <td><?php echo JText::_('Company Name'); ?>: 	<?php echo $this->subscription->companyName; ?> <br />
 				   <?php echo JText::_('Company Location'); ?>: <?php echo $this->subscription->companyLocation; ?> <br />
				   <?php echo JText::_('Company URL'); ?>: 		<?php echo $this->subscription->companyWebsite; ?>
               </td>
			  </tr>
              <tr>
			    <td class="key"><label><?php echo JText::_('Administrator Notes'); ?>:</label></td>
			   <td><textarea name="notes" id="notes"  cols="50" rows="10"><?php echo (stripslashes($this->subscription->notes)); ?></textarea></td>
			  </tr>
			 </tbody>
			</table>
		</fieldset>
		
		</div>
		<div class="col width-40 fltrt">
			<table class="meta" summary="<?php echo JText::_('Metadata for this item'); ?>">
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
			<table class="admintable">
			 <tbody>                
                <tr>
			   	<td colspan="2"><input type="radio" name="action" value="message"  /> <?php echo $onhold_msg; ?></td>
			  </tr>
                
                <?php
				if ($this->subscription->status == 2) {
				?>
                <?php if ($this->subscription->pendingpayment > 0) { ?> 
                <tr>
                <td colspan="2"><input type="radio" name="action" value="refund" /> <?php echo JText::_('Process refund / remove pending items'); ?></td>
                
			   </tr>
                <tr>
			   <td class="key"><label><?php echo JText::_('Pending Refund').' <br />for '.$this->subscription->pendingunits.' '.JText::_('unit(s)'); ?>:</label></td>
			   <td> <?php echo $this->subscription->pendingpayment; ?><?php if ($this->subscription->usepoints) { echo JText::_('POINTS'); } else { echo $this->subscription->currency; } ?></td>
			  </tr>
             
                <tr>
			   <td class="key"><label><?php echo JText::_('Refund posted');  ?>:</label></td>
			   <td><input type="text" name="received_refund" value="<?php echo $this->subscription->pendingpayment ?>"  /> <?php if ($this->subscription->usepoints) { echo JText::_('POINTS'); } else { echo $this->subscription->currency; } ?></td>
			  </tr>
               <?php } ?>
                
               <?php
				} else {
				?>                            
              <tr>
			   	<td colspan="2"><input type="radio" name="action" value="activate" /> <?php echo JText::_('Activate/ Extend this subscription (new payment or verification received) '); ?></td>
			  </tr>
              <tr>
			   <td class="key"><label><?php echo JText::_('New payment received'); ?>:</label></td>
			   <td> <?php if  ($this->subscription->pendingpayment > 0 ) { ?> <input type="text" name="received_payment" value="<?php echo $this->subscription->pendingpayment ?>"  />  <?php } else { echo $this->subscription->pendingpayment;  } ?><?php if ($this->subscription->usepoints) { echo JText::_('POINTS'); } else { echo $this->subscription->currency; } ?></td>
			  </tr>
              
               <tr>
			   <td class="key"><label><?php echo JText::_('Activate units'); ?>:</label></td>
			   <td> <?php if  ($this->subscription->pendingunits > 0 or $this->subscription->expires < $now ) { ?> <input type="text" name="newunits" value="<?php echo $this->subscription->pendingunits ?>"  /> <?php } else { echo $this->subscription->pendingunits;  } ?> </td>
			  </tr>
               <tr>
			   	<td colspan="2"><input type="radio" name="action" value="cancelsub" /> <?php echo JText::_('Cancel this subscription'); ?></td>
			  </tr>
             <?php
				}
				?>
                 <tr >
                 	<td colspan="2" style="border-top:3px solid #ccc;padding-top:2em;"></td>
                 </tr>
                
                <tr>
			   	 <td class="key"><?php echo JText::_('Send user a message').'<br />'.JText::_('(optional)'); ?>:</td>
			    <td><textarea name="message" id="message"  cols="30" rows="5"></textarea></td>
			  </tr>
              
                   
			 </tbody>
			</table>
		</fieldset>
		</div>
		<div class="clr"></div>
        <input type="hidden" name="usepoints" value="<?php echo $this->subscription->usepoints; ?>" />				 
		<input type="hidden" name="id" value="<?php echo $this->subscription->id; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="save" />
        <?php  } // end if id exists ?>
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
