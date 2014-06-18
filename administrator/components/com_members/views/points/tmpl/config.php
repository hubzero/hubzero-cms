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

JToolBarHelper::title( JText::_( 'MEMBERS' ).': Manage Points', 'user.png' );
JToolBarHelper::save( 'saveconfig', 'Save Configuration' );
JToolBarHelper::cancel();

?>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">#</th>
				<th scope="col">Points</th>
				<th scope="col">Alias</th>
				<th scope="col">Description</th>
			</tr>
		</thead>
		<tbody>
<?php
		$rows = 50;
		$i = 1;
		for ( $r = 0; $r < $rows; $r++ ) {
?>
			<tr>
				<th scope="row">(<?php echo $i; ?>)</th>
				<td><input type="text" name="points[<?php echo $i; ?>]" value="<?php echo @$this->params[$i-1]->points; ?>" size="10" maxlength="10" /></td>
				<td><input type="text" name="alias[<?php echo $i; ?>]" value="<?php echo $this->escape(@$this->params[$i-1]->alias); ?>" size="20" maxlength="50" /></td>
				<td><input type="text" name="description[<?php echo $i; ?>]" value="<?php echo $this->escape(@$this->params[$i-1]->description); ?>" size="100" maxlength="255" /></td>
<?php
				$i++;
?>
			</tr>
<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>