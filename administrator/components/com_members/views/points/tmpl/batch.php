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

JToolBarHelper::title( JText::_( 'MEMBERS' ).': <small><small>[ Manage Points ]</small></small>', 'user.png' );
JToolBarHelper::save( 'process_batch', 'Process Batch' );
JToolBarHelper::cancel();

?>
<div role="navigation" class="sub-navigation">
	<ul id="subsubmenu">
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>">Summary</a></li> 
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit">Look up User Balance</a></li>
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=config">Configuration</a></li> 
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=batch" class="active">Batch Transaction</a></li>
	</ul>
</div>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-60 fltlft">
		<fieldset>
			<legend><span>Process batch transaction</span></legend>

			<table class="adminform">
				<tbody>
					<tr>
						<td><label for="type">Transaction Type:</label></td>
						<td>
							<select name="transaction[type]" id="type">
								<option>deposit</option>
								<option>withdraw</option>
							</select>
						</td>
					</tr>
					<tr>
						<td><label for="amount">Amount:</label></td>
						<td><input type="text" name="transaction[amount]" id="amount"  maxlength="11" value="" /></td>
					</tr>
					<tr>
						<td><label for="description">Description:</label></td>
						<td><input type="text" name="transaction[description]" id="description"  maxlength="250"  value="" /></td>
					</tr>
					<tr>
						<td><label for="users">User list</label></td>
						<td>
							<textarea name="transaction[users]" id="users" rows="10"></textarea>
							<br /> Enter a comma-separated list of userids.
						</td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-40 fltrt">
		<fieldset>
			<legend><span>Transaction log details</span></legend>
			
			<table class="adminform">
				<tbody>
					<tr>
						<td><label for="com">Category / Component</label></td>
						<td><input type="text" name="log[com]" id="com" size="30" maxlength="250" value="" /><br />E.g. answers, survey, etc.</td>
					</tr>
					<tr>
						<td><label for="action">Action type</label></td>
						<td><input type="text" name="log[action]" id="action" size="30" maxlength="250" value="" /><br /> E.g. royalty, setup, etc.</td>
					</tr>
					<tr>
						<td><label for="ref">Reference id (optional)</label></td>
						<td><input type="text" name="log[ref]" id="ref" size="30" maxlength="250" value="" /></td>
					</tr>
				</tbody>
			</table>
        
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="process_batch" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>