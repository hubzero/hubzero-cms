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

// No direct access
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById("adminForm");
	if (pressbutton == "cancel") {
		submitform( pressbutton );
		return;
	}
	submitform( pressbutton );
}
</script>
<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=manage&amp;plugin=dashboard" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="manage" />
	<input type="hidden" name="plugin" value="dashboard" />
	<input type="hidden" name="action" value="" />

	<input type="hidden" name="uid" id="uid" value="<?php echo $this->juser->get('id'); ?>" />
	<input type="hidden" name="serials" id="serials" value="<?php echo $this->usermods[0].';'.$this->usermods[1].';'.$this->usermods[2]; ?>" />
	
	<fieldset id="filter-bar">
		<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=manage&amp;plugin=dashboard&amp;action=select">
			<?php echo JText::_('Push module to users'); ?>
		</a>
	</fieldset>
	
	<table id="droppables" class="adminlist" summary="<?php echo JText::_('PLG_MEMBERS_DASHBOARD_MY_MODULES'); ?>">
		<thead>
			<tr>
				<th scope="col">
					<?php echo JText::_('Available Modules'); ?>
				</th>
				<th scope="col" colspan="3">
					<?php echo JText::_('Default Dashboard'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td id="modules-dock" style="vertical-align: top;">
					<h3><?php echo JText::_('MODULES'); ?></h3>
					<p>Click on a module name from the list to add it to your page.</p>
					<div id="available">
<?php
					// Instantiate a view
					$view = new Hubzero_Plugin_View(
						array(
							'folder'  => 'members',
							'element' => 'dashboard',
							'name'    => 'list'
						)
					);
					$view->modules = $this->availmods;
					$view->display();
?>
					</div>
					<div class="clear"></div>
				</td>
<?php
// Loop through each column and output modules assigned to each one
for ($c = 0; $c < count($this->columns); $c++)
{
?>
				<td class="sortable" id="sortcol_<?php echo $c; ?>">
					<?php echo $this->columns[$c]; ?>
				</td>
<?php
}
?>
			</tr>
		</tbody>
	</table>
	
	<?php echo JHTML::_('form.token'); ?>
</form>