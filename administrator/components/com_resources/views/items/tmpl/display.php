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

JToolBarHelper::title(JText::_('COM_RESOURCES'), 'resources.png');
if ($canDo->get('core.admin'))
{
	JToolBarHelper::custom('check', 'scan', '', 'COM_RESOURCES_CHECK_PATHS', false);
	JToolBarHelper::spacer();
	JToolBarHelper::preferences($this->option, '550');
	JToolBarHelper::spacer();
}
if ($canDo->get('core.create'))
{
	JToolBarHelper::addNew('addchild', 'COM_RESOURCES_ADD_CHILD');
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

JHTML::_('behavior.tooltip');

$this->css();
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo JText::_('JSEARCH_FILTER'); ?>: </label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo JText::_('COM_RESOURCES_SEARCH_PLACEHOLDER'); ?>" />

		<label for="status"><?php echo JText::_('COM_RESOURCES_FILTER_STATUS'); ?>:</label>
		<select name="status" id="status">
			<option value="all"<?php echo ($this->filters['status'] == 'all') ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_RESOURCES_ALL'); ?></option>
			<option value="2"<?php echo ($this->filters['status'] == 2) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_RESOURCES_DRAFT_EXTERNAL'); ?></option>
			<option value="5"<?php echo ($this->filters['status'] == 5) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_RESOURCES_DRAFT_INTERNAL'); ?></option>
			<option value="3"<?php echo ($this->filters['status'] == 3) ? ' selected="selected"' : ''; ?>><?php echo JText::_('COM_RESOURCES_PENDING'); ?></option>
			<option value="0"<?php echo ($this->filters['status'] == 0 && $this->filters['status'] != 'all') ? ' selected="selected"' : ''; ?>><?php echo JText::_('JUNPUBLISHED'); ?></option>
			<option value="1"<?php echo ($this->filters['status'] == 1) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JPUBLISHED'); ?></option>
			<option value="4"<?php echo ($this->filters['status'] == 4) ? ' selected="selected"' : ''; ?>><?php echo JText::_('JTRASHED'); ?></option>
		</select>

		<label for="type"><?php echo JText::_('COM_RESOURCES_FILTER_TYPE'); ?>:</label>
		<?php echo ResourcesHtml::selectType($this->types, 'type', $this->filters['type'], JText::_('COM_RESOURCES_ALL'), '', '', ''); ?>

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('COM_RESOURCES_GO'); ?>" />
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo ($this->rows) ? count($this->rows) : 0; ?>);" /></th>
				<th><?php echo JHTML::_('grid.sort', 'COM_RESOURCES_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'COM_RESOURCES_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'COM_RESOURCES_COL_STATUS', 'published', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'COM_RESOURCES_COL_ACCESS', 'access', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'COM_RESOURCES_COL_MODIFIED', 'modified', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JText::_('COM_RESOURCES_COL_LICENSE'); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'COM_RESOURCES_COL_TYPE', 'type', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JHTML::_('grid.sort', 'COM_RESOURCES_COL_CHILDREN', 'children', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo JText::_('COM_RESOURCES_COL_TAGS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
$filterstring  = '';

$database = JFactory::getDBO();

for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row =& $this->rows[$i];

	$rparams = new JRegistry($row->params);
	$license = $rparams->get('license');

	// Build some publishing info
	$info  = JText::_('COM_RESOURCES_CREATED') . ': ' . JHTML::_('date', $row->created, JText::_('DATE_FORMAT_HZ1')) . '<br />';
	$info .= JText::_('COM_RESOURCES_CREATED_BY') . ': ' . $this->escape($row->created_by) . '<br />';

	// Get the published status
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

	// Get the tags on this item
	$rt = new ResourcesTags($row->id);
	$tags = $rt->tags('count');

	// See if it's checked out or not
	if ($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00')
	{
		$date = JHtml::_('date', $row->checked_out_time, JText::_('DATE_FORMAT_LC1'));
		$time = JHtml::_('date', $row->checked_out_time, 'H:i');

		$checked  = '<span class="editlinktip hasTip" title="' . JText::_('JLIB_HTML_CHECKED_OUT') . '::' . $this->escape($row->editor) . '<br />' . $date . '<br />' . $time . '">';
		$checked .= JHtml::_('image', 'admin/checked_out.png', null, null, true) . '</span>';

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
					<a class="editlinktip hasTip" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->id;  echo $filterstring; ?>" title="<?php echo JText::_('COM_RESOURCES_PUBLISH_INFO');?>::<?php echo $info; ?>">
						<span><?php echo $this->escape(stripslashes($row->title)); ?></span>
					</a>
				</td>
				<td>
					<a class="state <?php echo $class; ?> hasTip" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task; ?>&amp;id=<?php echo $row->id; echo $filterstring; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo $alt; ?> :: <?php echo JText::sprintf('COM_RESOURCES_SET_TASK_TO', $task); ?>">
						<span><?php echo $alt; ?></span>
					</a>
				</td>
				<td>
					<a class="access <?php echo $color_access; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $task_access; ?>&amp;id=<?php echo $row->id; echo $filterstring; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::_('COM_RESOURCES_CHANGE_ACCESS'); ?>">
						<span><?php echo $this->escape(JText::_($row->groupname)); ?></span>
					</a>
				</td>
				<td style="white-space: nowrap">
					<?php if ($row->modified == '0000-00-00 00:00:00') { echo JText::_('COM_RESOURCES_NOT_MODIFIED'); } else { ?>
						<time datetime="<?php echo ($row->modified != '0000-00-00 00:00:00' ? $row->modified : $row->created); ?>">
							<?php echo JHTML::_('date', ($row->modified != '0000-00-00 00:00:00' ? $row->modified : $row->created), JText::_('DATE_FORMAT_HZ1')); ?>
						</time>
					<?php } ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($license)); ?>
				</td>
				<td style="white-space: nowrap">
					<?php echo $this->escape(stripslashes($row->typetitle)); ?>
				</td>
				<td style="white-space: nowrap">
					<?php if ($row->children > 0) { ?>
						<a class="glyph menulist" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=children&amp;pid=<?php echo $row->id; ?>">
							<span><?php echo $row->children; ?></span>
						</a>
					<?php } else { ?>
						<a class="state add" href="index.php?option=<?php echo $this->option; ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=addchild&amp;pid=<?php echo $row->id; ?>">
							<span><?php echo JText::_('COM_RESOURCES_ADD'); ?></span>
						</a>
					<?php } ?>
				</td>
				<td style="white-space: nowrap">
					<?php if ($tags > 0) { ?>
						<a class="glyph tag" href="index.php?option=<?php echo $this->option; ?>&amp;controller=tags&amp;id=<?php echo $row->id; ?>">
							<span><?php echo $tags; ?></span>
						</a>
					<?php } else { ?>
						<a class="state add" href="index.php?option=<?php echo $this->option; ?>&amp;controller=tags&amp;id=<?php echo $row->id; ?>">
							<span><?php echo JText::_('COM_RESOURCES_ADD'); ?></span>
						</a>
					<?php } ?>
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
