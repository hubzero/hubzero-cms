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

JToolBarHelper::title(JText::_('COM_WISHLIST') . ': ' . JText::_('COM_WISHLIST_WISHES'), 'wishlist.png');
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
JToolBarHelper::help('wishes');
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
			<label for="filter-by"><?php echo JText::_('COM_WISHLIST_FILTERBY'); ?>:</label>
			<select name="filterby" id="filter-by">
				<option value="all"<?php echo ($this->filters['filterby'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WISHLIST_NONE'); ?></option>
				<option value="granted"<?php echo ($this->filters['filterby'] == 'granted') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WISHLIST_STATE_GRANTED'); ?></option>
				<option value="open"<?php echo ($this->filters['filterby'] == 'open') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WISHLIST_STATE_OPEN'); ?></option>
				<option value="accepted"<?php echo ($this->filters['filterby'] == 'accepted') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WISHLIST_STATE_ACCEPTED'); ?></option>
				<option value="pending"<?php echo ($this->filters['filterby'] == 'pending') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WISHLIST_STATE_PENDING'); ?></option>
				<option value="rejected"<?php echo ($this->filters['filterby'] == 'rejected') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WISHLIST_STATE_REJECTED'); ?></option>
				<option value="withdrawn"<?php echo ($this->filters['filterby'] == 'withdrawn') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WISHLIST_STATE_WITHDRAWN'); ?></option>
				<option value="deleted"<?php echo ($this->filters['filterby'] == 'deleted') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WISHLIST_STATE_DELETED'); ?></option>
				<option value="useraccepted"<?php echo ($this->filters['filterby'] == 'useraccepted') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WISHLIST_STATE_USER_ACCEPTED'); ?></option>
				<option value="private"<?php echo ($this->filters['filterby'] == 'private') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WISHLIST_STATE_PRIVATE'); ?></option>
				<option value="public"<?php echo ($this->filters['filterby'] == 'public') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WISHLIST_STATE_PUBLIC'); ?></option>
				<option value="assigned"<?php echo ($this->filters['filterby'] == 'assigned') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_WISHLIST_STATE_ASSIGNED'); ?></option>
			</select>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<?php if ($this->wishlist->id) { ?>
			<tr>
				<th colspan="8">
					(<?php echo $this->escape(stripslashes($this->wishlist->category)); ?>) &nbsp; <?php echo $this->escape(stripslashes($this->wishlist->title)); ?>
				</th>
			</tr>
			<?php } ?>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WISHLIST_WISH_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WISHLIST_TITLE', 'subject', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<?php if (!$this->wishlist->id) { ?>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WISHLIST_WISHLIST_ID', 'wishlist', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<?php } ?>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WISHLIST_PROPOSED_BY', 'proposed_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WISHLIST_PROPOSED', 'proposed', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WISHLIST_STATE', 'status', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WISHLIST_ACCESS', 'private', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_WISHLIST_COMMENTS', 'comments', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
 			<tr>
 				<td colspan="8"><?php echo $this->pageNav->getListFooter(); ?></td>
 			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row =& $this->rows[$i];
	switch ($row->status)
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
		default;
			$class = 'unpublish';
			$task = 'publish';
			$alt = JText::_('COM_WISHLIST_UNPUBLISHED');
		break;
	}

	if ($row->private)
	{
		$color_access = 'access private';
		$task_access = 'accesspublic';
		$groupname = 'Private';
	}
	else
	{
		$color_access = 'access public';
		$task_access = 'accessregistered';
		$groupname = 'Public';
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>">
							<span><?php echo $this->escape(stripslashes($row->subject)); ?></span>
						</a>
					<?php } else { ?>
						<span>
							<span><?php echo $this->escape(stripslashes($row->subject)); ?></span>
						</span>
					<?php } ?>
				</td>
				<?php if (!$this->wishlist->id) { ?>
					<td>
						<?php echo $row->wishlist; ?>
					</td>
				<?php } ?>
				<td>
					<?php echo $this->escape(stripslashes($row->authorname)); ?>
				</td>
				<td>
					<time datetime="<?php echo $row->proposed; ?>"><?php echo $row->proposed; ?></time>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $class; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task;?>&amp;id=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('COM_WISHLIST_SET_TASK',$task);?>">
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
					<a class="glyph comment" href="index.php?option=<?php echo $this->option ?>&amp;controller=comments&amp;wish=<?php echo $row->id; ?>">
						<span><?php echo $this->escape(stripslashes($row->numreplies)); ?></span>
					</a>
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
	<input type="hidden" name="wishlist" value="<?php echo $this->filters['wishlist']; ?>" />
	<input type="hidden" name="cid" value="<?php echo $this->filters['wishlist']; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>
