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
defined('_JEXEC') or die('Restricted access');

$canDo = StoreHelper::getActions('component');

$text = (!$this->store_enabled) ? ' <small><small style="color:red;">(store is disabled)</small></small>' : '';
JToolBarHelper::title(JText::_('COM_STORE_MANAGER') . $text, 'addedit.png');
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::save();
}
JToolBarHelper::cancel();

$order_date = (intval($this->row->ordered) <> 0) ? JHTML::_('date', $this->row->ordered, JText::_('COM_STORE_DATE_FORMAT_HZ1')) : NULL ;
$status_changed = (intval($this->row->status_changed) <> 0) ? JHTML::_('date', $this->row->status_changed, JText::_('COM_STORE_DATE_FORMAT_HZ1')) : NULL;

switch ($this->row->status)
{
	case 0:
		$status = '<span class="yes">' . strtolower(JText::_('COM_STORE_NEW')) . '</span>';
		break;
	case 1:
		$status = 'completed';
		break;
	case 2:
	default:
		$status = 'cancelled';
		break;
}

?>
<script type="text/javascript">
public function submitbutton(pressbutton) 
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	
	submitform(pressbutton);

}
</script>
<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset class="adminform">

<?php if (isset($this->row->id)) { ?>
			<legend><span><?php echo JText::_('COM_STORE_ORDER') . ' #' . $this->row->id . ' ' . JText::_('COM_STORE_DETAILS'); ?></span></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label><?php echo JText::_('COM_STORE_ITEMS'); ?>:</label></td>
						<td><p><?php 
		    $k=1;
			foreach ($this->orderitems as $o)
			{
				$avail = ($o->available) ?  'available' : 'unavailable';
				$html  = $k . ') ';
		   		$html .= $o->title . ' (x' . $o->quantity . ')';
				$html .= ($o->selectedsize) ? '- size ' . $o->selectedsize : '';
				$html .= '<br /><span style="color:#999;">' . JText::_('COM_STORE_ITEM') . ' ' . JText::_('COM_STORE_STORE') . ' ' . JText::_('COM_STORE_ID') . ' #' . $o->itemid . '. ' . JText::_('COM_STORE_STATUS') . ': ' . $avail;
				if (!$o->sizeavail) {
					$html .= JText::_('COM_STORE_WARNING_NOT_IN_STOCK');
				}
				$html .= '. ' . JText::_('COM_STORE_CURRENT_PRICE') . ': ' . $o->price . '</span><br />';
				$k++;
				echo $html;
			}
?></p>
						</td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('COM_STORE_SHIPPING'); ?>:</label></td>
						<td><pre><?php echo $this->escape(stripslashes($this->row->details)); ?></pre></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('COM_STORE_PROFILE_INFO'); ?>:</label></td>
						<td>
							<?php echo JText::_('COM_STORE_LOGIN'); ?>: <?php echo $this->escape(stripslashes($this->customer->get('username'))); ?> <br />
							<?php echo JText::_('COM_STORE_NAME'); ?>: <?php echo $this->escape(stripslashes($this->customer->get('name'))); ?> <br />
							<?php echo JText::_('COM_STORE_EMAIL'); ?>: <?php echo $this->escape(stripslashes($this->customer->get('email'))); ?>
						</td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('COM_STORE_ADMIN_NOTES'); ?>:</label></td>
						<td><textarea name="notes" id="notes"  cols="50" rows="10"><?php echo $this->escape(stripslashes($this->row->notes)); ?></textarea></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<fieldset class="adminform">
			<legend><span><?php echo JText::_('COM_STORE_PROCESS_ORDER'); ?></span></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><label><?php echo JText::_('COM_STORE_STATUS'); ?>:</label></td>
						<td><?php echo $status ?></td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('COM_STORE_ORDER_PLACED'); ?>:</label></td>
						<td><?php echo $order_date ?></td>
					</tr>
<?php if ($this->row->status != 0) { ?>
					<tr>
						<td class="key"><label><?php echo JText::_('COM_STORE_ORDER') . ' ' . $status; ?>:</label></td>
						<td><?php echo $status_changed ?></td>
					</tr>
<?php } ?>
					<tr>
						<td class="key"><label><?php echo JText::_('COM_STORE_ORDER') . ' ' . JText::_('COM_STORE_TOTAL'); ?>:</label></td>
						<td>
<?php if ($this->row->status == 0) { ?>
							<input type="text" name="total" value="<?php echo $this->row->total ?>"  /> <?php echo JText::_('COM_STORE_POINTS'); ?>
<?php } else { ?>
							<?php echo $this->row->total ?> <?php echo JText::_('COM_STORE_POINTS'); ?>
							<input type="hidden" name="total" value="<?php echo $this->row->total ?>"  />
<?php } ?>
						</td>
					</tr>
					<tr>
						<td class="key"><label><?php echo JText::_('COM_STORE_CURRENT_BALANCE'); ?>:</label></td>
						<td><strong><?php echo $this->funds ?></strong> points</td>
					</tr> 
<?php if ($this->row->status == 0) { ?>
					<tr>
						<td class="key"><label><?php echo JText::_('COM_STORE_MANAGE_ORDER'); ?>:</label></td>
						<td><input type="radio" name="action" value="message" /><?php echo JText::_('COM_STORE_ORDER_ON_HOLD'); ?></td>
					</tr>
					<tr>
						<th></th>
						<td><input type="radio" name="action" value="complete_order" /> <?php echo JText::_('COM_STORE_PROCESS_TRANSACTION'); ?></td>
					</tr>
					<tr>
						<th></th>
						<td><input type="radio" name="action" value="cancel_order" /> <?php echo JText::_('COM_STORE_RELEASE_FUNDS'); ?></td>
					</tr>
					<tr>
						<th></th>
						<td><?php echo JText::_('COM_STORE_SEND_A_MSG'); ?>: <br /><textarea name="message" id="message"  cols="30" rows="5"></textarea></td>
					</tr>
<?php } ?>                
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />
<?php  } // end if id exists ?>

	<?php echo JHTML::_('form.token'); ?>
</form>
