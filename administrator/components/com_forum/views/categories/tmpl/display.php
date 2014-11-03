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

$canDo = ForumHelperPermissions::getActions('category');

JToolBarHelper::title(JText::_('COM_FORUM') . ': ' . JText::_('COM_FORUM_CATEGORIES'), 'forum.png');
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
JToolBarHelper::help('categories');
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

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="scopeinfo"><?php echo JText::_('COM_FORUM_FILTER_SCOPE'); ?>:</label>
		<select name="scopeinfo" id="scopeinfo" style="max-width: 20em;" onchange="document.adminForm.submit();">
			<option value=""<?php if ($this->filters['scopeinfo'] == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_FORUM_FILTER_SCOPE_SELECT'); ?></option>
			<option value="site:0"<?php if ($this->filters['scopeinfo'] == 'site:0') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_FORUM_NONE'); ?></option>
			<?php
			$html = '';
			//if ($this->results)
			//{
				include_once(JPATH_ROOT . DS . 'components' . DS . 'com_courses' . DS . 'models' . DS . 'course.php');

				$list = array(
					'group'  => array(),
					'course' => array()
				);

				$database = JFactory::getDBO();

				$database->setQuery("
					SELECT s.scope, s.scope_id,
						CASE
							WHEN scope='course' THEN c.alias
							WHEN scope='group' THEN g.cn
							ELSE s.scope
						END AS caption
					FROM #__forum_sections AS s
					LEFT JOIN #__xgroups AS g ON g.gidNumber=s.scope_id AND s.scope='group'
					LEFT JOIN #__courses_offerings AS c ON c.id=s.scope_id AND s.scope='course'
				");
				$results = $database->loadObjectList();

				foreach ($results as $result)
				{
					if ($result->scope == 'site')
					{
						continue;
					}
					switch ($result->scope)
					{
						case 'group':
							$result->caption = $result->caption ? $result->caption : $result->scope . ' (' . $result->scope_id . ')';
						break;
						case 'course':
							$offering = CoursesModelOffering::getInstance($result->scope_id);
							$course = CoursesModelCourse::getInstance($offering->get('course_id'));
							$result->caption = \Hubzero\Utility\String::truncate($course->get('alias'), 50) . ': ' . \Hubzero\Utility\String::truncate($offering->get('alias'), 50);
						break;
						default:
							$result->caption = $result->scope . ($result->scope_id ? ' (' . $this->escape(stripslashes($result->scope_id)) . ')' : '');
						break;
					}
					$list[$result->scope][$result->scope_id] = $result;
				}

				foreach ($list as $label => $optgroup)
				{
					$html .= ' <optgroup label="' . $label . '">';
					foreach ($optgroup as $result)
					{
						$html .= ' <option value="' . $result->scope . ':' . $result->scope_id . '"';
						if ($this->filters['scopeinfo'] == $result->scope . ':' . $result->scope_id)
						{
							$html .= ' selected="selected"';
						}
						$html .= '>' . $this->escape(stripslashes($result->caption));
						$html .= '</option>'."\n";
					}
					$html .= '</optgroup>'."\n";
				}
			//}
			echo $html;
			?>
		</select>

<?php if ($this->filters['scopeinfo']) { ?>
		<label for="field-section_id"><?php echo JText::_('COM_FORUM_FILTER_SECTION'); ?>:</label>
		<select name="section_id" id="field-section_id" onchange="document.adminForm.submit( );">
			<option value="-1"><?php echo JText::_('COM_FORUM_FILTER_SECTION_SELECT'); ?></option>
	<?php
	/*$list = array();
	foreach ($this->sections as $scope => $sections)
	{
		if ($scope == $this->filters['scope'])
		{
	?>
			<optgroup label="<?php echo $this->escape(stripslashes($scope)); ?>">
			<?php*/
			foreach ($this->sections as $section)
			{
				?>
				<option value="<?php echo $section->id; ?>"<?php if ($this->filters['section_id'] == $section->id) { echo ' selected="selected"'; } ?>><?php echo $this->escape(stripslashes($section->title)); ?></option>
				<?php
				/*if (!isset($list[$section->scope]))
				{
					$list[$section->scope] = array();
				}
				if (!isset($list[$section->scope][$section->scope_id]))
				{
					$list[$section->scope][$section->scope_id] = $scope;
				}*/
			}
			/*
			?>
			</optgroup>
	<?php
		}
	}*/
	?>
		</select>
<?php } ?>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->results );?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_FORUM_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_FORUM_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_FORUM_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_FORUM_COL_ACCESS', 'access', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_FORUM_COL_SCOPE', 'scope', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JText::_('COM_FORUM_THREADS'); ?></th>
				<th scope="col"><?php echo JText::_('COM_FORUM_POSTS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php echo $this->pageNav->getListFooter(); ?></td>
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
		switch ($row->state)
		{
			case '2':
				$task = 'publish';
				$alt = JText::_('JTRASHED');
				$cls = 'trash';
			break;
			case '1':
				$task = 'unpublish';
				$alt = JText::_('JPUBLISHED');
				$cls = 'publish';
			break;
			case '0':
			default:
				$task = 'publish';
				$alt = JText::_('JUNPUBLISHED');
				$cls = 'unpublish';
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
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
							<?php echo $this->escape(stripslashes($row->title)); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape(stripslashes($row->title)); ?>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $cls; ?>" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=' . $this->controller . '&section_id=' . $this->filters['section_id'] . '&task=' . $task . '&id=' . $row->id . '&' . JUtility::getToken() . '=1'); ?>" title="<?php echo JText::sprintf('COM_FORUM_SET_TO', $task); ?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $cls; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td>
					<span class="access <?php echo $color_access; ?>">
						<span><?php echo $row->groupname; ?></span>
					</span>
				</td>
				<td>
					<span class="scope">
						<span><?php echo isset($list[$row->scope][$row->scope_id]) ? $this->escape($list[$row->scope][$row->scope_id]->caption) : $this->escape($row->scope) . ' (' . $this->escape($row->scope_id) . ')'; ?></span>
					</span>
				</td>
				<td>
					<?php if ($row->threads > 0) { ?>
						<a class="glyph thread" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=threads&category_id=' . $row->id); ?>" title="<?php echo JText::_('COM_FORUM_VIEW_THREADS_FOR'); ?>">
							<span><?php echo $row->threads; ?></span>
						</a>
					<?php } else { ?>
						<span class="glyph thread">
							<span><?php echo $row->threads; ?></span>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($row->posts > 0) { ?>
						<a class="glyph comment" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=threads&category_id=' . $row->id); ?>" title="<?php echo JText::_('COM_FORUM_VIEW_POSTS_FOR'); ?>">
							<span><?php echo $row->posts; ?></span>
						</a>
					<?php } else { ?>
						<span class="glyph comment">
							<span><?php echo $row->posts; ?></span>
						</span>
					<?php } ?>
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