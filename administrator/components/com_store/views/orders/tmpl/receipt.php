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

$dateFormat = '%d %b, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$tz = false;
}

$k = 1; 
$customer = $this->customer;
$row = $this->row;
?>
<?php if ($this->receipt_title) { ?>
<div style="text-align: center"><h2><?php echo $this->receipt_title; ?></h2></div>
<div style="height: 50px;"></div>
<?php } ?>

<table>
	<tbody>
		<?php if ($this->headertext_ln1) { ?>
		<tr style="background-color: #e7eaec;"><td style="font-weight: bold;"><?php echo $this->headertext_ln1; ?></td></tr>
		<?php } ?>
		<?php if ($this->headertext_ln2) { ?>
		<tr><td style="font-weight: bold;"><?php echo $this->headertext_ln2; ?></td></tr>
		<?php } ?>
		<?php 	
			for ($i=0; $i< count($this->hubaddress);$i++)
			{
				if ($this->hubaddress[$i]) {
					?>
						<tr>
							<td style="color: #525f6b;"><?php echo $this->hubaddress[$i]; ?></td>
						</tr>
				<?php
				}
			}
		?>
	
 </tbody>
</table>
<div style="height: 50px;"></div>
<h4><?php echo JText::_('Order Details'); ?></h4>
<table>
	<tbody>
		<tr>
			<td style="color: #525f6b;"><?php echo JText::_('Customer') . ': '; ?></td>
			<td><?php echo $customer->get('name').' ('.$customer->get('username').') '; ?></td>
		</tr>
		<tr>
			<td style="color: #525f6b;"><?php echo JText::_('Email') . ': '; ?></td>
			<td><?php echo $customer->get('email'); ?></td>
		</tr>
		<tr>
			<td style="color: #525f6b;"><?php echo JText::_('Order ID') . ': '; ?></td>
			<td><?php echo $row->id; ?></td>
		</tr>
		<tr>
			<td style="color: #525f6b;"><?php echo JText::_('Order placed') . ': '; ?></td>
			<td><?php echo JHTML::_('date', $row->ordered, $dateFormat, $tz); ?></td>
		</tr>
		<tr>
			<td style="color: #525f6b;"><?php echo JText::_('Order completed') . ': '; ?></td>
			<td><?php echo JHTML::_('date', date( 'Y-m-d H:i:s', time() ), $dateFormat, $tz); ?></td>
		</tr>
 </tbody>
</table>
<div style="height: 30px;"></div>
<table>
	<thead>
		<tr style="background-color: #e7eaec;">
			<th><?php echo JText::_('Ordered Item(s)'); ?></th>
			<th><?php echo JText::_('Price'); ?></th>
		</tr>
	</thead>
	<tbody>
<?php foreach ($this->orderitems as $o) { ?>
	<tr>
		<td><?php echo $k.'. ['.$o->category.$o->itemid.'] ' . $o->title. ' (x'.$o->quantity.')'; echo ($o->selectedsize) ? ' - size '.$o->selectedsize : ''; ?></td>
		<td><?php echo $o->price*$o->quantity.' '.JText::_('points'); ?></td>
	</tr>
	
<?php } ?>
	<tr>
		<td style="font-weight: bold; text-align: right;"><?php echo JText::_('Total: ') . '&nbsp;'; ?></td>
		<td style="font-weight: bold;"><?php echo $row->total.' '.JText::_('points'); ?></td>
	</tr>
	</tbody>
</table>
<div style="height: 50px;"></div>

<?php if ($this->receipt_note) { ?>
<div style="text-align: center"><h5><?php echo $this->receipt_note; ?></h5></div>
<?php } ?>
