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

$canDo = ForumHelper::getActions('category');

JToolBarHelper::title('<a href="index.php?option=' . $this->option . '">' . JText::_('Forums') . '</a>', 'forum.png');
if ($canDo->get('core.admin')) {
	JToolBarHelper::preferences($this->option, '550');
	JToolBarHelper::spacer();
}
if ($canDo->get('core.edit.state')) {
	JToolBarHelper::publishList();
	JToolBarHelper::unpublishList();
	JToolBarHelper::spacer();
}
if ($canDo->get('core.create')) {
	JToolBarHelper::addNew();
}
if ($canDo->get('core.delete')) {
	JToolBarHelper::deleteList();
}
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
		<label for="scopeinfo"><?php echo JText::_('Scope:'); ?></label> 
		<select name="scopeinfo" id="scopeinfo" style="max-width: 20em;" onchange="document.adminForm.submit();">
			<option value=""<?php if ($this->filters['scopeinfo'] == '') { echo ' selected="selected"'; } ?>><?php echo JText::_('Select...'); ?></option>
			<option value="site:0"<?php if ($this->filters['scopeinfo'] == 'site:0') { echo ' selected="selected"'; } ?>><?php echo JText::_('[ None ]'); ?></option>
			<?php
			$html = '';
			//if ($this->results) 
			//{
				ximport('Hubzero_Group');
				ximport('Hubzero_View_Helper_Html');
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
							$result->caption = Hubzero_View_Helper_Html::shortenText($course->get('alias'), 50, 0) . ': ' . Hubzero_View_Helper_Html::shortenText($offering->get('alias'), 50, 0);
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
		<label for="field-section_id"><?php echo JText::_('Section:'); ?></label> 
		<select name="section_id" id="field-section_id" onchange="document.adminForm.submit( );">
			<option value="-1"><?php echo JText::_('COM_FORUM_FIELD_SECTION_SELECT'); ?></option>
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
				<th scope="col"><?php echo JHTML::_('grid.sort', 'ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Title', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'State', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Access', 'access', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'Scope', 'scope', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JText::_('Threads'); ?></th>
				<th scope="col"><?php echo JText::_('Posts'); ?></th>
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
	for ($i=0, $n=count( $this->results ); $i < $n; $i++) 
	{
		$row =& $this->results[$i];
		switch ($row->state) 
		{
			case '2':
				$task = 'publish';
				$img = 'disabled.png';
				$alt = JText::_('Trashed');
				$cls = 'trash';
			break;
			case '1':
				$task = 'unpublish';
				$img = 'publish_g.png';
				$alt = JText::_('Published');
				$cls = 'publish';
			break;
			case '0':
			default:
				$task = 'publish';
				$img = 'publish_x.png';
				$alt = JText::_('Unpublished');
				$cls = 'unpublish';
			break;
		}
		
		switch ($row->access)
		{
			case 0:
				$color_access = 'style="color: green;"';
				$task_access  = '1';
				$row->groupname = JText::_('Public');
				break;
			case 1:
				$color_access = 'style="color: red;"';
				$task_access  = '2';
				$row->groupname = JText::_('Registered');
				break;
			case 2:
				$color_access = 'style="color: black;"';
				$task_access  = '3';
				$row->groupname = JText::_('Special');
				break;
			case 3:
				$color_access = 'style="color: blue;"';
				$task_access  = '4';
				$row->groupname = JText::_('Protected');
				break;
			case 4:
				$color_access = 'style="color: red;"';
				$task_access  = '0';
				$row->groupname = JText::_('Private');
				break;
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
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id[]=<?php echo $row->id; ?>">
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
					<a class="state <?php echo $cls; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;section_id=<?php echo $this->filters['section_id']; ?>&amp;task=<?php echo $task; ?>&amp;id[]=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="Set this to <?php echo $task;?>">
						<span><?php if (version_compare(JVERSION, '1.6', 'lt')) { ?><img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /><?php } else { echo $alt; } ?></span>
					</a>
<?php } else { ?>
					<span class="state <?php echo $cls; ?>">
						<span><?php if (version_compare(JVERSION, '1.6', 'lt')) { ?><img src="images/<?php echo $img;?>" width="16" height="16" border="0" alt="<?php echo $alt; ?>" /><?php } else { echo $alt; } ?></span>
					</span>
<?php } ?>
				</td>
				<td>
					<!-- <a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;section_id=<?php echo $this->filters['section_id']; ?>&amp;task=access&amp;access=<?php echo $task_access; ?>&amp;id[]=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" <?php echo $color_access; ?> title="Change Access"> -->
						<span><?php echo $row->access_level; ?></span>
					<!-- </a> -->
				</td>
				<td>
<?php //if ($this->escape($row->group_alias)) { ?>
					<span class="scope">
						<span><?php echo isset($list[$row->scope][$row->scope_id]) ? $this->escape($list[$row->scope][$row->scope_id]->caption) : $this->escape($row->scope) . ' (' . $this->escape($row->scope_id) . ')'; ?></span>
					</span>
<?php //} ?>
				</td>
				<td>
<?php if ($row->threads > 0) { ?>
					<a class="glyph thread" href="index.php?option=<?php echo $this->option ?>&amp;controller=threads&amp;category_id=<? echo $row->id; ?>" title="<?php echo JText::_('View the threads for this category'); ?>">
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
					<a class="glyph comment" href="index.php?option=<?php echo $this->option ?>&amp;controller=threads&amp;category_id=<? echo $row->id; ?>" title="<?php echo JText::_('View the posts for this category'); ?>">
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
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>