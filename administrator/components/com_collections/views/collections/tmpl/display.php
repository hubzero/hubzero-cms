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

$canDo = CollectionsHelperPermissions::getActions('collection');

JToolBarHelper::title(JText::_('COM_COLLECTIONS'), 'collection.png');
if ($canDo->get('core.admin'))
{
	JToolBarHelper::preferences($this->option, '550');
	JToolBarHelper::spacer();
}
if ($canDo->get('core.edit.state'))
{
	JToolBarHelper::publishList();
	JToolBarHelper::unpublishList();
	JToolBarHelper::spacer();
}
if ($canDo->get('core.create'))
{
	JToolBarHelper::addNew();
}
if ($canDo->get('core.edit'))
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.delete'))
{
	JToolBarHelper::deleteList();
}
JToolBarHelper::spacer();
JToolBarHelper::help('collections');
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
		<label for="filter_search"><?php echo JText::_('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_COLLECTIONS_FILTER_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo JText::_('COM_COLLECTIONS_GO'); ?>" />
		<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->rows->total(); ?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_COLLECTIONS_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_COLLECTIONS_COL_PUBLISHED', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_COLLECTIONS_COL_ACCESS', 'access', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_COLLECTIONS_COL_OWNER', 'object_type', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_COLLECTIONS_COL_POSTS', 'posts', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
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
	//$row =& $this->rows[$i];
	switch ($row->get('state'))
	{
		case 1:
			$class = 'publish';
			$task = 'unpublish';
			$alt = JText::_('JPUBLISHED');
			break;
		case 2:
			$class = 'expire';
			$task = 'publish';
			$alt = JText::_('JTRASHED');
			break;
		case 0:
			$class = 'unpublish';
			$task = 'publish';
			$alt = JText::_('JUNPUBLISHED');
			break;
	}

	switch ($row->get('access', 0))
	{
		case 0:
			$color_access = 'public';
			$task_access = 'accessregistered';
			$row->set('groupname', JText::_('COM_COLLECTIONS_ACCESS_PUBLIC'));
		break;
		case 1:
			$color_access = 'registered';
			//$task_access = 'accessspecial';
			$task_access = 'accessprivate';
			$row->set('groupname', JText::_('COM_COLLECTIONS_ACCESS_REGISTERED'));
		break;
		/*case 2:
			$color_access = 'special';
			$task_access = 'accessprivate';
			$row->set('groupname', JText::_('COM_COLLECTIONS_ACCESS_SPECIAL'));
		break;*/
		case 4:
			$color_access = 'private';
			$task_access = 'accesspublic';
			$row->set('groupname', JText::_('COM_COLLECTIONS_ACCESS_PRIVATE'));
		break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a class="glyph category" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->get('id'); ?>">
						<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
					</a>
				<?php } else { ?>
					<span class="glyph category">
						<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
					</span>
				<?php } ?>
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
				<td>
				<?php if ($canDo->get('core.edit.state')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task_access; ?>&amp;id=<?php echo $row->get('id'); ?>" class="access <?php echo $color_access; ?>" title="<?php echo JText::_('COM_COLLECTIONS_CHANGE_ACCESS'); ?>">
						<span><?php echo $row->get('groupname'); ?></span>
					</a>
				<?php } else { ?>
					<span class="access <?php echo $color_access; ?>">
						<span><?php echo $row->get('groupname'); ?></span>
					</span>
				<?php } ?>
				</td>
				<td>
					<span class="scope">
						<span><?php echo $this->escape($row->get('object_type')) . ' (' . $this->escape($row->get('object_id')) . ')'; ?></span>
					</span>
				</td>
				<td>
				<?php if ($row->get('posts', 0) > 0) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=posts&amp;collection_id=<?php echo $row->get('id'); ?>">
						<span><?php echo JText::sprintf('COM_COLLECTIONS_NUM_POSTS', $row->get('posts', 0)); ?></span>
					</a>
				<?php } else { ?>
					<span>
						<span><?php echo $row->get('posts', 0); ?></span>
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