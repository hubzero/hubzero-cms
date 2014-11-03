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

JToolBarHelper::title(JText::_('COM_RESOURCES') . ': ' . JText::_('COM_RESOURCES_PLUGINS'), 'plugin.png');
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
?>
<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<?php echo $this->states; ?>

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('COM_RESOURCES_GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" />
				</th>
				<th scope="col">
					<?php echo JHTML::_('grid.sort', 'COM_RESOURCES_COL_ID', 'p.extension_id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
				<th scope="col" class="title">
					<?php echo JHTML::_('grid.sort', 'COM_RESOURCES_COL_PLUGIN_NAME', 'p.name', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
				<th scope="col">
					<?php echo JHTML::_('grid.sort', 'COM_RESOURCES_COL_PUBLISHED', 'p.published', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
				<th scope="col">
					<?php echo JHTML::_('grid.sort', 'COM_RESOURCES_COL_ORDER', 'p.folder', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
					<?php echo JHTML::_('grid.order', $this->rows); ?>
				</th>
				<th scope="col">
					<?php echo JHTML::_('grid.sort', 'COM_RESOURCES_COL_ACCESS', 'groupname', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
				<th scope="col">
					<?php echo JText::_('COM_RESOURCES_COL_MANAGE'); ?>
				</th>
				<th scope="col">
					<?php echo JHTML::_('grid.sort', 'COM_RESOURCES_COL_FILE', 'p.element', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php echo $this->pagination->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$db = JFactory::getDBO();
$tbl = new JTableExtension($db);

$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$link = JRoute::_('index.php?option=com_plugins&task=plugin.edit&extension_id='.$row->id.'&component=resources');

	$access    = JHTML::_('grid.access', $row, $i);
	//$checked = JHTML::_('grid.checkedout', $row, $i);
	$published = JHTML::_('grid.published', $row, $i);

	$ordering = ($this->filters['sort'] == 'p.folder');

	switch ($row->published)
	{
		case '2':
			$task = 'publish';
			$img  = 'disabled.png';
			$alt  = JText::_('JTRASHED');
			$cls  = 'trashed';
		break;
		case '1':
			$task = 'unpublish';
			$img  = 'publish_g.png';
			$alt  = JText::_('JPUBLISHED');
			$cls  = 'publish';
		break;
		case '0':
		default:
			$task = 'publish';
			$img  = 'publish_x.png';
			$alt  = JText::_('JUNPUBLISHED');
			$cls  = 'unpublish';
		break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
					<?php
						if ($tbl->isCheckedOut($this->user->get('id'), $row->checked_out)) {
							echo $this->escape($row->name);
						} else {
					?>
						<a class="editlinktip hasTip" href="<?php echo $link; ?>">
							<span><?php echo $this->escape($row->name); ?></span>
						</a>
					<?php } ?>
				</td>
				<td>
					<?php if ($tbl->isCheckedOut($this->user->get('id'), $row->checked_out)) { ?>
						<span class="state <?php echo $cls; ?>">
							<span class="text"><?php echo $alt; ?></span>
						</span>
					<?php } else { ?>
						<a class="state <?php echo $cls; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task; ?>&amp;id[]=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('COM_RESOURCES_SET_TASK_TO', $task); ?>">
							<span class="text"><?php echo $alt; ?></span>
						</a>
					<?php } ?>
				</td>
				<td class="order">
					<span><?php echo $this->pagination->orderUpIcon($i, ($row->folder == @$this->rows[$i-1]->folder && $row->ordering > -10000 && $row->ordering < 10000), 'orderup', 'COM_RESOURCES_MOVE_UP', $row->ordering); ?></span>
					<span><?php echo $this->pagination->orderDownIcon($i, $n, ($row->folder == @$this->rows[$i+1]->folder && $row->ordering > -10000 && $row->ordering < 10000), 'orderdown', 'COM_RESOURCES_MOVE_DOWN', $row->ordering); ?></span>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo ($row->ordering ? '' : 'disabled="disabled"'); ?> class="text_area" style="text-align: center" />
				</td>
				<td align="center">
					<?php echo $access; ?>
				</td>
				<td nowrap="nowrap">
					<?php if (in_array($row->element, $this->manage)) { ?>
						<a href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=manage&amp;plugin=<?php echo $row->element; ?>">
							<span><?php echo JText::_('COM_RESOURCES_COL_MANAGE'); ?></span>
						</a>
					<?php } ?>
				</td>
				<td>
					<?php echo $this->escape($row->element); ?>
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
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>