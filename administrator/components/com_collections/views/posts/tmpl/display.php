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

$canDo = CollectionsHelperPermissions::getActions('post');

JToolBarHelper::title(JText::_('COM_COLLECTIONS') . ': ' . JText::_('COM_COLLECTIONS_POSTS'), 'collection.png');
/*if ($canDo->get('core.create')) 
{
	JToolBarHelper::addNew();
}
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::editList();
}*/
if ($canDo->get('core.delete')) 
{
	JToolBarHelper::deleteList();
}

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<p class="warning">Posts are currently not editable via the adminitrator interface.</p>

		<label for="filter_search"><?php echo JText::_('Search'); ?>:</label> 
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" />

		<input type="submit" value="<?php echo JText::_('GO'); ?>" />

		<input type="hidden" name="collection_id" value="<?php echo $this->filters['collection_id']; ?>" />
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->rows->total(); ?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('COM_COLLECTIONS_TITLE'), 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('COM_COLLECTIONS_POSTED'), 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('COM_COLLECTIONS_POSTEDBY'), 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', JText::_('COM_COLLECTIONS_ORIGINAL'), 'original', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php 
					jimport('joomla.html.pagination');
					$pageNav = new JPagination(
						$this->total, 
						$this->filters['start'], 
						$this->filters['limit']
					);
					echo $pageNav->getListFooter();
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
$i = 0;
foreach ($this->rows as $row)
{
	switch ($row->get('original'))
	{
		case 1:
			$class = 'publish';
			$task = 'unoriginal';
			$alt = JText::_('COM_COLLECTIONS_IS_ORIGINAL');
			break;

		case 0:
			$class = 'unpublish';
			$task = 'original';
			$alt = JText::_('COM_COLLECTIONS_IS_NOT_ORIGINAL');
			break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
				<?php /*if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->get('id'); ?>" title="<?php echo JText::_('COM_COLLECTIONS_EDIT_CATEGORY'); ?>">
						<span><?php echo $row->item()->description('clean', 75); ?></span>
					</a>
				<?php } else {*/ ?>
					<span>
						<span><?php echo $row->item()->description('clean', 75); ?></span>
					</span>
				<?php //} ?>
				</td>
				<td>
					<time datetime="<?php echo $row->get('created'); ?>"><?php echo $row->get('created'); ?></time>
				</td>
				<td>
					<span class="glyph member">
						<?php echo $this->escape($row->creator('name')); ?>
					</span>
				</td>
				<td>
				<?php if ($canDo->get('core.edit.state')) { ?>
					<a class="state <?php echo $class; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task; ?>&amp;id[]=<?php echo $row->get('id'); ?>" title="<?php echo JText::sprintf('COM_COLLECTIONS_SET_TASK', $task);?>">
						<span><?php echo $alt; ?></span>
					</a>
				<?php } else { ?>
					<span class="state <?php echo $class; ?>">
						<span><?php echo $alt; ?></span>
					</span>
				<?php } ?>
				</td>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>