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

$canDo = WishlistHelper::getActions('component');

JToolBarHelper::title(JText::_('COM_WISHLIST'), 'wishlist.png');
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
JToolBarHelper::help('lists');
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
		<div class="col width-50 fltlft">
			<label for="filter_search"><?php echo JText::_('COM_WISHLIST_SEARCH'); ?>:</label>
			<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_WISHLIST_SEARCH_PLACEHOLDER'); ?>" />

			<input type="submit" value="<?php echo JText::_('COM_WISHLIST_GO'); ?>" />
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="col width-50 fltrt">
			<label for="filter-category"><?php echo JText::_('COM_WISHLIST_CATEGORY'); ?>:</label>
			<select name="category" id="filter-category">
				<option value=""<?php echo ($this->filters['category'] == '') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WISHLIST_SELECT_CATEGORY'); ?></option>
				<option value="general"<?php echo ($this->filters['category'] == 'general') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WISHLIST_CATEGORY_GENERAL'); ?></option>
				<option value="group"<?php echo ($this->filters['category'] == 'group') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WISHLIST_CATEGORY_GROUP'); ?></option>
				<option value="resource"<?php echo ($this->filters['category'] == 'resource') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WISHLIST_CATEGORY_RESOURCE'); ?></option>
			</select>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WISHLIST_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WISHLIST_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WISHLIST_ACCESS', 'public', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" colspan="2"><?php echo JHTML::_('grid.sort', 'COM_WISHLIST_CATEGORY', 'category', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WISHLIST_WISHES', 'total', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row =& $this->rows[$i];
	switch ($row->state)
	{
		case 1:
			$class = 'publish';
			$task = 'unpublish';
			$alt = JText::_('COM_WISHLIST_PUBLISHED');
		break;
		case 2:
			$class = 'expire';
			$task = 'publish';
			$alt = JText::_('COM_WISHLIST_TRASHED');
		break;
		case 0:
			$class = 'unpublish';
			$task = 'publish';
			$alt = JText::_('COM_WISHLIST_UNPUBLISHED');
		break;
	}

	if (!$row->public)
	{
		$color_access = 'access private';
		$task_access = 'accessregistered';
		$groupname = 'Private';
	}
	else
	{
		$color_access = 'access public';
		$task_access = 'accesspublic';
		$groupname = 'Public';
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id; ?>">
						<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
					</a>
				<?php } else { ?>
					<span>
						<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
					</span>
				<?php } ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit.state')) { ?>
					<a class="state <?php echo $class; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task;?>&amp;id[]=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('COM_WISHLIST_SET_TASK',$task);?>">
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
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task_access; ?>&amp;id=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" class="<?php echo $color_access; ?>" title="<?php echo JText::_('COM_WISHLIST_CHANGE_ACCESS'); ?>">
						<?php echo $groupname; ?>
					</a>
				<?php } else { ?>
					<span class="<?php echo $color_access; ?>">
						<?php echo $groupname; ?>
					</span>
				<?php } ?>
				</td>
				<td>
					<span class="glyph category">
						<span><?php echo $this->escape(stripslashes($row->category)); ?></span>
					</span>
				</td>
				<td>
					<span>
						<span><?php echo $this->escape(stripslashes($row->referenceid)); ?></span>
					</span>
				</td>
				<td>
				<?php if ($row->wishes > 0) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=wishes&amp;wishlist=<?php echo $row->id; ?>">
						<span><?php echo $row->wishes . ' ' . JText::_('COM_WISHLIST_LIST_WISHES'); ?></span>
					</a>
				<?php } else { ?>
					<span>
						<span><?php echo $row->wishes; ?></span>
					</span>
				<?php } ?>
				</td>
			</tr>
<?php
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
