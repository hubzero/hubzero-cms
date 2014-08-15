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

$canDo = ResourcesHelperPermissions::getActions('resource');

JToolBarHelper::title(JText::_('COM_RESOURCES') . ': ' . JText::_('COM_RESOURCES_CHILDREN'), 'resources.png');
if ($this->filters['parent_id'] > 0)
{
	if ($canDo->get('core.create'))
	{
		JToolBarHelper::addNew('addchild', 'COM_RESOURCES_ADD_CHILD');
	}
	if ($canDo->get('core.delete'))
	{
		JToolBarHelper::deleteList('', 'removechild', 'COM_RESOURCES_REMOVE_CHILD');
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

$this->css();

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
	<table class="adminlist">
		<thead>
		<?php if ($this->filters['parent_id'] > 0) { ?>
			<tr>
				<th colspan="9">
					<?php echo '<a href="index.php?option=' . $this->option . '&amp;controller=' . $this->controller . '&amp;task=edit&amp;id=' . $this->filters['parent_id'] . '">' . $this->escape(stripslashes($this->parent->title)) . '</a>'; ?>
				</th>
			</tr>
		<?php } ?>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th><?php echo JText::_('COM_RESOURCES_COL_ID'); ?></th>
				<th><?php echo JText::_('COM_RESOURCES_COL_TITLE'); ?></th>
				<th><?php echo JText::_('COM_RESOURCES_COL_STATUS'); ?></th>
				<th><?php echo JText::_('COM_RESOURCES_COL_ACCESS'); ?></th>
				<th><?php echo JText::_('COM_RESOURCES_COL_TYPE'); ?></th>
			<?php if ($this->filters['parent_id'] > 0) { ?>
				<th colspan="3"><?php echo JText::_('COM_RESOURCES_COL_ORDER'); ?></th>
			<?php } ?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo $colspan; ?>"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
			<?php /*<tr>
				<td colspan="9">
					ResourcesHtml::statusKey();
				</td>
			</tr> */ ?>
		</tfoot>
		<tbody>
<?php
$k = 0;

$juser = JFactory::getUser();
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row =& $this->rows[$i];

	// Build some publishing info
	$info  = JText::_('COM_RESOURCES_CREATED') . ': ' . $row->created . '<br />';
	$info .= JText::_('COM_RESOURCES_CREATED_BY') . ': ' . $this->escape($row->created_by) . '<br />';

	$now = JFactory::getDate()->toSql();
	switch ($row->published)
	{
		case 0:
			$alt   = JText::_('JUNPUBLISHED');
			$class = 'unpublished';
			$task  = 'publish';
			break;
		case 1:
			if ($now <= $row->publish_up)
			{
				$alt   = JText::_('COM_RESOURCES_PENDING');
				$class = 'pending';
				$task  = 'unpublish';
			} else if ($now <= $row->publish_down || $row->publish_down == "0000-00-00 00:00:00")
			{
				$alt   = JText::_('JPUBLISHED');
				$class = 'published';
				$task  = 'unpublish';
			}
			else if ($now > $row->publish_down)
			{
				$alt   = JText::_('COM_RESOURCES_EXPIRED');
				$class = 'expired';
				$task  = 'unpublish';
			}

			$info .= JText::_('JPUBLISHED') . ': ' . JHTML::_('date', $row->publish_up, JText::_('DATE_FORMAT_HZ1')) . '<br />';
			break;
		case 2:
			$alt   = JText::_('COM_RESOURCES_DRAFT_EXTERNAL');
			$class = 'draftexternal';
			$task  = 'publish';
			break;
		case 3:
			$alt   = JText::_('COM_RESOURCES_NEW');
			$class = 'submitted';
			$task  = 'publish';
			break;
		case 4:
			$alt   = JText::_('JTRASHED');
			$class = 'trashed';
			$task  = 'publish';
			break;
		case 5:
			$alt   = JText::_('COM_RESOURCES_DRAFT_INTERNAL');
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
			$color_access = 'public';
			$task_access  = 'accessregistered';
			$row->groupname = 'COM_RESOURCES_ACCESS_PUBLIC';
			break;
		case 1:
			$color_access = 'registered';
			$task_access  = 'accessspecial';
			$row->groupname = 'COM_RESOURCES_ACCESS_REGISTERED';
			break;
		case 2:
			$color_access = 'special';
			$task_access  = 'accessprotected';
			$row->groupname = 'COM_RESOURCES_ACCESS_SPECIAL';
			break;
		case 3:
			$color_access = 'protected';
			$task_access  = 'accessprivate';
			$row->groupname = 'COM_RESOURCES_ACCESS_PROTECTED';
			break;
		case 4:
			$color_access = 'private';
			$task_access  = 'accesspublic';
			$row->groupname = 'COM_RESOURCES_ACCESS_PRIVATE';
			break;
	}

	if (!isset($row->child_id))
	{
		$row->child_id = $row->id;
	}

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
	if (($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00') && $row->checked_out != $juser->get('id'))
	{
		//$checked = JHTML::_('grid.checkedOut', $row, $i);
		$checked = JHtml::_('image', 'admin/checked_out.png', null, null, true);
		$info .= ($row->checked_out_time != '0000-00-00 00:00:00')
				 ? JText::_('COM_RESOURCES_CHECKED_OUT') . ': ' . JHTML::_('date', $row->checked_out_time, JText::_('DATE_FORMAT_HZ1')) . '<br />'
				 : '';
		if ($row->editor)
		{
			$info .= JText::_('COM_RESOURCES_CHECKED_OUT_BY') . ': ' . $this->escape($row->editor);
		}
	}
	else
	{
		$checked = JHTML::_('grid.id', $i, $row->child_id, false, 'id');
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $checked; ?>
				</td>
				<td>
					<?php echo $row->child_id; ?>
				</td>
				<td>
					<?php if ((($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00') && $row->checked_out != $juser->get('id')) || !$canDo->get('core.edit')) { ?>
						<span class="editlinktip hasTip" title="<?php echo JText::_('COM_RESOURCES_PUBLISH_INFO');?>::<?php echo $info; ?>">
							<?php echo $this->escape(stripslashes($row->title)); ?>
						</span>
						<?php echo ($row->standalone != 1 && $row->path != '') ? '<br /><small>' . $row->path . '</small>': ''; ?>
					<?php } else { ?>
						<a class="editlinktip hasTip" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->child_id; ?>&amp;pid=<?php echo $this->filters['parent_id']; ?>" title="<?php echo JText::_('COM_RESOURCES_PUBLISH_INFO');?>::<?php echo $info; ?>">
							<?php echo $this->escape(stripslashes($row->title)); ?>
						</a>
						<?php echo ($row->standalone != 1 && $row->path != '') ? '<br /><small>' . $row->path . '</small>': ''; ?>
					<?php } ?>
				</td>
				<td>
					<?php if ($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00' || !$canDo->get('core.edit.state')) { ?>
						<span class="state <?php echo $class;?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } else { ?>
						<a class="state <?php echo $class;?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task; ?>&amp;id=<?php echo $row->child_id; ?>&amp;pid=<?php echo $this->filters['parent_id']; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::sprintf('COM_RESOURCES_SET_TASK_TO', $task); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } ?>
				</td>
				<td>
					<?php if ($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00' || !$canDo->get('core.edit.state')) { ?>
						<span class="access <?php echo $color_access; ?>">
							<span><?php echo $row->groupname; ?></span>
						</span>
					<?php } else { ?>
						<a class="access <?php echo $color_access; ?>" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task_access; ?>&amp;id=<?php echo $row->child_id; ?>&amp;pid=<?php echo $this->filters['parent_id']; ?>" title="<?php echo JText::_('COM_RESOURCES_CHANGE_ACCESS'); ?>">
							<span><?php echo JText::_($row->groupname); ?></span>
						</a>
					<?php } ?>
				</td>
				<td>
					<?php echo $typec; ?>
				</td>
			<?php if ($this->filters['parent_id'] > 0) { ?>
				<td>
					<?php echo $this->pageNav->orderUpIcon( $i, ($row->position == @$rows[$i-1]->position) ); ?>
				</td>
				<td>
					<?php echo $this->pageNav->orderDownIcon( $i, $n, ($row->position == @$rows[$i+1]->position) ); ?>
				</td>
				<td>
					<?php echo $row->ordering; ?>
				</td>
			<?php } ?>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="viewtask" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="pid" value="<?php echo $this->filters['parent_id']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>
