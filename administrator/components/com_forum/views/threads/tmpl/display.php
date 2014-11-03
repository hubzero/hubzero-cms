<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = ForumHelperPermissions::getActions('thread');

JToolBarHelper::title(JText::_('COM_FORUM') . ': ' . JText::_('COM_FORUM_THREADS'), 'forum.png');
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
if ($canDo->get('core.delete'))
{
	JToolBarHelper::deleteList();
}
JToolBarHelper::spacer();
JToolBarHelper::help('threads');
?>
<script type="text/javascript">
function submitbutton(pressbutton) {
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="field-category_id"><?php echo JText::_('COM_FORUM_FILTER_CATEGORY'); ?>:</label>
		<select name="category_id" id="field-category_id" onchange="document.adminForm.submit( );">
			<option value="-1"><?php echo JText::_('COM_FORUM_FILTER_CATEGORY_SELECT'); ?></option>
		<?php foreach ($this->sections as $scope => $sections) { ?>
			<optgroup label="<?php echo $this->escape(stripslashes($scope)); ?>">
			<?php foreach ($sections as $section) { ?>
				<optgroup label="&nbsp; &nbsp; <?php echo $this->escape(stripslashes($section->title)); ?>">
				<?php foreach ($section->categories as $category) { ?>
					<option value="<?php echo $category->id; ?>"<?php if ($this->filters['category_id'] == $category->id) { echo ' selected="selected"'; } ?>>&nbsp; &nbsp; <?php echo $this->escape(stripslashes($category->title)); ?></option>
				<?php } ?>
				</optgroup>
			<?php
				if (!isset($list[$section->scope]))
				{
					$list[$section->scope] = array();
				}
				if (!isset($list[$section->scope][$section->scope_id]))
				{
					$list[$section->scope][$section->scope_id] = $scope;
				}
			}
			?>
			</optgroup>
		<?php } ?>
		</select>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->results );?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_FORUM_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_FORUM_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_FORUM_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_FORUM_COL_STICKY', 'sticky', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_FORUM_COL_ACCESS', 'access', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_FORUM_COL_SCOPE', 'scope', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_FORUM_COL_CREATOR', 'created_by', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_FORUM_COL_CREATED', 'created', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
if ($this->results)
{
	$k = 0;
	for ($i=0, $n=count($this->results); $i < $n; $i++)
	{
		$row =& $this->results[$i];
		switch (intval($row->state))
		{
			case 2:
				$task = 'publish';
				$alt = JText::_('JTRASHED');
				$cls = 'trash';
			break;
			case 1:
				$task = 'unpublish';
				$alt = JText::_('JPUBLISHED');
				$cls = 'publish';
			break;
			case 0:
			default:
				$task = 'publish';
				$alt = JText::_('JUNPUBLISHED');
				$cls = 'unpublish';
			break;
		}

		switch ($row->sticky)
		{
			case '1':
				$stickyTask = '0';
				$stickyAlt = JText::_('COM_FORUM_STICKY');
				$stickyTitle = JText::_('COM_FORUM_NOT_STICKY');
				$scls = 'publish';
			break;
			case '0':
			default:
				$stickyTask = '1';
				$stickyAlt = JText::_('COM_FORUM_NOT_STICKY');
				$stickyTitle = JText::_('COM_FORUM_STICKY');
				$scls = 'unpublish';
			break;
		}

		switch ($row->access)
		{
			case 0:
				$color_access = 'public';
				$task_access  = '1';
				$row->groupname = JText::_('COM_FORUM_ACCESS_PUBLIC');
				break;
			case 1:
				$color_access = 'registered';
				$task_access  = '2';
				$row->groupname = JText::_('COM_FORUM_ACCESS_REGISTERED');
				break;
			case 2:
				$color_access = 'special';
				$task_access  = '3';
				$row->groupname = JText::_('COM_FORUM_ACCESS_SPECIAL');
				break;
			case 3:
				$color_access = 'protected';
				$task_access  = '4';
				$row->groupname = JText::_('COM_FORUM_ACCESS_PROTECTED');
				break;
			case 4:
				$color_access = 'private';
				$task_access  = '0';
				$row->groupname = JText::_('COM_FORUM_ACCESS_PRIVATE');
				break;
		}
?>
			<tr class="<?php echo "row$k" . ($row->state ==2 ? ' archived' : ''); ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<?php echo $row->id; ?>
				</td>
				<td>
					<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&category_id=' . $this->filters['category_id'] . '&task=thread&thread=' . $row->thread); ?>">
						<?php echo $this->escape(stripslashes($row->title)); ?>
					</a>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $cls; ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&category_id=' . $this->filters['category_id'] . '&task=' . $task . '&id=' . $row->id . '&' . JUtility::getToken() . '=1'); ?>" title="<?php echo JText::sprintf('COM_FORUM_SET_TO', $task); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $cls; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $scls; ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&category_id=' . $this->filters['category_id'] . '&task=sticky&sticky=' . $stickyTask . '&id=' . $row->id . '&' . JUtility::getToken() . '=1'); ?>" title="<?php echo JText::sprintf('COM_FORUM_SET_TO', $stickyTitle); ?>">
							<span><?php echo $stickyAlt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $scls; ?>">
							<span><?php echo $stickyAlt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td>
					<span class="access <?php echo $color_access; ?>">
						<span><?php echo $this->escape($row->groupname); ?></span>
					</span>
				</td>
				<td>
					<span class="scope">
						<span><?php echo $this->escape($list[$row->scope][$row->scope_id]); /*$this->escape($row->scope); ?> <?php echo ($row->scope_id) ? '(' . $this->escape($row->scope_id) . ')' : '';*/ ?></span>
					</span>
				</td>
				<td>
					<span class="creator">
						<span><?php echo $this->escape($row->created_by); ?></span>
					</span>
				</td>
				<td>
					<span class="created">
						<span><?php echo $this->escape($row->created); ?></span>
					</span>
				</td>
			</tr>
<?php
		$k = 1 - $k;
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