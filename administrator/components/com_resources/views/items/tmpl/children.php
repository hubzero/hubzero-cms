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

$dateFormat = '%d %b, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$tz = false;
}

$canDo = ResourcesHelper::getActions('resource');

JToolBarHelper::title('<a href="index.php?option=' . $this->option . '&amp;controller=' . $this->controller . '">' . JText::_('Resource Manager') . '</a>', 'resources.png');
if ($this->filters['parent_id'] > 0)
{
	if ($canDo->get('core.create')) 
	{
		JToolBarHelper::addNew('addchild', 'Add Child');
	}
	if ($canDo->get('core.delete')) 
	{
		JToolBarHelper::deleteList('', 'removechild', 'Remove Child');
	}
	JToolBarHelper::spacer();
}
if ($canDo->get('core.edit.state')) 
{
	JToolBarHelper::publishList();
	JToolBarHelper::unpublishList();
	JToolBarHelper::spacer();
}
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.delete')) 
{
	JToolBarHelper::deleteList();
}

JHTML::_('behavior.tooltip');
include_once(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'html' . DS . 'html' . DS . 'grid.php');

if ($this->filters['parent_id'] > 0)
{
	$colspan = 9;
	if ($this->parent->type == 5)
	{
		$colspan = 10;
	}
}
else
{
	$colspan = 7;
}
?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('adminForm');
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
<?php
	if ($this->filters['parent_id'] > 0) {
		//echo '<h3 class="extranav" style="text-align: left;"><a href="index2.php?option='.$this->option.'&amp;task=edit&amp;id[]='.$this->pid.'" title="Edit this resource">'. stripslashes($parent->title) .'</a> <span>[ <a href="index2.php?option='.$this->option.'&amp;type='.$parent->type.'">'.$parent->type.'</a> ]</span></h3>'."\n";
		echo '<h3><a href="index.php?option=' . $this->option . '&amp;controller=' . $this->controller . '&amp;task=edit&amp;id[]=' . $this->filters['parent_id'] . '">' . stripslashes($this->parent->title) . '</a></h3>' . "\n";
	}
?>

	<!-- <fieldset id="filter">
		<label for="search">
			Search: 
			<input type="text" name="search" id="search" value="<?php echo $this->filters['search']; ?>" />
		</label>

		<label for="sort">
			Sort: 
			<select name="sort" id="sort">
				<option value="ordering"<?php if($this->filters['sort'] == 'ordering') { echo ' selected="selected"'; } ?>>Ordering</option>
				<option value="created DESC"<?php if($this->filters['sort'] == 'created') { echo ' selected="selected"'; } ?>>Date</option>
				<option value="title"<?php if($this->filters['sort'] == 'title') { echo ' selected="selected"'; } ?>>Title</option>
				<option value="id"<?php if($this->filters['sort'] == 'id') { echo ' selected="selected"'; } ?>>ID number</option>
			</select>
		</label>
	
		<label for="status">
			Status:
			<select name="status" id="status">
				<option value="all"<?php echo ($this->filters['status'] == 'all') ? ' selected="selected"' : ''; ?>>[ all ]</option>
				<option value="2"<?php echo ($this->filters['status'] == 2) ? ' selected="selected"' : ''; ?>>Draft (user created)</option>
				<option value="5"<?php echo ($this->filters['status'] == 5) ? ' selected="selected"' : ''; ?>>Draft (internal)</option>
				<option value="3"<?php echo ($this->filters['status'] == 3) ? ' selected="selected"' : ''; ?>>Pending</option>
				<option value="0"<?php echo ($this->filters['status'] == 0 && $this->filters['status'] != 'all') ? ' selected="selected"' : ''; ?>>Unpublished</option>
				<option value="1"<?php echo ($this->filters['status'] == 1) ? ' selected="selected"' : ''; ?>>Published</option>
				<option value="4"<?php echo ($this->filters['status'] == 4) ? ' selected="selected"' : ''; ?>>Deleted</option>
			</select>
		</label>

		<input type="submit" name="filter_submit" id="filter_submit" value="Go" />
	</fieldset> -->

	<table class="adminlist" summary="<?php echo JText::_('A list of resources and their types, published status, access levels, and other relevant data'); ?>">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th><?php echo JText::_('ID'); ?></th>
				<th><?php echo JText::_('Title'); ?></th>
				<th><?php echo JText::_('Status'); ?></th>
				<th><?php echo JText::_('Access'); ?></th>
				<th><?php echo JText::_('Type'); ?></th>
<?php 	if ($this->filters['parent_id'] > 0) { ?>
				<th colspan="3"><?php echo JText::_('Reorder'); ?></th>
<?php 		if ($this->parent->type == 4) { ?>
				<th><?php echo JText::_('Section'); ?></th>
<?php 		} ?>
<?php 	} ?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo $colspan; ?>"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
//$filterstring  = '&amp;pid=' . $this->pid;
//$filterstring .= ($this->filters['sort'])   ? '&amp;sort=' . $this->filters['sort']     : '';
//$filterstring .= '&amp;status=' . $this->filters['status'];

for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row =& $this->rows[$i];

	// Build some publishing info
	$info  = JText::_('Created') . ': ' . $row->created . '<br />';
	$info .= JText::_('Created by') . ': ' . $this->escape($row->created_by) . '<br />';

	$now = date( "Y-m-d H:i:s" );
	switch ($row->published)
	{
		case 0:
			$alt   = 'Unpublish';
			$class = 'unpublished';
			$task  = 'publish';
			break;
		case 1:
			if ($now <= $row->publish_up) {
				$alt   = 'Pending';
				$class = 'pending';
				$task  = 'unpublish';
			} else if ($now <= $row->publish_down || $row->publish_down == "0000-00-00 00:00:00") {
				$alt   = 'Published';
				$class = 'published';
				$task  = 'unpublish';
			} else if ($now > $row->publish_down) {
				$alt   = 'Expired';
				$class = 'expired';
				$task  = 'unpublish';
			}
			$info .= JText::_('Published') . ': ' . $row->publish_up . '<br />';
			break;
		case 2:
			$alt   = 'Draft (user created)';
			$class = 'draftexternal';
			$task  = 'publish';
			break;
		case 3:
			$alt   = 'New';
			$class = 'new';
			$task  = 'publish';
			break;
		case 4:
			$alt   = 'Delete';
			$class = 'deleted';
			$task  = 'publish';
			break;
		case 5:
			$alt   = 'Draft (internal production)';
			$class = 'draftinternal';
			$task  = 'publish';
			break;
		default:
			$alt   = '-';
			$class = '';
			$task  = '';
			break;
	}

	switch ($row->access)
	{
		case 0:
			$color_access = 'style="color: green;"';
			$task_access = 'accessregistered';
			break;
		case 1:
			$color_access = 'style="color: red;"';
			$task_access = 'accessspecial';
			break;
		case 2:
			$color_access = 'style="color: black;"';
			$task_access = 'accessprotected';
			break;
		case 3:
			$color_access = 'style="color: blue;"';
			$task_access = 'accessprivate';
			$row->groupname = 'Protected';
			break;
		case 4:
			$color_access = 'style="color: red;"';
			$task_access = 'accesspublic';
			$row->groupname = 'Private';
			break;
	}

	/*if ($this->pid != '-1') {
		if ($row->multiuse > 0) {
			$beingused = true;
		} else {
			$beingused = false;
		}
	}*/

	if ($row->logicaltitle)
	{
		$typec  = $this->escape($row->logicaltitle);
		$typec .= ' (' . $this->escape(stripslashes($row->typetitle)) . ')';
	}
	else
	{
		$typec = $this->escape(stripslashes($row->typetitle));
	}

	// See if it's checked out or not
	if ($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00')
	{
		$checked = JHTMLGrid::_checkedOut($row);
		$info .= ($row->checked_out_time != '0000-00-00 00:00:00')
				 ? JText::_('Checked out') . ': ' . JHTML::_('date', $row->checked_out_time, $dateFormat, $tz) . '<br />'
				 : '';
		if ($row->editor)
		{
			$info .= JText::_('Checked out by') . ': ' . $this->escape($row->editor);
		}
	}
	else
	{
		$checked = JHTML::_('grid.id', $i, $row->id, false, 'id');
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $checked; ?>
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
<?php if ($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00' || !$canDo->get('core.edit')) { ?>
					<span class="editlinktip hasTip" title="<?php echo JText::_('Publish Information');?>::<?php echo $info; ?>">
						<?php echo $this->escape(stripslashes($row->title)); ?>
					</span>
<?php } else { ?>
					<a class="editlinktip hasTip" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id; ?>&amp;pid=<?php echo $this->filters['parent_id']; ?>" title="<?php echo JText::_('Publish Information');?>::<?php echo $info; ?>">
						<?php echo $this->escape(stripslashes($row->title)); ?>
					</a>
					<?php echo ($row->standalone != 1 && $row->path != '') ? '<br /><small>' . $row->path . '</small>': ''; ?>
<?php } ?>
				</td>
				<td>
<?php if ($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00' || !$canDo->get('core.edit.state')) { ?>
					<span class="<?php echo $class;?>">
						<span><?php echo $alt; ?></span>
					</span>
<?php } else { ?>
					<a class="<?php echo $class;?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task; ?>&amp;id[]=<?php echo $row->id; ?>&amp;pid=<?php echo $this->filters['parent_id']; ?>" title="Set this to <?php echo $task;?>">
						<span><?php echo $alt; ?></span>
					</a>
<?php } ?>
				</td>
				<td>
<?php if ($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00' || !$canDo->get('core.edit.state')) { ?>
					<span class="access" <?php echo $color_access; ?>>
						<span><?php echo $row->groupname; ?></span>
					</span>
<?php } else { ?>
					<a class="access" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task_access; ?>&amp;id=<?php echo $row->id; ?>&amp;pid=<?php echo $this->filters['parent_id']; ?>" <?php echo $color_access; ?> title="Change Access">
						<span><?php echo $row->groupname; ?></span>
					</a>
<?php } ?>
				</td>
				<td>
					<?php echo $typec; ?>
				</td>
<?php 	if ($this->filters['parent_id'] > 0) { ?>
				<td>
					<?php echo $this->pageNav->orderUpIcon( $i, ($row->position == @$rows[$i-1]->position) ); ?>
				</td>
				<td>
					<?php echo $this->pageNav->orderDownIcon( $i, $n, ($row->position == @$rows[$i+1]->position) ); ?>
				</td>
				<td>
					<?php echo $row->ordering; ?>
				</td>
<?php 		if ($this->parent->type == 4) { ?>
				<td>
					<?php echo ResourcesHtml::selectSection('grouping' . $row->id, $this->sections, $row->grouping, '', $i); ?>
				</td>
<?php 		} ?>
<?php 	} ?>
	  		</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<?php ResourcesHtml::statusKey(); ?>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="viewtask" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="pid" value="<?php echo $this->filters['parent_id']; ?>" />
	
	<?php echo JHTML::_('form.token'); ?>
</form>
