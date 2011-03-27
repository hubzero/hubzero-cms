<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
JToolBarHelper::title( JText::_( 'Manage Points' ), 'addedit.png' );
JToolBarHelper::save( 'saveconfig', 'Save Configuration' );
JToolBarHelper::cancel();

?>
<div id="submenu-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
	 	</div>
 	</div>
	<div class="m">
		<ul id="submenu">
			<li><a href="index.php?option=<?php echo $this->option; ?>">Summary</a></li> 
			<li><a href="index.php?option=<?php echo $this->option; ?>&amp;task=edit">Look up User Balance</a></li>
			<li><a href="index.php?option=<?php echo $this->option; ?>&amp;task=config">Configuration</a></li> 
			<li><a href="index.php?option=<?php echo $this->option; ?>&amp;task=batch" class="active">Batch Transaction</a></li>
		</ul>
		<div class="clr"></div>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>

<div id="element-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">

		<form action="index2.php" method="post" name="adminForm">
		<table class="adminform">
				 <thead>
				  <tr>
				   <th colspan="2">Process batch transaction</th>
				  </tr>
				 </thead>
				 <tbody>
                  <tr>
				   <td><label for="type">Transaction Type:</label></td>
				   <td><select name="type" id="type">
						<option>deposit</option>
						<option>withdraw</option>
				   </select></td>
				  </tr>
				  <tr>
				   <td><label for="amount">Amount:</label></td>
				   <td><input type="text" name="amount" id="amount"  maxlength="11" value="" /></td>
				  </tr>
				  <tr>
				   <td><label for="description">Description:</label></td>
				   <td><input type="text" name="description" id="description"  maxlength="250"style="width:100%"  value="" /></td>
				  </tr>
                    <tr>
				   <td><label for="users">User list</label></td>
				   <td><textarea name="users" id="users" rows="10" style="width:100%"></textarea>
                   <br /> Enter a comma-separated list of userids.</td>
				  </tr>
                  <thead>
				  <tr>
				   <th colspan="2">Transaction log details</th>
				  </tr>
				 </thead>
                  <tr>
				   <td><label for="com">Category / Component</label></td>
				   <td><input type="text" name="com" id="com" size="30" maxlength="250" value="" />
                   <br />E.g. answers, survey, etc.</td>
				  </tr>
                  <tr>
				   <td><label for="action">Action type</label></td>
				   <td><input type="text" name="action" id="action" size="30" maxlength="250" value="" />
                   <br /> E.g. royalty, setup, etc.</td>
				  </tr>
                  <tr>
				   <td><label for="ref">Reference id (optional)</label></td>
				   <td><input type="text" name="ref" id="ref" size="30" maxlength="250" value="" /></td>
				  </tr>
                 
				 </tbody>
		</table>
        <input type="hidden" name="option" value="<?php echo $option;?>" />
		<input type="hidden" name="task" value="process_batch" />
        <input type="submit" name="submit" value="Process batch transaction" />	
			<?php echo JHTML::_( 'form.token' ); ?>
		</form>

		<div class="clr"></div>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>
