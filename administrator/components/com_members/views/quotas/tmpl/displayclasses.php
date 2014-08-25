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

// Menu
JToolBarHelper::title(JText::_('COM_MEMBERS_QUOTA_CLASSES'), 'user.png');
JToolBarHelper::addNew('addClass');
JToolBarHelper::editList('editClass');
JToolBarHelper::deleteList('Are you sure?', 'deleteClass');
JToolBarHelper::spacer();
JToolBarHelper::help('quotaclasses');
?>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
		 	<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_ID'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_ALIAS'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_SOFT_BLOCKS'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_HARD_BLOCKS'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_SOFT_FILES'); ?></th>
				<th><?php echo JText::_('COM_MEMBERS_QUOTA_HARD_FILES'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=editClass&amp;id=<?php echo $row->id; ?>">
						<?php echo $this->escape($row->id); ?>
					</a>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=editClass&amp;id=<?php echo $row->id; ?>">
						<?php echo $this->escape($row->alias); ?>
					</a>
				</td>
				<td>
					<?php echo $this->escape($row->soft_blocks); ?>
				</td>
				<td>
					<?php echo $this->escape($row->hard_blocks); ?>
				</td>
				<td>
					<?php echo $this->escape($row->soft_files); ?>
				</td>
				<td>
					<?php echo $this->escape($row->hard_files); ?>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_('form.token'); ?>
</form>