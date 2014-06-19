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

JToolBarHelper::title(JText::_('COM_TOOLS') . ': ' . JText::_('COM_TOOLS_ZONES'), 'tools.png');
JToolBarHelper::spacer();
JToolBarHelper::addNew();
JToolBarHelper::deleteList();
JToolBarHelper::spacer();
JToolBarHelper::help('zones');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_ZONE', 'zone', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_TYPE', 'type', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_MASTER', 'master', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_TOOLS_COL_SSH_KEY', 'ssh_key_path', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JText::_('COM_TOOLS_COL_LOCATIONS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
if ($this->rows)
{
	$i = 0;
	foreach ($this->rows as $row)
	{
?>
			<tr>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->get('id'); ?>">
						<span><?php echo $this->escape(stripslashes($row->get('zone'))); ?></span>
					</a>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->get('type'))); ?>
				</td>
				<td>
					<a class="state <?php echo ($row->get('state') == 'up') ? 'publish' : 'unpublish'; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=state&amp;id=<?php echo $row->get('id'); ?>&amp;state=<?php echo ($row->get('state') == 'up') ? 'down' : 'up'; ?>&amp;<?php echo JUtility::getToken(); ?>=1">
						<span><?php echo $this->escape(stripslashes($row->get('state'))); ?></span>
					</a>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->get('master'))); ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->get('ssh_key_path'))); ?>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=locations&amp;zone=<?php echo $row->get('id'); ?>">
						<span><?php echo $row->locations('count'); ?></span>
					</a>
				</td>
			</tr>
<?php
	}
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>
