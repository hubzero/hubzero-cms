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
			<li><a href="index.php?option=<?php echo $this->option; ?>" class="active">Summary</a></li> 
			<li><a href="index.php?option=<?php echo $this->option; ?>&amp;task=edit">Look up User Balance</a></li>
			<li><a href="index.php?option=<?php echo $this->option; ?>&amp;task=config">Configuration</a></li> 
			<li><a href="index.php?option=<?php echo $this->option; ?>&amp;task=batch">Batch Transaction</a></li>
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

		<form action="index.php" method="post" name="adminForm">
		<table class="adminform">
		 <thead>
		  <tr>
		   <th>#</th>
		   <th>Points</th>
		   <th>Alias</th>
		   <th>Description</th>
		  </tr>
		 </thead>
		 <tbody>
<?php
		$rows = 50;
		$i = 1;
		for ( $r = 0; $r < $rows; $r++ ) {
?>
		  <tr>
		   <td>(<?php echo $i; ?>)</td>
		   <td><input type="text" name="points[<?php echo $i; ?>]" value="<?php echo @$this->params[$i-1]->points; ?>" size="10" maxlength="10" /></td>
		   <td><input type="text" name="alias[<?php echo $i; ?>]" value="<?php echo htmlspecialchars( @$this->params[$i-1]->alias ); ?>" size="20" maxlength="50" /></td>
		   <td><input type="text" name="description[<?php echo $i; ?>]" value="<?php echo htmlspecialchars( @$this->params[$i-1]->description ); ?>" size="50" maxlength="255" /></td>
<?php
				$i++;
?>
		  </tr>
<?php } ?>
		 </tbody>
		</table>
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="task" value="" />
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
