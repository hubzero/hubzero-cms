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

?>
<div role="navigation" class="sub-navigation">
	<ul id="subsubmenu">
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>">Summary</a></li> 
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit" class="active">Look up User Balance</a></li>
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=config">Configuration</a></li> 
		<li><a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=batch">Batch Transaction</a></li>
	</ul>
</div>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-50 fltlft">
		<fieldset>
			<legend><span>Find User Details</span></legend>
			
			<table class="adminform">
				<tbody>
					<tr>
						<td><label for="uid">UID:</label></td>
						<td><input type="text" name="uid" id="uid" size="30" maxlength="250" value="" /> <input type="submit" value="Go" /></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-50 fltrt">
		<p>Enter a user ID to view their point history and balance.</p>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="edit" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>