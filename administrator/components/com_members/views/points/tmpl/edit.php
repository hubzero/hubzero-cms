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

$dateTimeFormat = '%d %b, %Y %I:%M %p';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateTimeFormat = 'd M, Y h:i A';
	$tz = false;
}

JToolBarHelper::title( JText::_( 'MEMBERS' ).': <small><small>[ Manage Points ]</small></small>', 'user.png' );

?>
<div role="navigation" class="sub-navigation">
	<ul id="subsubmenu">
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>">Summary</a></li> 
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit" class="active">Look up User Balance</a></li>
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=config">Configuration</a></li> 
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=batch">Batch Transaction</a></li>
	</ul>
</div>

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

<form action="index.php" method="post" name="adminForm" id="item-form">
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
					<td><?php echo JHTML::_('date',$item->created, $dateTimeFormat, $tz); ?></td>
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
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
